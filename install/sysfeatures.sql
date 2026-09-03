-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 07, 2025 at 08:19 AM
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
-- Table structure for table `sysfeatures`
--
DROP TABLE IF EXISTS `sysfeatures`;
CREATE TABLE `sysfeatures` (
  `SFID` int(11) NOT NULL,
  `FeatureName` varchar(45) DEFAULT NULL,
  `SystemModules_SMID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `sysfeatures`
--

INSERT INTO `sysfeatures` (`SFID`, `FeatureName`, `SystemModules_SMID`) VALUES
(1, 'Store', 1),
(2, 'Goods Received', 1),
(3, 'Adjustment', 1),
(4, 'Transfer Note', 1),
(5, 'Supplier Return', 1),
(6, 'Price Change', 1),
(7, 'Retail Sales', 2),
(8, 'WholeSale Sales', 2),
(9, 'Customer Due Payment', 2),
(10, 'Sales Return', 2),
(11, 'Expense Categories', 3),
(12, 'Expense Type', 3),
(13, 'Expenses', 3),
(14, 'Main Category', 5),
(15, 'Subcategory Category', 5),
(16, 'Products', 5),
(17, 'Suppliers', 5),
(18, 'Customers', 5),
(19, 'Salesman', 5),
(20, 'Add Users', 5),
(21, 'Section and Racks', 5),
(22, 'Units', 5),
(31, 'Master Unit List', 4),
(32, 'Master Sub Category List', 4),
(33, 'Master Category List', 4),
(34, 'Master Item List', 4),
(35, 'Master Supplier List', 4),
(36, 'Master Customer List', 4),
(37, 'Master Salesmen List', 4),
(38, 'Inventory Summary', 4),
(39, 'Sales Summary', 4),
(40, 'Salesman Wise Sales', 4),
(41, 'User wise Sales', 4),
(42, 'Item Return', 4),
(43, 'Customer Profiles', 4),
(44, 'Credit Customer', 4),
(45, 'Customer Sales', 4),
(46, 'Due Sales', 4),
(47, 'Top Selling Product', 4),
(48, 'Product Variations', 4),
(49, 'Supplier Return', 4),
(50, 'Supplier Payment', 4),
(51, 'Category Selling', 4),
(52, 'Transfer Note Report', 4),
(53, 'Expense Summary', 4),
(54, 'Add User Role', 5),
(55, 'Invoice Return', 4),
(56, 'Invoice List', 2),
(57, 'Profit & Loss', 4),
(58, 'Paymethod Sale', 4),
(59, 'Expired Items', 4),
(62, 'Sale Z Report', 4),
(63, 'Supplier Purchase', 4),
(64, 'Monthly Sale', 4),
(65, 'Inventory Price', 4),
(66, 'Supplier Due Payments', 1),
(67, 'Label Print', 5),
(68, 'Prescription', 2),
(69, 'Salesman Details', 4),
(70, 'Test Feature', 6),
(71, 'Promotions', 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sysfeatures`
--
ALTER TABLE `sysfeatures`
  ADD PRIMARY KEY (`SFID`),
  ADD KEY `fk_SysFeatures_SystemModules1_idx` (`SystemModules_SMID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sysfeatures`
--
ALTER TABLE `sysfeatures`
  MODIFY `SFID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
