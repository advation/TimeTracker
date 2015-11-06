-- phpMyAdmin SQL Dump
-- version 4.4.13.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 05, 2015 at 05:04 PM
-- Server version: 5.6.27-0ubuntu1
-- PHP Version: 5.6.11-1ubuntu3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `timetracker`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE IF NOT EXISTS `accounts` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `pin` varchar(255) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `accountType` varchar(255) NOT NULL,
  `batchId` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `username`, `password`, `pin`, `firstName`, `lastName`, `accountType`, `batchId`) VALUES
(6, 'test', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 'Test', 'Tester', '900', '12345');

-- --------------------------------------------------------

--
-- Table structure for table `timeCodes`
--

CREATE TABLE IF NOT EXISTS `timeCodes` (
  `id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `multiplier` int(11) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `timeCodes`
--

INSERT INTO `timeCodes` (`id`, `name`, `multiplier`, `description`) VALUES
(1, 'Normal', 1, 'Normal time entry. '),
(2, 'Vacation', 1, 'Normal Vacation entry.'),
(3, 'Sick', 1, 'Normal sick entry. '),
(4, 'Holiday', 1, 'Normal Holiday entry. '),
(5, 'Holiday - Worked', 2, 'Holiday worked rate. ');

-- --------------------------------------------------------

--
-- Table structure for table `timeEntries`
--

CREATE TABLE IF NOT EXISTS `timeEntries` (
  `id` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `inTime` int(11) NOT NULL,
  `outTime` int(11) NOT NULL,
  `lessTime` int(11) NOT NULL,
  `codeId` int(11) NOT NULL,
  `batchId` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `timeEntries`
--

INSERT INTO `timeEntries` (`id`, `userId`, `inTime`, `outTime`, `lessTime`, `codeId`, `batchId`) VALUES
(1, 1, 12345, 12345, 0, 0, '12345');

-- --------------------------------------------------------

--
-- Table structure for table `timeSheetBatches`
--

CREATE TABLE IF NOT EXISTS `timeSheetBatches` (
  `id` int(11) NOT NULL,
  `batchId` int(255) NOT NULL,
  `approved` int(1) NOT NULL,
  `approvedDate` int(11) NOT NULL,
  `approvalUserId` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pin` (`pin`);

--
-- Indexes for table `timeCodes`
--
ALTER TABLE `timeCodes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `timeEntries`
--
ALTER TABLE `timeEntries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `timeSheetBatches`
--
ALTER TABLE `timeSheetBatches`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `timeCodes`
--
ALTER TABLE `timeCodes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `timeEntries`
--
ALTER TABLE `timeEntries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=64;
--
-- AUTO_INCREMENT for table `timeSheetBatches`
--
ALTER TABLE `timeSheetBatches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
