--
-- Table structure for table `inboundLog`
--

CREATE TABLE IF NOT EXISTS `inboundLog` (
  `date` date NOT NULL,
  `inboundTypeID` int(10) NOT NULL DEFAULT '0',
  `gatewayID` int(10) NOT NULL DEFAULT '0',
  `prs` varchar(20) NOT NULL,
  `networkID` int(10) NOT NULL DEFAULT '0',
  `received` int(10) NOT NULL DEFAULT '0',
  UNIQUE KEY `date` (`date`,`inboundTypeID`,`gatewayID`,`networkID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `inboundLog`
--


-- --------------------------------------------------------

--
-- Table structure for table `outboundLog`
--

CREATE TABLE IF NOT EXISTS `outboundLog` (
  `date` date NOT NULL,
  `outboundTypeID` int(10) NOT NULL DEFAULT '0',
  `gatewayID` int(10) NOT NULL DEFAULT '0',
  `gatewayAccountID` int(10) NOT NULL DEFAULT '0',
  `networkID` int(10) NOT NULL DEFAULT '0',
  `sent` int(10) NOT NULL DEFAULT '0',
  `acknowledged` int(10) NOT NULL DEFAULT '0',
  UNIQUE KEY `date` (`date`,`outboundTypeID`,`gatewayID`,`gatewayAccountID`,`networkID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
