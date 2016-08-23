# phpMyAdmin MySQL-Dump
# version 2.3.1-dev
# http://www.phpmyadmin.net/ (download page)
#
# Host: localhost
# Erstellungszeit: 13. Januar 2003 um 10:23
# Server Version: 4.00.07
# PHP-Version: 4.2.2
# Datenbank: `my_db`
# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `authors`
#

CREATE TABLE authors (
  id int(11) NOT NULL auto_increment,
  name varchar(255) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Daten für Tabelle `authors`
#

INSERT INTO authors VALUES (1, 'Wilbur Smith');
INSERT INTO authors VALUES (2, 'Stephen King');
# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `books`
#

CREATE TABLE books (
  id int(11) NOT NULL auto_increment,
  author_id mediumint(9) NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Daten für Tabelle `books`
#

INSERT INTO books VALUES (1, 2, 'The Stand');
INSERT INTO books VALUES (2, 1, 'Warlock');
INSERT INTO books VALUES (3, 2, 'Cujo');
INSERT INTO books VALUES (4, 1, 'The Sunbird');

