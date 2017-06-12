-- phpMyAdmin SQL Dump
-- version 4.6.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 27, 2017 at 01:25 PM
-- Server version: 5.7.12-log
-- PHP Version: 5.6.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `discuss_forum`
--

-- --------------------------------------------------------

--
-- Table structure for table `ci_sessions`
--

CREATE TABLE `ci_sessions` (
  `session_id` varchar(40) COLLATE utf8_bin NOT NULL DEFAULT '0',
  `ip_address` varchar(16) COLLATE utf8_bin NOT NULL DEFAULT '0',
  `user_agent` varchar(120) COLLATE utf8_bin DEFAULT NULL,
  `last_activity` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `user_data` text COLLATE utf8_bin NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `cm_id` int(11) NOT NULL,
  `ds_id` int(11) NOT NULL,
  `cm_body` text NOT NULL,
  `cm_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usr_id` int(11) NOT NULL,
  `cm_is_active` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`cm_id`, `ds_id`, `cm_body`, `cm_created_at`, `usr_id`, `cm_is_active`) VALUES
(4, 4, 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum', '2017-02-27 11:21:59', 2, 0);

-- --------------------------------------------------------

--
-- Table structure for table `discussions`
--

CREATE TABLE `discussions` (
  `ds_id` int(11) NOT NULL,
  `usr_id` int(11) NOT NULL,
  `ds_title` varchar(255) NOT NULL,
  `ds_body` text NOT NULL,
  `ds_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ds_is_active` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `discussions`
--

INSERT INTO `discussions` (`ds_id`, `usr_id`, `ds_title`, `ds_body`, `ds_created_at`, `ds_is_active`) VALUES
(4, 2, 'which language to select', 'php or js', '2016-11-23 11:47:31', 1),
(6, 2, 'which language to select', 'php or js', '2016-11-23 11:47:45', 1),
(13, 3, 'which language to select', 'php or f', '2016-11-23 11:50:48', 1),
(14, 3, 'which language to select', 'php or java', '2016-11-23 11:51:53', 0),
(16, 1, 'my first article', 'php or js', '2016-11-23 11:53:29', 1),
(24, 4, 'which language to select', 'f', '2016-11-23 12:59:01', 1),
(25, 5, 'which language to select', 'kki', '2016-11-23 15:32:40', 1),
(26, 6, 'my first article', 'kiju', '2016-11-23 15:33:37', 1),
(27, 7, 'which language to select', 'loki', '2016-11-23 15:33:56', 1),
(28, 6, 'which language to select', 'mjkuh nhbgyt', '2016-11-23 19:34:02', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `usr_id` int(11) NOT NULL,
  `usr_name` varchar(25) NOT NULL,
  `usr_hash` varchar(255) NOT NULL,
  `usr_email` varchar(125) NOT NULL,
  `usr_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usr_is_active` int(1) NOT NULL,
  `usr_level` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`usr_id`, `usr_name`, `usr_hash`, `usr_email`, `usr_created_at`, `usr_is_active`, `usr_level`) VALUES
(1, 'mahmoud', 'O2W89s2L8byqUAsz', 'mahhashish@yahoo.com', '2016-11-23 09:20:43', 1, 1),
(2, 'mahmoud', '2kY6HUoqiNglXCii', 'j@mail.com', '2016-11-23 09:25:08', 1, 1),
(3, 'ahmed', '4CjyCe8ZMNWv45SL', 'j@yahoo.com', '2016-11-23 11:45:09', 1, 1),
(4, 'mahmoud', 'fKc9UlaBjLexbuoR', 'go@mail.com', '2016-11-23 11:55:31', 1, 1),
(5, 'mahmoud', 'V9wKfxIBV8hIzWPt', 'hh@yahoo.com', '2016-11-23 15:32:40', 1, 1),
(6, 'mahmoud', 'wlP8YpPZVVuwTIXy', 'ku@mail.com', '2016-11-23 15:33:37', 1, 1),
(7, 'ahmed', 'L1P4tJOQ6EUObpvv', 'lkj@yahoo.com', '2016-11-23 15:33:55', 1, 1),
(8, 'hh', 'lcGpLwHjJGIfnT2b', 'jvv@gg.cc', '2017-02-17 11:29:23', 1, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ci_sessions`
--
ALTER TABLE `ci_sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `last_activity_idx` (`last_activity`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`cm_id`);

--
-- Indexes for table `discussions`
--
ALTER TABLE `discussions`
  ADD PRIMARY KEY (`ds_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`usr_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `cm_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `discussions`
--
ALTER TABLE `discussions`
  MODIFY `ds_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `usr_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
