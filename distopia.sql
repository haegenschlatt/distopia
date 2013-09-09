-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 09, 2013 at 05:42 PM
-- Server version: 5.5.31
-- PHP Version: 5.4.4-14+deb7u4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `distopia`
--

-- --------------------------------------------------------

--
-- Table structure for table `boardmeta`
--

CREATE TABLE IF NOT EXISTS `boardmeta` (
  `name` varchar(10) NOT NULL,
  `description` varchar(50) NOT NULL,
  `remark` varchar(150) NOT NULL,
  `r9k` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `boardmeta`
--

INSERT INTO `boardmeta` (`name`, `description`, `remark`, `r9k`) VALUES
('a', 'beta test', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE IF NOT EXISTS `posts` (
  `name` varchar(100) DEFAULT NULL,
  `content` varchar(900) DEFAULT NULL,
  `date` varchar(20) DEFAULT NULL,
  `id` int(11) DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL,
  `board` varchar(5) DEFAULT NULL,
  `color` varchar(6) DEFAULT NULL,
  `thread` int(11) NOT NULL,
  `parent` int(11) NOT NULL,
  `latest` varchar(50) NOT NULL,
  `image` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
