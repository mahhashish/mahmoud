# Connection: Obione
# Host: obione
# Saved: 2003-01-10 20:15:54
# 
# Host: obione
# Database: PHP_Articles
# Table: 'users'
# 
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL auto_increment,
  `username` varchar(20) NOT NULL default '',
  `password` varchar(100) NOT NULL default '',
  `email` varchar(50) NOT NULL default '',
  `firstname` varchar(50) NOT NULL default '',
  `lastname` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`user_id`)
) TYPE=MyISAM; 

