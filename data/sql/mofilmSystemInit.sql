-- phpMyAdmin SQL Dump
-- version 3.2.5
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Mar 16, 2010 at 01:31 PM
-- Server version: 5.1.42
-- PHP Version: 5.3.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `mofilm_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `dbUpdateLog`
--

CREATE TABLE IF NOT EXISTS `dbUpdateLog` (
  `dbUpdateID` int(11) NOT NULL AUTO_INCREMENT,
  `updateType` enum('SQL','Function') NOT NULL DEFAULT 'SQL',
  `updateCommand` mediumtext NOT NULL,
  `updateResult` enum('Success','Failure','Test') NOT NULL DEFAULT 'Failure',
  `messages` text NOT NULL,
  `createDate` datetime NOT NULL,
  `updateDate` datetime NOT NULL,
  PRIMARY KEY (`dbUpdateID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `dbUpdateLog`
--

-- --------------------------------------------------------

--
-- Table structure for table `dbUpdates`
--

CREATE TABLE IF NOT EXISTS `dbUpdates` (
  `dbName` varchar(255) NOT NULL,
  `version` int(10) NOT NULL DEFAULT '0',
  `lastUpdateID` int(11) NOT NULL DEFAULT '0',
  `createDate` datetime NOT NULL,
  `updateDate` datetime NOT NULL,
  UNIQUE KEY `database` (`dbName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dbUpdates`
--

-- --------------------------------------------------------

--
-- Table structure for table `mimeTypes`
--

CREATE TABLE IF NOT EXISTS `mimeTypes` (
  `mimeTypeID` int(10) NOT NULL AUTO_INCREMENT,
  `mimeType` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  `extension` varchar(10) NOT NULL,
  `primaryTypeID` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`mimeTypeID`),
  UNIQUE KEY `uniqTypeExtn` (`mimeType`,`extension`),
  KEY `extension` (`extension`),
  KEY `mimeType` (`mimeType`),
  KEY `primaryTypeID` (`primaryTypeID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=851 ;

--
-- Dumping data for table `mimeTypes`
--

INSERT INTO `mimeTypes` (`mimeTypeID`, `mimeType`, `description`, `extension`, `primaryTypeID`) VALUES
(1, 'application/acad', '', 'dwg', 0),
(2, 'application/arj', '', 'arj', 0),
(3, 'application/base64', '', 'mme', 0),
(4, 'application/base64', '', 'mm', 0),
(5, 'application/binhex', '', 'hqx', 0),
(6, 'application/binhex4', '', 'hqx', 0),
(7, 'application/book', '', 'boo', 0),
(8, 'application/book', '', 'book', 0),
(9, 'application/cdf', '', 'cdf', 0),
(10, 'application/clariscad', '', 'ccad', 0),
(11, 'application/commonground', '', 'dp', 0),
(12, 'application/drafting', '', 'drw', 0),
(13, 'application/dsptype', '', 'tsp', 0),
(14, 'application/dxf', '', 'dxf', 0),
(16, 'application/envoy', '', 'evy', 0),
(17, 'application/excel', '', 'xlm', 19),
(18, 'application/excel', '', 'xlb', 19),
(19, 'application/excel', '', 'xls', 0),
(20, 'application/excel', '', 'xlc', 19),
(21, 'application/excel', '', 'xlw', 19),
(22, 'application/excel', '', 'xld', 19),
(23, 'application/excel', '', 'xlt', 19),
(24, 'application/excel', '', 'xll', 19),
(25, 'application/excel', '', 'xlv', 19),
(26, 'application/excel', '', 'xl', 19),
(27, 'application/excel', '', 'xla', 19),
(28, 'application/excel', '', 'xlk', 19),
(30, 'application/fractals', '', 'fif', 0),
(31, 'application/freeloader', '', 'frl', 0),
(33, 'application/futuresplash', '', 'spl', 0),
(34, 'application/gnutar', '', 'tgz', 0),
(35, 'application/groupwise', '', 'vew', 0),
(36, 'application/hlp', '', 'hlp', 0),
(38, 'application/hta', '', 'hta', 0),
(39, 'application/i-deas', '', 'unv', 0),
(40, 'application/iges', '', 'iges', 0),
(41, 'application/iges', '', 'igs', 0),
(42, 'application/inf', '', 'inf', 0),
(43, 'application/internet-property-stream', '', 'acx', 0),
(44, 'application/java', '', 'class', 0),
(45, 'application/java-byte-code', '', 'class', 0),
(46, 'application/lha', '', 'lha', 0),
(47, 'application/lzx', '', 'lzx', 0),
(48, 'application/mac-binary', '', 'bin', 0),
(49, 'application/mac-binhex', '', 'hqx', 0),
(51, 'application/mac-binhex40', '', 'hqx', 0),
(52, 'application/mac-compactpro', '', 'cpt', 0),
(53, 'application/macbinary', '', 'bin', 0),
(54, 'application/marc', '', 'mrc', 0),
(55, 'application/mbedlet', '', 'mbd', 0),
(56, 'application/mcad', '', 'mcd', 0),
(57, 'application/mime', '', 'aps', 0),
(58, 'application/mspowerpoint', '', 'pot', 59),
(59, 'application/mspowerpoint', '', 'ppt', 0),
(60, 'application/mspowerpoint', '', 'ppz', 59),
(61, 'application/mspowerpoint', '', 'pps', 59),
(63, 'application/msword', '', 'dot', 65),
(65, 'application/msword', '', 'doc', 0),
(66, 'application/msword', '', 'wiz', 65),
(67, 'application/msword', '', 'w6w', 65),
(68, 'application/msword', '', 'word', 59),
(69, 'application/mswrite', '', 'wri', 0),
(70, 'application/netmc', '', 'mcp', 0),
(71, 'application/octet-stream', '', 'class', 0),
(72, 'application/octet-stream', '', 'dump', 0),
(73, 'application/octet-stream', '', 'lhx', 0),
(74, 'application/octet-stream', '', 'lzx', 0),
(77, 'application/octet-stream', '', 'lzh', 0),
(78, 'application/octet-stream', '', 'lha', 0),
(79, 'application/octet-stream', '', 'saveme', 0),
(90, 'application/octet-stream', '', 'exe', 0),
(91, 'application/octet-stream', '', 'bin', 0),
(82, 'application/octet-stream', '', '', 0),
(83, 'application/octet-stream', '', 'dms', 0),
(84, 'application/octet-stream', '', 'arj', 0),
(85, 'application/octet-stream', '', 'zoo', 0),
(86, 'application/octet-stream', '', 'arc', 0),
(87, 'application/octet-stream', '', 'com', 0),
(88, 'application/octet-stream', '', 'uu', 0),
(89, 'application/octet-stream', '', 'o', 0),
(92, 'application/octet-stream', '', 'a', 0),
(93, 'application/octet-stream', '', 'psd', 0),
(95, 'application/oda', '', 'oda', 0),
(96, 'application/olescript', '', 'axs', 0),
(98, 'application/pdf', '', 'pdf', 0),
(99, 'application/pics-rules', '', 'prf', 0),
(100, 'application/pkcs-12', '', 'p12', 0),
(101, 'application/pkcs-crl', '', 'crl', 0),
(103, 'application/pkcs10', '', 'p10', 0),
(104, 'application/pkcs7-mime', '', 'p7m', 0),
(105, 'application/pkcs7-mime', '', 'p7c', 0),
(106, 'application/pkcs7-signature', '', 'p7s', 0),
(107, 'application/pkix-cert', '', 'cer', 0),
(108, 'application/pkix-cert', '', 'crt', 0),
(110, 'application/pkix-crl', '', 'crl', 0),
(111, 'application/plain', '', 'text', 0),
(115, 'application/postscript', '', 'ps', 0),
(117, 'application/postscript', '', 'eps', 115),
(116, 'application/postscript', '', 'ai', 115),
(118, 'application/powerpoint', '', 'ppt', 0),
(119, 'application/pro_eng', '', 'part', 0),
(120, 'application/pro_eng', '', 'prt', 0),
(121, 'application/ringing-tones', '', 'rng', 0),
(122, 'application/rtf', '', 'rtx', 0),
(124, 'application/rtf', '', 'rtf', 0),
(125, 'application/sdp', '', 'sdp', 0),
(126, 'application/sea', '', 'sea', 0),
(127, 'application/set', '', 'set', 0),
(128, 'application/set-payment-initiation', '', 'setpay', 0),
(129, 'application/set-registration-initiation', '', 'setreg', 0),
(130, 'application/sla', '', 'stl', 0),
(131, 'application/smil', '', 'smil', 0),
(132, 'application/smil', '', 'smi', 131),
(133, 'application/solids', '', 'sol', 0),
(134, 'application/sounder', '', 'sdr', 0),
(135, 'application/step', '', 'step', 0),
(136, 'application/step', '', 'stp', 0),
(137, 'application/streamingmedia', '', 'ssm', 0),
(138, 'application/toolbook', '', 'tbk', 0),
(139, 'application/vda', '', 'vda', 0),
(140, 'application/vnd.fdf', '', 'fdf', 0),
(141, 'application/vnd.hp-hpgl', '', 'hgl', 0),
(142, 'application/vnd.hp-hpgl', '', 'hpgl', 0),
(143, 'application/vnd.hp-hpgl', '', 'hpg', 0),
(144, 'application/vnd.hp-pcl', '', 'pcl', 0),
(153, 'application/vnd.ms-excel', '', 'xlc', 155),
(155, 'application/vnd.ms-excel', '', 'xls', 0),
(150, 'application/vnd.ms-excel', '', 'xlm', 155),
(148, 'application/vnd.ms-excel', '', 'xlt', 155),
(156, 'application/vnd.ms-excel', '', 'xlw', 155),
(151, 'application/vnd.ms-excel', '', 'xll', 155),
(152, 'application/vnd.ms-excel', '', 'xla', 155),
(154, 'application/vnd.ms-excel', '', 'xlb', 155),
(158, 'application/vnd.ms-pki.certstore', '', 'sst', 0),
(159, 'application/vnd.ms-pki.pko', '', 'pko', 0),
(161, 'application/vnd.ms-pki.seccat', '', 'cat', 0),
(163, 'application/vnd.ms-pki.stl', '', 'stl', 0),
(168, 'application/vnd.ms-powerpoint', '', 'pps', 171),
(165, 'application/vnd.ms-powerpoint', '', 'pot', 171),
(171, 'application/vnd.ms-powerpoint', '', 'ppt', 0),
(167, 'application/vnd.ms-powerpoint', '', 'pot,', 171),
(169, 'application/vnd.ms-powerpoint', '', 'ppa', 171),
(170, 'application/vnd.ms-powerpoint', '', 'pwz', 171),
(173, 'application/vnd.ms-project', '', 'mpp', 0),
(174, 'application/vnd.ms-works', '', 'wcm', 176),
(175, 'application/vnd.ms-works', '', 'wdb', 176),
(176, 'application/vnd.ms-works', '', 'wks', 0),
(177, 'application/vnd.ms-works', '', 'wps', 176),
(178, 'application/vnd.nokia.configuration-message', '', 'ncm', 0),
(179, 'application/vnd.nokia.ringing-tone', '', 'rng', 0),
(180, 'application/vnd.rn-realmedia', '', 'rm', 0),
(181, 'application/vnd.rn-realplayer', '', 'rnx', 0),
(182, 'application/vnd.wap.wmlc', '', 'wmlc', 0),
(183, 'application/vnd.wap.wml.scriptc', '', 'wmlsc', 0),
(184, 'application/vnd.xara', '', 'web', 0),
(185, 'application/vocaltec-media-desc', '', 'vmd', 0),
(186, 'application/vocaltec-media-file', '', 'vmf', 0),
(187, 'application/winhlp', '', 'hlp', 0),
(188, 'application/wordperfect', '', 'wpd', 0),
(189, 'application/wordperfect', '', 'wp5', 0),
(190, 'application/wordperfect', '', 'wp6', 0),
(191, 'application/wordperfect', '', 'wp', 0),
(192, 'application/wordperfect6.0', '', 'wp5', 0),
(193, 'application/wordperfect6.0', '', 'w60', 0),
(194, 'application/wordperfect6.1', '', 'w61', 0),
(195, 'application/x-123', '', 'wk1', 0),
(196, 'application/x-aim', '', 'aim', 0),
(197, 'application/x-authorware-bin', '', 'aab', 0),
(198, 'application/x-authorware-map', '', 'aam', 0),
(199, 'application/x-authorware-seg', '', 'aas', 0),
(201, 'application/x-bcpio', '', 'bcpio', 0),
(202, 'application/x-binary', '', 'bin', 0),
(203, 'application/x-binhex40', '', 'hqx', 0),
(204, 'application/x-bsh', '', 'sh', 0),
(205, 'application/x-bsh', '', 'shar', 0),
(206, 'application/x-bsh', '', 'bsh', 0),
(207, 'application/x-bytecode.elisp', 'Compiled elisp', 'elc', 0),
(208, 'application/x-bytecode.python', 'Compiled python', 'pyc', 0),
(209, 'application/x-bzip', '', 'bz', 0),
(210, 'application/x-bzip2', '', 'bz2', 0),
(211, 'application/x-bzip2', '', 'boz', 0),
(213, 'application/x-cdf', '', 'cdf', 0),
(214, 'application/x-cdlink', '', 'vcd', 0),
(215, 'application/x-chat', '', 'cha', 0),
(216, 'application/x-chat', '', 'chat', 0),
(217, 'application/x-cmu-raster', '', 'ras', 0),
(218, 'application/x-cocoa', '', 'cco', 0),
(219, 'application/x-compactpro', '', 'cpt', 0),
(221, 'application/x-compress', '', 'z', 0),
(222, 'application/x-compressed', '', 'gz', 0),
(223, 'application/x-compressed', '', 'zip', 0),
(224, 'application/x-compressed', '', 'z', 0),
(226, 'application/x-compressed', '', 'tgz', 0),
(227, 'application/x-conference', '', 'nsc', 0),
(229, 'application/x-cpio', '', 'cpio', 0),
(230, 'application/x-cpt', '', 'cpt', 0),
(232, 'application/x-csh', '', 'csh', 0),
(233, 'application/x-deepv', '', 'deepv', 0),
(237, 'application/x-director', '', 'dxr', 0),
(239, 'application/x-director', '', 'dir', 0),
(238, 'application/x-director', '', 'dcr', 0),
(241, 'application/x-dvi', '', 'dvi', 0),
(242, 'application/x-elc', '', 'elc', 0),
(243, 'application/x-envoy', '', 'env', 0),
(244, 'application/x-envoy', '', 'evy', 0),
(245, 'application/x-esrehber', '', 'es', 0),
(246, 'application/x-excel', '', 'xla', 254),
(247, 'application/x-excel', '', 'xlw', 254),
(248, 'application/x-excel', '', 'xlv', 254),
(249, 'application/x-excel', '', 'xlb', 254),
(250, 'application/x-excel', '', 'xll', 254),
(251, 'application/x-excel', '', 'xlt', 254),
(252, 'application/x-excel', '', 'xlk', 254),
(253, 'application/x-excel', '', 'xlc', 254),
(254, 'application/x-excel', '', 'xls', 0),
(255, 'application/x-excel', '', 'xlm', 254),
(256, 'application/x-excel', '', 'xld', 254),
(257, 'application/x-frame', '', 'mif', 0),
(258, 'application/x-freelance', '', 'pre', 0),
(259, 'application/x-gsp', '', 'gsp', 0),
(260, 'application/x-gss', '', 'gss', 0),
(262, 'application/x-gtar', '', 'gtar', 0),
(264, 'application/x-gzip', '', 'gz', 0),
(265, 'application/x-gzip', '', 'gzip', 0),
(267, 'application/x-hdf', '', 'hdf', 0),
(268, 'application/x-helpfile', '', 'help', 0),
(269, 'application/x-helpfile', '', 'hlp', 0),
(270, 'application/x-httpd-imap', '', 'imap', 0),
(271, 'application/x-ima', '', 'ima', 0),
(272, 'application/x-internet-signup', '', 'ins', 0),
(273, 'application/x-internet-signup', '', 'isp', 0),
(275, 'application/x-inventor', '', 'iv', 0),
(276, 'application/x-ip2', '', 'ip', 0),
(277, 'application/x-iphone', '', 'iii', 0),
(278, 'application/x-java-class', '', 'class', 0),
(279, 'application/x-java-commerce', '', 'jcm', 0),
(281, 'application/x-javascript', '', 'js', 0),
(282, 'application/x-koan', '', 'skp', 0),
(283, 'application/x-koan', '', 'skd', 0),
(284, 'application/x-koan', '', 'skm', 0),
(285, 'application/x-koan', '', 'skt', 0),
(286, 'application/x-ksh', '', 'ksh', 0),
(289, 'application/x-latex', '', 'latex', 0),
(288, 'application/x-latex', '', 'ltx', 0),
(290, 'application/x-lha', '', 'lha', 0),
(291, 'application/x-lisp', '', 'lsp', 0),
(292, 'application/x-livescreen', '', 'ivy', 0),
(293, 'application/x-lotus', '', 'wq1', 0),
(294, 'application/x-lotusscreencam', '', 'scm', 0),
(295, 'application/x-lzh', '', 'lzh', 0),
(296, 'application/x-lzx', '', 'lzx', 0),
(297, 'application/x-mac-binhex40', '', 'hqx', 0),
(298, 'application/x-macbinary', '', 'bin', 0),
(299, 'application/x-magic-cap-package-1.0', '', 'mc$', 0),
(300, 'application/x-mathcad', '', 'mcd', 0),
(301, 'application/x-meme', '', 'mm', 0),
(302, 'application/x-midi', '', 'midi', 0),
(303, 'application/x-midi', '', 'mid', 456),
(304, 'application/x-mif', '', 'mif', 0),
(305, 'application/x-mix-transfer', '', 'nix', 0),
(306, 'application/x-mplayer2', '', 'asx', 0),
(307, 'application/x-msaccess', '', 'mdb', 0),
(308, 'application/x-mscardfile', '', 'crd', 0),
(309, 'application/x-msclip', '', 'clp', 0),
(310, 'application/x-msdownload', '', 'dll', 0),
(311, 'application/x-msexcel', '', 'xlw', 313),
(312, 'application/x-msexcel', '', 'xla', 313),
(313, 'application/x-msexcel', '', 'xls', 0),
(314, 'application/x-msmediaview', '', 'mvb', 0),
(315, 'application/x-msmediaview', '', 'm13', 0),
(316, 'application/x-msmediaview', '', 'm14', 0),
(317, 'application/x-msmetafile', '', 'wmf', 0),
(318, 'application/x-msmoney', '', 'mny', 0),
(319, 'application/x-mspowerpoint', '', 'ppt', 0),
(320, 'application/x-mspublisher', '', 'pub', 0),
(321, 'application/x-msschedule', '', 'scd', 0),
(322, 'application/x-msterminal', '', 'trm', 0),
(323, 'application/x-mswrite', '', 'wri', 0),
(324, 'application/x-navi-animation', '', 'ani', 0),
(325, 'application/x-navidoc', '', 'nvd', 0),
(326, 'application/x-navimap', '', 'map', 0),
(327, 'application/x-navistyle', '', 'stl', 0),
(328, 'application/x-netcdf', '', 'nc', 0),
(329, 'application/x-netcdf', '', 'cdf', 0),
(330, 'application/x-newton-compatible-pkg', '', 'pkg', 0),
(331, 'application/x-nokia-9000-communicator-add-on-software', '', 'aos', 0),
(332, 'application/x-omc', '', 'omc', 0),
(333, 'application/x-omcdatamaker', '', 'omcd', 0),
(334, 'application/x-omcregerator', '', 'omcr', 0),
(335, 'application/x-pagemaker', '', 'pm4', 0),
(336, 'application/x-pagemaker', '', 'pm5', 0),
(337, 'application/x-pcl', '', 'pcl', 0),
(338, 'application/x-perfmon', '', 'pma', 0),
(339, 'application/x-perfmon', '', 'pmw', 0),
(340, 'application/x-perfmon', '', 'pmc', 0),
(341, 'application/x-perfmon', '', 'pmr', 0),
(342, 'application/x-perfmon', '', 'pml', 0),
(343, 'application/x-pixclscript', '', 'plx', 0),
(344, 'application/x-pkcs10', '', 'p10', 0),
(345, 'application/x-pkcs12', '', 'pfx', 0),
(347, 'application/x-pkcs12', '', 'p12', 0),
(349, 'application/x-pkcs7-certificates', '', 'spc', 0),
(350, 'application/x-pkcs7-certificates', '', 'p7b', 0),
(352, 'application/x-pkcs7-certreqresp', '', 'p7r', 0),
(356, 'application/x-pkcs7-mime', '', 'p7m', 0),
(355, 'application/x-pkcs7-mime', '', 'p7c', 0),
(357, 'application/x-pkcs7-signature', '', 'p7a', 0),
(358, 'application/x-pkcs7-signature', '', 'p7s', 0),
(359, 'application/x-pointplus', '', 'css', 0),
(360, 'application/x-portable-anymap', '', 'pnm', 0),
(361, 'application/x-project', '', 'mpt', 0),
(362, 'application/x-project', '', 'mpc', 0),
(363, 'application/x-project', '', 'mpv', 0),
(364, 'application/x-project', '', 'mpx', 0),
(365, 'application/x-qpro', '', 'wb1', 0),
(366, 'application/x-rtf', '', 'rtf', 0),
(367, 'application/x-sdp', '', 'sdp', 0),
(368, 'application/x-sea', '', 'sea', 0),
(369, 'application/x-seelogo', '', 'sl', 0),
(371, 'application/x-sh', '', 'sh', 0),
(374, 'application/x-shar', '', 'shar', 0),
(373, 'application/x-shar', '', 'sh', 0),
(376, 'application/x-shockwave-flash', '', 'swf', 0),
(377, 'application/x-sit', '', 'sit', 0),
(378, 'application/x-sprite', '', 'sprite', 0),
(379, 'application/x-sprite', '', 'spr', 0),
(381, 'application/x-stuffit', '', 'sit', 0),
(383, 'application/x-sv4cpio', '', 'sv4cpio', 0),
(385, 'application/x-sv4crc', '', 'sv4crc', 0),
(387, 'application/x-tar', '', 'tar', 0),
(388, 'application/x-tbook', '', 'tbk', 0),
(389, 'application/x-tbook', '', 'sbk', 0),
(391, 'application/x-tcl', '', 'tcl', 0),
(393, 'application/x-tex', '', 'tex', 0),
(397, 'application/x-texinfo', '', 'texi', 0),
(396, 'application/x-texinfo', '', 'texinfo', 0),
(399, 'application/x-troff', '', 'roff', 0),
(403, 'application/x-troff', '', 'tr', 0),
(402, 'application/x-troff', '', 't', 0),
(405, 'application/x-troff-man', '', 'man', 0),
(407, 'application/x-troff-me', '', 'me', 0),
(409, 'application/x-troff-ms', '', 'ms', 0),
(410, 'application/x-troff-msvideo', '', 'avi', 0),
(412, 'application/x-ustar', '', 'ustar', 0),
(413, 'application/x-visio', '', 'vsw', 0),
(414, 'application/x-visio', '', 'vsd', 0),
(415, 'application/x-visio', '', 'vst', 0),
(416, 'application/x-vnd.audioexplosion.mzz', '', 'mzz', 0),
(417, 'application/x-vnd.ls-xpix', '', 'xpix', 0),
(418, 'application/x-vrml', '', 'vrml', 0),
(419, 'application/x-wais-source', '', 'wsrc', 0),
(421, 'application/x-wais-source', '', 'src', 0),
(422, 'application/x-winhelp', '', 'hlp', 0),
(423, 'application/x-wintalk', '', 'wtk', 0),
(424, 'application/x-world', '', 'wrl', 0),
(425, 'application/x-world', '', 'svr', 0),
(426, 'application/x-wpwin', '', 'wpd', 0),
(427, 'application/x-wri', '', 'wri', 0),
(431, 'application/x-x509-ca-cert', '', 'cer', 0),
(430, 'application/x-x509-ca-cert', '', 'der', 0),
(433, 'application/x-x509-ca-cert', '', 'crt', 0),
(434, 'application/x-x509-user-cert', '', 'crt', 0),
(435, 'application/x-zip-compressed', '', 'zip', 0),
(436, 'application/xml', '', 'xml', 0),
(437, 'application/yndms-pkipko', '', 'pko', 0),
(439, 'application/zip', '', 'zip', 0),
(440, 'audio/aiff', '', 'aifc', 0),
(441, 'audio/aiff', '', 'aiff', 0),
(442, 'audio/aiff', '', 'aif', 0),
(444, 'audio/basic', '', 'au', 0),
(446, 'audio/basic', '', 'snd', 0),
(447, 'audio/it', '', 'it', 0),
(448, 'audio/make', '', 'pfunk', 0),
(449, 'audio/make', '', 'my', 0),
(450, 'audio/make', '', 'funk', 0),
(451, 'audio/make.my.funk', '', 'pfunk', 0),
(454, 'audio/mid', '', 'rmi', 0),
(453, 'audio/mid', '', 'mid', 456),
(455, 'audio/midi', '', 'kar', 0),
(456, 'audio/midi', '', 'mid', 0),
(457, 'audio/midi', '', 'midi', 0),
(458, 'audio/mod', '', 'mod', 0),
(459, 'audio/mpeg', '', 'mp3', 0),
(460, 'audio/mpeg', '', 'mpa', 0),
(461, 'audio/mpeg', '', 'mpg', 0),
(462, 'audio/mpeg', '', 'mpga', 0),
(463, 'audio/mpeg', '', 'm2a', 464),
(464, 'audio/mpeg', '', 'mp2', 0),
(465, 'audio/mpeg3', '', 'mp3', 459),
(466, 'audio/nspaudio', '', 'lma', 0),
(467, 'audio/nspaudio', '', 'la', 0),
(468, 'audio/s3m', '', 's3m', 0),
(469, 'audio/tsp-audio', '', 'tsi', 0),
(470, 'audio/tsplayer', '', 'tsp', 0),
(471, 'audio/vnd.qcelp', '', 'qcp', 0),
(472, 'audio/voc', '', 'voc', 0),
(473, 'audio/voxware', '', 'vox', 0),
(474, 'audio/wav', '', 'wav', 0),
(475, 'audio/x-adpcm', '', 'snd', 0),
(480, 'audio/x-aiff', '', 'aifc', 0),
(479, 'audio/x-aiff', '', 'aiff', 0),
(481, 'audio/x-aiff', '', 'aif', 0),
(482, 'audio/x-au', '', 'au', 0),
(483, 'audio/x-gsm', '', 'gsd', 0),
(484, 'audio/x-gsm', '', 'gsm', 0),
(485, 'audio/x-jam', '', 'jam', 0),
(486, 'audio/x-liveaudio', '', 'lam', 0),
(487, 'audio/x-mid', '', 'midi', 0),
(488, 'audio/x-mid', '', 'mid', 456),
(489, 'audio/x-midi', '', 'midi', 0),
(490, 'audio/x-midi', '', 'mid', 456),
(491, 'audio/x-mod', '', 'mod', 0),
(492, 'audio/x-mpeg', '', 'mp2', 0),
(493, 'audio/x-mpeg-3', '', 'mp3', 459),
(494, 'audio/x-mpegurl', '', 'm3u', 0),
(496, 'audio/x-nspaudio', '', 'lma', 0),
(497, 'audio/x-nspaudio', '', 'la', 0),
(498, 'audio/x-pn-realaudio', '', 'rmp', 0),
(503, 'audio/x-pn-realaudio', '', 'ram', 0),
(500, 'audio/x-pn-realaudio', '', 'rmm', 0),
(502, 'audio/x-pn-realaudio', '', 'ra', 0),
(504, 'audio/x-pn-realaudio', '', 'rm', 0),
(505, 'audio/x-pn-realaudio-plugin', '', 'rmp', 0),
(506, 'audio/x-pn-realaudio-plugin', '', 'ra', 0),
(507, 'audio/x-pn-realaudio-plugin', '', 'rpm', 0),
(508, 'audio/x-psid', '', 'sid', 0),
(509, 'audio/x-realaudio', '', 'ra', 0),
(510, 'audio/x-twinvq', '', 'vqf', 0),
(511, 'audio/x-twinvq-plugin', '', 'vqe', 0),
(512, 'audio/x-twinvq-plugin', '', 'vql', 0),
(513, 'audio/x-vnd.audioexplosion.mjuicemediafile', '', 'mjf', 0),
(514, 'audio/x-voc', '', 'voc', 0),
(516, 'audio/x-wav', '', 'wav', 0),
(517, 'audio/xm', '', 'xm', 0),
(518, 'chemical/x-pdb', '', 'pdb', 0),
(519, 'chemical/x-pdb', '', 'xyz', 0),
(520, 'drawing/x-dwf (old)', '', 'dwf', 0),
(521, 'i-world/i-vrml', '', 'ivr', 0),
(523, 'image/bmp', '', 'bmp', 0),
(524, 'image/bmp', '', 'bm', 0),
(525, 'image/cis-cod', '', 'cod', 0),
(526, 'image/cmu-raster', '', 'ras', 0),
(527, 'image/cmu-raster', '', 'rast', 0),
(528, 'image/fif', '', 'fif', 0),
(529, 'image/florian', '', 'flo', 0),
(530, 'image/florian', '', 'turbot', 0),
(531, 'image/g3fax', '', 'g3', 0),
(533, 'image/gif', '', 'gif', 0),
(535, 'image/ief', '', 'ief', 0),
(536, 'image/ief', '', 'iefs', 0),
(542, 'image/jpeg', '', 'jpg', 0),
(539, 'image/jpeg', '', 'jpe', 542),
(543, 'image/jpeg', '', 'jpeg', 542),
(541, 'image/jpeg', '', 'jfif', 542),
(544, 'image/jpeg', '', 'jfif-tbnl', 542),
(545, 'image/jutvision', '', 'jut', 0),
(546, 'image/naplps', '', 'nap', 0),
(547, 'image/naplps', '', 'naplps', 0),
(548, 'image/pict', '', 'pic', 0),
(549, 'image/pict', '', 'pict', 0),
(550, 'image/pipeg', '', 'jfif', 554),
(551, 'image/pjpeg', '', 'jfif', 554),
(552, 'image/pjpeg', '', 'jpe', 554),
(553, 'image/pjpeg', '', 'jpeg', 554),
(554, 'image/pjpeg', '', 'jpg', 0),
(555, 'image/png', '', 'png', 0),
(556, 'image/png', '', 'x-png', 0),
(557, 'image/svg+xml', '', 'svg', 0),
(559, 'image/tiff', '', 'tif', 0),
(561, 'image/tiff', '', 'tiff', 559),
(562, 'image/vasa', '', 'mcf', 0),
(563, 'image/vnd.dwg', '', 'svf', 0),
(564, 'image/vnd.dwg', '', 'dwg', 0),
(565, 'image/vnd.dwg', '', 'dxf', 0),
(566, 'image/vnd.fpx', '', 'fpx', 0),
(567, 'image/vnd.net-fpx', '', 'fpx', 0),
(568, 'image/vnd.rn-realflash', '', 'rf', 0),
(569, 'image/vnd.rn-realpix', '', 'rp', 0),
(570, 'image/vnd.wap.wbmp', '', 'wbmp', 0),
(571, 'image/vnd.xiff', '', 'xif', 0),
(573, 'image/x-cmu-raster', '', 'ras', 0),
(574, 'image/x-cmx', '', 'cmx', 0),
(575, 'image/x-dwg', '', 'dxf', 0),
(576, 'image/x-dwg', '', 'svf', 0),
(577, 'image/x-dwg', '', 'dwg', 0),
(579, 'image/x-icon', '', 'ico', 0),
(580, 'image/x-jg', '', 'art', 0),
(581, 'image/x-jps', '', 'jps', 0),
(582, 'image/x-niff', '', 'nif', 0),
(583, 'image/x-niff', '', 'niff', 0),
(584, 'image/x-pcx', '', 'pcx', 0),
(585, 'image/x-pict', '', 'pct', 0),
(587, 'image/x-portable-anymap', '', 'pnm', 0),
(589, 'image/x-portable-bitmap', '', 'pbm', 0),
(591, 'image/x-portable-graymap', '', 'pgm', 0),
(592, 'image/x-portable-greymap', '', 'pgm', 0),
(594, 'image/x-portable-pixmap', '', 'ppm', 0),
(595, 'image/x-quicktime', '', 'qtif', 0),
(596, 'image/x-quicktime', '', 'qif', 0),
(597, 'image/x-quicktime', '', 'qti', 0),
(599, 'image/x-rgb', '', 'rgb', 0),
(600, 'image/x-tiff', '', 'tiff', 601),
(601, 'image/x-tiff', '', 'tif', 0),
(602, 'image/x-windows-bmp', '', 'bmp', 0),
(604, 'image/x-xbitmap', '', 'xbm', 0),
(605, 'image/x-xbm', '', 'xbm', 0),
(607, 'image/x-xpixmap', '', 'xpm', 0),
(608, 'image/x-xpixmap', '', 'pm', 0),
(609, 'image/x-xwd', '', 'xwd', 0),
(611, 'image/x-xwindowdump', '', 'xwd', 0),
(612, 'image/xbm', '', 'xbm', 0),
(613, 'image/xpm', '', 'xpm', 0),
(619, 'message/rfc822', '', 'mht', 0),
(615, 'message/rfc822', '', 'mime', 0),
(616, 'message/rfc822', '', 'nws', 0),
(618, 'message/rfc822', '', 'mhtml', 0),
(620, 'model/iges', '', 'igs', 0),
(621, 'model/iges', '', 'iges', 0),
(622, 'model/vnd.dwf', '', 'dwf', 0),
(623, 'model/vrml', '', 'vrml', 0),
(624, 'model/vrml', '', 'wrl', 0),
(625, 'model/vrml', '', 'wrz', 0),
(626, 'model/x-pov', '', 'pov', 0),
(627, 'multipart/x-gzip', '', 'gzip', 0),
(628, 'multipart/x-ustar', '', 'ustar', 0),
(629, 'multipart/x-zip', '', 'zip', 0),
(630, 'music/crescendo', '', 'mid', 456),
(631, 'music/crescendo', '', 'midi', 0),
(632, 'music/x-karaoke', '', 'kar', 0),
(633, 'paleovu/x-pv', '', 'pvu', 0),
(634, 'text/asp', '', 'asp', 0),
(636, 'text/css', '', 'css', 0),
(637, 'text/h323', '', '323', 0),
(646, 'text/html', '', 'htm', 0),
(643, 'text/html', '', 'html', 646),
(640, 'text/html', '', 'htmls', 646),
(641, 'text/html', '', 'shtml', 646),
(642, 'text/html', '', 'acgi', 646),
(644, 'text/html', '', 'stm', 646),
(645, 'text/html', '', 'htx', 646),
(647, 'text/iuls', '', 'uls', 0),
(648, 'text/mcf', '', 'mcf', 0),
(649, 'text/pascal', '', 'pas', 0),
(650, 'text/plain', '', 'lst', 673),
(651, 'text/plain', '', 'log', 673),
(671, 'text/plain', '', 'h', 673),
(653, 'text/plain', '', 'c++', 673),
(654, 'text/plain', '', 'f', 673),
(655, 'text/plain', '', 'pl', 673),
(656, 'text/plain', '', 'for', 673),
(657, 'text/plain', '', 'idc', 673),
(658, 'text/plain', '', 'cxx', 673),
(659, 'text/plain', '', 'jav', 673),
(660, 'text/plain', '', 'bas', 673),
(673, 'text/plain', '', 'txt', 0),
(662, 'text/plain', '', 'sdml', 673),
(663, 'text/plain', '', 'java', 673),
(664, 'text/plain', '', 'f90', 673),
(669, 'text/plain', '', 'c', 673),
(666, 'text/plain', '', 'hh', 673),
(667, 'text/plain', '', 'com', 673),
(668, 'text/plain', '', 'm', 673),
(670, 'text/plain', '', 'mar', 673),
(672, 'text/plain', '', 'list', 673),
(674, 'text/plain', '', 'def', 673),
(675, 'text/plain', '', 'cc', 673),
(676, 'text/plain', '', 'text', 673),
(677, 'text/plain', '', 'conf', 673),
(678, 'text/plain', '', 'g', 673),
(679, 'text/richtext', '', 'rtf', 0),
(682, 'text/richtext', '', 'rtx', 679),
(681, 'text/richtext', '', 'rt', 679),
(683, 'text/scriplet', '', 'wsc', 0),
(684, 'text/scriptlet', '', 'sct', 0),
(685, 'text/sgml', '', 'sgml', 0),
(686, 'text/sgml', '', 'sgm', 0),
(688, 'text/tab-separated-values', '', 'tsv', 0),
(689, 'text/uri-list', '', 'unis', 0),
(690, 'text/uri-list', '', 'uris', 0),
(691, 'text/uri-list', '', 'uri', 0),
(692, 'text/uri-list', '', 'uni', 0),
(693, 'text/vnd.abc', '', 'abc', 0),
(694, 'text/vnd.fmi.flexstor', '', 'flx', 0),
(695, 'text/vnd.rn-realtext', '', 'rt', 0),
(696, 'text/vnd.wap.wml', '', 'wml', 0),
(697, 'text/vnd.wap.wml.script', '', 'wmls', 0),
(699, 'text/webviewhtml', '', 'htt', 0),
(700, 'text/x-asm', '', 'asm', 0),
(701, 'text/x-asm', '', 's', 0),
(702, 'text/x-audiosoft-intra', '', 'aip', 0),
(703, 'text/x-c', '', 'cpp', 0),
(704, 'text/x-c', '', 'c', 0),
(705, 'text/x-c', '', 'cc', 0),
(707, 'text/x-component', '', 'htc', 0),
(708, 'text/x-fortran', '', 'for', 0),
(709, 'text/x-fortran', '', 'f90', 0),
(710, 'text/x-fortran', '', 'f', 0),
(711, 'text/x-fortran', '', 'f77', 0),
(712, 'text/x-h', '', 'hh', 0),
(713, 'text/x-h', '', 'h', 0),
(714, 'text/x-java-source', '', 'jav', 0),
(715, 'text/x-java-source', '', 'java', 0),
(716, 'text/x-la-asf', '', 'lsx', 0),
(717, 'text/x-m', '', 'm', 0),
(718, 'text/x-pascal', '', 'p', 0),
(719, 'text/x-script', '', 'hlb', 0),
(720, 'text/x-script.csh', '', 'csh', 0),
(721, 'text/x-script.elisp', '', 'el', 0),
(722, 'text/x-script.guile', '', 'scm', 0),
(723, 'text/x-script.ksh', '', 'ksh', 0),
(724, 'text/x-script.lisp', '', 'lsp', 0),
(725, 'text/x-script.perl', '', 'pl', 0),
(726, 'text/x-script.perl-module', '', 'pm', 0),
(727, 'text/x-script.python', '', 'py', 0),
(728, 'text/x-script.rexx', '', 'rexx', 0),
(729, 'text/x-script.scheme', '', 'scm', 0),
(730, 'text/x-script.sh', '', 'sh', 0),
(731, 'text/x-script.tcl', '', 'tcl', 0),
(732, 'text/x-script.tcsh', '', 'tcsh', 0),
(733, 'text/x-script.zsh', '', 'zsh', 0),
(734, 'text/x-server-parsed-html', '', 'shtml', 0),
(735, 'text/x-server-parsed-html', '', 'ssi', 0),
(737, 'text/x-setext', '', 'etx', 0),
(738, 'text/x-sgml', '', 'sgm', 0),
(739, 'text/x-sgml', '', 'sgml', 0),
(740, 'text/x-speech', '', 'spc', 0),
(741, 'text/x-speech', '', 'talk', 0),
(742, 'text/x-uil', '', 'uil', 0),
(743, 'text/x-uuencode', '', 'uue', 0),
(744, 'text/x-uuencode', '', 'uu', 0),
(745, 'text/x-vcalendar', '', 'vcs', 0),
(746, 'text/x-vcard', '', 'vcf', 0),
(747, 'text/xml', '', 'xml', 0),
(748, 'video/animaflex', '', 'afl', 0),
(749, 'video/avi', '', 'avi', 0),
(750, 'video/avs-video', '', 'avs', 0),
(751, 'video/dl', '', 'dl', 0),
(752, 'video/fli', '', 'fli', 0),
(753, 'video/gl', '', 'gl', 0),
(754, 'video/mpeg', '', 'm2v', 759),
(763, 'video/mpeg', '', 'mpeg', 759),
(759, 'video/mpeg', '', 'mpg', 0),
(766, 'video/mpeg', '', 'mpe', 759),
(760, 'video/mpeg', '', 'mpa', 759),
(761, 'video/mpeg', '', 'mpv2', 759),
(762, 'video/mpeg', '', 'm1v', 759),
(765, 'video/mpeg', '', 'mp2', 759),
(767, 'video/mpeg', '', 'mp3', 759),
(768, 'video/msvideo', '', 'avi', 0),
(772, 'video/quicktime', '', 'qt', 773),
(770, 'video/quicktime', '', 'moov', 773),
(773, 'video/quicktime', '', 'mov', 0),
(774, 'video/vdo', '', 'vdo', 0),
(775, 'video/vivo', '', 'vivo', 0),
(776, 'video/vivo', '', 'viv', 0),
(777, 'video/vnd.rn-realvideo', '', 'rv', 0),
(778, 'video/vnd.vivo', '', 'vivo', 0),
(779, 'video/vnd.vivo', '', 'viv', 0),
(780, 'video/vosaic', '', 'vos', 0),
(781, 'video/x-amt-demorun', '', 'xdr', 0),
(782, 'video/x-amt-showrun', '', 'xsr', 0),
(783, 'video/x-atomic3d-feature', '', 'fmf', 0),
(784, 'video/x-dl', '', 'dl', 0),
(785, 'video/x-dv', '', 'dif', 0),
(786, 'video/x-dv', '', 'dv', 0),
(787, 'video/x-fli', '', 'fli', 0),
(788, 'video/x-gl', '', 'gl', 0),
(789, 'video/x-isvideo', '', 'isu', 0),
(790, 'video/x-la-asf', '', 'lsx', 0),
(791, 'video/x-la-asf', '', 'lsf', 0),
(792, 'video/x-motion-jpeg', '', 'mjpg', 0),
(793, 'video/x-mpeg', '', 'mp2', 0),
(794, 'video/x-mpeg', '', 'mp3', 0),
(795, 'video/x-mpeq2a', '', 'mp2', 0),
(796, 'video/x-ms-asf', '', 'asr', 0),
(798, 'video/x-ms-asf', '', 'asx', 0),
(800, 'video/x-ms-asf', '', 'asf', 0),
(801, 'video/x-ms-asf-plugin', '', 'asx', 0),
(803, 'video/x-msvideo', '', 'avi', 0),
(804, 'video/x-qtc', '', 'qtc', 0),
(805, 'video/x-scm', '', 'scm', 0),
(806, 'video/x-sgi-movie', '', 'mv', 0),
(808, 'video/x-sgi-movie', '', 'movie', 0),
(809, 'windows/metafile', '', 'wmf', 0),
(810, 'www/mime', '', 'mime', 0),
(811, 'x-conference/x-cooltalk', '', 'ice', 0),
(812, 'x-music/x-midi', '', 'mid', 456),
(813, 'x-music/x-midi', '', 'midi', 0),
(814, 'x-world/x-3dmf', '', '3dm', 0),
(815, 'x-world/x-3dmf', '', '3dmf', 0),
(816, 'x-world/x-3dmf', '', 'qd3d', 0),
(817, 'x-world/x-3dmf', '', 'qd3', 0),
(818, 'x-world/x-svr', '', 'svr', 0),
(824, 'x-world/x-vrml', '', 'wrz', 821),
(821, 'x-world/x-vrml', '', 'vrml', 0),
(826, 'x-world/x-vrml', '', 'wrl', 821),
(823, 'x-world/x-vrml', '', 'xaf', 821),
(825, 'x-world/x-vrml', '', 'xof', 821),
(827, 'x-world/x-vrml', '', 'flr', 821),
(828, 'x-world/x-vrt', '', 'vrt', 0),
(829, 'xgl/drawing', '', 'xgz', 0),
(830, 'xgl/movie', '', 'xmz', 0),
(831, 'audio/x-ms-wma', 'Windows Media Audio', 'wma', 0),
(832, 'video/x-ms-wmv', 'Windows Media Video', 'wmv', 0),
(833, 'text/x-script.php', 'PHP Script', 'php', 0),
(834, 'application/x-http-php', 'PHP Script', 'php', 0),
(835, 'application/x-httpd-php-source', 'PHP Source File', 'phps', 0),
(836, 'application/xhtml+xml', '', 'xhtml', 0),
(837, 'text/plain', '', 'php', 673),
(838, 'audio/amr', '', 'amr', 0),
(839, 'audio/amr-wb', '', 'awb', 0),
(840, 'audio/aac', '', 'aac', 0),
(841, 'application/x-smaf', '', 'mmf', 0),
(842, 'audio/3gpp', '', '3gp', 0),
(843, 'audio/3gpp', '', '3gpp', 842),
(844, 'video/3gpp', '', '3gp', 0),
(845, 'audio/mp4', '', 'mp4', 0),
(846, 'audio/mp4-latm', '', 'mp4', 0),
(847, 'audio/x-mp4', '', 'mp4', 845),
(848, 'audio/mp4', '', 'm4a', 845),
(849, 'application/x-beatnik', '', 'rmf', 0),
(850, 'application/beatnik', '', 'rmf', 849);

-- --------------------------------------------------------

--
-- Table structure for table `motd`
--

CREATE TABLE IF NOT EXISTS `motd` (
  `motdID` int(10) NOT NULL AUTO_INCREMENT,
  `userID` int(10) NOT NULL,
  `lastEditedBy` int(10) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `createDate` datetime NOT NULL,
  `updateDate` datetime NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`motdID`),
  KEY `userID` (`userID`),
  KEY `active` (`active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `motd`
--

