-- phpMyAdmin SQL Dump
-- version 4.2.3
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 23, 2014 at 10:48 AM
-- Server version: 5.5.38
-- PHP Version: 5.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `documenttracker`
--

-- --------------------------------------------------------

--
-- Table structure for table `auditlog`
--
-- Creation: Jun 23, 2014 at 01:25 AM
-- Last update: Jun 23, 2014 at 09:43 AM
--

CREATE TABLE `auditlog` (
`id` bigint(20) unsigned NOT NULL COMMENT 'Audit ID',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Action Timestamp',
  `type` enum('Critical','Warning','Notice','Info') NOT NULL DEFAULT 'Info' COMMENT 'Audit Type',
  `user` int(10) unsigned zerofill NOT NULL COMMENT 'User',
  `page` varchar(100) NOT NULL COMMENT 'Page Name',
  `msg` varchar(512) NOT NULL COMMENT 'Message'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Audit Log' AUTO_INCREMENT=12 ;

--
-- Truncate table before insert `auditlog`
--

TRUNCATE TABLE `auditlog`;
--
-- Dumping data for table `auditlog`
--

INSERT INTO `auditlog` (`id`, `ts`, `type`, `user`, `page`, `msg`) VALUES
(1, '2014-06-22 17:53:30', 'Info', 0000000001, 'login', 'System Admin(3597) logged in to the system.'),
(2, '2014-06-22 17:54:38', 'Info', 0000000001, 'login', 'System Admin(3597) logged in to the system.'),
(3, '2014-06-22 18:37:05', 'Info', 0000000001, 'login', 'System Admin(3597) logged in to the system.'),
(4, '2014-06-22 18:38:14', 'Info', 0000000001, 'login', 'System Admin(3597) logged in to the system.'),
(5, '2014-06-22 18:43:13', 'Info', 0000000001, 'login', 'System Admin(3597) logged in to the system.'),
(6, '2014-06-23 01:25:41', 'Info', 0000000001, 'login', 'System Admin(3597) logged in to the system.'),
(7, '2014-06-23 02:23:07', 'Info', 0000000001, 'login', 'System Admin(3597) logged in to the system.'),
(8, '2014-06-23 07:15:06', 'Info', 0000000001, 'login', 'System Admin(3597) logged in to the system.'),
(9, '2014-06-23 08:54:29', 'Info', 0000000001, 'login', 'System Admin(3597) logged in to the system.'),
(10, '2014-06-23 08:59:33', 'Info', 0000000001, 'login', 'System Admin(3597) logged in to the system.'),
(11, '2014-06-23 09:43:50', 'Info', 0000000001, 'adddoc', 'Document 1 has been added by System Admin(3597).');

-- --------------------------------------------------------

--
-- Table structure for table `document`
--
-- Creation: Jun 23, 2014 at 09:43 AM
-- Last update: Jun 23, 2014 at 09:43 AM
--

CREATE TABLE `document` (
`trackingnumber` int(8) unsigned zerofill NOT NULL COMMENT 'Tracking Number',
  `documentnumber` varchar(100) DEFAULT NULL COMMENT 'Document Number',
  `remarks` varchar(512) DEFAULT NULL COMMENT 'Remarks',
  `datecreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date Created',
  `author` int(10) unsigned zerofill NOT NULL COMMENT 'Author'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Document Information' AUTO_INCREMENT=2 ;

--
-- Truncate table before insert `document`
--

TRUNCATE TABLE `document`;
--
-- Dumping data for table `document`
--

INSERT INTO `document` (`trackingnumber`, `documentnumber`, `remarks`, `datecreated`, `author`) VALUES
(00000001, 'PR-00001', 'PR for Computer', '2014-06-23 09:43:50', 0000000001);

-- --------------------------------------------------------

--
-- Table structure for table `documentlog`
--
-- Creation: Jun 23, 2014 at 01:25 AM
-- Last update: Jun 23, 2014 at 09:43 AM
--

CREATE TABLE `documentlog` (
`logid` bigint(10) unsigned zerofill NOT NULL COMMENT 'Document Log ID',
  `trackingnumber` int(8) unsigned zerofill NOT NULL COMMENT 'Tracking Number',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp',
  `remarks` varchar(512) NOT NULL COMMENT 'Remarks',
  `user` int(10) unsigned zerofill NOT NULL COMMENT 'User FK'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Document Log' AUTO_INCREMENT=2 ;

--
-- Truncate table before insert `documentlog`
--

TRUNCATE TABLE `documentlog`;
--
-- Dumping data for table `documentlog`
--

INSERT INTO `documentlog` (`logid`, `trackingnumber`, `ts`, `remarks`, `user`) VALUES
(0000000001, 00000001, '2014-06-23 09:43:50', 'Document received at PICTO (Medix). Document Remarks: PR for Computer', 0000000001);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--
-- Creation: Jun 23, 2014 at 01:25 AM
-- Last update: Jun 23, 2014 at 01:25 AM
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
-- Truncate table before insert `user`
--

TRUNCATE TABLE `user`;
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
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Audit ID',AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `document`
--
ALTER TABLE `document`
MODIFY `trackingnumber` int(8) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Tracking Number',AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `documentlog`
--
ALTER TABLE `documentlog`
MODIFY `logid` bigint(10) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Document Log ID',AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
MODIFY `id` int(10) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'User ID of user',AUTO_INCREMENT=2;
