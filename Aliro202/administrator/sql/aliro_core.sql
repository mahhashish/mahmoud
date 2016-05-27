
--
-- Table structure for table `#__admin_menu`
--

CREATE TABLE IF NOT EXISTS `#__admin_menu` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `link` text NOT NULL,
  `type` varchar(255) NOT NULL default '',
  `published` tinyint(1) NOT NULL default '0',
  `parent` int(11) unsigned NOT NULL default '0',
  `component` varchar(255) NOT NULL default '',
  `componentid` int(11) unsigned NOT NULL default '0',
  `sublevel` int(11) default '0',
  `ordering` int(11) default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `browserNav` tinyint(4) default '0',
  `params` text NOT NULL,
  `xmlfile` varchar(255) NOT NULL default '',
  `parmspec` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `componentid` (`componentid`,`published`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `#__admin_menu` (`id`, `name`, `link`, `type`, `published`, `parent`, `component`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `params`, `xmlfile`, `parmspec`) VALUES
(1, 'Home', '', 'core', 1, 0, '', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, '', '', ''),
(2, 'Manage Site', 'index.php?placeholder=manage_site', 'placeholder', 1, 0, '', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, '', '', ''),
(3, 'Plugins', 'index.php?core=cor_mambots', 'core', 1, 0, '', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, '', '', ''),
(4, 'Display Boxes', 'index.php?placeholder=view_panels', 'placeholder', 1, 0, '', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, '', '', ''),
(5, 'Themes', 'index.php?core=cor_templates', 'core', 1, 2, '', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, '', '', ''),
(6, 'Applications', 'index.php?placeholder=manage_applications', 'placeholder', 1, 0, 'components', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, '', '', ''),
(8, 'Information', 'index.php?placeholder=information', 'placeholder', 1, 0, '', 0, 0, 10, 0, '0000-00-00 00:00:00', 0, '', '', ''),
(9, 'Extensions', 'index.php?core=cor_extensions', 'core', 1, 0, '', 0, 0, 2, 0, '0000-00-00 00:00:00', 0, '', '', ''),
(10, 'Folders', 'index.php?core=cor_folders', 'core', 1, 2, '', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, '', '', ''),
(11, 'Menus', 'index.php?core=cor_menus&act=type', 'core', 1, 2, '', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, '', '', ''),
(12, 'Help', 'index.php?core=cor_help', 'core', 1, 8, '', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, '', '', ''),
(13, 'System', 'index.php?core=cor_sysinfo', 'core', 1, 8, '', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, '', '', ''),
(18, 'User Boxes', 'index.php?core=cor_modules&client=user', 'core', 1, 4, '', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, '', '', ''),
(19, 'Admin Boxes', 'index.php?core=cor_modules&client=admin', 'core', 1, 4, '', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, '', '', ''),
(20, 'Configuration', 'index.php?core=cor_config', 'core', 1, 2, '', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, '', '', ''),
(21, 'Error reports', 'index.php?core=cor_errors', 'core', 1, 2, 'cor_errors', 0, 0, 6, 0, '0000-00-00 00:00:00', 0, '', '', ''),
(22, 'Page 404 reports', 'index.php?core=cor_err404', 'core', 1, 2, 'cor_err404', 0, 0, 6, 0, '0000-00-00 00:00:00', 0, '', '', ''),
(23, 'Languages', 'index.php?core=cor_languages', 'core', 1, 2, '', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, '', '', ''),
(24, 'SEF', 'index.php?core=cor_sef', 'core', 1, 2, '', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, '', '', ''),
(25, 'Spam', 'index.php?core=cor_spam', 'core', 1, 2, '', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, '', '', '')
ON DUPLICATE KEY UPDATE name = name;

-- --------------------------------------------------------

--
-- Table structure for table `#__assignments`
--

CREATE TABLE IF NOT EXISTS `#__assignments` (
  `id` int(11) NOT NULL auto_increment,
  `access_type` varchar(60) NOT NULL,
  `access_id` text NOT NULL,
  `role` varchar(60) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `access_type` (`access_type`,`access_id`(60),`role`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__classmap`
--

CREATE TABLE IF NOT EXISTS `#__classmap` (
  `id` int(11) NOT NULL auto_increment,
  `type` varchar(100) NOT NULL default '',
  `formalname` varchar(100) NOT NULL default '',
  `side` varchar(12) NOT NULL default '',
  `filename` varchar(255) NOT NULL default '',
  `classname` varchar(255) NOT NULL default '',
  `extends` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__cmsapi_configurations`
--

CREATE TABLE IF NOT EXISTS `#__cmsapi_configurations` (
  `component` varchar(100) NOT NULL,
  `instance` int(10) NOT NULL default '0',
  `configuration` mediumtext NOT NULL,
  PRIMARY KEY  (`component`, `instance`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__components`
--

CREATE TABLE IF NOT EXISTS `#__components` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `extformalname` varchar(100) NOT NULL default '',
  `option` varchar(100) NOT NULL default '',
  `ordering` int(11) NOT NULL default '0',
  `class` varchar(255) NOT NULL default '',
  `adminclass` varchar(255) NOT NULL default '',
  `menuclass` varchar(255) NOT NULL default '',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__core_users`
--

CREATE TABLE IF NOT EXISTS `#__core_users` (
  `id` int(11) NOT NULL auto_increment,
  `password` varchar(255) NOT NULL default '',
  `salt` varchar (40) NOT NULL default '',
  `activation` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `activate` (`activation`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__error_404`
--

CREATE TABLE IF NOT EXISTS `#__error_404` (
  `uri` text NOT NULL,
  `timestamp` datetime NOT NULL,
  `referer` text NOT NULL,
  `ip` varchar(15) NOT NULL default '',
  `errortype` char(3) NOT NULL default '',
  `post` text NOT NULL,
  `trace` text NOT NULL,
  PRIMARY KEY  (`uri`(250))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__error_log`
--

CREATE TABLE IF NOT EXISTS `#__error_log` (
  `id` int(11) NOT NULL auto_increment,
  `timestamp` datetime NOT NULL,
  `smessage` varchar(255) NOT NULL,
  `dbname` varchar(255) NOT NULL default '',
  `dberror` varchar(255) NOT NULL default '',
  `dbmessage` text NOT NULL,
  `dbtrace` text NOT NULL,
  `sql` text NOT NULL,
  `lmessage` text NOT NULL,
  `errorkey` text NOT NULL,
  `referer` text NOT NULL,
  `ip` varchar(15) NOT NULL default '',
  `get` text NOT NULL,
  `post` text NOT NULL,
  `trace` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `stamp` (`timestamp`),
  KEY `errorkey` (`errorkey`(40))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__extensions`
--

CREATE TABLE IF NOT EXISTS `#__extensions` (
  `id` int(11) NOT NULL auto_increment,
  `type` varchar(100) NOT NULL default '',
  `package` varchar(255) NOT NULL default '',
  `application` varchar(100) NOT NULL default '',
  `formalname` varchar(100) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `admin` smallint(5) unsigned NOT NULL default '0',
  `author` varchar(255) NOT NULL default '',
  `version` varchar(255) NOT NULL default '',
  `date` varchar(24) NOT NULL default '',
  `authoremail` varchar(255) NOT NULL default '',
  `authorurl` text NOT NULL,
  `description` text NOT NULL,
  `class` varchar(255) NOT NULL default '',
  `adminclass` varchar(255) NOT NULL default '',
  `menuclass` varchar(255) NOT NULL default '',
  `default_template` smallint(6) unsigned NOT NULL default '0',
  `inner` tinyint(3) unsigned NOT NULL default '0',
  `timestamp` datetime NOT NULL default '0000-00-00 00:00:00',
  `xmlfile` varchar(255) NOT NULL default '',
  `parmspec` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__file_system`
--

CREATE TABLE IF NOT EXISTS `#__file_system` (
  `id` int(11) NOT NULL auto_increment,
  `application` varchar (100),
  `filename` varchar(255) NOT NULL,
  `mimetype` varchar(25) NOT NULL,
  `filesize` int(11) NOT NULL default '0',
  `headers` text NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `accessByFileName` (`filename`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__file_system_data`
--

CREATE TABLE IF NOT EXISTS `#__file_system_data` (
  `id` int(11) NOT NULL auto_increment,
  `fileid` int(11) NOT NULL default '0',
  `chunkid` int(11) NOT NULL default '0',
  `bloblength` int(11) NOT NULL default '0',
  `datachunk` mediumblob NOT NULL,
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `filechunk` (`fileid`,`chunkid`),
  KEY `size` (`fileid`,`bloblength`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__mail_log`
--

CREATE TABLE IF NOT EXISTS `#__mail_log` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL default '0',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `transport` char (10) NOT NULL default '',
  `recipient` varchar (255) NOT NULL default '',
  `ip` varchar (15) NOT NULL default '',
  `query` text NOT NULL,
  `post` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `date` (`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__mambots`
--

CREATE TABLE IF NOT EXISTS `#__mambots` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `element` varchar(100) NOT NULL default '',
  `class` varchar(255) NOT NULL default '',
  `triggers` text NOT NULL,
  `ordering` int(11) NOT NULL default '0',
  `published` tinyint(3) NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_folder` (`published`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__menu`
--

CREATE TABLE IF NOT EXISTS `#__menu` (
  `id` int(11) NOT NULL auto_increment,
  `menutype` varchar(100) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `link` text NOT NULL,
  `type` varchar(100) NOT NULL default '',
  `published` tinyint(1) unsigned NOT NULL default '0',
  `home` tinyint(1) unsigned NOT NULL default '0',
  `parent` int(11) unsigned NOT NULL default '0',
  `component` varchar(100) NOT NULL default '',
  `componentid` int(11) unsigned NOT NULL default '0',
  `ordering` int(11) NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `browserNav` tinyint(4) NOT NULL default '0',
  `xmlfile` varchar(255) NOT NULL default '',
  `parmspec` text NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `componentid` (`componentid`,`menutype`,`published`),
  KEY `menutype` (`menutype`),
  KEY `ordering` (`ordering`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__menutype`
--

CREATE TABLE IF NOT EXISTS `#__menutype` (
  `id` int(11) NOT NULL auto_increment,
  `ordering` int(11) NOT NULL default '0',
  `type` varchar(25) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__modules`
--

CREATE TABLE IF NOT EXISTS `#__modules` (
  `id` int(11) NOT NULL auto_increment,
  `title` text NOT NULL,
  `suffix` varchar(100) NOT NULL default '',
  `ordering` int(11) NOT NULL default '0',
  `repeats` tinyint(3) unsigned NOT NULL default '1',
  `exclude` tinyint(3) unsigned NOT NULL default '0',
  `position` varchar(100) NOT NULL default '',
  `incountry` varchar(255) NOT NULL default '',
  `excountry` varchar(255) NOT NULL default '',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `published` tinyint(1) NOT NULL default '0',
  `module` varchar(100) NOT NULL default '',
  `showtitle` tinyint(3) unsigned NOT NULL default '1',
  `admin` tinyint(3) unsigned NOT NULL default '0',
  `class` varchar(255) NOT NULL default '',
  `adminclass` varchar(255) NOT NULL default '',
  `params` text NOT NULL,
  `customcontent` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `published` (`published`),
  KEY `newsfeeds` (`module`,`published`),
  KEY `ordering` (`position`,`ordering`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__modules_menu`
--

CREATE TABLE IF NOT EXISTS `#__modules_menu` (
  `moduleid` int(11) NOT NULL default '0',
  `menuid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`moduleid`,`menuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__new_passwords`
--

CREATE TABLE IF NOT EXISTS `#__new_passwords` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL default '0',
  `stamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `password` char(32) NOT NULL,
  `salt` char(32) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `datestamp` (`stamp`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__orphan_data`
--

CREATE TABLE IF NOT EXISTS `#__orphan_data` (
  `session_id` char(32) NOT NULL,
  `orphandata` text NOT NULL,
  `stamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`session_id`),
  KEY `stamp` (`stamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__permissions`
--

CREATE TABLE IF NOT EXISTS `#__permissions` (
  `id` int(11) NOT NULL auto_increment,
  `role` varchar(60) NOT NULL,
  `control` tinyint(3) unsigned NOT NULL default '0',
  `action` varchar(60) NOT NULL,
  `subject_type` varchar(60) NOT NULL,
  `subject_id` text NOT NULL,
  `system` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `role_type` (`role`,`action`,`subject_type`,`subject_id`(60)),
  KEY `subaction` (`subject_type`,`action`,`subject_id`(60))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `#__permissions` (`id`, `role`, `control`, `action`, `subject_type`, `subject_id`, `system`) VALUES
(1, 'Manager', 2, 'access', 'adminSide', '*', 1),
(2, 'Administrator', 2, 'access', 'adminSide', '*', 1),
(3, 'Super Administrator', 3, 'access', 'adminSide', '*', 1),
(4, 'Super Administrator', 3, 'administer', 'Administrator', '*', 1),
(5, 'Super Administrator', 3, 'administer', 'Author', '*', 1),
(6, 'Super Administrator', 3, 'administer', 'Editor', '*', 1),
(7, 'Super Administrator', 3, 'administer', 'Manager', '*', 1),
(8, 'Super Administrator', 3, 'administer', 'Publisher', '*', 1),
(9, 'Super Administrator', 3, 'administer', 'Registered', '*', 1),
(10, 'Super Administrator', 3, 'administer', 'Super Administrator', '*', 1),
(11, 'Super Administrator', 3, 'block', 'aUser', '*', 1),
(12, 'Super Administrator', 3, 'edit', 'aliroComponent', '*', 1),
(13, 'Super Administrator', 3, 'edit', 'aliroMambot', '*', 1),
(14, 'Super Administrator', 3, 'edit', 'aliroModule', '*', 1),
(15, 'Super Administrator', 3, 'emailEvents', 'aUser', '*', 1),
(16, 'Super Administrator', 3, 'install', 'aliroComponent', '*', 1),
(17, 'Super Administrator', 3, 'install', 'aliroMambot', '*', 1),
(18, 'Super Administrator', 3, 'install', 'aliroModule', '*', 1),
(19, 'Super Administrator', 3, 'manage', 'aConfig', '*', 1),
(20, 'Super Administrator', 3, 'manage', 'aLanguage', '*', 1),
(21, 'Super Administrator', 3, 'manage', 'aliroMenu', '*', 1),
(22, 'Super Administrator', 3, 'manage', 'aliroTemplate', '*', 1),
(23, 'Super Administrator', 3, 'manage', 'aliroTrash', '*', 1),
(24, 'Super Administrator', 3, 'manage', 'aUser', '*', 1),
(25, 'Super Administrator', 3, 'massMail', 'aUser', '*', 1) ON DUPLICATE KEY UPDATE system = 1;

-- --------------------------------------------------------

--
-- Table structure for table `#__query_slow`
--

CREATE TABLE IF NOT EXISTS `#__query_slow` (
  `id` int(11) NOT NULL auto_increment,
  `queryid` int(11) NOT NULL default '0',
  `time` float NOT NULL default '0',
  `trace` text NOT NULL,
  `querytext` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `querystats` (`queryid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__query_stats`
--

CREATE TABLE IF NOT EXISTS `#__query_stats` (
  `id` int(11) NOT NULL auto_increment,
  `count` smallint(6) NOT NULL default '0',
  `mean` float NOT NULL default '0',
  `median` float NOT NULL default '0',
  `stdev` float NOT NULL default '0',
  `best` float NOT NULL default '0',
  `worst` float NOT NULL default '0',
  `total` float NOT NULL default '0',
  `elapsed` float NOT NULL default '0',
  `memory` int(11) NOT NULL default '0',
  `uri` text character set latin1 collate latin1_general_ci NOT NULL,
  `post` text NOT NULL,
  `ip` varchar(15) NOT NULL default '',
  `stamp` timestamp,
  PRIMARY KEY  (`id`),
  KEY `stamp` (`stamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__role_link`
--

CREATE TABLE IF NOT EXISTS `#__role_link` (
  `role` varchar(60) NOT NULL default '',
  `implied` varchar(60) NOT NULL default '',
  PRIMARY KEY  (`role`, `implied`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `#__role_link` (`role`, `implied`) VALUES
('Administrator', 'Manager'),
('Author', 'Registered'),
('Editor', 'Author'),
('Manager', 'Publisher'),
('Publisher', 'Editor'),
('Super Administrator', 'Administrator') ON DUPLICATE KEY UPDATE role = role;

-- --------------------------------------------------------

--
-- Table structure for table `#__role_properties`
--

CREATE TABLE IF NOT EXISTS `#__role_properties` (
  `id` int(11) NOT NULL auto_increment,
  `role` varchar(60) NOT NULL default '',
  `formalname` varchar(100) NOT NULL default '',
  `property` varchar(60) NOT NULL default '',
  `value` text NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `find_properties` (`role`,`formalname`,`property`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__session`
--

CREATE TABLE IF NOT EXISTS `#__session` (
  `time` int(11) NOT NULL default '0',
  `session_id` char(32) NOT NULL,
  `isadmin` tinyint(3) unsigned NOT NULL default '0',
  `guest` tinyint(4) NOT NULL default '1',
  `userid` int(11) NOT NULL default '0',
  `gid` tinyint(3) unsigned NOT NULL default '0',
  `marker` smallint(6) NOT NULL default '0',
  `ipaddress` varchar(15) NOT NULL,
  PRIMARY KEY  (`session_id`),
  KEY `time` (`time`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__session_data`
--

CREATE TABLE IF NOT EXISTS `#__session_data` (
  `session_id` char(32) NOT NULL,
  `marker` int(11) NOT NULL default '0',
  `session_id_crc` int(11) unsigned NOT NULL default '0',
  `session_data` mediumtext NOT NULL,
  KEY `session_id_crc` (`session_id_crc`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__spam_log`
--

CREATE TABLE IF NOT EXISTS `#__spam_log` (
  `id` int(11) NOT NULL auto_increment,
  `checkers` int(11) NOT NULL default '0',
  `spaminess` float NOT NULL default '0',
  `status` varchar(25) NOT NULL,
  `title` text NOT NULL,
  `body` mediumtext NOT NULL,
  `authorname` varchar(255) NOT NULL,
  `authoruri` tinytext NOT NULL,
  `authoremail` tinytext NOT NULL,
  `authorip` varchar(15) NOT NULL,
  `authoropenid` tinytext NOT NULL,
  `authorid` int(11) NOT NULL default '0',
  `permalink` tinytext NOT NULL,
  `articledate` datetime NOT NULL,
  `trusted` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__spam_log_results`
--

CREATE TABLE IF NOT EXISTS `#__spam_log_results` (
  `id` int(11) NOT NULL auto_increment,
  `spamid` int(11) NOT NULL default '0',
  `checker` varchar(25) NOT NULL,
  `status` varchar(25) NOT NULL,
  `spaminess` float NOT NULL default '0',
  `identifier` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `aliro_spam_blacklist`
--

CREATE TABLE IF NOT EXISTS `#__spam_blacklist` (
  `ip` varchar(15) NOT NULL,
  `stamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `status` varchar(25) NOT NULL,
  `spaminess` float NOT NULL default '0',
  `identifier` int(11) NOT NULL,
  PRIMARY KEY  (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
-- --------------------------------------------------------

--
-- Table structure for table `#__urilinks`
--
CREATE TABLE IF NOT EXISTS `#__urilinks` (
  `id` int(11) NOT NULL auto_increment,
  `application` varchar(100) NOT NULL,
  `published` tinyint(4) unsigned NOT NULL default '0',
  `notemplate` tinyint(4) unsigned NOT NULL default '1',
  `nohtml` tinyint(4) unsigned NOT NULL default '1',
  `uri_crc` int(10) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `uri` text NOT NULL,
  `class` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `urilinks_name` (`name`),
  KEY `urililnks_uri_crc` (`uri_crc`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

