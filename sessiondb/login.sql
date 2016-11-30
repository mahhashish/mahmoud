-- phpMyAdmin SQL Dump
-- version 4.4.10
-- http://www.phpmyadmin.net
--
-- Host: localhost:8889
-- Generation Time: Nov 27, 2016 at 04:29 PM
-- Server version: 5.5.42
-- PHP Version: 5.6.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `login`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_details`
--

CREATE TABLE `tb_details` (
  `name` varchar(255) NOT NULL,
  `userid` int(24) NOT NULL,
  `email_add` varchar(23) NOT NULL,
  `PhoneNumber` int(13) NOT NULL,
  `access_level` int(2) NOT NULL,
  `password` varchar(255) NOT NULL,
  `date_registration` datetime NOT NULL,
  `timein` datetime NOT NULL,
  `timeout` datetime NOT NULL,
  `session_active` varchar(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_details`
--
ALTER TABLE `tb_details`
  ADD UNIQUE KEY `userid` (`userid`),
  ADD UNIQUE KEY `userid_2` (`userid`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `email_add` (`email_add`);

