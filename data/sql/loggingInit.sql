-- phpMyAdmin SQL Dump
-- version 2.11.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 01, 2008 at 07:13 PM
-- Server version: 5.0.45
-- PHP Version: 5.2.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- --------------------------------------------------------

--
-- Table structure for table `logQueue`
--

DROP TABLE IF EXISTS `logQueue`;
CREATE TABLE IF NOT EXISTS `logQueue` (
  `logFile` varchar(255) NOT NULL,
  `logMessage` mediumtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `logQueue`
--

