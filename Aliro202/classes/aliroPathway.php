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
 * aliroPathway handles the pathway or breadcrumb trail.  The management of the
 * pathway is primarily the responsibility of each component.  Aliro simply makes
 * it easy for the component to deal with it by providing simple to use methods.
 * The details of how the trail is displayed are the responsibility of the template.
 *
 */

class aliroPathway {
	private static $instance = __CLASS__;
    private $_names = array();
    private $_urls = array();
    private $sef = null;
	private $custom_pathway = array();

    private function __construct () {
		$this->sef = aliroSEF::getInstance();
        if ($home = aliroMenuHandler::getInstance()->getHome()) {
	        $this->_names[] = $home->name;
        	$this->_urls[] = $this->sef->sefRelToAbs($home->link);
        }
    }

    private function __clone () {
    	// Declared to enforce singleton
    }

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance());
	}

    public function getPathway () {
    	$data = new stdClass();
    	$data->names = $this->_names;
    	$data->urls = $this->_urls;
    	$data->custom = $this->custom_pathway;
    	return $data;
    }

    public function setPathway ($data) {
    	$this->_names = $data->names;
    	$this->_urls = $data->urls;
    	$this->_custom_pathway = $data->custom;
    }

    public function addItem ($name, $givenurl='') {
        if (!$name) return;
        $next = count($this->_names);
        $url = $this->sef->sefRelToAbs($givenurl);
        if ($next > 0 AND $url AND $url == $this->_urls[$next-1]) return;
        $this->_names[$next] = $name;
        $this->_urls[$next] = $url;
    }

    function reduceToOne () {
        for ($i = count($this->_names) - 1; $i > 0; $i--) {
            unset($this->_names[$i]);
            unset($this->_urls[$i]);
        }
    }

    function reduceByOne () {
    	array_pop($this->_names);
    	array_pop($this->_urls);
    }

    // The next two functions are deprecated and should not be used - addItem is much easier!
    function getCustomPathWay() {
        return $this->custom_pathway;
    }

    function appendPathWay($html) {
        $this->custom_pathway[] = $html;
    }

    function makePathway () {
    	$customs = $this->getCustomPathWay();
    	$last = count($this->_names) - 1;
        if (0 == $last AND 0 == count($customs)) return '';
		$starthtml = $result = "<span class='pathway'>";
        $config = aliroCore::getInstance();
        $rootpath = _ALIRO_ABSOLUTE_PATH;
        $request = aliroRequest::getInstance();
        $relPath =  '/templates/'.$request->getTemplate().'/images/arrow.png';
        $livesite = $request->getCfg('live_site');
        $template = $request->getTemplateObject();
        if (method_exists($template, 'getPathwayImagePath')) $image_path = $template->getPathwayImagePath();
        elseif (method_exists($template, 'getPathwayImageText')) $img = $template->getPathwayImageText();
        elseif (file_exists( _ALIRO_ABSOLUTE_PATH.$relPath )) $image_path = $livesite.$relPath;
        elseif (file_exists(_ALIRO_ABSOLUTE_PATH.'/images/M_images/arrow.png')) $image_path = $livesite.'/images/M_images/arrow.png';
        else $img = '&gt;';
        if (isset($image_path)) $img = "<img src='$image_path'  alt='arrow' />";
		foreach ($this->_names as $i=>$name) {
			if ($i === $last AND count($customs) == 0) $result .= "$name</span>";
			else {
                $sefurl = $this->sef->sefRelToAbs($this->_urls[$i]);
                $result .= "<a href='$sefurl' class='pathway'>$name</a>";
                $result .= "&nbsp;$img&nbsp;";
            }
        }
    	$last = count($customs) - 1;
        foreach ($customs as $i=>$custom) $result .= ($i == $last ? strip_tags($custom).'</span>' : $custom."&nbsp;$img&nbsp;");
        //return ($starthtml == $result) ? '' : $result;
		// This is a temporary fix for Jim Galley
		return ($starthtml == $result) ? '<span class="pathway">Home</span>' : $result;
    }

}

class mosPathway extends aliroPathway {
	// Provided purely for backwards compatability
}