<?php

/*******************************************************************************
 * Aliro - the modern, accessible content management system
 *
 * This code is copyright (c) Aliro Software Ltd - please see the notice in the
 * index.php file for full details or visit http://aliro.org/copyright
 *
 * Some parts of Aliro are developed from other open source code, and for more
 * information on this, please see the index.php file or visit
 * http://aliro.org/credits
 *
 * Author: Martin Brampton
 * counterpoint@aliro.org
 *
 * aliroSEFHelper provides substantial but infrequently used methods for
 * aliroSEF.  It is a separate class purely for efficiency, to avoid
 * loading so much code.
 *
 */

class aliroSEFHelper {
	
	public function getConfig ($sefspace, $content_tasks) {
		$config = new stdClass();
		
		$config->enabled = 0;
		$config->url_rewrite = 0;
		$config->buffer_size = '100';
		$config->use_cache = 1;
		$config->cache_time = '600';
		$config->strip_chars = '';
		$config->lower_case = '0';
		$config->unique_id = '0';
		$config->underscore = 0;
		$config->log_transform = 0;
		$config->pagetitles = 1;
		$config->max_words = 12;
		$config->home_title = 'Home';
		$config->default_robots = 'index, follow';
		$config->title_separator = '|';
		$config->google_verify = '';
		$config->google_analytics = '';
		
		$config->custom_code = array();
		$config->custom_name = array();
		$config->custom_name_list = '';
		$config->custom_PHP = array();
		$config->custom_short = array();
		$config->sef_content_task = array();
		$config->sef_name_chars = array();
		$config->sef_translate_chars = array();
		$config->component_details = array();
		$config->sef_substitutions_exact = array();
		$config->sef_substitutions_in = array();
		$config->sef_substitutions_out = array();
		$config->legal_content_tasks = array();
		$config->legal_content_list = '';
		$config->sef_name_chars = array(' ');
		$config->sef_translate_chars = array($sefspace);

		$baseconfig = aliroComponentConfiguration::getInstance('cor_sef');
		foreach (get_object_vars($baseconfig) as $name=>$value) $config->$name = $value;
		
		$configdata = aliroDatabase::getInstance()->doSQLget ("SELECT * FROM #__remosef_config");
		foreach ($configdata as $item) {
			if ('components' == $item->type) {
				$config->custom_code[] = $item->name;
				$config->custom_name[] = $item->modified;
			}
			elseif ('characters' == $item->type) {
				$config->sef_name_chars[] = $item->name;
				$config->sef_translate_chars[] = $item->modified;
			}
			elseif ('substitutions' == $item->type) {
				$config->sef_substitutions_exact[$item->name] = $item->modified;
			}
			elseif ('substitutions_in' == $item->type) {
				$config->sef_substitutions_in["#$item->name#i"] = $item->modified;
			}
			elseif ('substitutions_out' == $item->type) {
				$config->sef_substitutions_out["#$item->name#i"] = $item->modified;
			}
			elseif ('content' == $item->type) $config->sef_content_task[$item->name] = $item->modified;
			else $config->component_details[$item->type][$item->name] = $item->modified;
		}
		unset($configdata);
		$strips = explode ('|', $config->strip_chars);
		$strips[] = '"';
		foreach ($strips as $strip) {
			$config->sef_name_chars[] = $strip;
			$config->sef_translate_chars[] = '';
		}
		if (!in_array('&', $config->sef_name_chars)) {
			$config->sef_name_chars[] = '&';
			$config->sef_translate_chars[] = '+';
		}
		if (!in_array('/', $config->sef_name_chars)) {
			$config->sef_name_chars[] = '/';
			$config->sef_translate_chars[] = '-';
		}
		foreach ($content_tasks as $task) {
			$config->legal_content_tasks[] = isset($config->sef_content_task[$task]) ? $config->sef_content_task[$task] : $task;
		}
		foreach ($config->custom_code as $code) {
			$codefile = _ALIRO_ABSOLUTE_PATH."/components/$code/sef_ext.php";
			$seffile = dirname(__FILE__)."/sef_ext/$code/sef_ext.php";
			if (file_exists($codefile)) $config->custom_PHP[] = $codefile;
			elseif (file_exists($seffile)) $config->custom_PHP[] = $seffile;
			else $config->custom_PHP[] = false;
			$split = explode('_',$code);
			$config->custom_short[] = $split[1];
		}
		$config->legal_content_list = str_replace(' ', $sefspace, implode('/', $config->legal_content_tasks));
		$config->custom_name_list = str_replace(' ', $sefspace, implode('/', $config->custom_name));
		return $config;
	}
	
	public function basicRetrieve ($uri, $config, $alirosef, $live_site, $sefspace) {
		$url_array = explode('/', $uri);
		if (0 == strlen($url_array[0])) array_shift($url_array);
		if ($l = count($url_array) AND 0 == strlen($url_array[$l-1])) array_pop($url_array);
		$prefix = $subdir = '';
		$legal = $config->custom_name_list.'/content/component/'.$config->legal_content_list;
		while (count($url_array)) {
			$element = array_shift($url_array);
			if ($element) {
				if (false !== stripos($legal,$element)) {
					$prefix = $element;
					break;
				}
				elseif (stripos($live_site, $element) === false) break;
				else $subdir .= '/'.$element;
			}
		}

		$QUERY_STRING = '';
		$foundit = false;
		/**
		* Content
		* /$option/$task/$sectionid/$id/$limit/$limitstart
		*/
		if ($prefix == 'content') {
			$foundit = true;
			$_REQUEST['option'] = $_GET['option'] = $option = 'com_content';
			// language hook for content
			$lang = "";
			$parms = array();
			foreach($url_array as $key=>$value) {
				if ( strcasecmp(substr($value,0,5),'lang,') == 0 ) {
					$parts = explode(",", $value);
					if (count($parts) > 1) {
						$lang = $_REQUEST['lang'] = $_GET['lang'] = $parts[1];
					}
				}
				elseif (strlen($value)) $parms[] = $value;
			}
			if (empty($parms)) return false;
			// $option/$task/$sectionid/$id/$limit/$limitstart
			$task = $alirosef->untranslateContentTask($parms[0]);
			$_REQUEST['task'] = $_GET['task'] = $task;
			$QUERY_STRING .= "option=com_content&task=$task";
			if ($task == 'archivecategory') {
				$_REQUEST['year'] = $_GET['year'] = $year = intval(@$parms[1]);
				$_REQUEST['month'] = $_GET['month'] = $month = intval(@$parms[2]);
				$_REQUEST['module'] = $_GET['module'] = $module = intval(@$parms[3]);
				$QUERY_STRING .= "&year=$year&month=$month&module=$module";
			}
			else {
				$num = count($parms);
				for ($i = 1; $i <= $num-1; $i++) {
					if (strcmp($parms[$i], (int)$parms[$i]) !== 0) return false;
				}
				$i = 1;
				if (5 == $num OR 3 == $num) {
					$_REQUEST['sectionid'] = $_GET['sectionid'] = $sectionid = $parms[$i];
					$QUERY_STRING .= "&sectionid=$sectionid";
					$i++;
				}
				if ($num > 1) {
					$_REQUEST['id'] = $_GET['id'] = $id = $parms[$i];
					$QUERY_STRING .= "&id=$id";
				}
				if ($num > 3) {
					$_REQUEST['limit'] = $_GET['limit'] = $limit = $parms[$i+1];
					$_REQUEST['limitstart'] = $_GET['limitstart'] = $limitstart = $parms[$i+2];
					$QUERY_STRING .= "&limit=$limit&limitstart=$limitstart";
				}
			}
			if ($lang!="") {
				$QUERY_STRING .= "&lang=$lang";
			}
		}

		/*
		Components
		http://www.domain.com/component/$name,$value
		*/
		elseif ($prefix == 'component') {
			$QUERY_STRING = $this->default_revert('component', $uri);
			if ($QUERY_STRING) $foundit = true;
		}
		else {
			// Wouldn't be necessary, but wanted to avoid negative parameter to sef_ext.php
			array_unshift ($url_array, $prefix);
			array_unshift ($url_array, 'dummy');
			foreach ($config->custom_name as $i=>$compname) {
				$compname = str_replace(' ', $sefspace, $compname);
				if (isset($url_array[1]) AND 0 == strcasecmp($url_array[1],$compname)) {
					$origname = $config->custom_code[$i];
					if ($config->custom_PHP[$i] AND file_exists($config->custom_PHP[$i])) {
						if (!empty($url_array[2])) {
							$customsef = $alirosef->invoke_plugin($i, 'revert', $url_array, 0);
							if ($customsef) $QUERY_STRING .= 'option='.$origname.$customsef;
							else return false;
						}
						else $QUERY_STRING .= 'option='.$origname;
					}
					else $QUERY_STRING .= 'option='.$origname.$this->default_revert($url_array[1], $uri);
					$_REQUEST['option'] = $_GET['option'] = $option = $origname;
					$foundit = true;
					break;
				}
			}
			if (!$foundit AND isset($url_array[1])) {
				$content_sef = _ALIRO_CLASS_BASE.'/components/com_content/sef_ext.php';
				if (file_exists($content_sef)) {
					require_once($content_sef);
					$crevert = sef_content::revert($url_array,0);
					if ($crevert) {
						$foundit = true;
						$QUERY_STRING .= $crevert;
					}
				}
			}
		}
		if ($foundit) return $QUERY_STRING;
		else return false;
	}

	private function default_revert ($specialname, $uri) {
		$request = explode($specialname.'/', $uri);
		$parmset = isset($request[1]) ? explode("/", $request[1]) : array();
		$QUERY_STRING = '';
		foreach($parmset as $values) {
			$commapos = strpos($values, ',');
			if ($commapos) {
				$base = substr($values,0,$commapos);
				$rest = substr($values, $commapos+1);
				$_REQUEST[$base] = $_GET[$base] = $rest;
				if ('option' == $base) {
					if (file_exists(_ALIRO_ABSOLUTE_PATH.'/components/'.$rest)) $QUERY_STRING .= "option=$rest";
					else return '';
				}
				else $QUERY_STRING .= "&$base=$rest";
			}
		}
		return $QUERY_STRING;
	}

}
