-- phpMyAdmin SQL Dump
-- version 3.5.0
-- http://www.phpmyadmin.net
--
-- Host: wm43.wedos.net:3306
-- Generation Time: Aug 25, 2013 at 11:33 AM
-- Server version: 5.6.12
-- PHP Version: 5.4.11

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `d38035_twrobot`
--

-- --------------------------------------------------------

--
-- Table structure for table `twbot_me`
--

CREATE TABLE IF NOT EXISTS `twbot_me` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(32) NOT NULL,
  `user_name` varchar(48) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='List of ME users' AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
