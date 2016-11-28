/*
MySQL Data Transfer
Source Host: localhost
Source Database: hotel
Target Host: localhost
Target Database: hotel
Date: 1/17/2016 5:08:30 PM
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for customer
-- ----------------------------
DROP TABLE IF EXISTS `customer`;
CREATE TABLE `customer` (
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(20) NOT NULL,
  `mi` varchar(6) NOT NULL,
  `lastname` varchar(20) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `Phone` varchar(11) NOT NULL,
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records 
-- ----------------------------
INSERT INTO `customer` VALUES ('62', 'Diover', '', 'Sawada', 'sawada@gwapa.com', '1234567890');
INSERT INTO `customer` VALUES ('63', 'Albert', '', 'Guigayoma', 'guigzal@yahoo.com', '42341');
INSERT INTO `customer` VALUES ('65', 'Sam', 'P', 'Le', 'sample@yahoo.com', '0917-jollib');
