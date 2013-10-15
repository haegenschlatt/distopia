-- phpMyAdmin SQL Dump
-- version 4.0.6deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 14, 2013 at 08:46 PM
-- Server version: 5.5.33-1-log
-- PHP Version: 5.5.4-1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
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


--
-- Table structure for table `posts`
--

CREATE TABLE IF NOT EXISTS `posts` (
  `userid` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `content` varchar(900) DEFAULT NULL,
  `date` varchar(20) DEFAULT NULL,
  `id` int(11) DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL,
  `board` varchar(5) DEFAULT NULL,
  `thread` int(11) NOT NULL,
  `parent` int(11) NOT NULL,
  `image` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `sessions`
--

CREATE TABLE IF NOT EXISTS `sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(45) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_activity_idx` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `threads`
--

CREATE TABLE IF NOT EXISTS `threads` (
  `userid` int(11) NOT NULL,
  `username` text NOT NULL,
  `title` text NOT NULL,
  `content` text NOT NULL,
  `date` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `ip` text NOT NULL,
  `board` text NOT NULL,
  `type` text NOT NULL,
  `latest` int(11) NOT NULL,
  `image` int(11) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL,
  `username` text NOT NULL,
  `passhash` text NOT NULL,
  `admin` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `passhash`, `admin`) VALUES
(1, 'test', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
