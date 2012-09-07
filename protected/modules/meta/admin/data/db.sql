-- phpMyAdmin SQL Dump
-- version 3.2.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 07, 2012 at 06:06 PM
-- Server version: 5.1.40
-- PHP Version: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
 
CREATE TABLE IF NOT EXISTS `meta_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(128) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `body` longtext,
  `keywords` text,
  `description` text,
  `active` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='TEXT RU:SEO оптимизация' AUTO_INCREMENT=1 ;

  
CREATE TABLE IF NOT EXISTS `ru_meta_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(128) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `body` longtext,
  `keywords` text,
  `description` text,
  `active` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url_new` (`url`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=16384 COMMENT='TEXT RU:SEO оптимизация' AUTO_INCREMENT=3 ;

 
--
-- Constraints for table `ru_meta_data`
--
ALTER TABLE `ru_meta_data`
  ADD CONSTRAINT `ru_meta_data_fk1` FOREIGN KEY (`id`) REFERENCES `meta_data` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
