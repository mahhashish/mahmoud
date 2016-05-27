
--
-- Table structure for table `#__categories`
--

CREATE TABLE IF NOT EXISTS `#__categories` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) NOT NULL default '0',
  `title` varchar(50) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `image` varchar(100) NOT NULL default '',
  `section` varchar(50) NOT NULL default '',
  `image_position` varchar(10) NOT NULL default '',
  `description` text NOT NULL,
  `published` tinyint(1) NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `editor` varchar(50) default NULL,
  `ordering` int(11) NOT NULL default '0',
  `access` tinyint(3) unsigned NOT NULL default '0',
  `count` int(11) NOT NULL default '0',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `cat_idx` (`section`,`published`,`access`),
  KEY `idx_section` (`section`),
  KEY `idx_access` (`access`),
  KEY `idx_checkout` (`checked_out`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__core_acl_aro_groups`
--

CREATE TABLE IF NOT EXISTS `#__core_acl_aro_groups` (
  `group_id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `lft` int(11) NOT NULL default '0',
  `rgt` int(11) NOT NULL default '0',
  PRIMARY KEY  (`group_id`),
  KEY `parent_id_aro_groups` (`parent_id`),
  KEY `al_gacl_parent_id_aro_groups` (`parent_id`),
  KEY `al_gacl_lft_rgt_aro_groups` (`lft`,`rgt`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `#__core_acl_aro_groups` (`group_id`, `parent_id`, `name`, `lft`, `rgt`) VALUES
(17, 0, 'ROOT', 1, 22),
(28, 17, 'USERS', 2, 21),
(29, 28, 'Public Frontend', 3, 12),
(18, 29, 'Registered', 4, 11),
(19, 18, 'Author', 5, 10),
(20, 19, 'Editor', 6, 9),
(21, 20, 'Publisher', 7, 8),
(30, 28, 'Public Backend', 13, 20),
(23, 30, 'Manager', 14, 19),
(24, 23, 'Administrator', 15, 18),
(25, 24, 'Super Administrator', 16, 17) ON DUPLICATE KEY UPDATE name = name;

-- --------------------------------------------------------

--
-- Table structure for table `#__core_log_items`
--

CREATE TABLE IF NOT EXISTS `#__core_log_items` (
  `time_stamp` date NOT NULL default '0000-00-00',
  `item_table` varchar(50) NOT NULL default '',
  `item_id` int(11) unsigned NOT NULL default '0',
  `hits` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`time_stamp`,`item_table`,`item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__folders`
--

CREATE TABLE IF NOT EXISTS `#__folders` (
  `id` int(11) NOT NULL auto_increment,
  `parentid` int(11) NOT NULL default '0',
  `ordering` int(11) NOT NULL default '0',
  `published` smallint(6) NOT NULL default '0',
  `childcount` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `title` varchar(100) NOT NULL default '',
  `windowtitle` varchar(100) NOT NULL default '',
  `keywords` varchar(255) NOT NULL default '',
  `icon` varchar(25) NOT NULL default '',
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL default '',
  `image_position` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `ordering` (`ordering`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__groups`
--

CREATE TABLE IF NOT EXISTS `#__groups` (
  `id` tinyint(3) unsigned NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `#__groups` (`id`, `name`) VALUES
(0, 'Public'),
(1, 'Registered'),
(2, 'Special') ON DUPLICATE KEY UPDATE name = name;

-- --------------------------------------------------------

--
-- Table structure for table `#__messages`
--

CREATE TABLE IF NOT EXISTS `#__messages` (
  `message_id` int(10) unsigned NOT NULL auto_increment,
  `user_id_from` int(10) unsigned NOT NULL default '0',
  `user_id_to` int(10) unsigned NOT NULL default '0',
  `pooled_from` tinyint(3) unsigned NOT NULL default '0',
  `pooled_to` tinyint(3) unsigned NOT NULL default '0',
  `thread_id` int(10) unsigned NOT NULL default '0',
  `folder_id` int(10) unsigned NOT NULL default '0',
  `date_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `state` int(11) NOT NULL default '0',
  `priority` int(1) unsigned NOT NULL default '0',
  `datum` int(11) default NULL,
  `toread` tinyint(4) NOT NULL default '0',
  `totrash` tinyint(4) NOT NULL default '0',
  `totrashdate` int(11) default NULL,
  `totrashoutbox` tinyint(4) NOT NULL default '0',
  `totrashdateoutbox` int(11) default NULL,
  `expires` int(11) NOT NULL default '0',
  `disablereply` tinyint(4) NOT NULL default '0',
  `systemmessage` varchar(60) NOT NULL,
  `archived` tinyint(4) NOT NULL default '0',
  `cryptmode` tinyint(4) NOT NULL default '0',
  `flagged` tinyint(4) NOT NULL default '0',
  `crypthash` varchar(32) default NULL,
  `publicname` text NOT NULL,
  `publicemail` text NOT NULL,
  `subject` text NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY  (`message_id`),
  KEY `thread_index` (`thread_id`,`datum`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__messages_cfg`
--

CREATE TABLE IF NOT EXISTS `#__messages_cfg` (
  `user_id` int(10) unsigned NOT NULL default '0',
  `cfg_name` varchar(100) NOT NULL default '',
  `cfg_value` varchar(255) NOT NULL default '',
  UNIQUE KEY `idx_user_var_name` (`user_id`,`cfg_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__parameters`
--

CREATE TABLE IF NOT EXISTS `#__parameters` (
  `id` int(11) NOT NULL auto_increment,
  `param_name` varchar(100) NOT NULL default '',
  `param_file` varchar(255) NOT NULL default '',
  `param_version` varchar(50) NOT NULL default '',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `names` (`param_name`,`param_version`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table structure for table `#__remosef_config`
--

CREATE TABLE IF NOT EXISTS `#__remosef_config` (
  `id` int(11) NOT NULL auto_increment,
  `flags` tinyint(3) UNSIGNED NOT NULL default 0,
  `type` varchar(20) NOT NULL default '',
  `name` text NOT NULL,
  `modified` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `sequence` (`type`,`name`(50))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table structure for table `#__remosef_uri`
--

CREATE TABLE IF NOT EXISTS `#__remosef_uri` (
  `id` int(11) NOT NULL auto_increment,
  `marker` tinyint(4) NOT NULL default '0',
  `shortterm` tinyint(4) NOT NULL default '0',
  `lastused` int(11) NOT NULL default '0',
  `refreshed` int(11) NOT NULL default '0',
  `sef_crc` int(11) unsigned NOT NULL default '0',
  `uri_crc` int(11) unsigned NOT NULL default '0',
  `ipaddress` varchar(15) NOT NULL,
  `sef` text NOT NULL,
  `uri` text NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `refreshed` (`refreshed`,`id`),
  KEY `sef_crc` (`sef_crc`),
  KEY `uri_crc` (`uri_crc`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table structure for table `#__remosef_metadata`
--

CREATE TABLE IF NOT EXISTS `#__remosef_metadata` (
  `id` int(11) NOT NULL auto_increment,
  `type` varchar(10) NOT NULL,
  `uri` text NOT NULL,
  `htmltitle` varchar(255) NOT NULL,
  `robots` varchar(255) NOT NULL,
  `keywords` text NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `finduri` (`uri`(60))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__sections`
--

CREATE TABLE IF NOT EXISTS `#__sections` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(50) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `image` varchar(100) NOT NULL default '',
  `scope` varchar(50) NOT NULL default '',
  `image_position` varchar(10) NOT NULL default '',
  `description` text NOT NULL,
  `published` tinyint(1) NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL default '0',
  `access` tinyint(3) unsigned NOT NULL default '0',
  `count` int(11) NOT NULL default '0',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_scope` (`scope`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__tags`
--
CREATE TABLE IF NOT EXISTS `#__tags` (
  `id` int(11) NOT NULL auto_increment,
  `ordering` int(11) NOT NULL default '0',
  `frequency` int(11) NOT NULL default '0',
  `describes` int(11) NOT NULL default '0',
  `published` tinyint(3) unsigned NOT NULL default '0',
  `hidden` tinyint(3) unsigned NOT NULL default '0',
  `type` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `searchname` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__users`
--

CREATE TABLE IF NOT EXISTS `#__users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL default '',
  `username` varchar(100) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `jobtitle` varchar(100) NOT NULL default '',
  `timezone` varchar(255) NOT NULL default '',
  `location` varchar(255) NOT NULL default '',
  `phone` varchar(100) NOT NULL default '',
  `website` varchar(255) NOT NULL default '',
  `usertype` varchar(100) NOT NULL default '',
  `ipaddress` varchar(15) NOT NULL default '',
  `countrycode` varchar(15) NOT NULL default '',
  `block` tinyint(4) NOT NULL default '0',
  `sendEmail` tinyint(4) NOT NULL default '0',
  `gid` tinyint(3) unsigned NOT NULL default '1',
  `registerDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `lastvisitDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `avatype` varchar(4) NOT NULL default '',
  `params` text NOT NULL,
  `avatar` blob NOT NULL,
  `special` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `idx_username` (`username`),
  UNIQUE KEY `idx_email` (`email`),
  KEY `idx_name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__users_former_emails`
--

CREATE TABLE IF NOT EXISTS `#__users_former_emails` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL default '',
  `changed` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `idx_email` (`email`)
) DEFAULT CHARSET=utf8;
