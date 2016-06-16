
--
-- Table structure for table `#__admin_menu`
--

CREATE TABLE IF NOT EXISTS `#__admin_menu` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `link` text NOT NULL default '',
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
  `params` text NOT NULL default '',
  `xmlfile` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `componentid` (`componentid`,`published`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `#__admin_menu` (`id`, `name`, `link`, `type`, `published`, `parent`, `component`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `params`, `xmlfile`) VALUES
(1, 'Home', '', 'core', 1, 0, '', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, '', ''),
(2, 'Manage Site', 'index.php?placeholder=manage_site', 'placeholder', 1, 0, '', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, '', ''),
(3, 'Plugins', 'index.php?core=cor_mambots', 'core', 1, 0, '', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, '', ''),
(4, 'Display Boxes', 'index.php?placeholder=view_panels', 'placeholder', 1, 0, '', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, '', ''),
(5, 'Themes', 'index.php?core=cor_templates', 'core', 1, 2, '', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, '', ''),
(6, 'Applications', 'index.php?placeholder=manage_applications', 'placeholder', 1, 0, 'components', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, '', ''),
(7, 'Installer', 'index.php?core=cor_installer', 'core', 1, 9, '', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, '', ''),
(8, 'Information', 'index.php?placeholder=information', 'placeholder', 1, 0, '', 0, 0, 10, 0, '0000-00-00 00:00:00', 0, '', ''),
(9, 'Extensions', 'index.php?core=cor_extensions', 'core', 1, 0, '', 0, 0, 2, 0, '0000-00-00 00:00:00', 0, '', ''),
(10, 'Folders', 'index.php?core=cor_folders', 'core', 1, 2, '', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, '', ''),
(11, 'Menus', 'index.php?core=cor_menutypes', 'core', 1, 2, '', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, '', ''),
(12, 'Help', 'index.php?core=cor_help', 'core', 1, 8, '', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, '', ''),
(13, 'System', 'index.php?core=cor_sysinfo', 'core', 1, 8, '', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, '', ''),
(14, 'Main menu', 'index.php?core=cor_menus&task=list&menutype=mainmenu', 'core', 1, 11, '', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, '', ''),
(15, 'Other Menu', 'index.php?core=cor_menus&task=list&menutype=othermenu', 'core', 1, 11, '', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, '', ''),
(16, 'Top Menu', 'index.php?core=cor_menus&task=list&menutype=topmenu', 'core', 1, 11, '', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, '', ''),
(17, 'User Menu', 'index.php?core=cor_menus&task=list&menutype=usermenu', 'core', 1, 11, '', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, '', ''),
(18, 'User Boxes', 'index.php?core=cor_modules&client=user', 'core', 1, 4, '', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, '', ''),
(19, 'Admin Boxes', 'index.php?core=cor_modules&client=admin', 'core', 1, 4, '', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, '', ''),
(20, 'Configuration', 'index.php?core=cor_config', 'core', 1, 2, '', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, '', ''),
(21, 'Error reports', 'index.php?core=cor_errors', 'core', 1, 2, 'cor_errors', 0, 0, 6, 0, '0000-00-00 00:00:00', 0, '', ''),
(22, 'Page 404 reports', 'index.php?core=cor_err404', 'core', 1, 2, 'cor_err404', 0, 0, 6, 0, '0000-00-00 00:00:00', 0, '', ''),
(23, 'Languages', 'index.php?core=cor_languages', 'core', 1, 2, '', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, '', ''),
(24, 'SEF', 'index.php?core=cor_sef', 'core', 1, 2, '', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, '', ''),
(25, 'URI list', 'index.php?core=cor_sef&act=uri', 'core', 1, 24, '', 0, 1, 0, 0, '0000-00-00 00:00:00', 0, '', ''),
(26, 'Metadata', 'index.php?core=cor_sef&act=metadata', 'core', 1, 24, '', 0, 1, 0, 0, '0000-00-00 00:00:00', 0, '', '')
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
  PRIMARY KEY  (`id`)
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
  `params` text NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__extensions`
--

CREATE TABLE IF NOT EXISTS `#__extensions` (
  `id` int(11) NOT NULL auto_increment,
  `type` varchar(100) NOT NULL default '',
  `formalname` varchar(100) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `admin` smallint(5) unsigned NOT NULL default '0',
  `author` varchar(255) NOT NULL default '',
  `version` varchar(255) NOT NULL default '',
  `date` varchar(24) NOT NULL default '',
  `authoremail` varchar(255) NOT NULL default '',
  `authorurl` text NOT NULL default '',
  `description` text NOT NULL default '',
  `class` varchar(255) NOT NULL default '',
  `adminclass` varchar(255) NOT NULL default '',
  `menuclass` varchar(255) NOT NULL default '',
  `default_template` smallint(6) unsigned NOT NULL default '0',
  `inner` smallint(6) unsigned NOT NULL default '0',
  `timestamp` datetime NOT NULL default '0000-00-00 00:00:00',
  `xmlfile` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
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
  `triggers` text NOT NULL default '',
  `ordering` int(11) NOT NULL default '0',
  `published` tinyint(3) NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `params` text NOT NULL default '',
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
  `link` text NOT NULL default '',
  `type` varchar(100) NOT NULL default '',
  `published` tinyint(1) NOT NULL default '0',
  `parent` int(11) unsigned NOT NULL default '0',
  `component` varchar(100) NOT NULL default '',
  `componentid` int(11) unsigned NOT NULL default '0',
  `ordering` int(11) NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `browserNav` tinyint(4) NOT NULL default '0',
  `xmlfile` varchar(255) NOT NULL default '',
  `params` text NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `componentid` (`componentid`,`menutype`,`published`),
  KEY `menutype` (`menutype`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__modules`
--

CREATE TABLE IF NOT EXISTS `#__modules` (
  `id` int(11) NOT NULL auto_increment,
  `title` text NOT NULL default '',
  `suffix` varchar(100) NOT NULL default '',
  `ordering` int(11) NOT NULL default '0',
  `position` varchar(100) NOT NULL default '',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `published` tinyint(1) NOT NULL default '0',
  `module` varchar(100) NOT NULL default '',
  `showtitle` tinyint(3) unsigned NOT NULL default '1',
  `admin` tinyint(3) unsigned NOT NULL default '0',
  `class` varchar(255) NOT NULL default '',
  `adminclass` varchar(255) NOT NULL default '',
  `params` text NOT NULL default '',
  `customcontent` text NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `published` (`published`),
  KEY `newsfeeds` (`module`,`published`)
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
-- Table structure for table `#__orphan_data`
--

CREATE TABLE IF NOT EXISTS `#__orphan_data` (
  `session_id` varchar(32) NOT NULL,
  `orphandata` text NOT NULL,
  PRIMARY KEY  (`session_id`)
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
  `stamp` timestamp,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

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
-- Table structure for table `#__session`
--

CREATE TABLE IF NOT EXISTS `#__session` (
  `username` varchar(50) NOT NULL default '',
  `time` int(11) NOT NULL default '0',
  `session_id` varchar(32) NOT NULL default '',
  `isadmin` tinyint(3) unsigned NOT NULL default '0',
  `guest` tinyint(4) NOT NULL default '1',
  `userid` int(11) NOT NULL default '0',
  `usertype` varchar(100) NOT NULL default '',
  `gid` tinyint(3) unsigned NOT NULL default '0',
  `marker` smallint(6) NOT NULL default '0',
  `httphost` varchar(255) NOT NULL,
  `servername` varchar(255) NOT NULL,
  `ipaddress` varchar(15) NOT NULL,
  PRIMARY KEY  (`session_id`),
  KEY `whosonline` (`guest`,`usertype`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__session_data`
--

CREATE TABLE IF NOT EXISTS `#__session_data` (
  `session_id` varchar(32) NOT NULL,
  `timestamp` varchar(14) NOT NULL,
  `session_data` text NOT NULL,
  PRIMARY KEY (`session_id`)
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
-- Table structure for table `#__mail_log`
--

CREATE TABLE IF NOT EXISTS `#__mail_log` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL default '0',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `transport` char (10) NOT NULL default '',
  `recipient` varchar (255) NOT NULL default '',
  `query` text NOT NULL default '',
  `post` text NOT NULL default '',
  PRIMARY KEY  (`id`)
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
  `dbmessage` varchar(255) NOT NULL default '',
  `dbtrace` text NOT NULL default '',
  `sql` text NOT NULL default '',
  `lmessage` text NOT NULL default '',
  `errorkey` text NOT NULL default '',
  `referer` text NOT NULL default '',
  `get` text NOT NULL default '',
  `post` text NOT NULL default '',
  `trace` text NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__error_404`
--

CREATE TABLE IF NOT EXISTS `#__error_404` (
  `uri` text NOT NULL,
  `timestamp` datetime NOT NULL,
  `referer` text NOT NULL,
  `post` text NOT NULL,
  `trace` text NOT NULL,
  PRIMARY KEY  (`uri`(250))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
