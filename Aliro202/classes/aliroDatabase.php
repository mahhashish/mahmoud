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
 * aliroDataCache is not yet used - it exists ready for development into a cache
 * for database queries.  This has low priority or may even be abandonded, since
 * it is usually more effective to cache complete output, or more structured
 * data derived from the database, as happens in cached singletons.
 *
 * databaseException uses PHP5 exception handling for database errors, rather
 * than expecting other applications to handle them.  This is combined with the
 * introduction of an error logging table, since detailed diagnostic information
 * is useful to developers, but not much use to users.  Only basic messages are
 * shown to users.
 *
 * database is a class provided for backwards compatibility with Mambo 4.x and
 * Joomla! 1.0.x.  aliroDatabaseHandler is simply the preferred name for a class
 * with the same functions as "database".
 *
 * aliroDatabase is a singleton extension of the abstract database class.  It is
 * created from the stored credentials for the general database driving Aliro.
 *
 * aliroCoreDatabase is another singleton extension of the abstract database class.
 * It is the optionally separate database holding critical tables relating only to
 * the core of Aliro, such as information about menus, components, etc.  It is also
 * the only place where user passwords are stored, thus reducing the impact of
 * SQL injection attacks that penetrate only the general database.  If it is not
 * possible to have two databases, Aliro will run with both being the same.
 *
 * Other names are purely for compatibility and are deprecated.
 *
 */

// Provided for backwards compatibility
class database extends aliroBasicDatabase {

}

// The general database for an Aliro system
class aliroDatabase extends aliroExtendedDatabase {
	protected static $instance = null;

	protected function __construct () {
		$credentials = aliroCore::getConfigData('credentials.php');
		parent::__construct ($credentials['dbhost'], $credentials['dbusername'], $credentials['dbpassword'], $credentials['dbname'], $credentials['dbprefix']);
		if (aliro::getInstance()->installed) aliroCore::set('dbprefix', $credentials['dbprefix']);
	}

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self());
	}

	// Not intended for general use - public only to allow access by system upgrader
	public function DBUpgrade () {
		$sql = file_get_contents(_ALIRO_ADMIN_CLASS_BASE.'/sql/aliro_general.sql');
		$this->setQuery($sql);
		$this->query_batch();
		$this->clearCache();
		$this->addFieldIfMissing('#__remosef_uri', 'marker', "tinyint(4) NOT NULL default '0'");
		if ($this->tableExists('#__remosef_uri') AND $this->addFieldIfMissing('#__remosef_uri', 'sef_crc', 'int(11) UNSIGNED NOT NULL default 0 AFTER `id`')) {
			$this->doSQL("ALTER TABLE `#__remosef_uri` DROP INDEX `sef`");
			$this->doSQL("UPDATE #__remosef_uri SET sef_crc = CRC32(sef)");
			$this->doSQL("ALTER TABLE `#__remosef_uri` ADD INDEX (`sef_crc`) ");
		}
		if ($this->tableExists('#__remosef_uri') AND $this->addFieldIfMissing('#__remosef_uri', 'uri_crc', 'int(11) UNSIGNED NOT NULL default 0 AFTER `id`')) {
			$this->doSQL("ALTER TABLE `#__remosef_uri` DROP INDEX `uri`");
			$this->doSQL("UPDATE #__remosef_uri SET uri_crc = CRC32(uri)");
			$this->doSQL("ALTER TABLE `#__remosef_uri` ADD INDEX (`uri_crc`) ");
		}
		if ($this->tableExists('#__remosef_metadata') AND $this->addFieldIfMissing('#__remosef_metadata', 'uri_crc', 'int(11) UNSIGNED NOT NULL default 0 AFTER `id`')) {
			$this->doSQL("ALTER TABLE `#__remosef_metadata` DROP INDEX `finduri`");
			$this->doSQL("UPDATE #__remosef_metadata SET uri_crc = CRC32(uri)");
			$this->doSQL("ALTER TABLE `#__remosef_metadata` ADD INDEX (`uri_crc`) ");
		}
		if ($this->tableExists('#__remosef_uri') AND $this->addFieldIfMissing('#__remosef_uri', 'shortterm', 'tinyint(4) UNSIGNED NOT NULL default 0 AFTER `id`')) {
			$this->doSQL("ALTER TABLE `#__remosef_uri` ADD INDEX (`shortterm`) ");
		}
		$this->addFieldIfMissing('#__remosef_uri', 'ipaddress', "VARCHAR(15) NOT NULL default '' AFTER `sef_crc`");
		$this->addFieldIfMissing('#__remosef_config', 'flags', 'tinyint(3) UNSIGNED NOT NULL default 0 AFTER `id`');
		$this->addFieldIfMissing('#__users', 'jobtitle', "VARCHAR(100) NOT NULL default '' AFTER `email`");
		$this->addFieldIfMissing('#__users', 'timezone', "VARCHAR(255) NOT NULL default '' AFTER `jobtitle`");
		$this->addFieldIfMissing('#__users', 'location', "VARCHAR(255) NOT NULL default '' AFTER `timezone`");
		$this->addFieldIfMissing('#__users', 'phone', "VARCHAR(100) NOT NULL default '' AFTER `location`");
		$this->addFieldIfMissing('#__users', 'website', "VARCHAR(255) NOT NULL default '' AFTER `location`");
		$this->addFieldIfMissing('#__users', 'avatype', "VARCHAR(4) NOT NULL default '' AFTER `lastvisitDate`");
		$this->addFieldIfMissing('#__users', 'avatar', "BLOB NOT NULL AFTER `params`");
		$this->addFieldIfMissing('#__users', 'special', "VARCHAR(255) NOT NULL default '' AFTER `avatar`");
		$this->addFieldIfMissing('#__users', 'ipaddress', "VARCHAR(15) NOT NULL default '' AFTER `usertype`");
		$this->addFieldIfMissing('#__users', 'countrycode', "VARCHAR(15) NOT NULL default '' AFTER `ipaddress`");
		
		if (in_array('sequence', $this->getIndexNames('#__remosef_config'))) $this->doSQL("ALTER TABLE `#__remosef_config` DROP INDEX `sequence`");

		$this->alterField('#__remosef_config', 'name', "TEXT NOT NULL");
		$this->alterField('#__remosef_config', 'modified', "TEXT NOT NULL");
		
		$this->clearCache();
	}
}

// For backwards compatibility
class mamboDatabase extends aliroDatabase {
	// Just an alias really
}

// Similar to aliroDatabase but with any conflicting methods overriden
class joomlaDatabase extends aliroDatabase {
	protected static $instance = null;
	
	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self());
	}
	
	public function loadObject (&$object=null) {
		$object = null;
		$this->loadObject($object);
		return $object;
	}
}

// The core database for an Aliro system - applications should not normally need to use it
