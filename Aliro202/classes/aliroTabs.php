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
 * Everything here is to do with tabs.
 * 
 * aliroTabs uses YUI services to create tabbed panes
 *
 * aliroOldTabs is the class that supports easy creation of tabbed displays using
 * Erik Arvidsson's Tab Pane JavaScript based tabbing utility
 *
 * mosTabs is for backwards compatibility and is based on aliroOldTabs
 *
 */

class aliroTabs extends aliroFriendlyBase {
	protected static $jsneeded = true;
	protected $identifier = '';
	protected $selected = 0;
	protected $positionclass = 'yui-nav-left';
	protected $yuiclass = true;
	
	public function __construct ($identifier='', $right=false, $yuiclass=true) {
		$this->identifier = $identifier ? $identifier : 'tab'.$this->getUnique();
		if ($right) $this->positionclass = 'yui-nav-right';
		$this->yuiclass = $yuiclass;
	}
	
	public function select ($selected) {
		$this->selected = $selected;
	}
	
	public function startPane ($names) {
		if (self::$jsneeded) {
		    $aliroCore = aliroCore::getInstance();
		    
		    //Load up some extra YUI modules (required by gallery-yui2)
    	    $yuiReqs = array('loader','node-base','get','async-queue');
    	    aliroResourceLoader::getInstance()->addYUIModule($yuiReqs);
    	    
			$script = <<<JSTAG

                YUI().use('*', function(Y) {
                    //Override default config to assure Gallery 2 loads YUI modules locally
     			    YAHOO_config = {
                 		base: '{$aliroCore->getCfg('live_site')}/extclasses/yui/lib/'+YUI.ALIRO.CORE.get("yui2version")+'/build/',
                 		combine: false
                 	};

         		    //Using YUI 2 modules within a YUI 3 sandbox object
                    YUI({
                         base: '{$aliroCore->getCfg('live_site')}/extclasses/yui/lib/'+YUI.ALIRO.CORE.get("yui3version")+'/build/',
                         modules: {
                             'gallery-yui2': {
                                 fullpath: '{$aliroCore->getCfg('live_site')}/extclasses/yui/lib/gallery-modules/gallery-yui2.js',
                                 base: '{$aliroCore->getCfg('live_site')}/extclasses/yui/lib/'+YUI.ALIRO.CORE.get("yui2version")+'/build/',
                                 requires: ['node-base','get','async-queue'],
                                 optional: [],
                                 supersedes: []
                             }
                         }
                    }).use('gallery-yui2', function (Y) {
                         Y.yui2().use("element", "tabview", function () {
                     	    var tabView = new YAHOO.widget.TabView('{$this->identifier}');
                     	});
                    });
                });
                
JSTAG;

            aliroRequest::getInstance()->addScriptText($script, 'late', true);
			self::$jsneeded = false;
		}
		$mainclass = $this->yuiclass ? ' class="yui-navset"' : '';
		$html = <<<TABS_START
		
			<div$mainclass id="$this->identifier">
				<ul class="yui-nav $this->positionclass">
TABS_START;

		foreach ($names as $sub=>$name) $html .= <<<ONE_TAB
		
					<li{$this->isSelected($sub==$this->selected)}>
						<a href="{$this->tabID($sub)}"><em>$name</em></a>
					</li>
ONE_TAB;

		$html .= <<<TABS_HEADEND
		
				</ul>
				<div class="yui-content">
				
TABS_HEADEND;

		return $html;
	}
	
	public function endPane () {
		return <<<END_TABS
		
				</div>
			</div>
		
END_TABS;

	}
	
	public function startTab () {
		return <<<START_TAB
		
        			<div>
		
START_TAB;

	}
	
	public function endTab () {
		return <<<END_TAB
		
					</div>
					
END_TAB;

	}
	
	protected function isSelected ($bool) {
		if ($bool) return ' class="selected"';
	}
	
	protected function tabID ($subscript) {
		$counter = $subscript + 1;
		return '#tab'.$counter;	
	}	
}


class aliroOldTabs {
	private $useCookies = 0;

	public function __construct($useCookies=true) {
		$request = aliroRequest::getInstance();
		$tab_headers = <<<TAB_TAGS

<link id="luna-tab-style-sheet" type="text/css" rel="stylesheet" href="{$request->getCfg('live_site')}/includes/js/tabs/tab.css" />
<script type="text/javascript" src="{$request->getCfg('live_site')}/includes/js/tabs/tabpane.js"></script>

TAB_TAGS;

		$request->addCustomHeadTag($tab_headers);
		$this->useCookies = $useCookies;
	}

	public function startPane($id){
		return <<<START_PANE

		<div class="tab-page" id="$id">
		<script type="text/javascript">
			var tabPane1 = new WebFXTabPane( document.getElementById( "$id" ), $this->useCookies )
		</script>

START_PANE;

	}

	public function endPane() {
		return "</div>";
	}

	public static function startTab( $tabText, $paneid ) {
		return <<<START_TAB

		<div class="tab-page" id="$paneid">
		<h2 class="tab">$tabText</h2>
		<script type="text/javascript">
			tabPane1.addTabPage( document.getElementById( "$paneid" ) );
		</script>

START_TAB;

	}

	public static function endTab() {
		return "</div>";
	}
}

class mosTabs extends aliroOldTabs {

	function mosTabs ($useCookies=true) {
		parent::__construct($useCookies);
	}
	
	public function startPane ($id) {
		echo parent::startPane($id);
	}
	
	public function endPane () {
		echo parent::endPane();
	}
	
	public static function startTab ($tabText, $paneid) {
		echo aliroOldTabs::startTab($tabText, $paneid);
	}
	
	public static function endTab () {
		echo aliroOldTabs::endTab();
	}
	
}

class cmsapiPane extends mosTabs {
	function remosPane () {
		parent::mosTabs(0);
	}
}
