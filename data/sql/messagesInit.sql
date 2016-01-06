-- phpMyAdmin SQL Dump
-- version 3.2.5
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Mar 13, 2010 at 02:06 AM
-- Server version: 5.1.42
-- PHP Version: 5.3.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `mofilm_messages`
--

-- --------------------------------------------------------

--
-- Table structure for table `applicationMessageGroups`
--

CREATE TABLE IF NOT EXISTS `applicationMessageGroups` (
  `messageGroupID` int(10) NOT NULL AUTO_INCREMENT,
  `messageType` enum('SMS','Email') DEFAULT 'SMS',
  `description` varchar(50) NOT NULL,
  PRIMARY KEY (`messageGroupID`),
  KEY `description` (`description`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

--
-- Dumping data for table `applicationMessageGroups`
--


-- --------------------------------------------------------

--
-- Table structure for table `applicationMessages`
--

CREATE TABLE IF NOT EXISTS `applicationMessages` (
  `messageID` int(11) NOT NULL AUTO_INCREMENT,
  `applicationID` int(10) NOT NULL DEFAULT '0',
  `outboundTypeID` int(10) NOT NULL DEFAULT '0',
  `messageGroupID` int(10) NOT NULL DEFAULT '0',
  `currencyID` int(10) NOT NULL DEFAULT '0',
  `charge` decimal(8,3) NOT NULL DEFAULT '0.000',
  `language` varchar(10) NOT NULL DEFAULT 'en',
  `messageHeader` varchar(255) DEFAULT NULL,
  `messageBody` text NOT NULL,
  `isHtml` tinyint(1) NOT NULL DEFAULT '0',
  `delay` int(10) NOT NULL DEFAULT '0',
  `messageOrder` int(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`messageID`),
  UNIQUE KEY `uniqueMessage` (`applicationID`,`outboundTypeID`,`messageGroupID`,`messageOrder`,`language`),
  KEY `messageGroupID` (`messageGroupID`),
  KEY `outboundTypeID` (`outboundTypeID`),
  KEY `messageOrder` (`messageOrder`),
  KEY `language` (`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

--
-- Dumping data for table `applicationMessages`
--


-- --------------------------------------------------------

--
-- Table structure for table `gatewayAccountParams`
--

CREATE TABLE IF NOT EXISTS `gatewayAccountParams` (
  `gatewayAccountID` tinyint(3) NOT NULL,
  `paramName` varchar(255) NOT NULL,
  `paramValue` text NOT NULL,
  PRIMARY KEY (`gatewayAccountID`,`paramName`),
  KEY `paramName` (`paramName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `gatewayAccountParams`
--


-- --------------------------------------------------------

--
-- Table structure for table `gatewayAccounts`
--

CREATE TABLE IF NOT EXISTS `gatewayAccounts` (
  `gatewayAccountID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `gatewayID` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `description` varchar(255) NOT NULL,
  `prs` varchar(20) DEFAULT NULL COMMENT 'shortcode',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `networkID` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `tariff` decimal(8,3) NOT NULL DEFAULT '0.000',
  `countryID` tinyint(4) NOT NULL DEFAULT '0',
  `currencyID` tinyint(4) NOT NULL DEFAULT '1',
  `requireAcknowledgement` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`gatewayAccountID`),
  KEY `idxGWroute` (`active`,`prs`,`tariff`,`currencyID`,`networkID`),
  KEY `countryID` (`countryID`),
  KEY `gatewayID` (`gatewayID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 PACK_KEYS=0 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=4 ;

--
-- Dumping data for table `gatewayAccounts`
--

INSERT INTO `gatewayAccounts` (`gatewayAccountID`, `gatewayID`, `description`, `prs`, `active`, `networkID`, `tariff`, `countryID`, `currencyID`, `requireAcknowledgement`) VALUES
(1, 1, 'Application Loop Back', NULL, 0, 0, '0.000', 0, 0, 0),
(2, 2, 'Application Simulator', NULL, 0, 0, '0.000', 0, 0, 0),
(3, 3, 'Application Email', NULL, 0, 0, '0.000', 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `gatewayNetworkMappings`
--

CREATE TABLE IF NOT EXISTS `gatewayNetworkMappings` (
  `gatewayID` int(10) NOT NULL,
  `networkID` int(10) NOT NULL,
  `gatewayRef` varchar(50) NOT NULL,
  PRIMARY KEY (`gatewayID`,`gatewayRef`),
  KEY `gatewayRef` (`gatewayRef`),
  KEY `networkID` (`networkID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `gatewayNetworkMappings`
--


-- --------------------------------------------------------

--
-- Table structure for table `gatewayParams`
--

CREATE TABLE IF NOT EXISTS `gatewayParams` (
  `gatewayID` tinyint(3) NOT NULL,
  `paramName` varchar(255) NOT NULL,
  `paramValue` text NOT NULL,
  PRIMARY KEY (`gatewayID`,`paramName`),
  KEY `paramName` (`paramName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `gatewayParams`
--


-- --------------------------------------------------------

--
-- Table structure for table `gateways`
--

CREATE TABLE IF NOT EXISTS `gateways` (
  `gatewayID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `active` tinyint(4) NOT NULL DEFAULT '0',
  `description` varchar(30) NOT NULL,
  `className` varchar(30) NOT NULL,
  `transportClass` varchar(50) NOT NULL,
  `createDate` datetime NOT NULL,
  `updateDate` datetime NOT NULL,
  PRIMARY KEY (`gatewayID`),
  KEY `active` (`active`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `gateways`
--

INSERT INTO `gateways` (`gatewayID`, `active`, `description`, `className`, `transportClass`, `createDate`, `updateDate`) VALUES
(1, 1, 'Application Loop Back', 'commsGatewayAdaptorAppLoopBack', 'transportAgentApplication', '2010-03-12 21:02:50', '2010-03-12 21:02:50'),
(2, 1, 'Application Simulator', 'commsGatewayAdaptorSimulator', 'transportAgentApplication', '2010-03-12 21:02:50', '2010-03-12 21:02:50'),
(3, 1, 'Application Email', 'commsGatewayAdaptorEmail', 'transportAgentEmail', '2010-03-12 21:02:50', '2010-03-12 21:02:50');

-- --------------------------------------------------------

--
-- Table structure for table `inboundMessages`
--

CREATE TABLE IF NOT EXISTS `inboundMessages` (
  `messageID` int(12) NOT NULL AUTO_INCREMENT,
  `inboundTypeID` tinyint(3) NOT NULL,
  `gatewayID` tinyint(3) NOT NULL,
  `gatewayRef` varchar(100) NOT NULL DEFAULT '',
  `sender` varchar(255) NOT NULL,
  `customerID` int(10) NOT NULL DEFAULT '0',
  `statusID` tinyint(3) NOT NULL DEFAULT '1',
  `createDate` datetime NOT NULL,
  `updateDate` datetime NOT NULL,
  `sentDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `receivedDate` datetime NOT NULL,
  PRIMARY KEY (`messageID`),
  UNIQUE KEY `gatewayRef` (`gatewayID`,`gatewayRef`),
  KEY `statusID` (`statusID`),
  KEY `customerID` (`customerID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

--
-- Dumping data for table `inboundMessages`
--


-- --------------------------------------------------------

--
-- Table structure for table `inboundMessagesParams`
--

CREATE TABLE IF NOT EXISTS `inboundMessagesParams` (
  `messageID` int(12) NOT NULL DEFAULT '0',
  `paramName` varchar(255) NOT NULL,
  `paramValue` text NOT NULL,
  PRIMARY KEY (`messageID`,`paramName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `inboundMessagesParams`
--


-- --------------------------------------------------------

--
-- Table structure for table `inboundMessagesQueue`
--

CREATE TABLE IF NOT EXISTS `inboundMessagesQueue` (
  `received` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `messageID` int(12) NOT NULL DEFAULT '0',
  UNIQUE KEY `uniqueMsg` (`messageID`),
  KEY `dateReceived` (`received`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

--
-- Dumping data for table `inboundMessagesQueue`
--


-- --------------------------------------------------------

--
-- Table structure for table `inboundMessagesTransactions`
--

CREATE TABLE IF NOT EXISTS `inboundMessagesTransactions` (
  `messageID` int(12) NOT NULL,
  `transactionID` int(12) NOT NULL,
  UNIQUE KEY `messageID` (`messageID`,`transactionID`),
  KEY `transactionID` (`transactionID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `inboundMessagesTransactions`
--


-- --------------------------------------------------------

--
-- Table structure for table `inboundStatus`
--

CREATE TABLE IF NOT EXISTS `inboundStatus` (
  `statusID` tinyint(3) NOT NULL DEFAULT '0',
  `description` varchar(60) NOT NULL DEFAULT '',
  PRIMARY KEY (`statusID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `inboundStatus`
--

INSERT INTO `inboundStatus` (`statusID`, `description`) VALUES
(1, 'Waiting to be Processed'),
(2, 'Processing'),
(3, 'Launching App'),
(4, 'Succesfully Processed'),
(10, 'Dropped (another message already in process)'),
(11, 'Unallocated'),
(12, 'Dropped (Valid Stop Command)'),
(96, 'Error returned by application'),
(97, 'Target application not executable'),
(98, 'Target application missing'),
(99, 'Failed (Unknown Reason)');

-- --------------------------------------------------------

--
-- Table structure for table `inboundTypes`
--

CREATE TABLE IF NOT EXISTS `inboundTypes` (
  `inboundTypeID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(50) NOT NULL,
  `className` varchar(100) NOT NULL,
  PRIMARY KEY (`inboundTypeID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `inboundTypes`
--

INSERT INTO `inboundTypes` (`inboundTypeID`, `description`, `className`) VALUES
(1, 'SMS', 'commsInboundMessageSms'),
(2, 'IVR', 'commsInboundMessageIvr'),
(3, 'WAP', ''),
(4, 'WEB', '');

-- --------------------------------------------------------

--
-- Table structure for table `networkMappings`
--

CREATE TABLE IF NOT EXISTS `networkMappings` (
  `mcc` int(3) NOT NULL,
  `mnc` int(3) NOT NULL,
  `mvno` int(3) NOT NULL DEFAULT '0',
  `countryIsoCode` varchar(10) NOT NULL,
  `networkID` int(10) NOT NULL,
  PRIMARY KEY (`mcc`,`mnc`,`mvno`),
  KEY `networkID` (`networkID`),
  KEY `countryIsoCode` (`countryIsoCode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `networkMappings`
--

INSERT INTO `networkMappings` (`mcc`, `mnc`, `mvno`, `countryIsoCode`, `networkID`) VALUES
(234, 15, 0, 'GB', 1),
(234, 10, 0, 'GB', 2),
(234, 33, 0, 'GB', 3),
(234, 30, 0, 'GB', 4),
(234, 30, 4, 'GB', 5),
(234, 20, 0, 'GB', 6),
(234, 10, 2, 'GB', 7),
(234, 58, 0, 'GB', 11),
(655, 10, 0, 'ZA', 8),
(655, 1, 0, 'ZA', 9),
(655, 7, 0, 'ZA', 10),
(234, 2, 0, 'GB', 2),
(272, 1, 0, 'IE', 13),
(272, 2, 0, 'IE', 14),
(272, 3, 0, 'IE', 12),
(272, 53, 0, 'IE', 15),
(234, 50, 0, 'GB', 16),
(234, 55, 0, 'GB', 17);

-- --------------------------------------------------------

--
-- Table structure for table `networkParams`
--

CREATE TABLE IF NOT EXISTS `networkParams` (
  `networkID` tinyint(4) NOT NULL DEFAULT '0',
  `paramName` varchar(255) NOT NULL,
  `paramValue` text NOT NULL,
  PRIMARY KEY (`networkID`,`paramName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `networkParams`
--

INSERT INTO `networkParams` (`networkID`, `paramName`, `paramValue`) VALUES
(6, 'sms.wappush.sendAsText', 'true'),
(15, 'sms.wappush.sendAsText', 'true');

-- --------------------------------------------------------

--
-- Table structure for table `networks`
--

CREATE TABLE IF NOT EXISTS `networks` (
  `networkID` int(10) NOT NULL AUTO_INCREMENT,
  `description` varchar(50) NOT NULL,
  `uri` varchar(255) NOT NULL,
  `createDate` datetime NOT NULL,
  `updateDate` datetime NOT NULL,
  PRIMARY KEY (`networkID`),
  KEY `description` (`description`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

--
-- Dumping data for table `networks`
--

INSERT INTO `networks` (`networkID`, `description`, `uri`, `createDate`, `updateDate`) VALUES
(1, 'Vodafone', 'www.vodafone.co.uk', '2010-03-10 12:21:35', '2010-03-10 12:21:35'),
(2, 'O2', 'www.o2.co.uk', '2010-03-10 12:21:35', '2010-03-10 12:21:35'),
(3, 'Orange', 'www.orange.co.uk', '2010-03-10 12:21:35', '2010-03-10 12:21:35'),
(4, 'T-Mobile', 'www.t-mobile.co.uk', '2010-03-10 12:21:35', '2010-03-10 12:21:35'),
(5, 'Virgin', 'www.virginmobile.co.uk', '2010-03-10 12:21:35', '2010-03-10 12:21:35'),
(6, 'Three', 'www.three.co.uk', '2010-03-10 12:21:35', '2010-03-10 12:21:35'),
(7, 'Tesco Mobile', 'www.tescomobile.com', '2010-03-10 12:21:35', '2010-03-10 12:21:35'),
(8, 'MTN', 'www.mtn.co.za', '2010-03-10 12:21:35', '2010-03-10 12:21:35'),
(9, 'Vodacom', 'www.vodacom.co.za', '2010-03-10 12:21:35', '2010-03-10 12:21:35'),
(10, 'Cell C', 'www.cellc.co.za', '2010-03-10 12:21:35', '2010-03-10 12:21:35'),
(11, 'Manx Telecom', 'www.manx-telecom.com', '2010-03-10 12:21:35', '2010-03-10 12:21:35'),
(12, 'Meteor', 'www.meteor.ie', '2010-03-10 12:21:35', '2010-03-10 12:21:35'),
(13, 'Vodafone IE', 'www.vodafone.ie', '2010-03-10 12:21:35', '2010-03-10 12:21:35'),
(14, 'O2 IE', 'www.o2.ie', '2010-03-10 12:21:35', '2010-03-10 12:21:35'),
(15, 'Three IE', 'www.three.ie', '2010-03-10 12:21:35', '2010-03-10 12:21:35'),
(16, 'Jersey Telecom', 'www.jerseytelecom.com', '2010-03-10 12:21:35', '2010-03-10 12:21:35'),
(17, 'Wave Telecom', 'www.wavetelecom.com', '2010-03-10 12:21:35', '2010-03-10 12:21:35');

-- --------------------------------------------------------

--
-- Table structure for table `outboundMessages`
--

CREATE TABLE IF NOT EXISTS `outboundMessages` (
  `messageID` int(12) NOT NULL AUTO_INCREMENT,
  `outboundTypeID` tinyint(3) NOT NULL,
  `gatewayID` tinyint(3) NOT NULL DEFAULT '0',
  `gatewayAccountID` tinyint(3) NOT NULL DEFAULT '0',
  `recipient` varchar(255) NOT NULL,
  `originator` varchar(255) DEFAULT NULL,
  `charge` decimal(8,3) DEFAULT NULL,
  `currencyID` tinyint(4) DEFAULT NULL,
  `createDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `scheduledDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sentDate` datetime DEFAULT NULL,
  `acknowledgedDate` datetime DEFAULT NULL,
  `statusID` tinyint(3) NOT NULL DEFAULT '1',
  PRIMARY KEY (`messageID`),
  KEY `idxIntended` (`charge`,`scheduledDate`),
  KEY `statusID` (`statusID`),
  KEY `outboundTypeID` (`outboundTypeID`),
  KEY `recipient` (`recipient`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

--
-- Dumping data for table `outboundMessages`
--


-- --------------------------------------------------------

--
-- Table structure for table `outboundMessagesEmbargo`
--

CREATE TABLE IF NOT EXISTS `outboundMessagesEmbargo` (
  `messageID` int(12) NOT NULL DEFAULT '0',
  `recipient` varchar(255) NOT NULL,
  `state` enum('Queued','InProcess') NOT NULL DEFAULT 'Queued',
  `expires` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`recipient`),
  KEY `idxState` (`state`,`expires`),
  KEY `messageID` (`messageID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='Back of Queue';

--
-- Dumping data for table `outboundMessagesEmbargo`
--


-- --------------------------------------------------------

--
-- Table structure for table `outboundMessagesParams`
--

CREATE TABLE IF NOT EXISTS `outboundMessagesParams` (
  `messageID` int(12) NOT NULL,
  `paramName` varchar(255) NOT NULL,
  `paramValue` text NOT NULL,
  PRIMARY KEY (`messageID`,`paramName`),
  KEY `paramName` (`paramName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `outboundMessagesParams`
--


-- --------------------------------------------------------

--
-- Table structure for table `outboundMessagesQueue`
--

CREATE TABLE IF NOT EXISTS `outboundMessagesQueue` (
  `scheduled` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `messageID` int(12) NOT NULL DEFAULT '0',
  `transactionID` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `messageID` (`messageID`),
  KEY `scheduled` (`scheduled`),
  KEY `idxTransaction` (`transactionID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED COMMENT='Back of Queue';

--
-- Dumping data for table `outboundMessagesQueue`
--


-- --------------------------------------------------------

--
-- Table structure for table `outboundMessagesTracking`
--

CREATE TABLE IF NOT EXISTS `outboundMessagesTracking` (
  `messageID` int(12) NOT NULL DEFAULT '0',
  `gatewayID` tinyint(3) NOT NULL DEFAULT '0',
  `reference` varchar(100) NOT NULL,
  `parts` tinyint(2) NOT NULL DEFAULT '1',
  `remaining` tinyint(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`gatewayID`,`reference`),
  KEY `id` (`messageID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='Tracking of messages using the gateway reference';

--
-- Dumping data for table `outboundMessagesTracking`
--


-- --------------------------------------------------------

--
-- Table structure for table `outboundMessagesTransactions`
--

CREATE TABLE IF NOT EXISTS `outboundMessagesTransactions` (
  `messageID` int(12) NOT NULL,
  `transactionID` int(12) NOT NULL,
  UNIQUE KEY `messageID` (`messageID`,`transactionID`),
  KEY `transactionID` (`transactionID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `outboundMessagesTransactions`
--


-- --------------------------------------------------------

--
-- Table structure for table `outboundStatus`
--

CREATE TABLE IF NOT EXISTS `outboundStatus` (
  `statusID` tinyint(3) NOT NULL DEFAULT '0',
  `description` varchar(60) NOT NULL DEFAULT '',
  PRIMARY KEY (`statusID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `outboundStatus`
--

INSERT INTO `outboundStatus` (`statusID`, `description`) VALUES
(1, 'Waiting to be Processed'),
(2, 'Sending'),
(3, 'Sent'),
(4, 'Acknowledged'),
(5, 'Completed (Ack not required)'),
(92, 'Expired'),
(93, 'Acknowledgement Failure'),
(95, 'Dropped - Previous Message Failed'),
(99, 'Failed (Unknown Reason)');

-- --------------------------------------------------------

--
-- Table structure for table `outboundTypes`
--

CREATE TABLE IF NOT EXISTS `outboundTypes` (
  `outboundTypeID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(30) NOT NULL,
  `className` varchar(50) NOT NULL,
  PRIMARY KEY (`outboundTypeID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 PACK_KEYS=0 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `outboundTypes`
--

INSERT INTO `outboundTypes` (`outboundTypeID`, `description`, `className`) VALUES
(1, 'AppLoopBack', 'commsOutboundMessageAppLoopBack'),
(2, 'Email', 'commsOutboundMessageEmail'),
(3, 'IVR', 'commsOutboundMessageIvr'),
(4, 'SMS', 'commsOutboundMessageSms'),
(5, 'Wappush', 'commsOutboundMessageWapPush'),
(6, 'OMA WBXML Rights', 'commsOutboundMessageWbxml');

-- --------------------------------------------------------

--
-- Table structure for table `prsRoutes`
--

CREATE TABLE IF NOT EXISTS `prsRoutes` (
  `prs` varchar(20) NOT NULL,
  `countryID` tinyint(4) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `shared` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `prsCountry` (`prs`,`countryID`),
  KEY `countryID` (`countryID`),
  KEY `active` (`active`),
  KEY `shared` (`shared`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `prsRoutes`
--


-- --------------------------------------------------------

--
-- Table structure for table `stopKeywords`
--

CREATE TABLE IF NOT EXISTS `stopKeywords` (
  `countryID` mediumint(3) unsigned NOT NULL,
  `keyword` varchar(20) NOT NULL,
  UNIQUE KEY `countryStopCode` (`countryID`,`keyword`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='List of valid keywords that are a STOP request';

--
-- Dumping data for table `stopKeywords`
--


-- --------------------------------------------------------

--
-- Table structure for table `triggers`
--

CREATE TABLE IF NOT EXISTS `triggers` (
  `triggerID` int(11) NOT NULL AUTO_INCREMENT,
  `inboundTypeID` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `triggerField` varchar(40) NOT NULL DEFAULT '',
  `triggerValue` varchar(100) NOT NULL DEFAULT '',
  `applicationID` int(10) NOT NULL DEFAULT '0',
  `comment` varchar(50) DEFAULT NULL,
  `externalReference` varchar(50) DEFAULT NULL COMMENT 'External reference for this trigger e.g. gateway keywordID etc',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`triggerID`),
  UNIQUE KEY `triggerValue` (`triggerValue`),
  KEY `idxActiveTriggers` (`inboundTypeID`,`triggerField`,`triggerValue`,`active`),
  KEY `active` (`active`),
  KEY `applicationID` (`applicationID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Maps triggers to services' AUTO_INCREMENT=1 ;

--
-- Dumping data for table `triggers`
--

