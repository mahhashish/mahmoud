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
 * Everything here is to do with database management.
 *
 * aliroCoreDatabase is a singleton extension of the abstract database class.
 * It is the optionally separate database holding critical tables relating only to
 * the core of Aliro, such as information about menus, components, etc.  It is also
 * the only place where user passwords are stored, thus reducing the impact of
 * SQL injection attacks that penetrate only the general database.  If it is not
 * possible to have two databases, Aliro will run with both being the same.
 *
 */

class aliroCoreDatabase extends aliroExtendedDatabase {
	protected static $instance = null;

	protected function __construct () {
		$credentials = aliroCore::getConfigData('corecredentials.php');
		parent::__construct ($credentials['dbhost'], $credentials['dbusername'], $credentials['dbpassword'], $credentials['dbname'], $credentials['dbprefix']);
	}

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self());
	}
	
	public function changeDBContents () {
		$this->doSQL("UPDATE #__extensions SET xmlfile = SUBSTRING(xmlfile,15) WHERE '/administrator' = SUBSTRING(xmlfile,1,14)");
		$this->doSQL("UPDATE #__admin_menu SET xmlfile = SUBSTRING(xmlfile,15) WHERE '/administrator' = SUBSTRING(xmlfile,1,14)");
		$this->doSQL("UPDATE #__menu SET xmlfile = SUBSTRING(xmlfile,15) WHERE '/administrator' = SUBSTRING(xmlfile,1,14)");
	}
	
	// Not intended for general use - public only to allow access by system upgrader
	public function DBUpgrade () {
		$sql = file_get_contents(_ALIRO_ADMIN_CLASS_BASE.'/sql/aliro_core.sql');
		$this->setQuery($sql);
		$this->query_batch();
		$this->clearCache();
		if (!$this->tableExists('#__menutype')) {
			$this->doSQL("CREATE TABLE `#__menutype` ("
			." `id` int(11) NOT NULL auto_increment,"
			." `ordering` int(11) NOT NULL default '0',"
			." `type` varchar(25) NOT NULL default '',"
			." `name` varchar(255) NOT NULL default '',"
			." PRIMARY KEY  (`id`)"
			." ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
			$this->doSQL("INSERT INTO `#__menutype` (ordering, type, name) SELECT DISTINCT IF(menutype = 'mainmenu', 10, 20) AS ordering, menutype, menutype FROM `#__menu`");
		}
		else $this->doSQL("DELETE FROM #__menutype WHERE type NOT IN (SELECT DISTINCT menutype FROM #__menu)");
		$this->doSQL("DELETE FROM #__admin_menu WHERE link LIKE 'index.php?core=cor_menu%'");
		$this->setQuery("SELECT id FROM #__admin_menu WHERE link = 'index.php?placeholder=manage_site'");
		$sitemanager = $this->loadResult();
		$this->doSQL("INSERT INTO #__admin_menu (name, link, type, published, parent, checked_out_time) VALUES('Menus', 'index.php?core=cor_menus&act=type', 'core', 1, $sitemanager, '{$this->dateNow()}')");
		$menutop = $this->insertid();
		$this->doSQL("INSERT INTO #__admin_menu (name, link, type, published, parent, checked_out_time) SELECT name, CONCAT('index.php?core=cor_menus&task=list&menutype=', type) AS link, 'core' AS type, 1 AS published, $menutop, '{$this->dateNow()}' AS checked_out_time FROM #__menutype");
		$this->addFieldIfMissing('#__classmap', 'extends', "varchar(255) NOT NULL default '' AFTER `classname`");
		$this->addFieldIfMissing('#__extensions', 'inner', "tinyint(3) unsigned NOT NULL default '0' AFTER `default_template`");
		$this->addFieldIfMissing('#__extensions', 'package', "varchar(255) NOT NULL default '' AFTER `type`");
		if ($this->addFieldIfMissing('#__extensions', 'application', "varchar(100) NOT NULL default '' AFTER `package`")) {
			$this->doSQL("UPDATE #__extensions SET application = formalname");
			$clearHandlers = 1;
		}
		foreach (array('#__menu', '#__extensions', '#__admin_menu') as $tablename) {
			if ($this->addFieldIfMissing($tablename, 'parmspec', "text NOT NULL AFTER `xmlfile`")) {
				$this->makeParmSpecs($tablename);
				$clearHandlers = 1;
			}
		}
		if (!empty($clearHandlers)) {
			aliroSingletonObjectCache::getInstance()->delete('aliroExtensionHandler', 'aliroMenuHandler', 'aliroAdminMenuHandler');
		}
		
		$this->addFieldIfMissing('#__menu', 'home', 'tinyint(3) UNSIGNED NOT NULL default 0 AFTER `published`');
		
		$this->addFieldIfMissing('#__urilinks', 'notemplate', 'tinyint(3) UNSIGNED NOT NULL default 1 AFTER `published`');
		$this->addFieldIfMissing('#__urilinks', 'nohtml', 'tinyint(3) UNSIGNED NOT NULL default 1 AFTER `notemplate`');
		$this->addFieldIfMissing('#__urilinks', 'uri_crc', 'int(10) UNSIGNED NOT NULL default 1 AFTER `nohtml`');

		$this->dropFieldIfPresent('#__session_data', 'timestamp');
		if ($this->tableExists('#__session_data') AND $this->addFieldIfMissing('#__session_data', 'session_id_crc', 'int(11) UNSIGNED NOT NULL default 0 AFTER `session_id`')) {
			$this->doSQL("ALTER TABLE `#__session_data` DROP PRIMARY KEY");
			$this->doSQL("UPDATE #__session_data SET session_id_crc = CRC32(session_id)");
			$this->doSQL("ALTER TABLE `#__session_data` ADD INDEX (`session_id_crc`) ");
		}
		$this->addFieldIfMissing('#__session_data', 'marker', 'int(11) NOT NULL default 0 AFTER `session_id`');

		$this->addFieldIfMissing('#__modules', 'repeats', "tinyint(3) unsigned NOT NULL default 0 AFTER `ordering`");
		$this->addFieldIfMissing('#__modules', 'exclude', "tinyint(3) unsigned NOT NULL default 0 AFTER `repeats`");
		$this->addFieldIfMissing('#__modules', 'incountry', "varchar(255) NOT NULL default '' AFTER `position`");
		$this->addFieldIfMissing('#__modules', 'excountry', "varchar(255) NOT NULL default '' AFTER `incountry`");

		$this->addFieldIfMissing('#__query_stats', 'post', "text NOT NULL AFTER `uri`");
		
		$this->addFieldIfMissing('#__query_stats', 'ip', "varchar (15) NOT NULL default '' AFTER `post`");
		$this->addFieldIfMissing('#__error_404', 'ip', "varchar (15) NOT NULL default '' AFTER `referer`");
		$this->addFieldIfMissing('#__error_404', 'errortype', "varchar (5) NOT NULL default '' AFTER `ip`");
		$this->addFieldIfMissing('#__error_log', 'ip', "varchar (15) NOT NULL default '' AFTER `referer`");
		$this->alterField('#__error_log', 'dbmessage', "TEXT NOT NULL");
		$this->addFieldIfMissing('#__mail_log', 'ip', "varchar (15) NOT NULL default '' AFTER `recipient`");
		$this->addFieldIfMissing('#__orphan_data', 'stamp', "timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP AFTER `orphandata`");
		
		$this->dropFieldIfPresent('#__session', 'usertype');
		$this->dropFieldIfPresent('#__session', 'httphost');
		$this->dropFieldIfPresent('#__session', 'servername');
		$this->dropFieldIfPresent('#__session', 'username');
		$this->alterField('#__session', 'session_id', "char(32) NOT NULL");

		$this->alterField('#__session_data', 'session_id', "char(32) NOT NULL");
		
		$this->clearCache();
	}
	
	protected function makeParmSpecs ($tablename) {
		$rows = $this->doSQLget("SELECT id, xmlfile FROM $tablename");
		clearstatcache();
		foreach ($rows as $row) {
			if ($row->xmlfile AND file_exists(_ALIRO_CLASS_BASE.$row->xmlfile)) {
				$parmspec = aliroParameters::getParameterStringFromXMLFile(_ALIRO_CLASS_BASE.$row->xmlfile);
				$this->doSQL("UPDATE $tablename SET parmspec = '$parmspec' WHERE id = $row->id");
			}
		}
	}
	
}
