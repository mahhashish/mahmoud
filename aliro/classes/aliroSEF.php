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
 * aliroSEF provides advanced SEF functions where sef_ext.php files are installed.
 * When SEF is turned off, aliroSEF still operates to achieve two things.  It will
 * redirect any SEF URLs that it recognises back to the non-SEF equivalents, with
 * a 301 redirect.  It also ensures that all URLs have ampersands correctly encoded.
 *
 * When SEF is turned on, but no sef_ext.php files are present, aliroSEF behaves
 * pretty much like the standard SEF functionality of Mambo 4.x or Joomla! 1.0.x.
 *
 * If components are activated through the admin interface and sef_ext.php files
 * are present, more advanced SEF processing takes place.  There are Aliro standard
 * sef_ext.php files for some common components, including the retro-content
 * component.
 *
 */

class aliroUnAccent {

	// private vars
	private static $instance = null;
	private $tranmap = array();

	// private function
	private function aliroUnaccent () {

	$this->tranmap = array(
      "\xC3\x80" => "A",   "\xC3\x81" => "A",   "\xC3\x82" => "A",   "\xC3\x83" => "A",
      "\xC3\x84" => "A",   "\xC3\x85" => "A",   "\xC3\x86" => "AE",  "\xC3\x87" => "C",
      "\xC3\x88" => "E",   "\xC3\x89" => "E",   "\xC3\x8A" => "E",   "\xC3\x8B" => "E",
      "\xC3\x8C" => "I",   "\xC3\x8D" => "I",   "\xC3\x8E" => "I",   "\xC3\x8F" => "I",
      "\xC3\x90" => "D",   "\xC3\x91" => "N",   "\xC3\x92" => "O",   "\xC3\x93" => "O",
      "\xC3\x94" => "O",   "\xC3\x95" => "O",   "\xC3\x96" => "O",   "\xC3\x98" => "O",
      "\xC3\x99" => "U",   "\xC3\x9A" => "U",   "\xC3\x9B" => "U",   "\xC3\x9C" => "U",
      "\xC3\x9D" => "Y",   "\xC3\x9E" => "P",   "\xC3\x9F" => "ss",
      "\xC3\xA0" => "a",   "\xC3\xA1" => "a",   "\xC3\xA2" => "a",   "\xC3\xA3" => "a",
      "\xC3\xA4" => "a",   "\xC3\xA5" => "a",   "\xC3\xA6" => "ae",  "\xC3\xA7" => "c",
      "\xC3\xA8" => "e",   "\xC3\xA9" => "e",   "\xC3\xAA" => "e",   "\xC3\xAB" => "e",
      "\xC3\xAC" => "i",   "\xC3\xAD" => "i",   "\xC3\xAE" => "i",   "\xC3\xAF" => "i",
      "\xC3\xB0" => "o",   "\xC3\xB1" => "n",   "\xC3\xB2" => "o",   "\xC3\xB3" => "o",
      "\xC3\xB4" => "o",   "\xC3\xB5" => "o",   "\xC3\xB6" => "o",   "\xC3\xB8" => "o",
      "\xC3\xB9" => "u",   "\xC3\xBA" => "u",   "\xC3\xBB" => "u",   "\xC3\xBC" => "u",
      "\xC3\xBD" => "y",   "\xC3\xBE" => "p",   "\xC3\xBF" => "y",
      "\xC4\x80" => "A",   "\xC4\x81" => "a",   "\xC4\x82" => "A",   "\xC4\x83" => "a",
      "\xC4\x84" => "A",   "\xC4\x85" => "a",   "\xC4\x86" => "C",   "\xC4\x87" => "c",
      "\xC4\x88" => "C",   "\xC4\x89" => "c",   "\xC4\x8A" => "C",   "\xC4\x8B" => "c",
      "\xC4\x8C" => "C",   "\xC4\x8D" => "c",   "\xC4\x8E" => "D",   "\xC4\x8F" => "d",
      "\xC4\x90" => "D",   "\xC4\x91" => "d",   "\xC4\x92" => "E",   "\xC4\x93" => "e",
      "\xC4\x94" => "E",   "\xC4\x95" => "e",   "\xC4\x96" => "E",   "\xC4\x97" => "e",
      "\xC4\x98" => "E",   "\xC4\x99" => "e",   "\xC4\x9A" => "E",   "\xC4\x9B" => "e",
      "\xC4\x9C" => "G",   "\xC4\x9D" => "g",   "\xC4\x9E" => "G",   "\xC4\x9F" => "g",
      "\xC4\xA0" => "G",   "\xC4\xA1" => "g",   "\xC4\xA2" => "G",   "\xC4\xA3" => "g",
      "\xC4\xA4" => "H",   "\xC4\xA5" => "h",   "\xC4\xA6" => "H",   "\xC4\xA7" => "h",
      "\xC4\xA8" => "I",   "\xC4\xA9" => "i",   "\xC4\xAA" => "I",   "\xC4\xAB" => "i",
      "\xC4\xAC" => "I",   "\xC4\xAD" => "i",   "\xC4\xAE" => "I",   "\xC4\xAF" => "i",
      "\xC4\xB0" => "I",   "\xC4\xB1" => "i",   "\xC4\xB2" => "IJ",  "\xC4\xB3" => "ij",
      "\xC4\xB4" => "J",   "\xC4\xB5" => "j",   "\xC4\xB6" => "K",   "\xC4\xB7" => "k",
      "\xC4\xB8" => "k",   "\xC4\xB9" => "L",   "\xC4\xBA" => "l",   "\xC4\xBB" => "L",
      "\xC4\xBC" => "l",   "\xC4\xBD" => "L",   "\xC4\xBE" => "l",   "\xC4\xBF" => "L",
      "\xC5\x80" => "l",   "\xC5\x81" => "L",   "\xC5\x82" => "l",   "\xC5\x83" => "N",
      "\xC5\x84" => "n",   "\xC5\x85" => "N",   "\xC5\x86" => "n",   "\xC5\x87" => "N",
      "\xC5\x88" => "n",   "\xC5\x89" => "n",   "\xC5\x8A" => "N",   "\xC5\x8B" => "n",
      "\xC5\x8C" => "O",   "\xC5\x8D" => "o",   "\xC5\x8E" => "O",   "\xC5\x8F" => "o",
      "\xC5\x90" => "O",   "\xC5\x91" => "o",   "\xC5\x92" => "CE",  "\xC5\x93" => "ce",
      "\xC5\x94" => "R",   "\xC5\x95" => "r",   "\xC5\x96" => "R",   "\xC5\x97" => "r",
      "\xC5\x98" => "R",   "\xC5\x99" => "r",   "\xC5\x9A" => "S",   "\xC5\x9B" => "s",
      "\xC5\x9C" => "S",   "\xC5\x9D" => "s",   "\xC5\x9E" => "S",   "\xC5\x9F" => "s",
      "\xC5\xA0" => "S",   "\xC5\xA1" => "s",   "\xC5\xA2" => "T",   "\xC5\xA3" => "t",
      "\xC5\xA4" => "T",   "\xC5\xA5" => "t",   "\xC5\xA6" => "T",   "\xC5\xA7" => "t",
      "\xC5\xA8" => "U",   "\xC5\xA9" => "u",   "\xC5\xAA" => "U",   "\xC5\xAB" => "u",
      "\xC5\xAC" => "U",   "\xC5\xAD" => "u",   "\xC5\xAE" => "U",   "\xC5\xAF" => "u",
      "\xC5\xB0" => "U",   "\xC5\xB1" => "u",   "\xC5\xB2" => "U",   "\xC5\xB3" => "u",
      "\xC5\xB4" => "W",   "\xC5\xB5" => "w",   "\xC5\xB6" => "Y",   "\xC5\xB7" => "y",
      "\xC5\xB8" => "Y",   "\xC5\xB9" => "Z",   "\xC5\xBA" => "z",   "\xC5\xBB" => "Z",
      "\xC5\xBC" => "z",   "\xC5\xBD" => "Z",   "\xC5\xBE" => "z",   "\xC6\x8F" => "E",
      "\xC6\xA0" => "O",   "\xC6\xA1" => "o",   "\xC6\xAF" => "U",   "\xC6\xB0" => "u",
      "\xC7\x8D" => "A",   "\xC7\x8E" => "a",   "\xC7\x8F" => "I",
      "\xC7\x90" => "i",   "\xC7\x91" => "O",   "\xC7\x92" => "o",   "\xC7\x93" => "U",
      "\xC7\x94" => "u",   "\xC7\x95" => "U",   "\xC7\x96" => "u",   "\xC7\x97" => "U",
      "\xC7\x98" => "u",   "\xC7\x99" => "U",   "\xC7\x9A" => "u",   "\xC7\x9B" => "U",
      "\xC7\x9C" => "u",
      "\xC7\xBA" => "A",   "\xC7\xBB" => "a",   "\xC7\xBC" => "AE",  "\xC7\xBD" => "ae",
      "\xC7\xBE" => "O",   "\xC7\xBF" => "o",
      "\xC9\x99" => "e",

      "\xC2\x82" => ",",        // High code comma
      "\xC2\x84" => ",,",       // High code double comma
      "\xC2\x85" => "...",      // Tripple dot
      "\xC2\x88" => "^",        // High carat
      "\xC2\x91" => "\x27",     // Forward single quote
      "\xC2\x92" => "\x27",     // Reverse single quote
      "\xC2\x93" => "\x22",     // Forward double quote
      "\xC2\x94" => "\x22",     // Reverse double quote
      "\xC2\x96" => "-",        // High hyphen
      "\xC2\x97" => "--",       // Double hyphen
      "\xC2\xA6" => "|",        // Split vertical bar
      "\xC2\xAB" => "<<",       // Double less than
      "\xC2\xBB" => ">>",       // Double greater than
      "\xC2\xBC" => "1/4",      // one quarter
      "\xC2\xBD" => "1/2",      // one half
      "\xC2\xBE" => "3/4",      // three quarters

      "\xCA\xBF" => "\x27",     // c-single quote
      "\xCC\xA8" => "",         // modifier - under curve
      "\xCC\xB1" => ""          // modifier - under line
	);

	}

    public static function getInstance () {
        return self::$instance ? self::$instance : (self::$instance = new self());
    }

	public function unaccent ($utf8string) {
		return strtr($utf8string, $this->tranmap);
	}

}

class aliroSEF extends aliroFriendlyBase {
	protected static $instance = __CLASS__;

	// The following are private
	private $live_site = '';
	private $home_page = false;
	private $mainmenu = array();
	private $config = null;
	
/* These should have been incorporate into $this->config	
	private $underscore = 0;
	private $enabled = 1;
	private $strip_chars = '';
	private $lower_case = '0';
	private $unique_id = '0';
	private $cache_time = '600';
	private $buffer_size = '500';
	private $home_title = 'Home';
	private $title_separator = '|';
	private $default_robots = 'index, follow';
	private $custom_code = array();
	private $custom_name = array();
	private $custom_PHP = array();
	private $custom_short = array();
	private $sef_content_task = array();
	private $sef_name_chars = array();
	private $sef_translate_chars = array();
	private $component_details = array();
	private $sef_substitutions_exact = array();
	private $sef_substitutions_in = array();
	private $sef_substitutions_out = array();
	private $legal_content_tasks = array();
*/
	
	private $sef_name_string = '';
	private $sef_translate_string = '';
	private $content_tasks = array (
		'findkey',
		'view',
		'section',
		'category',
		'blogsection',
		'blogcategorymulti',
		'blogcategory',
		'archivesection',
		'archivecategory',
		'save',
		'cancel',
		'emailform',
		'emailsend',
		'vote',
		'showblogsection'
		);
	private $content_menus = array();
	private $metadata = null;

	// The following are public
	public $content_data = null;
	public $content_items = array();
	public $content_sections = array();
	public $content_categories = array();

	// The following are private
	private $SEF_SPACE;
	private $cache = null;
	private $cached = array();
	private $cacheWritten = array();
	private $cacheObject = null;
	
	private $database = null;

	private function __construct () {
		$this->database = aliroDatabase::getInstance();
		$this->live_site = $this->getCfg('live_site');

		/*******************************************************************************
		**  The following are parameters for the ReMOSef Search Engine
		**  Optimisation component.  $this->SEF_SPACE should be set to the
		**  character that is to replace blanks in names that form the URL.
		**  You can privatey this, although the only sensible choices seem to be
		**  underscore or hyphen (_ or -).
		**
		**  The arrays $this->config->custom_code and $this->config->custom_name must be kept exactly
		**  in step with each other.  $this->custom_code is a list of the components
		**  that are to be handled by ReMOSef.
		**
		**  The array $this->config->custom_name is the alternative name that will be used
		**  in the optimised URL to identify the component and can be whatever
		**  you please so long as it is unique and legal for a URL.  Apart from
		**  using the custom name for the component, ReMOSef will do no further
		**  translation of the URL than is done by the standard Mambo SEF - UNLESS
		**  there is a sef_ext.php file installed for that component.  The
		**  exception to this is ReMOSitory - the optimisation code for Remository
		**  is integrated in ReMOSef.  For other components, if a sef_ext.php is
		**  present, it will be invoked by ReMOSef.
		**
		**  The array $remosef_content_task is capable of translating the tasks
		**  understood by the Mambo content component.  Please DO NOT CHANGE what
		**  is to the left of the equals sign.  When putting different values on
		**  the right hand side, remember that they must all be different, and must
		**  also be different from any of the custom names used for components.
		**  Without that, Remosef cannot figure out what a SEF URL means and will
		**  give unpredictable results.
		**
		**  Note that the names in custom_name must not be allowed to clash with
		**  the names used as tasks by the content component, in their
		**  translated form - see below.
		*******************************************************************************/
		// Use of underscore is NOT recommended, as search engines then do not see the words
		global $_SEF_SPACE;									// divide words with hyphens
		$this->SEF_SPACE = $_SEF_SPACE = "-";				// divide words with hyphens
		/*******************************************************************************
		**  The following are the parameters for the optional content specific
		**  URL optimisation.  They are not used within the standard SEF processing
		**  unless you add in a sef_ext.php that is integrated with the Mambo SEF.
		**
		**  The following two lines define the translations that SEF will perform on
		**  names of sections and categories when translating them for inclusion in a URL.
		**  Each item in $this->config->sef_name_chars is translated into the corresponding
		**  element of $this->config->sef_translate_chars.
		**
		**  NOTE it is important that space be the last translate character, since the
		**  characters are processed in the order in which they appear.  Since earlier
		**  translates may create new spaces, it is vital that the space translation is
		**  done last.
		**
		**  You can extend these arrays as you wish, although it is obviously important
		**  to make sure that the items of one match the items of the other exactly.
		*******************************************************************************/
		$this->sef_name_string = '"\'';
		$this->sef_translate_string = '--';

		$this->cache =new aliroCache('aliroSEF');
		$this->config = $this->cache->get('sefConfig');
		if (!$this->config) {
			$helper = new aliroSEFHelper();
			$this->config = $helper->getConfig($this->SEF_SPACE, $this->content_tasks);
			$this->cache->save($this->config);
		}

		$this->mainmenu = aliroMenuHandler::getInstance()->getByParentOrder('mainmenu');
		if ($this->mainmenu) foreach ($this->mainmenu as $menu) {
			$stage2 = parse_url($menu->link);
			if (isset($stage2['query'])) {
				parse_str($stage2['query'], $parms);
				if (isset($parms['option']) AND 'com_content' == $parms['option']) {
					if (isset($parms['task'])) $this->content_menus[$parms['task']][$menu->id] = $parms;
				}
			}
		}

		$this->cached = $this->cache->get('sefDataURI');
		if (!$this->cached) {
			$this->cached['SEF'] = $this->cached['Time'] = array();
			$results = $this->database->doSQLget("SELECT * FROM #__remosef_uri ORDER BY refreshed DESC LIMIT {$this->config->buffer_size}");
			foreach ($results as $result) {
				$uri = $result->uri;
				$this->cached['SEF'][$uri] = $result->sef;
				$this->cached['Time'][$uri] = $result->refreshed;
			}
			unset($results);
			$this->cache->save($this->cached);
		}
	}

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance());
	}
	
	public function clearCache () {
		$this->cache->clean();
	}

	public function getContentMenuInfo () {
		return $this->content_menus;
	}

	public function nameForURL ($string) {
		$strips = explode ('|', $this->config->strip_chars);
		$string = str_replace($strips, '', (string) $string);
		$string = aliroUnaccent::getInstance()->unaccent($string);
       	$string = str_replace($this->config->sef_name_chars, $this->config->sef_translate_chars, $string);
		$string = urlencode($string);
		return $string;
	}

	public function translateContentTask ($task) {
		return isset($this->config->sef_content_task[$task]) ? $this->config->sef_content_task[$task] : $task;
	}

	public function untranslateContentTask ($tr_task) {
		$task = array_search ($tr_task, $this->config->sef_content_task);
		if (!$task) $task = $tr_task;
		return in_array($task, $this->content_tasks) ? $task : null;
	}

	private function analyseStandardURI ($uri) {
		$_SERVER['REQUEST_URI'] = $uri;
		// Should think about using cache for this
		if (isset($this->cached['metadata'][$uri])) $this->metadata = $this->cached['metadata'][$uri];
		else {
			$this->database->setQuery("SELECT * FROM #__remosef_metadata WHERE uri = '$uri'");
			$this->database->loadObject($this->metadata);
			if (is_null($this->metadata)) $this->metadata =  new stdClass();
			$this->cached['metadata'][$uri] = $this->metadata;
			$this->cache->save($this->cached, 'sefDataURI');
		}
		$mainparts = explode('?', $uri);
		if (empty($mainparts[1])) return;
		$_SERVER['QUERY_STRING'] = $mainparts[1];
		$vars = explode('&', $mainparts[1]);
		foreach ($vars as $var) {
			$parts = explode('=', $var);
			if (!empty($parts[1])) {
				$_REQUEST[$parts[0]] = $_GET[$parts[0]] = $parts[1];
			}
		}
	}
	
	private function redirect301 ($to) {
		header ('HTTP/1.1 301 Moved Permanently');
   		header ('Location:'.$to);
   		exit;
	}

	public function sefRetrieval() {
		$uri = $_SERVER['REQUEST_URI'];
		$sublength = strlen(dirname($_SERVER['PHP_SELF']));
		if (1 < $sublength) $uri = substr($uri,$sublength);
	    $uri = str_replace(array('!', '%21', '&amp;'), array('','','&'), $uri);
		if (!$uri OR $uri == '/' or $uri == '/index.php') {
			$this->home_page = true;
			return 0;
		}
	    if (preg_match('/(\b)GLOBALS|_REQUEST|_SERVER|_ENV|_COOKIE|_GET|_POST|_FILES|_SESSION(\b)/i', $uri) > 0) {
	        die('Invalid Request');
	    }
	    $regex = '#[<>\(\)@"\']+|/\?#';
	    if (preg_match($regex, $uri)) return 1;
	    $saveuri = $uri;
		$uri = str_replace('//', '/', $uri);
		if ($this->config->underscore AND $this->SEF_SPACE == '-' AND strpos($uri,'_') !== false) $uri = str_replace('_', '-', $uri);
	    $exactback = array_search($uri, $this->config->sef_substitutions_exact);
	    if (false === $exactback AND '/' != substr($uri,-1)) {
	    	$exactback = array_search($uri.'/', $this->config->sef_substitutions_exact);
	    	if (false !== $exactback) $this->redirect301($this->live_site.$uri.'/');
	    }
	    if (false !== $exactback) {
	    	$this->analyseStandardURI($exactback);
	    	return 0;
	    }
	    $uri = @preg_replace(array_keys($this->config->sef_substitutions_in), array_values($this->config->sef_substitutions_in), $uri);
		if ($indexloc = strpos($uri, 'index.php?')) {
			if ($_SERVER['REQUEST_METHOD'] == 'GET') {
				$sefagain = substr($this->sefRelToAbs(substr($uri,$indexloc), false),strlen($this->live_site));
				if ($saveuri != $sefagain) $this->redirect301($sefagain);
			}
			return 0;
		}
		elseif (false !== strpos($uri,'index2.php') OR false !== strpos($uri,'index3.php')) return 0;
		$retrieved = $this->retrieveURI ($uri);
		if (!$retrieved AND '/' != substr($uri,-1)) {
			$retrieved = $this->retrieveURI($uri.'/');
			if ($retrieved) $this->redirect301($this->live_site.$uri.'/');
		}
		if (!$retrieved) {
			$helper = new aliroSEFHelper();
			$retrieved = $helper->basicRetrieve($uri, $this->config, $this, $this->live_site);
			if ($retrieved) trigger_error('Had to invoke SEF basicRetrieve: '.$uri.' : '.$retrieved);
		}

		if ($retrieved) {
			$retrieved = 'index.php?'.$retrieved;
			$sefagain = substr($this->sefRelToAbs($retrieved),strlen($this->live_site));
			if ($saveuri != $sefagain AND $_SERVER['REQUEST_METHOD'] == 'GET') $this->redirect301($sefagain);
    		$this->analyseStandardURI($retrieved);
			$returncode = 0;
		}
		else $returncode = 1;

		return $returncode;
	}
	
	private function retrieveURI ($sef) {
		$retrieved = array_search($sef, $this->cached['SEF']);
		if ($retrieved) return $retrieved;
		$coded_sef = $this->database->getEscaped($sef);
		$this->database->setQuery("SELECT uri FROM #__remosef_uri WHERE sef='$coded_sef'");
		$this->database->loadObject($sefdata);
		if ($sefdata) return $sefdata->uri;
		else return false;
	}

	// Not intended for general use - public for use by helper class
	public function invoke_plugin ($i, $method, $parm1, $parm2=0) {
		error_reporting(E_ALL);
		require_once($this->config->custom_PHP[$i]);
		$classname = 'sef_'.$this->config->custom_short[$i];
		$compname = 'com_'.$this->config->custom_short[$i];
		$maptags = isset($this->config->component_details[$compname]) ? $this->config->component_details[$compname] : array();
		if (method_exists($classname, 'getInstance')) {
			$plugin = call_user_func(array($classname, 'getInstance'));
			if ('create' == $method) return $plugin->$method($parm1, $this->config->lower_case, $this->config->unique_id, $maptags);
			else return $plugin->revert($parm1, $parm2, $maptags);
		}
		else {
			$callplugin = array($classname, $method);
			if ('create' == $method) return call_user_func ($callplugin, $parm1, $this->config->lower_case, $this->config->unique_id, $maptags);
			else return call_user_func ($callplugin, $parm1, $parm2, $maptags);
		}
	}

	// Not intended for general use - public for use by helper class
	public function default_revert ($specialname) {
		$request = explode($specialname.'/', $_SERVER['REQUEST_URI']);
		if (isset($request[1])) $parmset = explode("/", $request[1]);
		else $parmset = array();
		$QUERY_STRING = '';
		foreach($parmset as $values) {
			$parts = explode(",", $values);
			if (count($parts) > 1) {
				$_REQUEST[$parts[0]] = $_GET[$parts[0]] = $parts[1];
				if ($parts[0] == 'option') $QUERY_STRING .= "option=$parts[1]";
				else $QUERY_STRING .= "&$parts[0]=$parts[1]";
			}
		}
		return $QUERY_STRING;
	}

	private function parse ($string, &$parms) {
		$parms = array();
		$parts = explode('&', $string);
		foreach ($parts as $part) {
			$assigns = explode('=', $part);
			if (count($assigns) == 2) $parms[$assigns[0]] = $assigns[1];
		}
	}

    public function getHead($title, $metatags, $customtags) {
		$head = $found = array();
		$block['title'] = 1;
		$sitename = $this->getCfg('sitename');
		if ($this->home_page) $extratitle = $this->config->home_title;
		elseif (empty($this->metadata->htmltitle)) {
			if (strlen($title) > strlen($sitename)) $extratitle = substr($title, 0, -(strlen($sitename)+3));
			else $extratitle = '';
		}
		else $extratitle = htmlspecialchars($this->metadata->htmltitle, ENT_QUOTES, 'UTF-8');
		if ($extratitle) {
			if (strlen($extratitle) + strlen($sitename) < 60) $extratitle .= ' '.$this->config->title_separator.' '.$sitename;
		}
		else $extratitle = $sitename;
		$head[] = '<title>' . $extratitle . '</title>';
		
		if (!empty($this->metadata->description)) {
			$head[] = $this->makeMeta('description', htmlspecialchars($this->metadata->description, ENT_QUOTES, 'UTF-8'));
			$block['description'] = 1;
		}
		if (!empty($this->metadata->keywords)) {
			$head[] = $this->makeMeta('keywords', htmlspecialchars($this->metadata->keywords, ENT_QUOTES, 'UTF-8'));
			$block['keywords'] = 1;
		}
		if (!empty($this->metadata->robots)) {
			$head[] = $this->makeMeta('robots', htmlspecialchars($this->metadata->robots, ENT_QUOTES, 'UTF-8'));
			$block['robots'] = 1;
		}

        foreach ($metatags as $name=>$meta) {
			if (isset($block[$name]) OR empty($meta[0])) continue;
			$found[$name] = 1;
            if ($meta[1]) $head[] = $meta[1];
			$head[] = $this->makeMeta ($name, $meta[0]);
            if ($meta[2]) $head[] = $meta[2];
        }

		if (empty($block['description']) AND empty($found['description'])) $head[] = $this->makeMeta('description', htmlspecialchars($this->getCfg('MetaDesc'), ENT_QUOTES, 'UTF-8'));
		if (empty($block['keywords']) AND empty($found['keywords'])) $head[] = $this->makeMeta('keywords', htmlspecialchars($this->getCfg('MetaKeys'), ENT_QUOTES, 'UTF-8'));
		if (empty($block['robots']) AND empty($found['robots'])) $head[] = $this->makeMeta('robots', $this->config->default_robots);

        foreach ($customtags as $html) $head[] = $html;
        return implode( "\n", $head )."\n";
    }

	function makeMeta ($name, $value) {
		return <<<META_DATA
<meta name="$name" content="$value" />
META_DATA;

	}
	
	public function sefComponentName ($cname) {
		$i = array_search($cname,$this->config->custom_code);
		return ($i !== false AND $i !== null) ? $this->config->custom_name[$i] : $cname;
	}

	public function sefRelToAbs ($string, $specialamp=true) {
		if ($string == 'index.php') return $this->live_site.'/';
		if (strtolower(substr($string,0,9)) != 'index.php' OR eregi('^(([^:/?#]+):)',$string)) return $string;
		$string = str_replace('&amp;', '&', $string);
		$clean_string = preg_replace('/\&Itemid=[0-9]*/', '', $string);
        $homelink = $this->mainmenu[0]->link;
		if ($clean_string == $homelink) return $this->live_site.'/';
		if (!($this->getCfg('sef')) OR !$this->config->enabled) return $this->live_site.'/'.($specialamp ? str_replace( '&', '&amp;', $clean_string ) : $clean_string);
		$string = substr($clean_string,10);
		if (isset($this->config->sef_substitutions_exact['/'.$clean_string])) return $this->live_site.$this->config->sef_substitutions_exact['/'.$clean_string];
		if (isset($this->cached['SEF'][$clean_string]) AND (time() - $this->config->cachedTime[$clean_string]) < $this->config->cache_time) return $this->live_site.$this->cached['SEF'][$clean_string];
		$oktasks = true;
		$option = $task = '';
		$this->parse($string, $params);
		foreach ($params as $key=>$value) {
			$lowkey = strtolower($key);
			$lowvalue = strtolower($value);
			$unset = true;
			switch ($lowkey) {
				case 'option':
				    $option = $lowvalue;
				    break;
				case 'task':
				    $task = $value;
					if ($lowvalue == 'new' OR $lowvalue == 'edit') $oktasks = false;
					break;
				default:
					$check_params[$lowkey] = $key;
					$unset = false;
			}
			if ($unset) unset($params[$key]);
		}
		// Process content items
		if (($option == 'com_content' OR $option == 'content') AND $oktasks) {
			/*
			Content
			index.php?option=com_content&task=$task&sectionid=$sectionid&id=$id&Itemid=$Itemid&limit=$limit&limitstart=$limitstart
			*/
			$content_sef = _ALIRO_CLASS_BASE.'/components/com_content/sef_ext.php';
			if (file_exists($content_sef)) {
				require_once($content_sef);
				$result = sef_content::create($task, $params, $this->config->lower_case, $this->config->unique_id);
				return $this->live_site.$this->outSubstitution($string, $result);
			}
			$keys = array('sectionid', 'id', 'itemid', 'limit', 'limitstart', 'year', 'month', 'module', 'lang');
			$result = '/content/'.$task.'/';
			foreach ($keys as $key) {
				if (isset($check_params[$key])) {
					$pkey = $check_params[$key];
					$result .= $params[$pkey].'/';
				}
			}
			return $this->live_site.$this->outSubstitution($string, $result);
		}
		// Process customised components
		$i = array_search($option,$this->config->custom_code);
		if ($i !== false AND $i !== null) {
			if ($this->config->custom_PHP[$i] AND file_exists($this->config->custom_PHP[$i])) {
				$result = $this->invoke_plugin ($i, 'create', $clean_string);
			}
			else $result = $this->componentDetails($params,$task);
			$cname = $this->config->custom_name[$i];
			$result = '/'.($this->config->lower_case ? strtolower($cname) : $cname).'/'.$result;
			return $this->live_site.$this->outSubstitution($string, $result);
		}
		// Process ordinary components
		if (strpos($option,'com_')===0 AND $option != 'com_registration' AND $oktasks) {
			$result = "/component/option,$option/".$this->componentDetails($params,$task);
			return $this->live_site.$this->outSubstitution($string, $result);
		}
		// Anything else is returned as received, except it is guaranteed that & will be &amp;
		return $this->live_site.'/'.($specialamp ? str_replace( '&', '&amp;', $clean_string ) : $clean_string);
	}

	private function outSubstitution ($inuri, $outuri) {
		$now = time();
		// if ($this->config->underscore AND $this->SEF_SPACE == '-' AND strpos($outuri,'_') !== false) $outuri = str_replace('_', '-', $outuri);
		$finishedurl = @preg_replace(array_keys($this->config->sef_substitutions_out), array_values($this->config->sef_substitutions_out), $outuri);
		if (isset($this->cached['SEF'][$inuri]) AND $finishedurl == $this->cached['SEF'][$inuri] AND ($now - $this->cached['Time'][$inuri] < (int) $this->config->cache_time)) return $finishedurl;
		$this->cached['SEF'][$inuri] = $finishedurl;
		$this->cached['Time'][$inuri] = $now;
		$this->cache->save($this->cached, 'sefDataURI');
		$sef = $this->database->getEscaped($finishedurl);
		$uri = $this->database->getEscaped($inuri);
		$this->database->doSQL("UPDATE #__remosef_uri SET sef = '$sef', refreshed = $now, marker = 1 - marker WHERE uri = '$uri'");
		if (0 == $this->database->getAffectedRows()) {
			$this->database->doSQL("INSERT INTO #__remosef_uri (uri, sef, refreshed) VALUES ('$uri', '$sef', $now)");
		}
		if (50 == mt_rand(0,99)) {
			$weekago = $now - 7*24*60*60;
			$chkcode = 'option=com_remository&Itemid=65&func=download&id';
			$this->database->doSQL("DELETE FROM #__remosef_uri WHERE uri LIKE '$chkcode%' AND refreshed < $weekago");
		}
		return $finishedurl;
	}

	private function componentDetails (&$params, $task) {
		$string = ($task ? "task,$task/" : '');
		foreach ($params as $key=>$param) {
                    $param = urlencode($param);
                    $string .= "$key,$param/";
                }
		return $string;
	}


}