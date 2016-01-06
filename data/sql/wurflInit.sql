-- phpMyAdmin SQL Dump
-- version 2.11.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 01, 2008 at 07:31 PM
-- Server version: 5.0.45
-- PHP Version: 5.2.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- --------------------------------------------------------

--
-- Table structure for table `capabilities`
--

DROP TABLE IF EXISTS `capabilities`;
CREATE TABLE IF NOT EXISTS `capabilities` (
  `capabilityID` int(10) NOT NULL auto_increment,
  `capabilityGroupID` int(5) NOT NULL,
  `description` varchar(100) NOT NULL,
  `varType` enum('Boolean','Integer','String') NOT NULL default 'String',
  `helpText` text NOT NULL,
  PRIMARY KEY  (`capabilityID`),
  KEY `capabilityGroupID` (`capabilityGroupID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `capabilityGroups`
--

DROP TABLE IF EXISTS `capabilityGroups`;
CREATE TABLE IF NOT EXISTS `capabilityGroups` (
  `capabilityGroupID` int(5) NOT NULL auto_increment,
  `description` varchar(100) NOT NULL,
  `displayName` varchar(100) NOT NULL,
  PRIMARY KEY  (`capabilityGroupID`),
  KEY `description` (`description`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `deviceCache`
--

DROP TABLE IF EXISTS `deviceCache`;
CREATE TABLE IF NOT EXISTS `deviceCache` (
  `deviceID` int(12) NOT NULL,
  `createDate` datetime NOT NULL,
  `updateDate` datetime NOT NULL,
  `data` mediumtext NOT NULL,
  UNIQUE KEY `deviceID` (`deviceID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `deviceCapabilities`
--

DROP TABLE IF EXISTS `deviceCapabilities`;
CREATE TABLE IF NOT EXISTS `deviceCapabilities` (
  `deviceID` int(12) NOT NULL,
  `capabilityID` int(10) NOT NULL,
  `wurflValue` text NOT NULL,
  `customValue` text,
  PRIMARY KEY  (`deviceID`,`capabilityID`),
  KEY `capabilityID` (`capabilityID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `devices`
--

DROP TABLE IF EXISTS `devices`;
CREATE TABLE IF NOT EXISTS `devices` (
  `deviceID` int(12) NOT NULL auto_increment,
  `manufacturerID` int(10) NOT NULL default '0',
  `modelName` varchar(100) NOT NULL,
  `userAgent` varchar(255) NOT NULL,
  `wurflID` varchar(255) NOT NULL,
  `fallBackID` varchar(255) NOT NULL,
  `rootDevice` tinyint(1) NOT NULL default '0',
  `createDate` datetime NOT NULL,
  `updateDate` datetime NOT NULL,
  PRIMARY KEY  (`deviceID`),
  KEY `userAgent` (`userAgent`),
  KEY `manufacturerID` (`manufacturerID`),
  KEY `wurflID` (`wurflID`),
  KEY `fallBackID` (`fallBackID`),
  KEY `rootDevice` (`rootDevice`),
  FULLTEXT KEY `idxFTUserAgent` (`userAgent`),
  FULLTEXT KEY `idxFTWurflId` (`wurflID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `manufacturers`
--

DROP TABLE IF EXISTS `manufacturers`;
CREATE TABLE IF NOT EXISTS `manufacturers` (
  `manufacturerID` int(10) NOT NULL auto_increment,
  `description` varchar(100) NOT NULL,
  `active` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`manufacturerID`),
  KEY `description` (`description`),
  KEY `active` (`active`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;
