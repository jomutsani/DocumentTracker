-- phpMyAdmin SQL Dump
-- version 4.2.3
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 23, 2014 at 08:05 PM
-- Server version: 5.5.38
-- PHP Version: 5.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `documenttracker`
--
CREATE DATABASE IF NOT EXISTS `documenttracker` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `documenttracker`;

-- --------------------------------------------------------

--
-- Table structure for table `auditlog`
--
-- Creation: Jun 23, 2014 at 02:18 PM
-- Last update: Jun 23, 2014 at 06:59 PM
-- Last check: Jun 23, 2014 at 07:04 PM
--

DROP TABLE IF EXISTS `auditlog`;
CREATE TABLE IF NOT EXISTS `auditlog` (
`id` bigint(20) unsigned NOT NULL COMMENT 'Audit ID',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Action Timestamp',
  `type` enum('Critical','Warning','Notice','Info') NOT NULL DEFAULT 'Info' COMMENT 'Audit Type',
  `user` int(4) unsigned zerofill NOT NULL COMMENT 'User',
  `page` varchar(100) NOT NULL COMMENT 'Page Name',
  `msg` varchar(512) NOT NULL COMMENT 'Message'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Audit Log' AUTO_INCREMENT=22 ;

--
-- RELATIONS FOR TABLE `auditlog`:
--   `user`
--       `user` -> `uid`
--

--
-- Truncate table before insert `auditlog`
--

TRUNCATE TABLE `auditlog`;
--
-- Dumping data for table `auditlog`
--

INSERT INTO `auditlog` (`id`, `ts`, `type`, `user`, `page`, `msg`) VALUES
(1, '2014-06-22 17:53:30', 'Info', 3597, 'login', 'System Admin(3597) logged in to the system.'),
(2, '2014-06-22 17:54:38', 'Info', 3597, 'login', 'System Admin(3597) logged in to the system.'),
(3, '2014-06-22 18:37:05', 'Info', 3597, 'login', 'System Admin(3597) logged in to the system.'),
(4, '2014-06-22 18:38:14', 'Info', 3597, 'login', 'System Admin(3597) logged in to the system.'),
(5, '2014-06-22 18:43:13', 'Info', 3597, 'login', 'System Admin(3597) logged in to the system.'),
(6, '2014-06-23 01:25:41', 'Info', 3597, 'login', 'System Admin(3597) logged in to the system.'),
(7, '2014-06-23 02:23:07', 'Info', 3597, 'login', 'System Admin(3597) logged in to the system.'),
(8, '2014-06-23 07:15:06', 'Info', 3597, 'login', 'System Admin(3597) logged in to the system.'),
(9, '2014-06-23 08:54:29', 'Info', 3597, 'login', 'System Admin(3597) logged in to the system.'),
(10, '2014-06-23 08:59:33', 'Info', 3597, 'login', 'System Admin(3597) logged in to the system.'),
(11, '2014-06-23 09:43:50', 'Info', 3597, 'adddoc', 'Document 1 has been added by System Admin(3597).'),
(12, '2014-06-23 14:26:57', 'Info', 3597, 'login', 'System Admin(3597) logged in to the system.'),
(13, '2014-06-23 16:30:04', 'Info', 3597, 'login', 'System Admin(3597) logged in to the system.'),
(14, '2014-06-23 18:09:35', 'Info', 3597, 'login', 'System Admin(3597) logged in to the system.'),
(15, '2014-06-23 18:32:31', 'Info', 3597, 'adddoc', 'Document 3 has been added by System Admin(3597).'),
(16, '2014-06-23 18:35:18', 'Info', 3597, 'login', 'System Admin(3597) logged in to the system.'),
(17, '2014-06-23 18:37:08', 'Info', 3597, 'login', 'System Admin(3597) logged in to the system.'),
(18, '2014-06-23 18:37:31', 'Info', 3597, 'login', 'System Admin(3597) logged in to the system.'),
(19, '2014-06-23 18:43:46', 'Info', 3597, 'login', 'System Admin(3597) logged in to the system.'),
(20, '2014-06-23 18:44:09', 'Info', 3597, 'adddoc', 'Document 4 has been added by System Admin(3597).'),
(21, '2014-06-23 18:59:58', 'Info', 3597, 'receive', 'Document 00000002 was received at PICTO (Medix).');

-- --------------------------------------------------------

--
-- Table structure for table `document`
--
-- Creation: Jun 23, 2014 at 02:18 PM
-- Last update: Jun 23, 2014 at 06:44 PM
--

DROP TABLE IF EXISTS `document`;
CREATE TABLE IF NOT EXISTS `document` (
`trackingnumber` int(8) unsigned zerofill NOT NULL COMMENT 'Tracking Number',
  `documentnumber` varchar(100) DEFAULT NULL COMMENT 'Document Number',
  `remarks` varchar(512) DEFAULT NULL COMMENT 'Remarks',
  `datecreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date Created',
  `author` int(4) unsigned zerofill NOT NULL COMMENT 'Author'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Document Information' AUTO_INCREMENT=5 ;

--
-- RELATIONS FOR TABLE `document`:
--   `author`
--       `user` -> `uid`
--

--
-- Truncate table before insert `document`
--

TRUNCATE TABLE `document`;
--
-- Dumping data for table `document`
--

INSERT INTO `document` (`trackingnumber`, `documentnumber`, `remarks`, `datecreated`, `author`) VALUES
(00000001, 'PR-00001', 'PR for Computer', '2014-06-23 09:43:50', 3597),
(00000002, '', 'Letter from PCSO', '2014-06-23 18:31:24', 3597),
(00000003, '', 'Letter from PCSO', '2014-06-23 18:32:31', 3597),
(00000004, '02920220', 'Meralco Bill', '2014-06-23 18:44:09', 3597);

-- --------------------------------------------------------

--
-- Table structure for table `documentlog`
--
-- Creation: Jun 23, 2014 at 02:18 PM
-- Last update: Jun 23, 2014 at 06:59 PM
--

DROP TABLE IF EXISTS `documentlog`;
CREATE TABLE IF NOT EXISTS `documentlog` (
`logid` bigint(10) unsigned zerofill NOT NULL COMMENT 'Document Log ID',
  `trackingnumber` int(8) unsigned zerofill NOT NULL COMMENT 'Tracking Number',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp',
  `remarks` varchar(512) NOT NULL COMMENT 'Remarks',
  `user` int(4) unsigned zerofill NOT NULL COMMENT 'User FK'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Document Log' AUTO_INCREMENT=5 ;

--
-- RELATIONS FOR TABLE `documentlog`:
--   `trackingnumber`
--       `document` -> `trackingnumber`
--   `user`
--       `user` -> `uid`
--

--
-- Truncate table before insert `documentlog`
--

TRUNCATE TABLE `documentlog`;
--
-- Dumping data for table `documentlog`
--

INSERT INTO `documentlog` (`logid`, `trackingnumber`, `ts`, `remarks`, `user`) VALUES
(0000000001, 00000001, '2014-06-23 09:43:50', 'Document received at PICTO (Medix). Document Remarks: PR for Computer', 3597),
(0000000002, 00000003, '2014-06-23 18:32:31', 'Document received at PICTO (Medix). Document Remarks: Letter from PCSO', 3597),
(0000000003, 00000004, '2014-06-23 18:44:09', 'Document received at PICTO (Medix). Document Remarks: Meralco Bill', 3597),
(0000000004, 00000002, '2014-06-23 18:59:58', '', 3597);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--
-- Creation: Jun 23, 2014 at 02:24 PM
-- Last update: Jun 23, 2014 at 02:24 PM
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `uid` int(4) unsigned zerofill NOT NULL COMMENT 'Employee ID Number',
  `password` varchar(48) NOT NULL COMMENT 'MD5 Hash of User''s Password',
  `fullname` varchar(255) NOT NULL COMMENT 'User''s full name',
  `department` varchar(255) NOT NULL COMMENT 'Department',
  `section` varchar(255) NOT NULL COMMENT 'Section',
  `regdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'User Registration Timestamp'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='User Information';

--
-- Truncate table before insert `user`
--

TRUNCATE TABLE `user`;
--
-- Dumping data for table `user`
--

INSERT INTO `user` (`uid`, `password`, `fullname`, `department`, `section`, `regdate`) VALUES
(3597, 'admin', 'System Admin', 'PICTO', 'Medix', '2014-06-21 23:48:59');

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
 ADD PRIMARY KEY (`uid`), ADD UNIQUE KEY `username` (`uid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `auditlog`
--
ALTER TABLE `auditlog`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Audit ID',AUTO_INCREMENT=22;
--
-- AUTO_INCREMENT for table `document`
--
ALTER TABLE `document`
MODIFY `trackingnumber` int(8) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Tracking Number',AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `documentlog`
--
ALTER TABLE `documentlog`
MODIFY `logid` bigint(10) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Document Log ID',AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
AUTO_INCREMENT=3598;