-- phpMyAdmin SQL Dump
-- version 2.6.2
-- http://www.phpmyadmin.net
-- 
-- Host: db1.modwest.com
-- Generation Time: May 03, 2008 at 11:06 AM
-- Server version: 5.0.32
-- PHP Version: 4.4.6
-- 
-- Database: `claudias_kids`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `admins`
-- 

DROP TABLE IF EXISTS `admins`;
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(16) NOT NULL,
  `email` varchar(255) NOT NULL,
  `status` enum('active','inactive') NOT NULL,
  `password` varchar(16) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `username` (`username`,`email`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- 
-- Dumping data for table `admins`
-- 

INSERT INTO `admins` (`id`, `username`, `email`, `status`, `password`) VALUES (1, 'admin', 'myerman@gmail.com', 'active', '8843d7f92416211d');

-- --------------------------------------------------------

-- 
-- Table structure for table `categories`
-- 

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `shortdesc` varchar(255) NOT NULL,
  `longdesc` text NOT NULL,
  `status` enum('active','inactive') NOT NULL,
  `parentid` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

-- 
-- Dumping data for table `categories`
-- 

INSERT INTO `categories` (`id`, `name`, `shortdesc`, `longdesc`, `status`, `parentid`) VALUES (1, 'shoes', 'Shoes for boys and girls.', '', 'active', 7);
INSERT INTO `categories` (`id`, `name`, `shortdesc`, `longdesc`, `status`, `parentid`) VALUES (2, 'shirts', 'Shirts and blouses!', '', 'active', 7);
INSERT INTO `categories` (`id`, `name`, `shortdesc`, `longdesc`, `status`, `parentid`) VALUES (3, 'pants', 'Stylish, durable pants for play or school.', '', 'active', 7);
INSERT INTO `categories` (`id`, `name`, `shortdesc`, `longdesc`, `status`, `parentid`) VALUES (4, 'dresses', 'Pretty dresses for the apple of your eye.', '', 'inactive', 7);
INSERT INTO `categories` (`id`, `name`, `shortdesc`, `longdesc`, `status`, `parentid`) VALUES (5, 'toys', 'Toys that are fun and mentally stimulating at the same time.', '', 'active', 8);
INSERT INTO `categories` (`id`, `name`, `shortdesc`, `longdesc`, `status`, `parentid`) VALUES (6, 'games', 'Fun for the whole family.', '', 'active', 8);
INSERT INTO `categories` (`id`, `name`, `shortdesc`, `longdesc`, `status`, `parentid`) VALUES (7, 'clothes', 'Clothes for school and play.', '', 'active', 0);
INSERT INTO `categories` (`id`, `name`, `shortdesc`, `longdesc`, `status`, `parentid`) VALUES (8, 'fun', 'It''s time to unwind!', '', 'active', 0);
INSERT INTO `categories` (`id`, `name`, `shortdesc`, `longdesc`, `status`, `parentid`) VALUES (9, 'test', 'testing', 'Testing!!!!', 'inactive', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `colors`
-- 

DROP TABLE IF EXISTS `colors`;
CREATE TABLE IF NOT EXISTS `colors` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(32) NOT NULL,
  `status` enum('active','inactive') NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- 
-- Dumping data for table `colors`
-- 

INSERT INTO `colors` (`id`, `name`, `status`) VALUES (1, 'red', 'active');
INSERT INTO `colors` (`id`, `name`, `status`) VALUES (2, 'green', 'active');
INSERT INTO `colors` (`id`, `name`, `status`) VALUES (3, 'blue', 'active');
INSERT INTO `colors` (`id`, `name`, `status`) VALUES (4, 'yellow', 'active');

-- --------------------------------------------------------

-- 
-- Table structure for table `pages`
-- 

DROP TABLE IF EXISTS `pages`;
CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `keywords` varchar(255) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `path` varchar(255) NOT NULL default '',
  `content` text NOT NULL,
  `status` enum('active','inactive') NOT NULL default 'active',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- 
-- Dumping data for table `pages`
-- 

INSERT INTO `pages` (`id`, `name`, `keywords`, `description`, `path`, `content`, `status`) VALUES (1, 'About Us', 'about us', 'about us page', 'about_us', '<p>Sample About Us information</p>', 'active');
INSERT INTO `pages` (`id`, `name`, `keywords`, `description`, `path`, `content`, `status`) VALUES (2, 'Privacy', 'privacy', 'privacy', 'privacy', 'Privacy information', 'active');
INSERT INTO `pages` (`id`, `name`, `keywords`, `description`, `path`, `content`, `status`) VALUES (3, 'Contact', 'contact', 'contact', 'contact', 'Contact Information goes here', 'active');

-- --------------------------------------------------------

-- 
-- Table structure for table `products`
-- 

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `shortdesc` varchar(255) NOT NULL,
  `longdesc` text NOT NULL,
  `thumbnail` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `grouping` varchar(16) default NULL,
  `status` enum('active','inactive') NOT NULL,
  `category_id` int(11) NOT NULL,
  `featured` enum('true','false') NOT NULL,
  `price` float(4,2) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=latin1 AUTO_INCREMENT=17 ;

-- 
-- Dumping data for table `products`
-- 

INSERT INTO `products` (`id`, `name`, `shortdesc`, `longdesc`, `thumbnail`, `image`, `grouping`, `status`, `category_id`, `featured`, `price`) VALUES (1, 'Game 1', 'This is a very good game.', 'What a product! You''ll love the way your kids will play with this game all day long. It''s terrific!', '/images/dummy-thumb.jpg', '/images/dummy-main.jpg', 'blob', 'active', 3, 'true', 19.95);
INSERT INTO `products` (`id`, `name`, `shortdesc`, `longdesc`, `thumbnail`, `image`, `grouping`, `status`, `category_id`, `featured`, `price`) VALUES (2, 'Game 2', 'This is a very good game.', 'What a product! You''ll love the way your kids will play with this game all day long. It''s terrific!', '/images/dummy-thumb.jpg', '/images/dummy-main.jpg', 'blob', 'active', 3, 'true', 19.95);
INSERT INTO `products` (`id`, `name`, `shortdesc`, `longdesc`, `thumbnail`, `image`, `grouping`, `status`, `category_id`, `featured`, `price`) VALUES (3, 'Game 3', 'This is a very good game.', 'What a product! You''ll love the way your kids will play with this game all day long. It''s terrific!', '/images/dummy-thumb.jpg', '/images/dummy-main.jpg', 'blob', 'active', 1, 'true', 39.95);
INSERT INTO `products` (`id`, `name`, `shortdesc`, `longdesc`, `thumbnail`, `image`, `grouping`, `status`, `category_id`, `featured`, `price`) VALUES (4, 'Toy 1', 'This is a very good toy.', 'What a product! You''ll love the way your kids will play with this game all day long. It''s terrific!', '/images/dummy-thumb.jpg', '/images/dummy-main.jpg', 'blob', 'active', 1, 'true', 9.95);
INSERT INTO `products` (`id`, `name`, `shortdesc`, `longdesc`, `thumbnail`, `image`, `grouping`, `status`, `category_id`, `featured`, `price`) VALUES (5, 'Toy 2', 'This is a very good toy.', 'What a product! You''ll love the way your kids will play with this game all day long. It''s terrific!', '/images/dummy-thumb.jpg', '/images/dummy-main.jpg', 'blob', 'active', 6, 'false', 23.95);
INSERT INTO `products` (`id`, `name`, `shortdesc`, `longdesc`, `thumbnail`, `image`, `grouping`, `status`, `category_id`, `featured`, `price`) VALUES (6, 'Shoes 1', 'This is a very good pair of shoes.', 'These shoes will last forever!', '/images/dummy-thumb.jpg', '/images/dummy-main.jpg', 'blob', 'active', 6, 'true', 23.95);
INSERT INTO `products` (`id`, `name`, `shortdesc`, `longdesc`, `thumbnail`, `image`, `grouping`, `status`, `category_id`, `featured`, `price`) VALUES (7, 'Shoes 2', 'This is a very good pair of shoes.', 'These shoes will last forever!', '/images/dummy-thumb.jpg', '/images/dummy-main.jpg', 'blob', 'active', 1, 'false', 23.95);
INSERT INTO `products` (`id`, `name`, `shortdesc`, `longdesc`, `thumbnail`, `image`, `grouping`, `status`, `category_id`, `featured`, `price`) VALUES (8, 'Shirt 1', 'Nice shirt!', 'A stylish shirt for school!', '/images/dummy-thumb.jpg', '/images/dummy-main.jpg', 'blob', 'active', 2, 'true', 23.95);
INSERT INTO `products` (`id`, `name`, `shortdesc`, `longdesc`, `thumbnail`, `image`, `grouping`, `status`, `category_id`, `featured`, `price`) VALUES (9, 'Shirt 2', 'Nice shirt!', 'A stylish shirt for school!', '/images/dummy-thumb.jpg', '/images/dummy-main.jpg', 'blob', 'active', 2, 'false', 23.95);
INSERT INTO `products` (`id`, `name`, `shortdesc`, `longdesc`, `thumbnail`, `image`, `grouping`, `status`, `category_id`, `featured`, `price`) VALUES (10, 'Dress 1', 'Nice dress!', 'A stylish dress just in time for school!', '/images/dummy-thumb.jpg', '/images/dummy-main.jpg', 'blob', 'active', 2, 'true', 33.95);
INSERT INTO `products` (`id`, `name`, `shortdesc`, `longdesc`, `thumbnail`, `image`, `grouping`, `status`, `category_id`, `featured`, `price`) VALUES (11, 'Dress 2', 'Nice dress!', 'A stylish dress just in time for school!', '/images/dummy-thumb.jpg', '/images/dummy-main.jpg', 'blob', 'active', 2, 'true', 43.95);
INSERT INTO `products` (`id`, `name`, `shortdesc`, `longdesc`, `thumbnail`, `image`, `grouping`, `status`, `category_id`, `featured`, `price`) VALUES (12, 'Pants 1', 'Nice pair of pants!', 'A stylish pair of pants just in time for school!', '/images/dummy-thumb.jpg', '/images/dummy-main.jpg', 'blob', 'active', 3, 'true', 33.95);
INSERT INTO `products` (`id`, `name`, `shortdesc`, `longdesc`, `thumbnail`, `image`, `grouping`, `status`, `category_id`, `featured`, `price`) VALUES (13, 'test123', 'test!!', 'test!!!!', '/images/dummy-thumb.jpg', '/images/dummy-main.jpg', 'xyz', 'active', 1, 'false', 10.95);
INSERT INTO `products` (`id`, `name`, `shortdesc`, `longdesc`, `thumbnail`, `image`, `grouping`, `status`, `category_id`, `featured`, `price`) VALUES (14, 'Long-sleeved t-shirt', 'Very comfy!', 'A great t-shirt for cold autumn days.', '/images/dummy-thumb1.jpg', '/images/dummy-main1.jpg', 'blah', 'active', 2, 'true', 29.95);
INSERT INTO `products` (`id`, `name`, `shortdesc`, `longdesc`, `thumbnail`, `image`, `grouping`, `status`, `category_id`, `featured`, `price`) VALUES (15, 'Shoes Testing', 'test', 'test', '/images/dummy-thumb4.jpg', '/images/dummy-main4.jpg', 'adfasdf', 'active', 1, 'true', 1.29);
INSERT INTO `products` (`id`, `name`, `shortdesc`, `longdesc`, `thumbnail`, `image`, `grouping`, `status`, `category_id`, `featured`, `price`) VALUES (16, 'afd', 'afd', 'adfds', '', '', 'aasdfd', 'active', 1, '', 5.99);

-- --------------------------------------------------------

-- 
-- Table structure for table `products_colors`
-- 

DROP TABLE IF EXISTS `products_colors`;
CREATE TABLE IF NOT EXISTS `products_colors` (
  `product_id` int(11) NOT NULL,
  `color_id` int(11) NOT NULL,
  PRIMARY KEY  (`product_id`,`color_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `products_colors`
-- 

INSERT INTO `products_colors` (`product_id`, `color_id`) VALUES (15, 2);
INSERT INTO `products_colors` (`product_id`, `color_id`) VALUES (15, 3);
INSERT INTO `products_colors` (`product_id`, `color_id`) VALUES (16, 3);

-- --------------------------------------------------------

-- 
-- Table structure for table `products_sizes`
-- 

DROP TABLE IF EXISTS `products_sizes`;
CREATE TABLE IF NOT EXISTS `products_sizes` (
  `product_id` int(11) NOT NULL,
  `size_id` int(11) NOT NULL,
  PRIMARY KEY  (`product_id`,`size_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `products_sizes`
-- 

INSERT INTO `products_sizes` (`product_id`, `size_id`) VALUES (15, 1);
INSERT INTO `products_sizes` (`product_id`, `size_id`) VALUES (15, 2);
INSERT INTO `products_sizes` (`product_id`, `size_id`) VALUES (15, 3);
INSERT INTO `products_sizes` (`product_id`, `size_id`) VALUES (15, 4);
INSERT INTO `products_sizes` (`product_id`, `size_id`) VALUES (16, 4);

-- --------------------------------------------------------

-- 
-- Table structure for table `sizes`
-- 

DROP TABLE IF EXISTS `sizes`;
CREATE TABLE IF NOT EXISTS `sizes` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(32) NOT NULL,
  `status` enum('active','inactive') NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- 
-- Dumping data for table `sizes`
-- 

INSERT INTO `sizes` (`id`, `name`, `status`) VALUES (1, 'S', 'active');
INSERT INTO `sizes` (`id`, `name`, `status`) VALUES (2, 'M', 'active');
INSERT INTO `sizes` (`id`, `name`, `status`) VALUES (3, 'L', 'active');
INSERT INTO `sizes` (`id`, `name`, `status`) VALUES (4, 'XL', 'active');

-- --------------------------------------------------------

-- 
-- Table structure for table `subscribers`
-- 

DROP TABLE IF EXISTS `subscribers`;
CREATE TABLE IF NOT EXISTS `subscribers` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `subscribers`
-- 

INSERT INTO `subscribers` (`id`, `name`, `email`) VALUES (1, 'Tom Myer', 'myerman@gmail.com');
