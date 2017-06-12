-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 13, 2017 at 09:24 AM
-- Server version: 5.5.49-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `school`
--

-- --------------------------------------------------------

--
-- Table structure for table `article`
--

CREATE TABLE IF NOT EXISTS `article` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `tag` varchar(255) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=44 ;

--
-- Dumping data for table `article`
--

INSERT INTO `article` (`id`, `title`, `content`, `tag`, `created_date`) VALUES
(43, 'Hello', 'dedhyfafha  ahfhajahd asjfafajfdjfdaf nsajnfjkafjnj sjfajknfjanf sjnafnkja wdaafafaf', 'poonam, priyanka', '2017-05-08 11:55:14');

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

CREATE TABLE IF NOT EXISTS `contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `cellnum` bigint(100) NOT NULL,
  `fax` bigint(100) NOT NULL,
  `mailadd` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `contact`
--

INSERT INTO `contact` (`id`, `email`, `cellnum`, `fax`, `mailadd`) VALUES
(13, 'anshgaurav223@gmail.com', 9166477223, 9166477223, ' abc building, model town jaipur');

-- --------------------------------------------------------

--
-- Table structure for table `user_registration`
--

CREATE TABLE IF NOT EXISTS `user_registration` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `email` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `dob` varchar(100) NOT NULL,
  `gender` varchar(100) NOT NULL,
  `identity` varchar(100) NOT NULL,
  `phone` bigint(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ;

--
-- Dumping data for table `user_registration`
--

INSERT INTO `user_registration` (`id`, `name`, `address`, `email`, `city`, `password`, `dob`, `gender`, `identity`, `phone`) VALUES
(11, 'afdfs', '31 A, Madhuvan colony sector-4 senthi chittorgarh', 'Anshgaudddddrav223@gmail.com', 'chittorgarh', '453435443', '03-1-1994', 'male', 'student', 9166477223),
(12, 'saini', '31 A, Madhuvan colony sector-4 senthi chittorgarh', 'Anshgrav223@gmail.com', 'chittorgarh', '453435443', '01-1-1994', 'male', 'student', 9166477223),
(15, 'Aarti', '31 A, Madhuvan colony sector-4 senthi ', 'aarti@gmail.com', 'Jaipur', '25d55ad283aa400af464c76d713c07ad', '25-03-1995', 'female', 'teacher', 9166477223),
(16, 'Aarti', 'Mansrover', 'abc@gmail.com', 'Jaipur', '25d55ad283aa400af464c76d713c07ad', '03-24-1994', 'male', 'student', 9166477223),
(18, 'Poonam Bansal', 'Model Town', 'poonambansal963@gmail.com', 'Jaipur', '25d55ad283aa400af464c76d713c07ad', '08-10-1995', 'female', 'student', 9166477223),
(19, 'Arohi Sharma', '31 a,madhuvan colony senthi chittorgarh', 'arohisharma@gmail.com', 'Shimla', '25d55ad283aa400af464c76d713c07ad', '25-10-1988', 'female', 'student', 9166477223),
(20, '31 A, Madhuvan colony sector-4 senthi chittorgarh', '31 A, Madhuvan colony sector-4 senthi chittorgarh', 'Anshgaurav223@gmail.com', 'chittorgarh', '4529e7882d377912ec7afa76a3fe99e4', '25-01-1994', 'select', 'select', 9166477223);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
