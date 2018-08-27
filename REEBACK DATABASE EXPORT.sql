-- ALL RIGHTS RESERVED. MIMIDOTS@2018
-- Server version: 10.1.26-MariaDB
-- PHP Version: 7.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+03:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `reeback`
--
CREATE DATABASE IF NOT EXISTS `reeback` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `reeback`;

-- --------------------------------------------------------

--
-- Table structure for table `accntstype`
--

CREATE TABLE IF NOT EXISTS `accntstype` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `rangeCode` varchar(11) NOT NULL,
  `dateRecCreated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `accntstype`
--

INSERT INTO `accntstype` (`Id`, `name`, `rangeCode`, `dateRecCreated`) VALUES
(1, 'Assets', '1000-1999', '2018-06-08 21:46:57'),
(2, 'Current Assests', '1100-1199', '2018-06-08 21:46:57'),
(3, 'Fixed-assets', '1200-1299', '2018-06-08 21:46:57'),
(4, 'Liabilities', '2000-2999', '2018-06-08 21:46:57'),
(5, 'Current Liabilities', '2100-2199', '2018-06-08 21:46:57'),
(6, 'Long term liabilities', '2200-2299', '2018-06-08 21:46:57'),
(7, 'Equity', '3000-3999', '2018-06-08 21:46:57'),
(8, 'Income', '4000-4999', '2018-06-08 21:46:57'),
(9, 'Expenses', '5000-5999', '2018-06-08 21:46:57'),
(10, 'Other', '6000-6999', '2018-06-08 21:46:57');

-- --------------------------------------------------------

--
-- Table structure for table `budget`
--

CREATE TABLE IF NOT EXISTS `budget` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `budgetCode` varchar(100) NOT NULL,
  `financialYear` varchar(50) NOT NULL,
  `amount` double NOT NULL,
  `departmentId` int(11) NOT NULL,
  `account` varchar(250) NOT NULL,
  `accntId` int(11) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `dateRecCreated` datetime NOT NULL,
  `dateRecUpdated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `budgetCode` (`budgetCode`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `budget`
--

INSERT INTO `budget` (`id`, `budgetCode`, `financialYear`, `amount`, `departmentId`, `account`, `accntId`, `description`, `dateRecCreated`, `dateRecUpdated`) VALUES
(1, '201720181', '2017-2018', 150000, 1, 'Imprest Funds', 5000, NULL, '2018-06-11 17:05:58', NULL),
(2, '201720185', '2017-2018', 130000, 5, 'Imprest Funds', 5000, NULL, '2018-06-11 17:06:10', NULL),
(3, '201720184', '2017-2018', 140000, 4, 'Imprest Funds', 5000, NULL, '2018-06-11 17:06:37', NULL),
(4, '201720183', '2017-2018', 160000, 3, 'Imprest Funds', 5000, NULL, '2018-06-11 17:06:50', NULL),
(5, '201720182', '2017-2018', 250000, 2, 'Imprest Funds', 5000, NULL, '2018-06-11 17:07:01', NULL),
(6, '201720187', '2017-2018', 350000, 7, 'Imprest Funds', 5000, NULL, '2018-06-11 17:07:11', NULL),
(7, '201720188', '2017-2018', 80000, 8, 'Imprest Funds', 5000, NULL, '2018-06-11 17:10:37', NULL),
(8, '201720189', '2017-2018', 160000, 9, 'Imprest Funds', 5000, NULL, '2018-06-11 17:10:53', NULL),
(9, '2017201810', '2017-2018', 110000, 10, 'Imprest Funds', 5000, NULL, '2018-06-11 17:11:19', NULL),
(10, '2017201811', '2017-2018', 120000, 11, 'Imprest Funds', 5000, NULL, '2018-06-11 17:11:34', NULL),
(11, '2017201813', '2017-2018', 360000, 13, 'Imprest Funds', 5000, NULL, '2018-06-11 17:11:58', NULL),
(12, '2017201814', '2017-2018', 100000, 14, 'Imprest Funds', 5000, NULL, '2018-06-11 17:12:14', NULL),
(13, '2017201815', '2017-2018', 120000, 15, 'Imprest Funds', 5000, NULL, '2018-06-11 17:13:14', NULL),
(14, '2017201816', '2017-2018', 240000, 16, 'Imprest Funds', 5000, NULL, '2018-06-11 17:13:27', NULL),
(15, '201720186', '2017-2018', 40000, 6, 'Imprest Funds', 5000, NULL, '2018-06-11 17:13:47', NULL),
(16, '2017201812', '2017-2018', 120000, 12, 'Imprest Funds', 5000, NULL, '2018-06-11 17:13:58', NULL);

-- --------------------------------------------------------

--
-- Stand-in structure for view `budgetoverview`
-- (See below for the actual view)
--
CREATE TABLE IF NOT EXISTS `budgetoverview` (
`budgetCode` varchar(100)
,`financialYear` varchar(50)
,`amount` double
,`departmentId` int(11)
,`name` varchar(250)
,`account` varchar(250)
,`accntId` int(11)
,`description` varchar(250)
,`dateRecCreated` datetime
,`dateRecUpdated` datetime
);

-- --------------------------------------------------------

--
-- Table structure for table `chartofaccnts`
--

CREATE TABLE IF NOT EXISTS `chartofaccnts` (
  `id` varchar(200) NOT NULL,
  `accntCode` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `accntTypeId` int(11) NOT NULL,
  `description` varchar(250) NOT NULL,
  `parentAccnt` int(11) DEFAULT NULL COMMENT 'Parent account Code if any',
  `departmentId` int(11) DEFAULT NULL,
  `dateRecCreated` datetime NOT NULL,
  `dateRecUpdated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `accntCode` (`accntCode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `chartofaccnts`
--

INSERT INTO `chartofaccnts` (`id`, `accntCode`, `name`, `accntTypeId`, `description`, `parentAccnt`, `departmentId`, `dateRecCreated`, `dateRecUpdated`) VALUES
('b9fe032', 6000, 'Error Account', 9, 'Error account to catch unbalanced transactions', NULL, 0, '2018-06-10 12:35:25', NULL),
('64760f7', 5003, 'ICT Department Imprest', 1, 'ICT Department Imprest account', 5000, 5, '2018-06-11 13:41:29', NULL),
('cc39b2e', 5017, 'Expense Account', 9, 'All Expenses Account', 6000, 0, '2018-06-14 12:01:55', NULL),
('bdd99c0', 5001, 'Vice Chancellor department Imprest', 1, 'Office of the Vice Chancellor department imprest account', 5000, 1, '2018-06-11 13:39:18', NULL),
('991ef50', 5000, 'Imprest Account', 1, 'Manages all imprest transactions', 5000, 0, '2018-06-11 13:34:08', NULL),
('daa2c5b', 5004, 'Internal Audit Department Imprest', 1, 'Internal Audit Department imprest Account', 5000, 4, '2018-06-11 13:41:56', NULL),
('647bdd2', 5005, 'ASA department Imprest', 1, 'Academics & Student Affairs department Imprest Account', 5000, 3, '2018-06-11 13:43:44', NULL),
('f95c59e', 5006, 'AP&F department Imprest', 1, 'Administration, Finance & Planning imprest account', 5000, 2, '2018-06-11 13:44:44', NULL),
('940781e', 5007, 'Finance Department Imprest', 1, 'Finance Department Imprest account', 5000, 7, '2018-06-11 13:45:25', NULL),
('4dcebc9', 5008, 'Medical Department Imprest', 1, 'Medical Department Imprest account', 5000, 8, '2018-06-11 14:07:32', NULL),
('ab79b01', 5016, 'Estates & Transport Department Imprest', 1, 'Estates & Transport Department Imprest account', 5000, 9, '2018-06-11 14:07:59', NULL),
('bb6b4e1', 5009, 'Procurement & Supplies Department Imprest', 1, 'Procurement & Supplies Department Imprest Account', 5000, 10, '2018-06-11 14:08:50', NULL),
('3054c52', 5010, 'Planning & Development Department Imprest', 1, 'Planning & Development Department Imprest Account', 5000, 11, '2018-06-11 14:09:31', NULL),
('6b590f9', 5002, 'Academics Affairs Department Imprest', 1, 'Academics Affairs Department Imprest Account', 5000, 13, '2018-06-11 14:10:03', NULL),
('cc5945e', 5012, 'Library Department Imprest Account', 1, 'Library Department	 Imprest Account', 5000, 14, '2018-06-11 14:10:23', NULL),
('eec872b', 5013, 'Research & Extension Department Imprest', 1, 'Research &amp;amp; Extension Department Imprest Account', 5000, 15, '2018-06-11 14:10:44', NULL),
('619a31c', 5014, 'Student Affairs Department Imprest', 1, 'Student Affairs Department Imprest Account', 5000, 16, '2018-06-11 14:11:06', NULL),
('3710297', 5011, 'Farm Department Imprest', 1, 'Farm Department Imprest Account', 5000, 6, '2018-06-11 14:11:30', NULL),
('35b9751', 5015, 'Administration Department Imprest', 1, 'Administration Department Imprest Account', 5000, 12, '2018-06-11 14:11:55', NULL),
('714e157', 1100, 'Cash Account', 2, 'Cash at hand account', 6000, NULL, '2018-06-17 14:34:43', NULL),
('80efd93', 1101, 'Vice Chancellor department Cash Account', 2, 'Vice Chancellor department Cash Account', 1100, 1, '2018-06-17 14:47:58', NULL),
('d74ec17', 1102, 'ICT Department Cash Account', 2, 'ICT Department Cash Account', 1100, 5, '2018-06-17 14:48:22', NULL),
('177c2dd', 1103, 'Internal Audit Department Cash Account', 2, 'Internal Audit Department Cash Account', 1100, 4, '2018-06-17 14:48:48', NULL),
('7182119', 1104, 'ASA department Cash Account', 2, 'Academics & Student Affairs department Cash Account', 1100, 3, '2018-06-17 14:49:28', NULL),
('d9aa9d1', 1105, 'AP&F department Cash Account', 2, 'Administration, Finance & Planning department Cash Account', 1100, 2, '2018-06-17 14:50:08', NULL),
('51d7c45', 1106, 'Finance Department Cash Account', 2, 'Finance Department Cash Account', 1100, 7, '2018-06-17 14:50:39', NULL),
('a78bd1d', 1107, 'Medical Department Cash Account', 2, 'Medical Department Cash Account', 1100, 8, '2018-06-17 15:00:42', NULL),
('db9831e', 1108, 'Estates & Transport Department Cash Account', 2, 'Estates & Transport Department Cash Account', 1100, 9, '2018-06-17 15:01:10', NULL),
('c74e83d', 1109, 'Procurement & Supplies Department Cash Account', 2, 'Procurement & Supplies Department Cash Account', 1100, 10, '2018-06-17 15:01:34', NULL),
('0c37863', 1110, 'Planning & Development Department Cash Account', 2, 'Planning & Development Department Cash Account', 1100, 11, '2018-06-17 15:02:01', NULL),
('b3e9873', 1111, 'Academics Affairs Department Cash Account', 2, 'Academics Affairs Department Cash Account', 1100, 13, '2018-06-17 15:02:30', NULL),
('fe0184e', 1112, 'Library Department Cash Account', 2, 'Library Department Cash Account', 1100, 14, '2018-06-17 15:02:53', NULL),
('784fe9b', 1113, 'Research & Extension Department  Cash Account', 2, 'Research & Extension Department  Cash Account', 1100, 15, '2018-06-17 15:03:19', NULL),
('adb528f', 1114, 'Student Affairs Department Cash Account', 2, 'Student Affairs Department Cash Account', 1100, 16, '2018-06-17 15:03:48', NULL),
('e44f392', 1115, 'Farm Department Cash Account', 2, 'Farm Department Cash Account', 1100, 6, '2018-06-17 15:04:22', NULL),
('1ed0258', 1116, 'Administration Department Cash Account', 2, 'Administration Department Cash Account', 1100, 12, '2018-06-17 15:04:51', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE IF NOT EXISTS `departments` (
  `departmentId` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `parentDepartment` int(11) NOT NULL,
  `dateRecCreated` date NOT NULL,
  PRIMARY KEY (`departmentId`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`departmentId`, `name`, `parentDepartment`, `dateRecCreated`) VALUES
(1, 'Office of the Vice Chancellor', 0, '2018-06-04'),
(5, 'ICT Department', 1, '2018-06-04'),
(4, 'Internal Audit Department', 1, '2018-06-04'),
(3, 'Academics & Student Affairs Department', 1, '2018-06-04'),
(2, 'Administration, Finance & Planning', 1, '2018-06-04'),
(7, 'Finance Department', 2, '2018-06-04'),
(8, 'Medical Department', 2, '2018-06-04'),
(9, 'Estates & Transport Department', 2, '2018-06-04'),
(10, 'Procurement & Supplies Department', 2, '2018-06-04'),
(11, 'Planning & Development Department', 2, '2018-06-04'),
(13, 'Academics Affairs Department', 3, '2018-06-04'),
(14, 'Library Department', 3, '2018-06-04'),
(15, 'Research & Extension Department', 3, '2018-06-04'),
(16, 'Student Affairs Department', 3, '2018-06-04'),
(6, 'Farm Department', 2, '2018-06-04'),
(12, 'Administration Department', 2, '2018-06-04');

-- --------------------------------------------------------

--
-- Table structure for table `financialyears`
--

CREATE TABLE IF NOT EXISTS `financialyears` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `financialYear` varchar(20) NOT NULL,
  `startdate` date NOT NULL,
  `enddate` date NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `dateRecCreated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `financialYear` (`financialYear`),
  UNIQUE KEY `startdate` (`startdate`),
  UNIQUE KEY `enddate` (`enddate`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `financialyears`
--

INSERT INTO `financialyears` (`id`, `financialYear`, `startdate`, `enddate`, `status`, `dateRecCreated`) VALUES
(1, '2017-2018', '2017-11-30', '2018-11-29', 1, '2018-06-11 17:03:44'),
(2, '2018-2019', '2018-12-31', '2019-12-30', 0, '2018-06-11 20:29:26');

-- --------------------------------------------------------

--
-- Table structure for table `genledger`
--

CREATE TABLE IF NOT EXISTS `genledger` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `financialYear` varchar(30) NOT NULL,
  `accntCode` int(11) NOT NULL,
  `credit` double DEFAULT NULL,
  `debit` double DEFAULT NULL,
  `runningBal` double NOT NULL,
  `transactionDate` date NOT NULL,
  `details` varchar(100) NOT NULL,
  `dateRecCreated` datetime NOT NULL,
  `dateRecUpdated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `genledger`
--

INSERT INTO `genledger` (`id`, `financialYear`, `accntCode`, `credit`, `debit`, `runningBal`, `transactionDate`, `details`, `dateRecCreated`, `dateRecUpdated`) VALUES
(1, '2017-2018', 5001, NULL, 150000, 150000, '2018-06-22', 'Start of year initializing', '2018-06-22 14:45:04', NULL),
(2, '2017-2018', 1101, 0, NULL, 0, '2018-06-22', '', '2018-06-22 14:45:04', NULL),
(3, '2017-2018', 5003, NULL, 130000, 130000, '2018-06-22', 'Start of year initializing', '2018-06-22 14:45:04', NULL),
(4, '2017-2018', 1102, 0, NULL, 0, '2018-06-22', '', '2018-06-22 14:45:04', NULL),
(5, '2017-2018', 5004, NULL, 140000, 140000, '2018-06-22', 'Start of year initializing', '2018-06-22 14:45:04', NULL),
(6, '2017-2018', 1103, 0, NULL, 0, '2018-06-22', '', '2018-06-22 14:45:04', NULL),
(7, '2017-2018', 5005, NULL, 160000, 160000, '2018-06-22', 'Start of year initializing', '2018-06-22 14:45:04', NULL),
(8, '2017-2018', 1104, 0, NULL, 0, '2018-06-22', '', '2018-06-22 14:45:04', NULL),
(9, '2017-2018', 5006, NULL, 250000, 250000, '2018-06-22', 'Start of year initializing', '2018-06-22 14:45:04', NULL),
(10, '2017-2018', 1105, 0, NULL, 0, '2018-06-22', '', '2018-06-22 14:45:04', NULL),
(11, '2017-2018', 5007, NULL, 350000, 350000, '2018-06-22', 'Start of year initializing', '2018-06-22 14:45:04', NULL),
(12, '2017-2018', 1106, 0, NULL, 0, '2018-06-22', '', '2018-06-22 14:45:04', NULL),
(13, '2017-2018', 5008, NULL, 80000, 80000, '2018-06-22', 'Start of year initializing', '2018-06-22 14:45:04', NULL),
(14, '2017-2018', 1107, 0, NULL, 0, '2018-06-22', '', '2018-06-22 14:45:04', NULL),
(15, '2017-2018', 5016, NULL, 160000, 160000, '2018-06-22', 'Start of year initializing', '2018-06-22 14:45:04', NULL),
(16, '2017-2018', 1108, 0, NULL, 0, '2018-06-22', '', '2018-06-22 14:45:04', NULL),
(17, '2017-2018', 5009, NULL, 110000, 110000, '2018-06-22', 'Start of year initializing', '2018-06-22 14:45:04', NULL),
(18, '2017-2018', 1109, 0, NULL, 0, '2018-06-22', '', '2018-06-22 14:45:04', NULL),
(19, '2017-2018', 5010, NULL, 120000, 120000, '2018-06-22', 'Start of year initializing', '2018-06-22 14:45:04', NULL),
(20, '2017-2018', 1110, 0, NULL, 0, '2018-06-22', '', '2018-06-22 14:45:04', NULL),
(21, '2017-2018', 5002, NULL, 360000, 360000, '2018-06-22', 'Start of year initializing', '2018-06-22 14:45:04', NULL),
(22, '2017-2018', 1111, 0, NULL, 0, '2018-06-22', '', '2018-06-22 14:45:04', NULL),
(23, '2017-2018', 5012, NULL, 100000, 100000, '2018-06-22', 'Start of year initializing', '2018-06-22 14:45:04', NULL),
(24, '2017-2018', 1112, 0, NULL, 0, '2018-06-22', '', '2018-06-22 14:45:04', NULL),
(25, '2017-2018', 5013, NULL, 120000, 120000, '2018-06-22', 'Start of year initializing', '2018-06-22 14:45:05', NULL),
(26, '2017-2018', 1113, 0, NULL, 0, '2018-06-22', '', '2018-06-22 14:45:05', NULL),
(27, '2017-2018', 5014, NULL, 240000, 240000, '2018-06-22', 'Start of year initializing', '2018-06-22 14:45:05', NULL),
(28, '2017-2018', 1114, 0, NULL, 0, '2018-06-22', '', '2018-06-22 14:45:05', NULL),
(29, '2017-2018', 5011, NULL, 40000, 40000, '2018-06-22', 'Start of year initializing', '2018-06-22 14:45:05', NULL),
(30, '2017-2018', 1115, 0, NULL, 0, '2018-06-22', '', '2018-06-22 14:45:05', NULL),
(31, '2017-2018', 5015, NULL, 120000, 120000, '2018-06-22', 'Start of year initializing', '2018-06-22 14:45:05', NULL),
(32, '2017-2018', 1116, 0, NULL, 0, '2018-06-22', '', '2018-06-22 14:45:05', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `imprestrequests`
--

CREATE TABLE IF NOT EXISTS `imprestrequests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ImprestId` varchar(200) NOT NULL,
  `financialYear` varchar(30) NOT NULL,
  `departmentId` int(11) NOT NULL,
  `screenId` varchar(200) NOT NULL COMMENT 'requesting staff id',
  `description` text NOT NULL,
  `itinerary` text,
  `est` int(11) NOT NULL COMMENT 'estomated days away',
  `amount` double NOT NULL,
  `attachmentsAvail` tinyint(4) NOT NULL COMMENT '1:attachment available, 0:no attachment',
  `expectedDate` date NOT NULL,
  `surrenderDate` date NOT NULL,
  `dateRecCreated` datetime NOT NULL,
  PRIMARY KEY (`ImprestId`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `imprestrequests`
--

INSERT INTO `imprestrequests` (`id`, `ImprestId`, `financialYear`, `departmentId`, `screenId`, `description`, `itinerary`, `est`, `amount`, `attachmentsAvail`, `expectedDate`, `surrenderDate`, `dateRecCreated`) VALUES
(1, 'IMPR001', '2017-2018', 14, '9e495de39', 'I hereby request for said amount to facilitate academic trip to mombasa by Fourth year students pursuing Bsc. Software Engineering from 1st July 2018 to 6th July 2018.', '', 0, 45000, 1, '2018-07-01', '2018-07-09', '2018-06-19 13:36:48'),
(2, 'IMPR02', '2017-2018', 13, '2cb319377', 'If you are reading this guide, it is likely that your organization has already decided to build a data warehouse. Moreover, it is likely that the business requirements are already defined, the scope of your application has been agreed upon, and you have a conceptual design. So now you need to translate your requirements into a system deliverable. In this step, you create the logical and physical design for the data warehouse and, in the process, define the specific data content', '', 1, 20000, 1, '2018-06-25', '2018-06-28', '2018-06-19 20:08:27'),
(3, 'IMPR03', '2017-2018', 13, '2cb319377', 'This compatibility table still uses the old format, because we haven\'t yet converted the data it contains.', '', 2, 20000, 1, '2018-06-25', '2018-06-28', '2018-06-19 20:55:02'),
(4, 'IMPR04', '2017-2018', 13, 'f18067017', 'Iâ€™m an expert on how technology hijacks our psychological vulnerabilities. Thatâ€™s why I spent the last three years as a Design Ethicist at Google caring about how to design things', '', 2, 20000, 1, '2018-06-25', '2018-06-28', '2018-06-21 19:05:37');

-- --------------------------------------------------------

--
-- Table structure for table `imprestsurrender`
--

CREATE TABLE IF NOT EXISTS `imprestsurrender` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `refId` varchar(100) NOT NULL,
  `screenId` varchar(100) NOT NULL,
  `imprestId` varchar(100) NOT NULL,
  `amountAssigned` double NOT NULL,
  `amountRefunding` double NOT NULL DEFAULT '0',
  `amountClaiming` double NOT NULL DEFAULT '0',
  `attachmentUrl` varchar(250) NOT NULL,
  `dateRecCreated` datetime NOT NULL,
  PRIMARY KEY (`refId`),
  UNIQUE KEY `imprestId` (`imprestId`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `imprestsurrender`
--

INSERT INTO `imprestsurrender` (`id`, `refId`, `screenId`, `imprestId`, `amountAssigned`, `amountRefunding`, `amountClaiming`, `attachmentUrl`, `dateRecCreated`) VALUES
(1, 'IMPRB978DD9', '9e495de39', 'IMPR001', 45000, 0, 0, 'IMPRB978DD9.pdf', '2018-06-19 15:45:45'),
(3, 'IMPR2735EA5', 'f18067017', 'IMPR04', 15000, 0, 0, 'IMPR2735EA5.pdf', '2018-06-22 10:12:21');

-- --------------------------------------------------------

--
-- Stand-in structure for view `ledgeraccntdetails`
-- (See below for the actual view)
--
CREATE TABLE IF NOT EXISTS `ledgeraccntdetails` (
`departmentId` int(11)
,`financialYear` varchar(30)
,`parentaccnt` int(11)
,`accntCode` int(11)
,`accntTypeId` int(11)
,`name` varchar(250)
,`credit` double
,`debit` double
,`runningBal` double
,`transactionDate` date
,`transacdetails` varchar(100)
,`dateRecCreated` datetime
,`dateRecUpdated` datetime
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `requestdetails`
-- (See below for the actual view)
--
CREATE TABLE IF NOT EXISTS `requestdetails` (
`id` int(11)
,`financialYear` varchar(30)
,`ImprestId` varchar(200)
,`departmentId` int(11)
,`applicant` varchar(200)
,`description` text
,`itinerary` text
,`est` int(11)
,`initAmount` double
,`attachmentsAvail` tinyint(4)
,`expectedDate` date
,`surrenderDate` date
,`amntApproved` double
,`designee` varchar(200)
,`comments` text
,`status` int(11)
,`requestLevel` int(11)
,`dateRequested` datetime
,`dateProcessed` datetime
);

-- --------------------------------------------------------

--
-- Table structure for table `requestsapprovals`
--

CREATE TABLE IF NOT EXISTS `requestsapprovals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `imprestId` varchar(200) NOT NULL,
  `amount` double NOT NULL COMMENT 'amount approved in previous stage',
  `screenId` varchar(200) NOT NULL COMMENT 'the next approving staff ',
  `comments` text,
  `status` int(11) NOT NULL COMMENT '0:pending 1:approved 2:rejected 3:cancelled 4:complete',
  `requestLevel` int(11) NOT NULL COMMENT 'at which level is the request ',
  `dateRecCreated` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `requestsapprovals`
--

INSERT INTO `requestsapprovals` (`id`, `imprestId`, `amount`, `screenId`, `comments`, `status`, `requestLevel`, `dateRecCreated`) VALUES
(1, 'IMPR001', 45000, '840b580a7', NULL, 0, 1, '2018-06-19 13:36:48'),
(2, 'IMPR001', 45000, '1cd266910', '', 1, 2, '2018-06-19 13:55:46'),
(3, 'IMPR001', 45000, '1d11881df', '', 1, 3, '2018-06-19 13:56:35'),
(4, 'IMPR001', 45000, '970641fc6', '', 1, 4, '2018-06-19 14:00:34'),
(5, 'IMPR001', 45000, 'c8d1852e0', '', 1, 5, '2018-06-19 14:01:16'),
(6, 'IMPR001', 45000, '', '', 4, 5, '2018-06-19 14:01:45'),
(7, 'IMPR02', 20000, 'a38e22adf', NULL, 0, 1, '2018-06-19 20:08:27'),
(8, 'IMPR02', 21000, '1cd266910', '', 1, 2, '2018-06-19 20:11:00'),
(9, 'IMPR02', 21000, '1d11881df', '', 1, 3, '2018-06-19 20:11:30'),
(10, 'IMPR02', 20000, '970641fc6', '', 1, 4, '2018-06-19 20:12:02'),
(11, 'IMPR02', 20000, 'c8d1852e0', '', 1, 5, '2018-06-19 20:13:20'),
(13, 'IMPR03', 20000, 'a38e22adf', NULL, 0, 1, '2018-06-19 20:55:02'),
(14, 'IMPR03', 20000, '1cd266910', '', 1, 2, '2018-06-19 20:55:56'),
(15, 'IMPR03', 20000, '1d11881df', 'Similar request is already in process.', 2, 3, '2018-06-19 20:56:55'),
(16, 'IMPR04', 20000, 'a38e22adf', NULL, 0, 1, '2018-06-21 19:05:37'),
(17, 'IMPR04', 20000, '1cd266910', 'Ok I approve', 1, 2, '2018-06-21 19:08:42'),
(18, 'IMPR04', 15000, '1d11881df', '', 1, 3, '2018-06-21 19:09:48'),
(19, 'IMPR04', 15000, '970641fc6', '', 1, 4, '2018-06-21 19:10:56'),
(20, 'IMPR04', 15000, 'c8d1852e0', '', 1, 5, '2018-06-21 19:12:29'),
(26, 'IMPR04', 15000, '', '', 4, 5, '2018-06-22 10:10:31');

-- --------------------------------------------------------

--
-- Stand-in structure for view `requeststatusdetails`
-- (See below for the actual view)
--
CREATE TABLE IF NOT EXISTS `requeststatusdetails` (
`id` int(11)
,`financialYear` varchar(30)
,`ImprestId` varchar(200)
,`departmentId` int(11)
,`applicant` varchar(200)
,`description` text
,`itinerary` text
,`est` int(11)
,`initAmount` double
,`attachmentsAvail` tinyint(4)
,`expectedDate` date
,`surrenderDate` date
,`amntApproved` double
,`designee` varchar(200)
,`comments` text
,`status` int(11)
,`requestLevel` int(11)
,`dateRequested` datetime
,`dateProcessed` datetime
);

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE IF NOT EXISTS `staff` (
  `screenId` varchar(100) NOT NULL,
  `staffnumber` varchar(200) NOT NULL,
  `firstname` varchar(250) NOT NULL,
  `lastname` varchar(250) NOT NULL,
  `surname` varchar(250) NOT NULL,
  `departmentId` int(11) NOT NULL,
  `roleId` int(11) NOT NULL,
  `active` tinyint(4) NOT NULL,
  `dateRecCreated` date NOT NULL,
  `dateRecUpdated` date DEFAULT NULL,
  PRIMARY KEY (`screenId`),
  UNIQUE KEY `staffnumber` (`staffnumber`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`screenId`, `staffnumber`, `firstname`, `lastname`, `surname`, `departmentId`, `roleId`, `active`, `dateRecCreated`, `dateRecUpdated`) VALUES
('1cd266910', '94823', 'Simon', 'Muturi', 'Mwangi', 7, 3, 1, '2018-06-12', NULL),
('045e9382c', '34556', 'Janet', 'Odhiambo', 'Otieno', 5, 1, 1, '2018-06-12', NULL),
('78415d6b1', '738847', 'Collins', 'Akama', 'Odhiambo', 1, 2, 1, '2018-06-12', NULL),
('d18a2d920', '877473', 'David', 'Kamaa', 'Gregory', 5, 2, 1, '2018-06-12', NULL),
('2e5c46f6c', '37745', 'Kelvin', 'Kiprop', 'Kiptum', 4, 2, 1, '2018-06-12', NULL),
('b95329281', '354345', 'Peter', 'Ngechu', 'Sarah', 3, 2, 1, '2018-06-12', NULL),
('970641fc6', '56778', 'Ann', 'Harper', 'Leona', 2, 2, 1, '2018-06-12', NULL),
('c8d1852e0', '43556', 'Nina', 'Douglas', 'McGuire', 7, 2, 1, '2018-06-12', NULL),
('639f266d7', '315876', 'Ricardo', 'Nellie', 'Okello', 8, 2, 1, '2018-06-12', NULL),
('93475f809', '237734', 'Francis', 'Mwangi', 'Nyambura', 9, 2, 1, '2018-06-12', NULL),
('35bcb1ba4', '27364', 'Catherine', 'Nyawira', 'Murage', 10, 2, 1, '2018-06-12', NULL),
('d6f7842d0', '84763', 'George', 'Orina', 'I', 11, 2, 1, '2018-06-12', NULL),
('a38e22adf', '84637', 'Joseph', 'Osida', 'Kiprono', 13, 2, 1, '2018-06-12', NULL),
('840b580a7', '34784', 'Chris', 'Rutto', 'Rono', 14, 2, 1, '2018-06-12', NULL),
('3b700c9a4', '39848', 'Brian', 'Rono', 'Kiplimo', 15, 2, 1, '2018-06-12', NULL),
('37072737b', '37662', 'Okemwa', 'Joseph', 'Maina', 16, 2, 1, '2018-06-12', NULL),
('950db7dab', '873763', 'Asbel', 'Kiprop', 'Cherotich', 6, 2, 1, '2018-06-12', NULL),
('7b65a57cd', '38472', 'Peter', 'Russell', 'Griffith', 12, 2, 1, '2018-06-12', NULL),
('1d11881df', '942232', 'Donald', 'Harvey', 'Blake', 7, 4, 1, '2018-06-12', NULL),
('2cb319377', '12345', 'Steve', 'Mararo', 'Mworia', 13, 1, 1, '2018-06-14', NULL),
('9e495de39', '09876', 'Mike', 'Ngotho', 'Masila', 14, 1, 1, '2018-06-14', '2018-06-21'),
('5081d23bb', '1217853', 'Justin', 'Mumo', 'Masila', 5, 1, 1, '2018-06-14', NULL),
('f18067017', '1234567', 'Lindah', 'Tulah', 'Cecelia', 13, 1, 1, '2018-06-21', NULL),
('e3a83ae14', '88888', 'Engineer', 'Vincent', 'Omollo', 15, 1, 1, '2018-06-22', NULL);

-- --------------------------------------------------------

--
-- Stand-in structure for view `staffdetails`
-- (See below for the actual view)
--
CREATE TABLE IF NOT EXISTS `staffdetails` (
`screenId` varchar(100)
,`staffnumber` varchar(200)
,`firstname` varchar(250)
,`lastname` varchar(250)
,`surname` varchar(250)
,`departmentId` int(11)
,`department` varchar(250)
,`parentDepartment` int(11)
,`roleId` int(11)
,`role` varchar(250)
,`active` tinyint(4)
,`dateRecCreated` date
,`dateRecUpdated` date
);

-- --------------------------------------------------------

--
-- Table structure for table `surrenderapprovals`
--

CREATE TABLE IF NOT EXISTS `surrenderapprovals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `refId` varchar(100) NOT NULL,
  `screenId` varchar(100) NOT NULL,
  `comments` text,
  `status` tinyint(4) NOT NULL,
  `requestLevel` tinyint(4) NOT NULL,
  `dateRecCreated` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `surrenderapprovals`
--

INSERT INTO `surrenderapprovals` (`id`, `refId`, `screenId`, `comments`, `status`, `requestLevel`, `dateRecCreated`) VALUES
(1, 'IMPRB978DD9', '840b580a7', NULL, 0, 1, '2018-06-19 15:45:45'),
(4, 'IMPR2735EA5', '1d11881df', '', 1, 2, '2018-06-22 10:15:17'),
(3, 'IMPR2735EA5', 'a38e22adf', NULL, 0, 1, '2018-06-22 10:12:21'),
(5, 'IMPR2735EA5', '78415d6b1', '', 1, 3, '2018-06-22 10:17:07'),
(6, 'IMPR2735EA5', '1cd266910', '', 1, 4, '2018-06-22 10:18:28'),
(7, 'IMPR2735EA5', 'c8d1852e0', '', 1, 5, '2018-06-22 10:19:35'),
(8, 'IMPR2735EA5', '', '', 4, 5, '2018-06-22 12:59:46');

-- --------------------------------------------------------

--
-- Stand-in structure for view `surrenderdetails`
-- (See below for the actual view)
--
CREATE TABLE IF NOT EXISTS `surrenderdetails` (
`id` int(11)
,`refId` varchar(100)
,`imprestId` varchar(100)
,`applicant` varchar(100)
,`amountAssigned` double
,`amountRefunding` double
,`amountClaiming` double
,`attachmentUrl` varchar(250)
,`designee` varchar(100)
,`comments` text
,`status` tinyint(4)
,`requestLevel` tinyint(4)
,`dateRequested` datetime
,`dateprocessed` datetime
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `surrenderstatusdetails`
-- (See below for the actual view)
--
CREATE TABLE IF NOT EXISTS `surrenderstatusdetails` (
`id` int(11)
,`refId` varchar(100)
,`imprestId` varchar(100)
,`applicant` varchar(100)
,`amountAssigned` double
,`amountRefunding` double
,`amountClaiming` double
,`attachmentUrl` varchar(250)
,`designee` varchar(100)
,`comments` text
,`status` tinyint(4)
,`requestLevel` tinyint(4)
,`dateRequested` datetime
,`dateprocessed` datetime
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `temprequeststracker`
-- (See below for the actual view)
--
CREATE TABLE IF NOT EXISTS `temprequeststracker` (
`id` int(11)
,`imprestId` varchar(200)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `tempsurrendertracker`
-- (See below for the actual view)
--
CREATE TABLE IF NOT EXISTS `tempsurrendertracker` (
`id` int(11)
,`refId` varchar(100)
);

-- --------------------------------------------------------

--
-- Table structure for table `userauth`
--

CREATE TABLE IF NOT EXISTS `userauth` (
  `screenId` varchar(100) NOT NULL,
  `username` varchar(250) NOT NULL,
  `privId` int(11) NOT NULL,
  `password` varchar(250) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `reset` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'require password reset?',
  `otp` varchar(10) DEFAULT NULL,
  `otpExpire` datetime DEFAULT NULL,
  `otrp` varchar(10) DEFAULT NULL,
  `otrpExpire` datetime DEFAULT NULL,
  `dateRecCreated` datetime NOT NULL,
  `dateRecUpdated` datetime DEFAULT NULL,
  PRIMARY KEY (`username`),
  UNIQUE KEY `screenId` (`screenId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `userauth`
--

INSERT INTO `userauth` (`screenId`, `username`, `privId`, `password`, `mobile`, `reset`, `otp`, `otpExpire`, `otrp`, `otrpExpire`, `dateRecCreated`, `dateRecUpdated`) VALUES
('045e9382c', '34556', 1, '$2y$10$JJr.PDptdZ4cw5rCdqXcfuTgJVXdPG.L3iJNGlShpcSXNDoTIxj/.', '+254704413577', 1, NULL, NULL, NULL, NULL, '2018-06-12 00:57:22', NULL),
('1cd266910', '94823', 2, '$2y$10$h2aROVOea8LbSiaoBkFQIOuVOmw/fw.EIDoqfjFHsZA84azIwpEGC', '+254799900445', 1, NULL, NULL, NULL, NULL, '2018-06-12 15:40:55', NULL),
('78415d6b1', '738847', 2, '$2y$10$T5YxQEZhALXwI04QqJA1wOq8QDjRgWRvQ9jrylA9MFbC7HkZ3Mf.u', '+254704413577', 1, NULL, NULL, NULL, NULL, '2018-06-12 07:58:18', NULL),
('d18a2d920', '877473', 2, '$2y$10$PNlw97Z27N.5LR5RRQJ06OLXuFNqR5k.Bx77AE9yMhT7sQbZaDwZ.', '+254793884899', 1, NULL, NULL, NULL, NULL, '2018-06-12 08:25:24', NULL),
('2e5c46f6c', '37745', 2, '$2y$10$Au1v7q8wYyCgZLJUNwbcfuHfmJTKpz/XzV8r/gpm5KCglbkNLm.xS', '+254799900445', 1, NULL, NULL, NULL, NULL, '2018-06-12 09:03:30', NULL),
('b95329281', '354345', 2, '$2y$10$DgPYnXepeM5WrFvoQW.tZOxrbX8zk6H.CRJAFBX3f/3ik0sayNmSS', '+254799900445', 1, NULL, NULL, NULL, NULL, '2018-06-12 09:06:12', NULL),
('970641fc6', '56778', 2, '$2y$10$gWclCLI1NE7fS0gmPZtd1enELS5WptmO5CftuavuDqtSQMWwq/bR.', '+254799900445', 1, NULL, NULL, NULL, NULL, '2018-06-12 09:07:12', NULL),
('c8d1852e0', '43556', 2, '$2y$10$BXU4y.cNq1d1Sngy6Aujn.bn.cdBFsVNDMN1v87/WM2T565eyk5OK', '+254799900445', 1, NULL, NULL, NULL, NULL, '2018-06-12 09:07:59', NULL),
('639f266d7', '315876', 2, '$2y$10$HehegknjgnIE2.DOxwPvvOSx5ixL2tBiRspjO2Foo03d91dj36bQy', '+254799900445', 1, NULL, NULL, NULL, NULL, '2018-06-12 09:09:09', NULL),
('93475f809', '237734', 2, '$2y$10$M7kL1cpssng82/mgQVvzOeH37KC/fxbaVdW2nXxQs/CUfgcyfZ9wy', '+254799900445', 1, NULL, NULL, NULL, NULL, '2018-06-12 09:15:06', NULL),
('35bcb1ba4', '27364', 2, '$2y$10$PnLgnzBLgeUjWuZBWi4Vq.MyCWLhkcJGDRmmKAunnntrhkiEQZR3m', '+254799900445', 1, NULL, NULL, NULL, NULL, '2018-06-12 09:15:40', NULL),
('d6f7842d0', '84763', 2, '$2y$10$A3XEFDHlesRFAJImFXyQpO5iRwt4QoL./UhtGMTf7rdpwBYguOyzS', '+254799900445', 1, NULL, NULL, NULL, NULL, '2018-06-12 09:16:31', NULL),
('a38e22adf', '84637', 2, '$2y$10$4JXevxmNR9W5TdgSDQYIZ.FbFox5zqKTK5iXBDeDivOKX1suV/JHK', '+254799900445', 1, NULL, NULL, NULL, NULL, '2018-06-12 09:17:29', NULL),
('840b580a7', '34784', 2, '$2y$10$UxsIT2JyytcitROe0sYqouJsfosRyHnXcs2QuoeAbhXnsTZSUJ6bW', '+254799900445', 1, NULL, NULL, NULL, NULL, '2018-06-12 09:18:40', NULL),
('3b700c9a4', '39848', 2, '$2y$10$NGEsLFvy92RMio1Jl1bCoO/XfleAoO4HPp1NweyclO4C193oReyFW', '+254799900445', 1, NULL, NULL, NULL, NULL, '2018-06-12 09:19:16', NULL),
('37072737b', '37662', 2, '$2y$10$aSQrxIB5eniAgVE4oYM3O.rhNiL89/c5Zc.YzHNwxFg8Gf.lPNLLa', '+254799900445', 1, NULL, NULL, NULL, NULL, '2018-06-12 09:19:59', NULL),
('950db7dab', '873763', 2, '$2y$10$.KAKx7ZDuysJD4p7PN3BRODBUUboBtStpez6Gtx4zLbRvDVTGjdb6', '+254799900445', 1, NULL, NULL, NULL, NULL, '2018-06-12 09:20:47', NULL),
('7b65a57cd', '38472', 2, '$2y$10$RCOVWvgsWHPzdxK7FNOo7.z/V2IGs5.TJ/pPNVkLVVxcxiU/HoiZy', '+254799900445', 1, NULL, NULL, NULL, NULL, '2018-06-12 09:21:18', NULL),
('20e8df16f', '403232', 2, '$2y$10$PY2u0zJ7b.CXv3DNgkLXEeC7DxWrZ.jmGVwguuhn4W3Sm4VR0pbeG', '+254799900445', 1, NULL, NULL, NULL, NULL, '2018-06-12 09:30:58', NULL),
('1d11881df', '942232', 2, '$2y$10$CYJym12pNwULtagKAHNFi.dcsTAaPOGj2IkoGPnXYjrrJEKZUEfe2', '+254799900445', 1, NULL, NULL, NULL, NULL, '2018-06-12 15:49:04', NULL),
('2cb319377', '12345', 2, '$2y$10$CW0RdVaTpTy0ZF3QJTaKauxMVU/Msd3wPVGRBUEJtAorAcLAeBayu', '+254799900445', 1, NULL, NULL, NULL, NULL, '2018-06-14 00:06:20', NULL),
('9e495de39', '09876', 2, '$2y$10$s//E/y66i2ZkZx5iyQs4rukMb02oOqwFVQc2I6lDazLS.WHpg9f9K', '+254799900445', 1, NULL, NULL, NULL, NULL, '2018-06-14 11:11:34', NULL),
('5081d23bb', '1217853', 2, '$2y$10$CHUZoT6W0RhDkoBv.OLUFeBxRGkuJwzbqnWwkw18HKtbVWHNnNmSe', '+254704936715', 1, NULL, NULL, NULL, NULL, '2018-06-14 11:36:18', NULL),
('f18067017', '1234567', 2, '$2y$10$2yurSIx9djod79WhyO9ltuRGiP/cWoTWAAlas3lkQw5ae4NNC4p0e', '+254799900445', 1, NULL, NULL, NULL, NULL, '2018-06-21 19:00:15', NULL),
('e3a83ae14', '88888', 2, '$2y$10$hcrlu4BWO2vXrRUpe/E0jOxJcoxjR.lfgR/c.dhf7.nEZHFJuLYD6', '+254725818924', 1, NULL, NULL, NULL, NULL, '2018-06-22 15:37:58', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `userroles`
--

CREATE TABLE IF NOT EXISTS `userroles` (
  `roleId` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `datRecCreated` date NOT NULL,
  PRIMARY KEY (`roleId`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `userroles`
--

INSERT INTO `userroles` (`roleId`, `name`, `datRecCreated`) VALUES
(1, 'regular staff', '2018-06-04'),
(2, 'Head Of Department', '2018-06-04'),
(3, 'Accountant Claimant', '2018-06-04'),
(4, 'Accountant Examiner', '2018-06-04'),
(5, 'HOD Secretary', '2018-06-04'),
(6, 'COD', '2018-06-04');

-- --------------------------------------------------------

--
-- Structure for view `budgetoverview`
--
DROP TABLE IF EXISTS `budgetoverview`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `budgetoverview`  AS  select `budget`.`budgetCode` AS `budgetCode`,`budget`.`financialYear` AS `financialYear`,`budget`.`amount` AS `amount`,`budget`.`departmentId` AS `departmentId`,`departments`.`name` AS `name`,`budget`.`account` AS `account`,`budget`.`accntId` AS `accntId`,`budget`.`description` AS `description`,`budget`.`dateRecCreated` AS `dateRecCreated`,`budget`.`dateRecUpdated` AS `dateRecUpdated` from (`budget` join `departments` on((`budget`.`departmentId` = `departments`.`departmentId`))) ;

-- --------------------------------------------------------

--
-- Structure for view `ledgeraccntdetails`
--
DROP TABLE IF EXISTS `ledgeraccntdetails`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `ledgeraccntdetails`  AS  select `chartofaccnts`.`departmentId` AS `departmentId`,`genledger`.`financialYear` AS `financialYear`,`chartofaccnts`.`parentAccnt` AS `parentaccnt`,`genledger`.`accntCode` AS `accntCode`,`chartofaccnts`.`accntTypeId` AS `accntTypeId`,`chartofaccnts`.`name` AS `name`,`genledger`.`credit` AS `credit`,`genledger`.`debit` AS `debit`,`genledger`.`runningBal` AS `runningBal`,`genledger`.`transactionDate` AS `transactionDate`,`genledger`.`details` AS `transacdetails`,`genledger`.`dateRecCreated` AS `dateRecCreated`,`genledger`.`dateRecUpdated` AS `dateRecUpdated` from (`genledger` join `chartofaccnts` on((`chartofaccnts`.`accntCode` = `genledger`.`accntCode`))) ;

-- --------------------------------------------------------

--
-- Structure for view `requestdetails`
--
DROP TABLE IF EXISTS `requestdetails`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `requestdetails`  AS  select `requestsapprovals`.`id` AS `id`,`imprestrequests`.`financialYear` AS `financialYear`,`imprestrequests`.`ImprestId` AS `ImprestId`,`imprestrequests`.`departmentId` AS `departmentId`,`imprestrequests`.`screenId` AS `applicant`,`imprestrequests`.`description` AS `description`,`imprestrequests`.`itinerary` AS `itinerary`,`imprestrequests`.`est` AS `est`,`imprestrequests`.`amount` AS `initAmount`,`imprestrequests`.`attachmentsAvail` AS `attachmentsAvail`,`imprestrequests`.`expectedDate` AS `expectedDate`,`imprestrequests`.`surrenderDate` AS `surrenderDate`,`requestsapprovals`.`amount` AS `amntApproved`,`requestsapprovals`.`screenId` AS `designee`,`requestsapprovals`.`comments` AS `comments`,`requestsapprovals`.`status` AS `status`,`requestsapprovals`.`requestLevel` AS `requestLevel`,`imprestrequests`.`dateRecCreated` AS `dateRequested`,`requestsapprovals`.`dateRecCreated` AS `dateProcessed` from (`imprestrequests` join `requestsapprovals` on((`imprestrequests`.`ImprestId` = `requestsapprovals`.`imprestId`))) ;

-- --------------------------------------------------------

--
-- Structure for view `requeststatusdetails`
--
DROP TABLE IF EXISTS `requeststatusdetails`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `requeststatusdetails`  AS  select `requestdetails`.`id` AS `id`,`requestdetails`.`financialYear` AS `financialYear`,`requestdetails`.`ImprestId` AS `ImprestId`,`requestdetails`.`departmentId` AS `departmentId`,`requestdetails`.`applicant` AS `applicant`,`requestdetails`.`description` AS `description`,`requestdetails`.`itinerary` AS `itinerary`,`requestdetails`.`est` AS `est`,`requestdetails`.`initAmount` AS `initAmount`,`requestdetails`.`attachmentsAvail` AS `attachmentsAvail`,`requestdetails`.`expectedDate` AS `expectedDate`,`requestdetails`.`surrenderDate` AS `surrenderDate`,`requestdetails`.`amntApproved` AS `amntApproved`,`requestdetails`.`designee` AS `designee`,`requestdetails`.`comments` AS `comments`,`requestdetails`.`status` AS `status`,`requestdetails`.`requestLevel` AS `requestLevel`,`requestdetails`.`dateRequested` AS `dateRequested`,`requestdetails`.`dateProcessed` AS `dateProcessed` from (`temprequeststracker` left join `requestdetails` on(((`requestdetails`.`id` = `temprequeststracker`.`id`) and (`requestdetails`.`ImprestId` = `temprequeststracker`.`imprestId`)))) ;

-- --------------------------------------------------------

--
-- Structure for view `staffdetails`
--
DROP TABLE IF EXISTS `staffdetails`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `staffdetails`  AS  select `staff`.`screenId` AS `screenId`,`staff`.`staffnumber` AS `staffnumber`,`staff`.`firstname` AS `firstname`,`staff`.`lastname` AS `lastname`,`staff`.`surname` AS `surname`,`departments`.`departmentId` AS `departmentId`,`departments`.`name` AS `department`,`departments`.`parentDepartment` AS `parentDepartment`,`userroles`.`roleId` AS `roleId`,`userroles`.`name` AS `role`,`staff`.`active` AS `active`,`staff`.`dateRecCreated` AS `dateRecCreated`,`staff`.`dateRecUpdated` AS `dateRecUpdated` from ((`staff` join `userroles` on((`userroles`.`roleId` = `staff`.`roleId`))) join `departments` on((`departments`.`departmentId` = `staff`.`departmentId`))) ;

-- --------------------------------------------------------

--
-- Structure for view `surrenderdetails`
--
DROP TABLE IF EXISTS `surrenderdetails`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `surrenderdetails`  AS  select `surrenderapprovals`.`id` AS `id`,`imprestsurrender`.`refId` AS `refId`,`imprestsurrender`.`imprestId` AS `imprestId`,`imprestsurrender`.`screenId` AS `applicant`,`imprestsurrender`.`amountAssigned` AS `amountAssigned`,`imprestsurrender`.`amountRefunding` AS `amountRefunding`,`imprestsurrender`.`amountClaiming` AS `amountClaiming`,`imprestsurrender`.`attachmentUrl` AS `attachmentUrl`,`surrenderapprovals`.`screenId` AS `designee`,`surrenderapprovals`.`comments` AS `comments`,`surrenderapprovals`.`status` AS `status`,`surrenderapprovals`.`requestLevel` AS `requestLevel`,`imprestsurrender`.`dateRecCreated` AS `dateRequested`,`surrenderapprovals`.`dateRecCreated` AS `dateprocessed` from (`imprestsurrender` join `surrenderapprovals` on((`imprestsurrender`.`refId` = `surrenderapprovals`.`refId`))) ;

-- --------------------------------------------------------

--
-- Structure for view `surrenderstatusdetails`
--
DROP TABLE IF EXISTS `surrenderstatusdetails`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `surrenderstatusdetails`  AS  select `surrenderdetails`.`id` AS `id`,`surrenderdetails`.`refId` AS `refId`,`surrenderdetails`.`imprestId` AS `imprestId`,`surrenderdetails`.`applicant` AS `applicant`,`surrenderdetails`.`amountAssigned` AS `amountAssigned`,`surrenderdetails`.`amountRefunding` AS `amountRefunding`,`surrenderdetails`.`amountClaiming` AS `amountClaiming`,`surrenderdetails`.`attachmentUrl` AS `attachmentUrl`,`surrenderdetails`.`designee` AS `designee`,`surrenderdetails`.`comments` AS `comments`,`surrenderdetails`.`status` AS `status`,`surrenderdetails`.`requestLevel` AS `requestLevel`,`surrenderdetails`.`dateRequested` AS `dateRequested`,`surrenderdetails`.`dateprocessed` AS `dateprocessed` from (`surrenderdetails` join `tempsurrendertracker` on(((`tempsurrendertracker`.`refId` = `surrenderdetails`.`refId`) and (`tempsurrendertracker`.`id` = `surrenderdetails`.`id`)))) ;

-- --------------------------------------------------------

--
-- Structure for view `temprequeststracker`
--
DROP TABLE IF EXISTS `temprequeststracker`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `temprequeststracker`  AS  select max(`requestsapprovals`.`id`) AS `id`,`requestsapprovals`.`imprestId` AS `imprestId` from `requestsapprovals` group by `requestsapprovals`.`imprestId` ;

-- --------------------------------------------------------

--
-- Structure for view `tempsurrendertracker`
--
DROP TABLE IF EXISTS `tempsurrendertracker`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `tempsurrendertracker`  AS  select max(`surrenderapprovals`.`id`) AS `id`,`surrenderapprovals`.`refId` AS `refId` from `surrenderapprovals` group by `surrenderapprovals`.`refId` ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
