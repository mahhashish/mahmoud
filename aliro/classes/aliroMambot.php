<?php

/*******************************************************************************
 * Aliro - the modern, accessible content management system
 *
 * Aliro is open source software, free to use, and licensed under GPL.
 * You can find the full licence at http://www.gnu.org/copyleft/gpl.html GNU/GPL
 *
 * The author freely draws attention to the fact that Aliro derives from Mambo,
 * software that is controlled by the Mambo Foundation.  However, this section
 * of code is totally new.  If it should contain any fragments that are similar
 * to Mambo, please bear in mind (1) there are only so many ways to do things
 * and (2) the author of Aliro is also the author and copyright owner for large
 * parts of Mambo 4.6.
 *
 * Tribute should be paid to all the developers who took Mambo to the stage
 * it had reached at the time Aliro was created.  It is a feature rich system
 * that contains a good deal of innovation.
 *
 * Your attention is also drawn to the fact that Aliro relies on other items of
 * open source software, which is very much in the spirit of open source.  Aliro
 * wishes to give credit to those items of code.  Please refer to
 * http://aliro.org/credits for details.  The credits are not included within
 * the Aliro package simply to avoid providing a marker that allows hackers to
 * identify the system.
 *
 * Copyright in this code is strictly reserved by its author, Martin Brampton.
 * If it seems appropriate, the copyright will be vested in the Aliro Organisation
 * at a suitable time.
 *
 * Copyright (c) 2007 Martin Brampton
 *
 * http://aliro.org
 *
 * counterpoint@aliro.org
 *
 * aliroMambot is the class for mambot objects - the descriptors for Aliro plugins.
 *
 * aliroMambotHandler manages all the descriptors, operating as a cached singleton.
 * It provides some basic methods to support the installer, can return an array of
 * the mambots that will respond to a particular trigger (used, for example, to find
 * all the editors) and most significantly implements the trigger and call methods
 * that actually invoke mambots.  The method loadBotGroup is provided for compatibility
 * but does nothing since all mambots in Aliro are loaded using the smart class
 * loader when they are triggered - there is no need to call loadBotGroup.
 *
 */


class aliroMambot extends aliroDatabaseRow {
	protected $DBclass = 'aliroCoreDatabase';
	protected $tableName = '#__mambots';
	protected $rowKey = 'id';
	protected $handler = 'aliroMambotHandler';
	protected $formalfield = 'element';

}

// Provided only for backwards compatibility
class mosMambotHandler {

	public static function getInstance () {
		return aliroMambotHandler::getInstance();
	}
}

class aliroMambotHandler extends aliroCommonExtHandler  {

    protected static $instance = __CLASS__;

    private static $defaults = array (
    'onIniEditor' => 'bot_nulleditor',
    'onGetEditorContents' => 'bot_nulleditor',
    'onEditorArea' => 'bot_nulleditor'
    );

    private $_events=array();
    private $_bots=null;
    private $_bot_objects = array();
	private $_botsByName = array();

    protected $extensiondir = '/mambots/';

    protected function __construct() {
        $database = aliroCoreDatabase::getInstance();
        $this->_bots = $database->doSQLget( "SELECT element, class, triggers, published, params, 0 AS isdefault FROM #__mambots ORDER BY ordering");
		foreach ($this->_bots as $bot) $this->_botsByName[$bot->element] = true;
        foreach (self::$defaults as $trigger=>$default) {
        	$defobj = new stdClass;
        	$defobj->class = $default;
        	$defobj->triggers = $trigger;
        	$defobj->isdefault = 1;
        	array_push($this->_bots, $defobj);
        }
        foreach ($this->_bots as $key=>$bot) {
        	$triggers = explode (',', $bot->triggers);
        	foreach ($triggers as $trigger) $this->_events[trim($trigger)][] = $key;
        }
    }

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = parent::getCachedSingleton(self::$instance));
	}
	
	public function isPluginPresent ($formalname, $andPublished=false) {
		if (!isset($this->_botsByName[$formalname])) return false;
		if (!$andPublished) return true;
		$bot = $this->_botsByName[$formalname];
		return $bot->published ? true : false;
	}

	// Not currently used in Aliro or its common extensions - provided for compatibility?
	public function getMambotsForTrigger ($trigger) {
		if (isset($this->_events[$trigger])) foreach ($this->_events[$trigger] as $botkey) {
			$results[] = $this->_bots[$botkey];
		}
		else $results = array();
		return $results;
	}

    public function loadBotGroup( $group ) {
    	// Only required for backward compatibility
    	return true;
    }

    // The bulk of the work of running plugins is done here
    // The main method for invoking Aliro plugins
    public function trigger( $event, $args=null, $doUnpublished=false, $maxbot=0 ) {
        if ($args === null) $args = array();
        elseif (!is_array($args)) $args = array($args);
        $result = array();
        $botcount = 0;
        if (isset( $this->_events[$event] )) foreach ($this->_events[$event] as $botkey) {
           	$bot = $this->_bots[$botkey];
           	if ($bot->isdefault) {
           		if (!isset($defaultbotkey)) $defaultbotkey = $botkey;
           	}
           	else {
	           	$botparams = new aliroParameters($bot->params);
	           	if ($doUnpublished OR $bot->published) {
	           		$result[] = $this->runOneBot($botkey, $args, $event, $botparams, $bot->published);
	           		$botcount ++;
	           		if ($maxbot AND $botcount >= $maxbot) break;
	           	}
           	}
        }
        if (0 == $botcount AND isset($defaultbotkey)) $result[] = $this->runOneBot($defaultbotkey, $args, $event, '', '1');
        return $result;
    }

    private function runOneBot ($botkey, $args, $event, $botparams, $published) {
       	if (isset($this->_bot_objects[$botkey])) $botobject = $this->_bot_objects[$botkey];
       	else $botobject = $this->_bot_objects[$botkey] = new $this->_bots[$botkey]->class;
    	array_unshift($args, $event, $botparams, $published);
    	return call_user_func_array(array($botobject, 'perform'), $args);
    }

    public function countBots ($event) {
    	return isset($this->_events[$event]) ? count($this->_events[$event]) : 0;
    }

    // Trigger function for activating just one bot - provided for convenience in calling
    public function triggerOnce ($event, $args=null, $doUnpublished=false) {
    	return $this->trigger ($event, $args, $doUnpublished, 1);
    }

	// Alternative way to invoke a plugin - doesn't appear to be used now
    public function call( $event ) {
        $args = func_get_args();
        array_shift($args);
        $result = $this->trigger($event, $args);
        if (isset($result[0])) return $result[0];
        return null;
    }
}