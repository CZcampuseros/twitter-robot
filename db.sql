SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


CREATE TABLE IF NOT EXISTS `twbot_ban` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(32) NOT NULL,
  `user_name` varchar(48) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='List of banned users' AUTO_INCREMENT=94 ;

CREATE TABLE IF NOT EXISTS `twbot_dm` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(32) NOT NULL,
  `user_name` varchar(48) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='List of DM users' AUTO_INCREMENT=31 ;

CREATE TABLE IF NOT EXISTS `twbot_hash` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `hash` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='List of RT hashtags' AUTO_INCREMENT=38 ;

CREATE TABLE IF NOT EXISTS `twbot_rt` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(32) NOT NULL,
  `user_name` varchar(48) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='List of RT users' AUTO_INCREMENT=27 ;

CREATE TABLE IF NOT EXISTS `twbot_short` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `short` varchar(32) CHARACTER SET latin1 NOT NULL,
  `long` varchar(128) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='list of mention shortcuts' AUTO_INCREMENT=19 ;

CREATE TABLE IF NOT EXISTS `twbot_tw` (
  `id` bigint(32) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(32) NOT NULL,
  `user_name` varchar(48) COLLATE utf8_czech_ci NOT NULL,
  `text` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `type` varchar(9) COLLATE utf8_czech_ci NOT NULL,
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='List of tweets';
