-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 05, 2025 at 09:42 AM
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
-- Database: `synnexpos`
--

-- --------------------------------------------------------

--
-- Table structure for table `stocktypes`
--

DROP TABLE IF EXISTS `stocktypes`;
CREATE TABLE `stocktypes` (
  `STID` int(11) NOT NULL,
  `StockTypeName` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `stocktypes`
--

INSERT INTO `stocktypes` (`STID`, `StockTypeName`) VALUES
(1, 'Average'),
(2, 'FIFO'),
(3, 'LIFO'),
(4, 'ExpireDate'),
(5, 'Batch');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `stocktypes`
--
ALTER TABLE `stocktypes`
  ADD PRIMARY KEY (`STID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `stocktypes`
--
ALTER TABLE `stocktypes`
  MODIFY `STID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
