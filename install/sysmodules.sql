-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 07, 2025 at 08:20 AM
-- Server version: 10.4.32-MariaDB-log
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fancymahal`
--

-- --------------------------------------------------------

--
-- Table structure for table `sysmodules`
--

DROP TABLE IF EXISTS `sysmodules`;

CREATE TABLE `sysmodules` (
  `SMID` int(11) NOT NULL,
  `ModuleName` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


--
-- Dumping data for table `sysmodules`
--

INSERT INTO `sysmodules` (`SMID`, `ModuleName`) VALUES
(1, 'Inventory'),
(2, 'Orders'),
(3, 'Accounts'),
(4, 'Reports'),
(5, 'Settings'),
(7, 'Test Module');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sysmodules`
--
ALTER TABLE `sysmodules`
  ADD PRIMARY KEY (`SMID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sysmodules`
--
ALTER TABLE `sysmodules`
  MODIFY `SMID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
