-- phpMyAdmin SQL Dump
-- version 4.2.3
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 22, 2014 at 07:08 PM
-- Server version: 5.5.38
-- PHP Version: 5.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `documenttracker`
--

-- --------------------------------------------------------

--
-- Table structure for table `auditlog`
--

CREATE TABLE `auditlog` (
`id` bigint(20) unsigned NOT NULL COMMENT 'Audit ID',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Action Timestamp',
  `type` enum('Critical','Warning','Notice','Info') NOT NULL DEFAULT 'Info' COMMENT 'Audit Type',
  `user` int(10) unsigned zerofill NOT NULL COMMENT 'User',
  `page` varchar(100) NOT NULL COMMENT 'Page Name',
  `msg` varchar(512) NOT NULL COMMENT 'Message'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Audit Log' AUTO_INCREMENT=3 ;

--
-- Dumping data for table `auditlog`
--

INSERT INTO `auditlog` (`id`, `ts`, `type`, `user`, `page`, `msg`) VALUES
(1, '2014-06-22 17:53:30', 'Info', 0000003597, 'login', 'System Admin(3597) logged in to the system.'),
(2, '2014-06-22 17:54:38', 'Info', 0000003597, 'login', 'System Admin(3597) logged in to the system.');

-- --------------------------------------------------------

--
-- Table structure for table `document`
--

CREATE TABLE `document` (
`trackingnumber` int(8) unsigned zerofill NOT NULL COMMENT 'Tracking Number',
  `documentnumber` varchar(100) DEFAULT NULL COMMENT 'Document Number',
  `remarks` varchar(512) DEFAULT NULL COMMENT 'Remarks',
  `datecreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date Created',
  `author` int(10) unsigned zerofill NOT NULL COMMENT 'Author'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Document Information' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `documentlog`
--

CREATE TABLE `documentlog` (
`logid` bigint(10) unsigned zerofill NOT NULL COMMENT 'Document Log ID',
  `trackingnumber` int(8) unsigned zerofill NOT NULL COMMENT 'Tracking Number',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp',
  `remarks` varchar(512) NOT NULL COMMENT 'Remarks',
  `user` int(10) unsigned zerofill NOT NULL COMMENT 'User FK'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Document Log' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
`id` int(10) unsigned zerofill NOT NULL COMMENT 'User ID of user',
  `uid` int(4) unsigned zerofill NOT NULL COMMENT 'Employee ID Number',
  `password` varchar(48) NOT NULL COMMENT 'MD5 Hash of User''s Password',
  `fullname` varchar(255) NOT NULL COMMENT 'User''s full name',
  `department` varchar(255) NOT NULL COMMENT 'Department',
  `section` varchar(255) NOT NULL COMMENT 'Section',
  `regdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'User Registration Timestamp'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='User Information' AUTO_INCREMENT=2 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `uid`, `password`, `fullname`, `department`, `section`, `regdate`) VALUES
(0000000001, 3597, 'admin', 'System Admin', 'PICTO', 'Medix', '2014-06-21 23:48:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `auditlog`
--
ALTER TABLE `auditlog`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `document`
--
ALTER TABLE `document`
 ADD PRIMARY KEY (`trackingnumber`);

--
-- Indexes for table `documentlog`
--
ALTER TABLE `documentlog`
 ADD PRIMARY KEY (`logid`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `username` (`uid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `auditlog`
--
ALTER TABLE `auditlog`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Audit ID',AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `document`
--
ALTER TABLE `document`
MODIFY `trackingnumber` int(8) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Tracking Number';
--
-- AUTO_INCREMENT for table `documentlog`
--
ALTER TABLE `documentlog`
MODIFY `logid` bigint(10) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Document Log ID';
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
MODIFY `id` int(10) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'User ID of user',AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
