<?php

/*
 * wurfl_class.php v1.15 (Oct, 13 2002)
 *
 * This is a working example of a class to read the WURFL xml, take a user agent
 * and make something useful with it. Once you will have created an object with
 * this class you have access to all its capabilities.
 *
 * Questions or comments can be sent to "Andrea Trasatti" <trasatti@bware.it>
 * Please, support this software, send any suggestion and improvement to me
 * or the mailing list and we will try to keep it updated and make it better
 * every day.
 *
 * This software is open-source and is offered AS IS, I take no responsability
 * of what you do with it or what it might cause to your applications or files
 * or anything else. You are free to use it in any way you like; I will not
 * be liable for anything related to this software.
 *
 * If you like it and use it, please let me know or contact the wmlprogramming
 * mailing list: wmlprogramming@yahoogroups.com
 *
 * License: GPL, please read the full detail on the GNU site
 * 	(http://www.gnu.org/copyleft/gpl.html)
 *
 */

// Where all data is stored (wurfl.xml, cache file, logs, etc)
if (!defined("DATADIR"))
    define("DATADIR", './');

// Set this true if you want to use cache. Strongly suggested
define("WURFL_USE_CACHE", true);
// Path and name of the cache file
define("CACHE_FILE", DATADIR . "cache.php");
// Path and name of the wurfl
define("WURFL_FILE", DATADIR . "wurfl.xml");
// Path and name of the log file
define("WURFL_LOG_FILE", DATADIR . "wurfl.log");
// Path and name of the file to store user_agent->id relation
define("WURFL_AGENT2ID_FILE", DATADIR . "agent2id.php4");
// Lock file for WURFL_AGENT2ID_FILE
define("WURFL_AGENT2ID_FILE_LOCK", DATADIR . "agent2id.lock");
// Set this true to use lock file before writing on WURFL_AGENT2ID_FILE
define("SAFE_WRITE_AGENT2ID", true);
// Set this true to enable debug features
define("DEBUG", true);

require_once("./wurfl_parser.php");

/**
 *
 * wurfl_class
 *
 * Example:
 * $myDevice = new wurfl_class($HTTP_USER_AGENT);
 * if ( $myDevice->browser_is_wap )
 * 	if ( $myDevice->capabilities['downloadfun']['downloadfun_support'] )
 * 		echo "downloadfun supported";
 * 	else
 * 		echo "WAP is supported, downloadfun is not";
 *
 */
class wurfl_class {

    /**
     * associative array created by wurfl_parser.php
     * @var associative array
     */
    var $_wurfl = "";

    /**
     * associative array user_agent=>id
     * @var associative array
     */
    var $_wurfl_agents = "";

    /**
     * device's complete user agent (just in case)
     * @var string
     */
    var $user_agent = "";

    /**
     * best fitting user agent found in the xml
     * @var string
     */
    var $wurfl_agent = "";

    /**
     * wurfl_id
     * @var string
     */
    var $id = "";

    /**
     * if true, Openwave's GUI (mostly wml 1.3) is supported
     * @var bool
     */
    var $GUI = false;

    /**
     * if this is a WAP device, this is set to true
     * @var bool
     */
    var $browser_is_wap = false;

    /**
     * associative array with all the device's capabilities.
     * 
     * Example :
     * $this->capabilities['downloadfun']['downloadfun_support'] 
     * 	true if downloadfun is supported, otherwise false
     *
     * @var associative array
     */
    var $capabilities = array();

    /**
     * Constructor, checks the user agent and sets the variables.
     *
     * @param $_ua	device's user_agent
     *
     * @access public
     *
     */
    function wurfl_class($_ua) {
        global $wurfl, $wurfl_agents;

        $this->_wurfl = $wurfl;
        $this->_wurfl_agents = $wurfl_agents;
        $this->_toLog[] = 'wurfl_class(' . $_ua . ') Constructor';
        if ((stristr($_ua, 'Mozilla') && !stristr($_ua, 'Sony')) || stristr($_ua, 'Opera')) {
            // � un browser web non lo cerco nemmeno nel wurfl
            $this->_toLog[] = 'Non � un browser WAP';
            $this->browser_is_wap = false;
        } else {
            $this->_toLog[] = 'Potrebbe essere un browser WAP, lo cerco';
            $_ua = trim(ereg_replace("UP.Link.*", "", $_ua));
            $this->_GetDeviceCapabilitiesFromAgent($_ua);
        }
        if (DEBUG)
            $this->_log();
    }

    /**
     * Given the device's id reads all its capabilities
     *
     * @param $_id	wurfl_id di un telefonino
     *
     * @access private
     *
     */
    function _GetFullCapabilities($_id) {
        $this->_toLog[] = '_GetFullCapabilities(' . $_id . ')';
        $$_id = $this->_GetDeviceCapabilitiesFromId($_id);
        $_curr_device = $$_id;
        $_fallback_list[] = $_id;
        while ($_curr_device['fall_back'] != 'generic') {
            $_fallback_list[] = $_curr_device['fall_back'];
            $this->_toLog[] = 'parent device:' . $_curr_device['fall_back'] . ' now going to read its capabilities';
            $$_curr_device['fall_back'] = $this->_GetDeviceCapabilitiesFromId($_curr_device['fall_back']);
            $_curr_device = $$_curr_device['fall_back'];
        }
        $this->_toLog[] = 'reading capabilities of \'generic\' device';
        $generic = $this->_GetDeviceCapabilitiesFromId('generic');
        $_fallback_list[] = 'generic';

        end($_fallback_list);

        $_final = $generic;
        for ($i = sizeof($_fallback_list) - 2; $i >= 0; $i--) {
            $curr_device = $_fallback_list[$i];
            while (list($key, $val) = each($$curr_device)) {
                if (is_array($val)) {
                    $_final[$key] = array_merge($_final[$key], $val);
                } else {
                    $_final[$key] = $val;
                }
            }
        }

        $this->capabilities = $_final;
    }

    /**
     * Given a device id reads its capabilities
     *
     * @param $_id	device's wurfl_id
     *
     * @access private
     *
     */
    function _GetDeviceCapabilitiesFromId($_id) {
        $this->_toLog[] = '_GetDeviceCapabilitiesFromId(' . $_id . ')';
        if ($_id == 'upgui_generic') {
            $this->GUI = true;
        }
        if (in_array($_id, $this->_wurfl_agents)) {
            $this->_toLog[] = 'I have it in wurfl_agents cache, done';
            return $this->_wurfl['devices'][$_id];
        }
        $this->_toLog[] = 'PANIC: the id is not present in wurfl_agents';
        // I should never get here!!
        return false;
    }

    /**
     * Given the user_agent reads the device's capabilities
     *
     * @param $_user_agent	device's user_agent
     *
     * @access private
     *
     */
    function _GetDeviceCapabilitiesFromAgent($_user_agent) {
        global $HTTP_ACCEPT, $HTTP_USER_AGENT;
        $this->_toLog[] = '_GetDeviceCapabilitiesFromAgent(' . $_user_agent . ')';
        if (trim($_user_agent) == '' || !$_user_agent) {
            // NO USER AGENT??? This is not a WAP device
            $this->browser_is_wap = false;
            return;
        }
        if (WURFL_USE_CACHE) {
            $this->_ReadFastAgentToId($HTTP_USER_AGENT);
            // if I find the device in my cache I'm done
            if ($this->browser_is_wap) {
                $this->_toLog[] = 'Device found in local cache, the id is ' . $this->id;
                $this->_GetFullCapabilities($this->id);
                return;
            }
        }

        $_ua = $_user_agent;
        $_ua_len = strlen($_ua);
        $_wurfl_user_agents = array_keys($this->_wurfl_agents);
        // Searching in wurfl_agents
        // The user_agent should not become shorter than 4 characters
        $this->_toLog[] = 'Searching in the agent database';
        while ($_ua_len > 4) {
            while ($_x = each($_wurfl_user_agents)) {
                if (substr($_x[1], 0, $_ua_len) == $_ua) {
                    $this->user_agent = $_user_agent;
                    $this->wurfl_agent = $_x[1];
                    $this->id = $this->_wurfl_agents[$_x[1]];
                    // calling FullCapabilities to define $this->capabilities
                    $this->_GetFullCapabilities($this->id);
                    $this->browser_is_wap = true;
                    reset($this->_wurfl_agents);
                    reset($_wurfl_user_agents);
                    if (WURFL_USE_CACHE) {
                        $this->_WriteFastAgentToId($HTTP_USER_AGENT);
                    }
                    return;
                }
            }
            // shortening the agent by one each time
            $_ua = substr($_ua, 0, -1);
            $_ua_len--;
            reset($_wurfl_user_agents);
        }

        $this->_toLog[] = 'I couldn\'t find the device in my list, the headers are my last chance';
        if (strstr($_user_agent, 'UP.Browser/') && strstr($_user_agent, '(GUI)')) {
            $this->browser_is_wap = true;
            $this->user_agent = $_user_agent;
            $this->wurfl_agent = 'upgui_generic';
            $this->id = 'upgui_generic';
        } else if (strstr($_user_agent, 'UP.Browser/')) {
            $this->browser_is_wap = true;
            $this->user_agent = $_user_agent;
            $this->wurfl_agent = 'uptext_generic';
            $this->id = 'uptext_generic';
        } else if (eregi('wml', $HTTP_ACCEPT) || eregi('wap', $HTTP_ACCEPT)) {
            $this->browser_is_wap = true;
            $this->user_agent = $_user_agent;
            $this->wurfl_agent = 'generic';
            $this->id = 'generic';
        } else {
            $this->_toLog[] = 'This should not be a WAP device, quitting';
            $this->browser_is_wap = false;
            $this->user_agent = $_user_agent;
            $this->wurfl_agent = 'generic';
            $this->id = 'generic';
            if (WURFL_USE_CACHE) {
                $this->_WriteFastAgentToId($HTTP_USER_AGENT);
            }
            return;
        }
        if (WURFL_USE_CACHE) {
            $this->_WriteFastAgentToId($HTTP_USER_AGENT);
        }
        // FullCapabilities defines $this->capabilities
        $this->_GetFullCapabilities($this->id);
    }

    /**
     * Given a capability name returns the value (true|false|<anythingelse>)
     *
     * @param $capability	capability name as a string
     *
     * @access public
     *
     */
    function getDeviceCapability($capability) {
        $this->_toLog[] = 'Searching for ' . $capability . ' as a capability';
        $deviceCapabilities = $this->capabilities;
        foreach ($deviceCapabilities as $group) {
            if (!is_array($group)) {
                continue;
            }
            while (list($key, $value) = each($group)) {
                if ($key == $capability) {
                    $this->_toLog[] = 'I found it, value is ' . $value;
                    if (DEBUG)
                        $this->_log();
                    return $value;
                }
            }
        }
        $this->_toLog[] = 'I could not find the requested capability, returning false';
        if (DEBUG)
            $this->_log();
        return false;
    }

    /**
     * Saves to file the correspondence between user_agent and wurfl_id
     *
     * @param $_ua	device's user_agent
     *
     * @access private
     *
     */
    function _WriteFastAgentToId($_ua) {
        if (file_exists(WURFL_AGENT2ID_FILE) && !is_writeable(WURFL_AGENT2ID_FILE)) {
            die('Unable to remove ' . WURFL_AGENT2ID_FILE);
            // I should never get here
            return;
        } else if (file_exists(WURFL_AGENT2ID_FILE)) {
            $file_ready = true;
        }
        $_ua = trim(ereg_replace("UP.Link.*", "", $_ua));
        if ($file_ready && SAFE_WRITE_AGENT2ID) {
            $cached_agents = file(WURFL_AGENT2ID_FILE);
            while ($t = each($cached_agents)) {
                if (stristr($t[1], $_ua)) {
                    $this->_toLog[] = 'This agent is already cached, I won\'t save it again';
                    return;
                }
            }
        }
        $agent_lock = fopen(WURFL_AGENT2ID_FILE_LOCK, "a");
        flock($agent_lock, LOCK_EX);
        $this->_toLog[] = 'locking cache file and writing. This device is ' . $_ua;
        $cache = "\tcase \"$_ua\":\n";
        $cache .= "\t\t" . '$this->user_agent = "' . $this->user_agent . "\";\n";
        $cache .= "\t\t" . '$this->wurfl_agent = "' . $this->wurfl_agent . "\";\n";
        $cache .= "\t\t" . '$this->id = "' . $this->id . "\";\n";
        $cache .= "\t\t" . '$this->browser_is_wap = ' . $this->browser_is_wap . ";\n";
        $cache .= "\t\t" . 'break;' . "\n";
        $cache .= "\tdefault:\n";
        $cache .= "\t\tbreak;\n";
        $cache .= "}\n";
        $cache .= "?>\n";
        if ($file_ready) {
            $fp_cache = fopen(WURFL_AGENT2ID_FILE, "a+");
            ftruncate($fp_cache, (filesize(WURFL_AGENT2ID_FILE) - 24));
        } else {
            $fp_cache = fopen(WURFL_AGENT2ID_FILE, "w");
            $agent_to_cache = "<?php\n";
            $agent_to_cache .= 'switch ($_ua) {' . "\n";
        }
        $agent_to_cache .= $cache;
        fwrite($fp_cache, $agent_to_cache);
        fclose($fp_cache);
        flock($agent_lock, LOCK_UN);
        fclose($agent_lock);
        $this->_toLog[] = 'Done caching user_agent to wurfl_id';
    }

    /**
     * Reads the file with the correspondence between user_agent and wurfl_id
     *
     * @param $_ua	device's user_agent
     *
     * @access private
     *
     */
    function _ReadFastAgentToId($_ua) {
        if (file_exists(WURFL_AGENT2ID_FILE)) {
            @require_once(WURFL_AGENT2ID_FILE);
        }
    }

    /**
     * Logging function, cicles $this->_toLog variable
     * and writes to file.
     *
     * @access private
     */
    function _log() {
        $_loopCount = sizeof($this->_toLog);
        $_logFP = fopen(WURFL_LOG_FILE, "a+");
        if ($_loopCount <= 0) {
            return;
        }
        fputs($_logFP, "-----Start " . date('r') . "-----\n");
        for ($i = 0; $i < $_loopCount; $i++) {
            fputs($_logFP, $this->_toLog[$i] . "\n");
        }
        fputs($_logFP, "-----End-----\n");
        fclose($_logFP);
        unset($this->_toLog);
        return;
    }

}

?>
