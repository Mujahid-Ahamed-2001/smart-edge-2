-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 05, 2025 at 11:32 AM
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
-- Table structure for table `salesreturntype`
--

DROP TABLE IF EXISTS `salesreturntype`;
CREATE TABLE `salesreturntype` (
  `SRTID` int(11) NOT NULL,
  `SRT_Name` varchar(255) NOT NULL,
  `SRT_Des` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `salesreturntype`
--

INSERT INTO `salesreturntype` (`SRTID`, `SRT_Name`, `SRT_Des`) VALUES
(1, 'Cash Refund', 'Cash Refund'),
(2, 'Exchange', 'Exchange'),
(3, 'Credit Reduction', 'Credit Reduction');
INSERT INTO `companytype` (`CTID`, `CompanyTypeName`) VALUES
(1, 'Grocery'),
(2, 'Textile'),
(3, 'Pharmacy'),
(4, 'Hardware');
--
-- Indexes for dumped tables
--

--
-- Indexes for table `salesreturntype`
--
ALTER TABLE `salesreturntype`
  ADD PRIMARY KEY (`SRTID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `salesreturntype`
--
ALTER TABLE `salesreturntype`
  MODIFY `SRTID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
