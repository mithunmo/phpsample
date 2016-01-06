-- phpMyAdmin SQL Dump
-- version 3.2.5
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Jun 03, 2010 at 02:13 PM
-- Server version: 5.1.42
-- PHP Version: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `mofilm_reports`
--

-- --------------------------------------------------------

--
-- Table structure for table `reportDeliveryTypes`
--

CREATE TABLE IF NOT EXISTS `reportDeliveryTypes` (
  `deliveryTypeID` int(11) NOT NULL AUTO_INCREMENT,
  `typeName` varchar(150) NOT NULL,
  `sendToInbox` tinyint(1) NOT NULL DEFAULT '0',
  `sendToUserEmail` tinyint(1) NOT NULL DEFAULT '0',
  `sendToGroup` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`deliveryTypeID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `reportDeliveryTypes`
--

INSERT INTO `reportDeliveryTypes` (`deliveryTypeID`, `typeName`, `sendToInbox`, `sendToUserEmail`, `sendToGroup`) VALUES
(1, 'My Inbox', 1, 0, 0),
(2, 'My Email', 0, 1, 0),
(3, 'My Inbox and Email', 1, 1, 0),
(4, 'My Inbox and Group', 1, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `reportParams`
--

CREATE TABLE IF NOT EXISTS `reportParams` (
  `reportID` int(12) NOT NULL,
  `paramName` varchar(255) NOT NULL,
  `paramValue` text NOT NULL,
  PRIMARY KEY (`reportID`,`paramName`),
  KEY `paramName` (`paramName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `reportParams`
--


-- --------------------------------------------------------

--
-- Table structure for table `reportQueue`
--

CREATE TABLE IF NOT EXISTS `reportQueue` (
  `scheduled` datetime NOT NULL,
  `reportID` int(11) NOT NULL DEFAULT '0',
  `createDate` datetime NOT NULL,
  KEY `scheduled` (`scheduled`),
  KEY `reportID` (`reportID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `reportQueue`
--


-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE IF NOT EXISTS `reports` (
  `reportID` int(11) NOT NULL AUTO_INCREMENT,
  `reportScheduleID` int(10) NOT NULL DEFAULT '1',
  `userID` int(10) NOT NULL DEFAULT '0',
  `isHidden` tinyint(1) NOT NULL DEFAULT '0',
  `reportStatusID` int(10) NOT NULL DEFAULT '1',
  `createDate` datetime NOT NULL,
  `requestDate` datetime NOT NULL,
  `updateDate` datetime NOT NULL,
  PRIMARY KEY (`reportID`),
  KEY `userID` (`userID`,`isHidden`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `reports`
--


-- --------------------------------------------------------

--
-- Table structure for table `reportSchedule`
--

CREATE TABLE IF NOT EXISTS `reportSchedule` (
  `reportScheduleID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(10) NOT NULL DEFAULT '0',
  `reportTypeID` int(10) NOT NULL DEFAULT '0',
  `reportTitle` varchar(255) NOT NULL,
  `reportScheduleTypeID` int(10) NOT NULL DEFAULT '0',
  `reportScheduleStatus` enum('Active','Inactive','Removed','Complete') NOT NULL DEFAULT 'Active',
  `deliveryTypeID` int(10) NOT NULL,
  `scheduledDate` datetime NOT NULL,
  `lastReportDate` datetime NOT NULL,
  `createDate` datetime NOT NULL,
  `updateDate` datetime NOT NULL,
  PRIMARY KEY (`reportScheduleID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `reportSchedule`
--


-- --------------------------------------------------------

--
-- Table structure for table `reportScheduleParams`
--

CREATE TABLE IF NOT EXISTS `reportScheduleParams` (
  `reportScheduleID` int(12) NOT NULL,
  `paramName` varchar(255) NOT NULL,
  `paramValue` text NOT NULL,
  PRIMARY KEY (`reportScheduleID`,`paramName`),
  KEY `paramName` (`paramName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `reportScheduleParams`
--


-- --------------------------------------------------------

--
-- Table structure for table `reportScheduleTypes`
--

CREATE TABLE IF NOT EXISTS `reportScheduleTypes` (
  `scheduleTypeID` tinyint(2) NOT NULL AUTO_INCREMENT,
  `description` varchar(50) NOT NULL,
  PRIMARY KEY (`scheduleTypeID`),
  KEY `description` (`description`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `reportScheduleTypes`
--

INSERT INTO `reportScheduleTypes` (`scheduleTypeID`, `description`) VALUES
(1, 'Once only'),
(2, 'Daily'),
(3, 'Weekly'),
(4, 'Fortnightly'),
(5, 'Monthly'),
(6, 'Quarterly'),
(7, 'Yearly');

-- --------------------------------------------------------

--
-- Table structure for table `reportStatus`
--

CREATE TABLE IF NOT EXISTS `reportStatus` (
  `reportStatusID` tinyint(2) NOT NULL AUTO_INCREMENT,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`reportStatusID`),
  KEY `description` (`description`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `reportStatus`
--

INSERT INTO `reportStatus` (`reportStatusID`, `description`) VALUES
(1, 'Queued'),
(2, 'Processing'),
(3, 'Refreshing'),
(4, 'Scheduled'),
(5, 'Failed no results'),
(6, 'Completed'),
(7, 'Failed unknown reason'),
(8, 'Removed from schedule');

-- --------------------------------------------------------

--
-- Table structure for table `reportTypes`
--

CREATE TABLE IF NOT EXISTS `reportTypes` (
  `reportTypeID` int(11) NOT NULL AUTO_INCREMENT,
  `typeName` varchar(120) NOT NULL,
  `description` varchar(255) NOT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT '0',
  `className` varchar(100) NOT NULL,
  PRIMARY KEY (`reportTypeID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `reportTypes`
--

