-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.5.41 - MySQL Community Server (GPL)
-- Server OS:                    Win32
-- HeidiSQL Version:             9.1.0.4867
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping database structure for documenttracker
CREATE DATABASE IF NOT EXISTS `documenttracker` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `documenttracker`;


-- Dumping structure for table documenttracker.auditlog
CREATE TABLE IF NOT EXISTS `auditlog` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Audit ID',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Action Timestamp',
  `type` enum('Critical','Warning','Notice','Info') NOT NULL DEFAULT 'Info' COMMENT 'Audit Type',
  `user` int(4) unsigned zerofill NOT NULL COMMENT 'User',
  `page` varchar(100) NOT NULL COMMENT 'Page Name',
  `msg` varchar(512) NOT NULL COMMENT 'Message',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Audit Log';

-- Data exporting was unselected.


-- Dumping structure for table documenttracker.document
CREATE TABLE IF NOT EXISTS `document` (
  `id` int(8) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Tracking Number',
  `barcodenumber` varchar(255) NOT NULL COMMENT 'Barcode Number',
  `documentcategory` int(8) unsigned NOT NULL DEFAULT '8' COMMENT 'FK for documentcateogry',
  `documentnumber` varchar(100) DEFAULT NULL COMMENT 'Document Number',
  `remarks` varchar(512) NOT NULL DEFAULT '' COMMENT 'Remarks',
  `datecreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date Created',
  `author` int(4) unsigned zerofill NOT NULL COMMENT 'Author',
  `end` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Tags document as released.',
  `active` bit(1) NOT NULL DEFAULT b'1' COMMENT 'Active Document Bit',
  PRIMARY KEY (`id`),
  UNIQUE KEY `barcodenumber` (`barcodenumber`),
  FULLTEXT KEY `advsearch` (`barcodenumber`,`remarks`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Document Information';

-- Data exporting was unselected.


-- Dumping structure for table documenttracker.documentcategory
CREATE TABLE IF NOT EXISTS `documentcategory` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique ID',
  `description` varchar(512) NOT NULL COMMENT 'Category Name',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Stores the category of the document.';

-- Data exporting was unselected.


-- Dumping structure for table documenttracker.documentlog
CREATE TABLE IF NOT EXISTS `documentlog` (
  `logid` bigint(10) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Document Log ID',
  `docid` int(8) unsigned zerofill NOT NULL COMMENT 'Tracking Number',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp',
  `remarks` varchar(512) NOT NULL COMMENT 'Remarks',
  `user` int(4) unsigned zerofill NOT NULL COMMENT 'User FK',
  `visible` bit(1) NOT NULL DEFAULT b'1',
  PRIMARY KEY (`logid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Document Log';

-- Data exporting was unselected.


-- Dumping structure for table documenttracker.user
CREATE TABLE IF NOT EXISTS `user` (
  `uid` int(4) unsigned zerofill NOT NULL COMMENT 'Employee ID Number',
  `password` varchar(48) NOT NULL COMMENT 'MD5 Hash of User''s Password',
  `fullname` varchar(255) NOT NULL COMMENT 'User''s full name',
  `department` varchar(255) NOT NULL COMMENT 'Department',
  `division` varchar(255) NOT NULL COMMENT 'Division',
  `section` varchar(255) NOT NULL COMMENT 'Section',
  `regdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'User Registration Timestamp',
  `permission` int(10) unsigned NOT NULL DEFAULT '5' COMMENT 'Permission Set (''Add Document'',''Edit Document'',''Receive Document'',''Edit Document Track'',''User Management'',''Audit Log'')',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `username` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='User Information';

-- Data exporting was unselected.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
