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
        $rootpath = criticalInfo::getInstance()->absolute_path;
        $mainframe = mosMainFrame::getInstance();
        $imgPath =  'templates/'.$mainframe->getTemplate().'/images/arrow.png';
        if (file_exists( "$rootpath/$imgPath" )) $img = "<img src='{$config->getCfg('live_site')}/$imgPath' border='0' alt='arrow' />";
        else {
            $imgPath = '/images/M_images/arrow.png';
            if (file_exists( "$rootpath/$imgPath" )) $img = "<img src='{$config->getCfg('live_site')}/images/M_images/arrow.png' alt='arrow' />";
            else $img = '&gt;';
        }
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