-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 19, 2013 at 06:36 AM
-- Server version: 5.5.31
-- PHP Version: 5.4.4-14+deb7u3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `twitter-robot`
--

-- --------------------------------------------------------

--
-- Table structure for table `twbot_dm`
--

CREATE TABLE IF NOT EXISTS `twbot_dm` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(32) NOT NULL,
  `user_name` varchar(48) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='List of DM users' AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

--
-- Table structure for table `twbot_hash`
--

CREATE TABLE IF NOT EXISTS `twbot_hash` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `hash` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='List of RT hashtags' AUTO_INCREMENT=31 ;

-- --------------------------------------------------------

--
-- Table structure for table `twbot_rt`
--

CREATE TABLE IF NOT EXISTS `twbot_rt` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(32) NOT NULL,
  `user_name` varchar(48) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='List of RT users' AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Table structure for table `twbot_tw`
--

CREATE TABLE IF NOT EXISTS `twbot_tw` (
  `id` bigint(32) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(32) NOT NULL,
  `user_name` varchar(48) COLLATE utf8_czech_ci NOT NULL,
  `text` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `type` varchar(9) COLLATE utf8_czech_ci NOT NULL,
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='List of tweets';

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
