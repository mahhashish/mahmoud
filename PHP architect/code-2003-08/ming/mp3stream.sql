# phpMyAdmin MySQL-Dump
# version 2.3.3pl1
# http://www.phpmyadmin.net/ (download page)
#
# Host: localhost
# Generation Time: Jul 02, 2003 at 08:55 PM
# Server version: 4.00.12
# PHP Version: 4.3.1
# Database : `mp3stream`
# --------------------------------------------------------

#
# Table structure for table `mp3info`
#

CREATE TABLE mp3info (
  mp3ID int(11) NOT NULL auto_increment,
  mp3Filename varchar(255) NOT NULL default '',
  mp3Length int(11) NOT NULL default '0',
  mp3Title varchar(255) NOT NULL default '',
  mp3Artist varchar(255) NOT NULL default '',
  mp3Album varchar(255) NOT NULL default '',
  mp3Year varchar(10) NOT NULL default '',
  mp3Comment varchar(255) NOT NULL default '',
  mp3Genre varchar(255) NOT NULL default '',
  PRIMARY KEY  (mp3ID)
) TYPE=MyISAM;

