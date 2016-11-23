-- MySQL dump 9.06
--
-- Host: localhost    Database: davor
---------------------------------------------------------
-- Server version	4.0.2-alpha-log

--
-- Table structure for table 'ARTICLE'
--

CREATE TABLE ARTICLE (
  article_id varchar(20) NOT NULL default '',
  ARTICLE_NAME varchar(100) default NULL,
  DESCRIPTION text,
  PRICE decimal(12,2) default NULL,
  PRIMARY KEY  (article_id)
) TYPE=MyISAM;

--
-- Dumping data for table 'ARTICLE'
--

INSERT INTO ARTICLE VALUES ('APPLE','Apple','This is tasty and very sweet apple!',5.00);
INSERT INTO ARTICLE VALUES ('PAPRIKA','Green paprika','Have you ever tried fried paprika?',3.20);
INSERT INTO ARTICLE VALUES ('LEMON','Fresh lemon','Make yourself a lemonade!',4.00);
INSERT INTO ARTICLE VALUES ('ONION','Onion','Excellent salad add-on!',2.10);
INSERT INTO ARTICLE VALUES ('MANDARINE','Mandarine','Enjoy this sweet fruit',3.50);
INSERT INTO ARTICLE VALUES ('SUGARPOT','Sugar pot','Use it while drinking coffee',11.45);
INSERT INTO ARTICLE VALUES ('PLANT','Plant in the glass','Decorate your working space with this plant',16.00);
INSERT INTO ARTICLE VALUES ('HONEY','Honey with dry fruit','Delicatesse',20.00);

--
-- Table structure for table 'CATALOGUE'
--

CREATE TABLE CATALOGUE (
  CAT_ID int(11) NOT NULL default '0',
  DESCRIPTION text,
  IMAGE_NAME varchar(250) default NULL,
  PRIMARY KEY  (CAT_ID)
) TYPE=MyISAM;

--
-- Dumping data for table 'CATALOGUE'
--

INSERT INTO CATALOGUE VALUES (1,'Fruits and vegetables','cat1.jpg');
INSERT INTO CATALOGUE VALUES (2,'Accessories','cat2.jpg');

--
-- Table structure for table 'CATAREA'
--

CREATE TABLE CATAREA (
  CAT_ID int(11) NOT NULL default '0',
  AREA_ID int(11) NOT NULL default '0',
  ARTICLE_ID varchar(20) default NULL,
  POINTS text,
  PRIMARY KEY  (CAT_ID,AREA_ID)
) TYPE=MyISAM;

--
-- Dumping data for table 'CATAREA'
--

INSERT INTO CATAREA VALUES (1,1,'APPLE','421,321,463,265,536,254,594,279,636,353,611,425,524,454,470,436,420,375\n');
INSERT INTO CATAREA VALUES (1,2,'LEMON','57,192,68,120,147,69,226,95,215,165,146,216,77,220\n');
INSERT INTO CATAREA VALUES (1,3,'MANDARINE','15,315,40,266,99,245,150,255,191,311,178,363,156,392,104,411,51,398,28,362\n');
INSERT INTO CATAREA VALUES (1,4,'ONION','433,181,456,138,486,123,479,39,532,39,525,113,568,143,595,196,572,238,515,255,478,260,442,215\n');
INSERT INTO CATAREA VALUES (1,5,'PAPRIKA','238,327,240,136,308,103,382,129,401,192,398,248,356,356,333,379,298,392,272,383\n');
INSERT INTO CATAREA VALUES (2,1,'SUGARPOT','121,125,187,198,228,287,215,334,158,366,98,351,70,301,86,244,82,195,98,138\n');
INSERT INTO CATAREA VALUES (2,2,'PLANT','182,178,217,90,434,89,481,184,407,216,385,272,330,292,271,267,253,214\n');
INSERT INTO CATAREA VALUES (2,3,'HONEY','457,206,533,193,591,225,595,284,567,353,492,391,452,372,425,298\n');

