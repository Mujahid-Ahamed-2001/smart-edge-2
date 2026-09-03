-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 19, 2026 at 06:43 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `smart_edge`
--

-- --------------------------------------------------------

--
-- Table structure for table `quote_stat`
--

CREATE TABLE `quote_stat` (
  `QSID` int(11) NOT NULL,
  `color` varchar(25) DEFAULT '#000',
  `bg_color` varchar(25) DEFAULT '#fff',
  `stat_name` varchar(25) DEFAULT NULL,
  `def` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `quote_stat`
--
ALTER TABLE `quote_stat`
  ADD PRIMARY KEY (`QSID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `quote_stat`
--
ALTER TABLE `quote_stat`
  MODIFY `QSID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `quote_stat` ADD `completion` INT NOT NULL DEFAULT '0' AFTER `def`;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
