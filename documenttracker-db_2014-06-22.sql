-- phpMyAdmin SQL Dump
-- version 4.2.3
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 04, 2014 at 05:02 AM
-- Server version: 5.5.38
-- PHP Version: 5.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `documenttracker`
--

-- --------------------------------------------------------

--
-- Table structure for table `auditlog`
--
-- Creation: Jun 24, 2014 at 03:15 AM
-- Last update: Sep 04, 2014 at 02:31 AM
--

CREATE TABLE IF NOT EXISTS `auditlog` (
`id` bigint(20) unsigned NOT NULL COMMENT 'Audit ID',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Action Timestamp',
  `type` enum('Critical','Warning','Notice','Info') NOT NULL DEFAULT 'Info' COMMENT 'Audit Type',
  `user` int(4) unsigned zerofill NOT NULL COMMENT 'User',
  `page` varchar(100) NOT NULL COMMENT 'Page Name',
  `msg` varchar(512) NOT NULL COMMENT 'Message'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Audit Log' AUTO_INCREMENT=82 ;

-- --------------------------------------------------------

--
-- Table structure for table `document`
--
-- Creation: Jun 24, 2014 at 03:15 AM
-- Last update: Jun 24, 2014 at 03:15 AM
--

CREATE TABLE IF NOT EXISTS `document` (
`trackingnumber` int(8) unsigned zerofill NOT NULL COMMENT 'Tracking Number',
  `documentnumber` varchar(100) DEFAULT NULL COMMENT 'Document Number',
  `remarks` varchar(512) DEFAULT NULL COMMENT 'Remarks',
  `datecreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date Created',
  `author` int(4) unsigned zerofill NOT NULL COMMENT 'Author'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Document Information' AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `documentlog`
--
-- Creation: Jun 24, 2014 at 03:15 AM
-- Last update: Jun 25, 2014 at 08:21 AM
--

CREATE TABLE IF NOT EXISTS `documentlog` (
`logid` bigint(10) unsigned zerofill NOT NULL COMMENT 'Document Log ID',
  `trackingnumber` int(8) unsigned zerofill NOT NULL COMMENT 'Tracking Number',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp',
  `remarks` varchar(512) NOT NULL COMMENT 'Remarks',
  `user` int(4) unsigned zerofill NOT NULL COMMENT 'User FK'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Document Log' AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--
-- Creation: Sep 04, 2014 at 02:23 AM
-- Last update: Sep 04, 2014 at 02:23 AM
--

CREATE TABLE IF NOT EXISTS `user` (
  `uid` int(4) unsigned zerofill NOT NULL COMMENT 'Employee ID Number',
  `password` varchar(48) NOT NULL COMMENT 'MD5 Hash of User''s Password',
  `fullname` varchar(255) NOT NULL COMMENT 'User''s full name',
  `department` varchar(255) NOT NULL COMMENT 'Department',
  `division` varchar(255) NOT NULL COMMENT 'Division',
  `section` varchar(255) NOT NULL COMMENT 'Section',
  `regdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'User Registration Timestamp',
  `permission` smallint(5) unsigned NOT NULL DEFAULT '5' COMMENT 'Permission Set (''Add Document'',''Edit Document'',''Receive Document'',''Edit Document Track'',''User Management'',''Audit Log'')'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='User Information';

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
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Audit ID',AUTO_INCREMENT=82;
--
-- AUTO_INCREMENT for table `document`
--
ALTER TABLE `document`
MODIFY `trackingnumber` int(8) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Tracking Number',AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `documentlog`
--
ALTER TABLE `documentlog`
MODIFY `logid` bigint(10) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Document Log ID',AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
AUTO_INCREMENT=3598;COMMIT;
