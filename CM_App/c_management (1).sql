-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Nov 09, 2016 at 01:12 AM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `c_management`
--
CREATE DATABASE IF NOT EXISTS `c_management` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `c_management`;

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE IF NOT EXISTS `articles` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `ref` varchar(100) NOT NULL,
  `desig` varchar(255) NOT NULL,
  `tva` int(2) NOT NULL,
  `supplier_id` int(20) NOT NULL,
  `category_id` int(20) NOT NULL DEFAULT '0',
  `unit_id` int(20) NOT NULL DEFAULT '0',
  `thumb` varchar(255) NOT NULL DEFAULT 'no_thumb.jpg',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int(20) NOT NULL,
  `updated_by` int(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`id`, `ref`, `desig`, `tva`, `supplier_id`, `category_id`, `unit_id`, `thumb`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, '4545454545', 'hp laser jet 1555', 20, 1, 1, 1, 'no_thumb.jpg', '2016-02-07 16:59:16', '2016-03-08 22:15:24', 1, 1),
(2, '4878787', 'acer aspire 5855', 20, 2, 2, 1, 'no_thumb.jpg', '2016-02-07 17:01:28', '2016-03-08 22:15:33', 1, 1),
(6, 'dkddfkldf', 'dflfkdfkl', 20, 44, 1, 1, 'logo.png', '2016-04-07 18:38:55', '2016-04-07 18:38:55', 1, 1),
(7, 'sdkldfdflsdsdsdsdfdf', 'dfkldfkl', 20, 1, 0, 1, '7.jpg', '2016-08-18 22:42:38', '2016-08-18 22:55:48', 1, 1),
(8, 'dfffdml', 'mllfdf', 20, 1, 0, 1, '8.jpg', '2016-08-18 23:01:02', '2016-08-18 23:01:02', 1, 1),
(13, 'fffgggffg', 'fggfgfg', 20, 1, 0, 1, 'no_thumb.jpg', '2016-09-25 19:24:29', '2016-09-25 19:24:29', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `category` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `category`) VALUES
(0, 'غير مصنف'),
(1, 'معدات  معلوماتية'),
(2, 'مكاتب'),
(3, '&lt;script&gt;alert(&quot;hello&quot;);&lt;/script&gt;'),
(4, 'حذف	&lt;script&gt;alert(&quot;hello&quot;);&lt;/script&gt;');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE IF NOT EXISTS `clients` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `tel` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `zip_code` varchar(10) NOT NULL,
  `city` varchar(50) NOT NULL,
  `address` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL DEFAULT '1',
  `updated_by` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `name`, `tel`, `email`, `zip_code`, `city`, `address`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(4, 'said hamri', '', 'ssdd@dd.com', '565656456', 'etetzete', 'zetzetzete', '2016-10-14 18:44:30', '2016-10-14 18:44:30', 1, 1),
(7, 'EDITED CLIENT', '45458686', 'ssdd@dd.com', '455445', 'FGKLDFKL', '&pound;DFDFMLDFDFMLMO', '2016-10-14 18:46:01', '2016-10-14 18:46:22', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `prs_clt`
--

CREATE TABLE IF NOT EXISTS `prs_clt` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `num` varchar(100) NOT NULL,
  `dt` varchar(10) DEFAULT NULL,
  `discr` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `client_id` int(20) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int(20) NOT NULL DEFAULT '1',
  `updated_by` int(20) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='prices request client' AUTO_INCREMENT=4 ;

--
-- Dumping data for table `prs_clt`
--

INSERT INTO `prs_clt` (`id`, `num`, `dt`, `discr`, `subject`, `client_id`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, '454545', '24-10-2016', 'صسبسثب', 'سيكمبسيلمسيكبمسي', 4, '2016-10-16 22:21:43', '2016-10-22 19:59:12', 1, 1),
(3, 'swdfsfsdff54', '06-06-2016', 'zfeefef', 'fefzefe', 4, '2016-10-16 23:10:38', '2016-10-16 23:10:38', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `prs_clt_arts`
--

CREATE TABLE IF NOT EXISTS `prs_clt_arts` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `pr_clt_id` int(20) NOT NULL DEFAULT '0',
  `art_id` int(20) NOT NULL,
  `qte` double NOT NULL,
  `creatred_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int(20) NOT NULL DEFAULT '1',
  `updated_by` int(20) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `prs_clt_arts`
--

INSERT INTO `prs_clt_arts` (`id`, `pr_clt_id`, `art_id`, `qte`, `creatred_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(4, 1, 7, 20, '2016-10-29 19:58:13', '2016-10-29 19:58:13', 1, 1),
(5, 1, 8, 54, '2016-11-01 21:29:08', '2016-11-01 21:29:08', 1, 1),
(6, 1, 1, 48, '2016-11-01 21:29:15', '2016-11-01 21:29:15', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `quotations_clt`
--

CREATE TABLE IF NOT EXISTS `quotations_clt` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `num` varchar(100) NOT NULL,
  `dt` varchar(10) DEFAULT NULL,
  `discr` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `client_id` int(20) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int(20) NOT NULL DEFAULT '1',
  `updated_by` int(20) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `quotations_clt`
--

INSERT INTO `quotations_clt` (`id`, `num`, `dt`, `discr`, `subject`, `client_id`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(5, 'dfdf', '05-11-2016', 'ffd', 'dfdf', 4, '2016-11-05 14:12:44', '2016-11-05 14:12:44', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `quotations_clt_arts`
--

CREATE TABLE IF NOT EXISTS `quotations_clt_arts` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `quotation_clt_id` int(20) NOT NULL DEFAULT '0',
  `art_id` int(20) NOT NULL,
  `qte` double NOT NULL,
  `price` double NOT NULL,
  `creatred_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int(20) NOT NULL DEFAULT '1',
  `updated_by` int(20) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(100) NOT NULL,
  `show_clients` int(1) NOT NULL DEFAULT '0',
  `aed_clients` int(1) NOT NULL DEFAULT '0',
  `show_suppliers` int(1) NOT NULL DEFAULT '0',
  `aed_suppliers` int(1) NOT NULL DEFAULT '0',
  `show_sales` int(1) NOT NULL DEFAULT '0',
  `aed_sales` int(1) NOT NULL DEFAULT '0',
  `show_purchases` int(1) NOT NULL DEFAULT '0',
  `aed_purchases` int(11) NOT NULL DEFAULT '0',
  `show_articles` int(1) NOT NULL DEFAULT '0',
  `aed_articles` int(1) NOT NULL DEFAULT '0',
  `show_stock` int(1) NOT NULL DEFAULT '0',
  `show_users_roles` int(1) NOT NULL DEFAULT '0',
  `aed_users_roles` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`, `show_clients`, `aed_clients`, `show_suppliers`, `aed_suppliers`, `show_sales`, `aed_sales`, `show_purchases`, `aed_purchases`, `show_articles`, `aed_articles`, `show_stock`, `show_users_roles`, `aed_users_roles`) VALUES
(1, 'أدمن', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(2, 'مستخدم عادي', 1, 0, 1, 0, 1, 0, 1, 0, 1, 0, 1, 1, 0),
(3, '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(4, 'مكلف بالمبيعات', 1, 1, 1, 0, 1, 1, 0, 0, 1, 0, 1, 0, 0),
(5, 'dffdff', 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `tel` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `zip_code` varchar(10) NOT NULL,
  `city` varchar(50) NOT NULL,
  `address` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL DEFAULT '1',
  `updated_by` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `tel`, `email`, `zip_code`, `city`, `address`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 'INFO TECH', '066556565656', 's.hamri@windowslive.com', '23000', 'Beni Mellal', '101,Hay fdkjdfkjdf, Beni Melall', '2016-03-08 22:13:23', '2016-03-08 22:13:23', 1, 1),
(3, 'said hamri', '4554545', 'ssdd@dd.com', '565656456', 'etetzete', 'zetzetzete', '2016-10-05 22:09:22', '2016-10-05 22:09:22', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tva`
--

CREATE TABLE IF NOT EXISTS `tva` (
  `tva` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tva`
--

INSERT INTO `tva` (`tva`) VALUES
(20),
(14),
(10),
(7);

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE IF NOT EXISTS `units` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `unit` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id`, `unit`) VALUES
(1, 'U'),
(2, 'Kg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `login` varchar(100) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `fname` varchar(50) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `function` varchar(100) NOT NULL,
  `phone` varchar(100) NOT NULL,
  `role_id` int(20) NOT NULL,
  `avatar` varchar(255) NOT NULL DEFAULT '0.jpg',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int(20) NOT NULL,
  `updated_by` int(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `login`, `pass`, `email`, `fname`, `lname`, `function`, `phone`, `role_id`, `avatar`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', 's.hamri@windowslive.com', 'Said', 'HAMRI', 'Admin', '', 1, '1.jpg', '2016-08-31 00:25:13', '2016-11-05 12:58:09', 1, 1),
(3, 'khalid', '94ca247fff5ad413788a1c8d8c80394a246dba1c', 's.hamri@windowslive.com', 'khalid', 'essaadani', 'dev', 'dfdpfl', 2, '0.jpg', '2016-08-31 00:43:58', '2016-09-06 21:40:54', 1, 1),
(4, 'errer', '8356c35003e02a509a7e5c466b9d22712891a1ce', 'erere@ffdf.com', 'dfd', 'df', '', '', 2, '0.jpg', '2016-09-06 21:57:49', '2016-09-06 21:57:49', 1, 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
