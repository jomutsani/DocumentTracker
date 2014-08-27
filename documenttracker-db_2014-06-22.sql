-- phpMyAdmin SQL Dump
-- version 4.2.3
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 27, 2014 at 07:59 AM
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
-- Creation: Jun 24, 2014 at 03:15 AM
-- Last update: Aug 27, 2014 at 06:52 AM
--

DROP TABLE IF EXISTS `auditlog`;
CREATE TABLE `auditlog` (
`id` bigint(20) unsigned NOT NULL COMMENT 'Audit ID',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Action Timestamp',
  `type` enum('Critical','Warning','Notice','Info') NOT NULL DEFAULT 'Info' COMMENT 'Audit Type',
  `user` int(4) unsigned zerofill NOT NULL COMMENT 'User',
  `page` varchar(100) NOT NULL COMMENT 'Page Name',
  `msg` varchar(512) NOT NULL COMMENT 'Message'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Audit Log' AUTO_INCREMENT=69 ;

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
(21, '2014-06-23 18:59:58', 'Info', 3597, 'receive', 'Document 00000002 was received at PICTO (Medix).'),
(22, '2014-06-24 03:16:46', 'Info', 3597, 'receive', 'Document 00000002 was received at PICTO (Medix).'),
(23, '2014-06-24 03:23:08', 'Info', 3597, 'login', 'System Admin(3597) logged in to the system.'),
(24, '2014-06-24 03:38:15', 'Info', 3597, 'login', 'System Admin(3597) logged in to the system.'),
(25, '2014-06-24 05:27:23', 'Info', 3597, 'login', '() logged in to the system.'),
(26, '2014-06-24 07:13:22', 'Info', 3597, 'receive', 'Document 00000002 was received at PICTO (Medix).'),
(27, '2014-06-24 07:35:44', 'Info', 3597, 'login', 'System Admin(3597) logged in to the system.'),
(28, '2014-06-24 07:46:43', 'Info', 3597, 'login', 'System Admin(3597) logged in to the system.'),
(29, '2014-06-24 08:40:12', 'Info', 3597, 'login', 'System Admin(3597) logged in to the system.'),
(30, '2014-06-25 04:51:32', 'Info', 3597, 'login', 'System Admin(3597) logged in to the system.'),
(31, '2014-06-25 04:55:09', 'Info', 3597, 'login', 'System Admin(3597) logged in to the system.'),
(32, '2014-06-25 05:06:36', 'Info', 3597, 'login', 'System Admin(3597) logged in to the system.'),
(33, '2014-06-25 05:30:52', 'Info', 3597, 'login', 'System Admin(3597) logged in to the system.'),
(34, '2014-06-25 07:18:24', 'Info', 3597, 'login', 'System Admin(3597) logged in to the system.'),
(35, '2014-06-25 08:02:21', 'Info', 3597, 'reguser', 'User Administrator(0000) has been registered.'),
(36, '2014-06-25 08:20:13', 'Info', 3597, 'login', 'System Admin(3597) logged in to the system.'),
(37, '2014-06-25 08:20:36', 'Info', 0000, 'login', 'Administrator(0) logged in to the system.'),
(38, '2014-06-25 08:21:09', 'Info', 0000, 'receive', 'Document 00000001 was received at IPHO (QMC).'),
(39, '2014-06-25 08:33:33', 'Info', 3597, 'login', 'System Admin(3597) logged in to the system.'),
(40, '2014-06-25 08:51:33', 'Info', 3597, 'users', 'System Admin(3597) logged in to the system.'),
(41, '2014-06-26 03:43:22', 'Info', 3597, 'users', 'System Admin(3597) logged in to the system.'),
(42, '2014-07-02 02:15:02', 'Info', 0001, 'users', 'System Admin(1) logged in to the system.'),
(43, '2014-07-31 03:02:15', 'Info', 0001, 'users', 'System Admin(1) logged in to the system.'),
(44, '2014-07-31 03:46:43', 'Info', 0001, 'users', 'System Admin(1) logged in to the system.'),
(45, '2014-07-31 03:47:35', 'Info', 0001, 'users', 'System Admin(1) logged in to the system.'),
(46, '2014-07-31 03:48:06', 'Info', 0001, 'users', 'System Admin(1) logged in to the system.'),
(47, '2014-07-31 03:49:58', 'Info', 3597, 'login', 'System Admin(3597) logged in to the system.'),
(48, '2014-07-31 03:50:27', 'Info', 3597, 'users', 'System Admin(3597) logged in to the system.'),
(49, '2014-07-31 03:54:32', 'Info', 3597, 'users', 'System Admin(3597) logged in to the system.'),
(50, '2014-07-31 03:54:56', 'Info', 3597, 'users', 'System Admin(3597) logged in to the system.'),
(51, '2014-07-31 03:55:05', 'Info', 3597, 'users', 'System Admin(3597) logged in to the system.'),
(52, '2014-07-31 03:56:06', 'Info', 3597, 'users', 'System Admin(3597) logged in to the system.'),
(53, '2014-07-31 07:20:23', 'Info', 3597, 'reguser', 'User System Adminz(3597) has been registered.'),
(54, '2014-07-31 07:23:27', 'Info', 3597, 'users', 'System Admin(3597) logged in to the system.'),
(55, '2014-07-31 07:24:02', 'Info', 3597, 'users', 'System Admin(3597) logged in to the system.'),
(56, '2014-07-31 07:24:20', 'Info', 3597, 'users', 'System Admin(3597) logged in to the system.'),
(57, '2014-07-31 07:25:19', 'Info', 3597, 'reguser', 'User System Adminz(3597) has been registered.'),
(58, '2014-07-31 07:26:12', 'Info', 3597, 'reguser', 'User System Adminz(3597) has been registered.'),
(59, '2014-07-31 07:36:00', 'Info', 3597, 'edituser', 'User System Administrator(3597) has been updated to Name=System Administrator, Dept=PICTOz, Section=Medixz, Perm=55, Password=74df9ed7b79cfcbca84002619b670802'),
(60, '2014-07-31 07:36:43', 'Info', 3597, 'edituser', 'User System Administrator(3597) has been updated to Name=System Administrator, Dept=PICTOz, Section=Medixz, Perm=55'),
(61, '2014-07-31 07:38:07', 'Info', 3597, 'edituser', 'User System Administrator(3597) has been updated to Name=System Administrator, Dept=PICTO, Section=Medix, Perm=63, Password=21232f297a57a5a743894a0e4a801fc3'),
(62, '2014-07-31 07:38:46', 'Info', 3597, 'users', 'System Admin(3597) logged in to the system.'),
(63, '2014-07-31 07:43:27', 'Info', 3597, 'users', 'System Admin(3597) logged in to the system.'),
(64, '2014-07-31 07:44:08', 'Info', 3597, 'users', 'System Admin(3597) logged in to the system.'),
(65, '2014-07-31 07:44:34', 'Info', 3597, 'users', 'System Admin(3597) logged in to the system.'),
(66, '2014-07-31 07:44:53', 'Info', 3597, 'users', 'System Admin(3597) logged in to the system.'),
(67, '2014-08-27 06:49:25', 'Info', 0001, 'users', 'System Admin(1) logged in to the system.'),
(68, '2014-08-27 06:52:36', 'Info', 3597, 'login', 'System Administrator(3597) logged in to the system.');

-- --------------------------------------------------------

--
-- Table structure for table `document`
--
-- Creation: Jun 24, 2014 at 03:15 AM
-- Last update: Jun 24, 2014 at 03:15 AM
--

DROP TABLE IF EXISTS `document`;
CREATE TABLE `document` (
`trackingnumber` int(8) unsigned zerofill NOT NULL COMMENT 'Tracking Number',
  `documentnumber` varchar(100) DEFAULT NULL COMMENT 'Document Number',
  `remarks` varchar(512) DEFAULT NULL COMMENT 'Remarks',
  `datecreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date Created',
  `author` int(4) unsigned zerofill NOT NULL COMMENT 'Author'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Document Information' AUTO_INCREMENT=5 ;

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
-- Creation: Jun 24, 2014 at 03:15 AM
-- Last update: Jun 25, 2014 at 08:21 AM
--

DROP TABLE IF EXISTS `documentlog`;
CREATE TABLE `documentlog` (
`logid` bigint(10) unsigned zerofill NOT NULL COMMENT 'Document Log ID',
  `trackingnumber` int(8) unsigned zerofill NOT NULL COMMENT 'Tracking Number',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp',
  `remarks` varchar(512) NOT NULL COMMENT 'Remarks',
  `user` int(4) unsigned zerofill NOT NULL COMMENT 'User FK'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Document Log' AUTO_INCREMENT=9 ;

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
(0000000004, 00000002, '2014-06-23 18:59:58', '', 3597),
(0000000005, 00000002, '2014-06-24 03:16:46', 'Return to Office', 3597),
(0000000006, 00000002, '2014-06-24 07:08:11', 'Resubmitted.', 3597),
(0000000007, 00000002, '2014-06-24 07:13:22', 'Returned to Office again.', 3597),
(0000000008, 00000001, '2014-06-25 08:21:09', 'Return to Office', 0000);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--
-- Creation: Jun 25, 2014 at 08:19 AM
-- Last update: Jul 31, 2014 at 07:38 AM
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `uid` int(4) unsigned zerofill NOT NULL COMMENT 'Employee ID Number',
  `password` varchar(48) NOT NULL COMMENT 'MD5 Hash of User''s Password',
  `fullname` varchar(255) NOT NULL COMMENT 'User''s full name',
  `department` varchar(255) NOT NULL COMMENT 'Department',
  `section` varchar(255) NOT NULL COMMENT 'Section',
  `regdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'User Registration Timestamp',
  `permission` smallint(5) unsigned NOT NULL DEFAULT '5' COMMENT 'Permission Set (''Add Document'',''Edit Document'',''Receive Document'',''Edit Document Track'',''User Management'',''Audit Log'')'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='User Information';

--
-- Truncate table before insert `user`
--

TRUNCATE TABLE `user`;
--
-- Dumping data for table `user`
--

INSERT INTO `user` (`uid`, `password`, `fullname`, `department`, `section`, `regdate`, `permission`) VALUES
(3597, '21232f297a57a5a743894a0e4a801fc3', 'System Administrator', 'PICTO', 'Medix', '2014-06-21 23:48:59', 63),
(0000, 'f6fdffe48c908deb0f4c3bd36c032e72', 'Administrator', 'IPHO', 'QMC', '2014-06-25 08:02:21', 5);

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
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Audit ID',AUTO_INCREMENT=69;
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
AUTO_INCREMENT=3598;