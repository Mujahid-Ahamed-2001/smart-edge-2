-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 20, 2026 at 11:24 AM
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
-- Table structure for table `quotations`
--

CREATE TABLE `quotations` (
  `id` int(11) NOT NULL,
  `quotation_no` varchar(50) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `status` tinyint(4) DEFAULT 1,
  `valid_until` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `terms` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `shop_SHID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quotations`
--

INSERT INTO `quotations` (`id`, `quotation_no`, `customer_id`, `status`, `valid_until`, `notes`, `terms`, `created_by`, `created_at`, `updated_at`, `shop_SHID`) VALUES
(5, 'Q20042026-001', 1, 1, NULL, NULL, NULL, 1, '2026-04-20 14:40:42', '2026-04-20 14:40:42', 1),
(6, 'Q20042026-002', 1, 1, NULL, NULL, NULL, 1, '2026-04-20 14:42:00', '2026-04-20 14:42:00', 1),
(7, 'Q20042026-003', 1, 1, NULL, NULL, NULL, 1, '2026-04-20 14:42:11', '2026-04-20 14:42:11', 1);

-- --------------------------------------------------------

--
-- Table structure for table `quotation_options`
--

CREATE TABLE `quotation_options` (
  `id` int(11) NOT NULL,
  `quotation_id` int(11) NOT NULL,
  `option_name` varchar(100) DEFAULT NULL,
  `is_selected` tinyint(4) DEFAULT 0,
  `subtotal` decimal(10,2) DEFAULT 0.00,
  `discount_type` tinyint(4) DEFAULT 1,
  `discount_value` decimal(10,2) DEFAULT 0.00,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `tot_discount_amount` decimal(10,2) DEFAULT 0.00,
  `total` decimal(10,2) DEFAULT 0.00,
  `sort_order` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quotation_options`
--

INSERT INTO `quotation_options` (`id`, `quotation_id`, `option_name`, `is_selected`, `subtotal`, `discount_type`, `discount_value`, `discount_amount`, `tot_discount_amount`, `total`, `sort_order`, `created_at`) VALUES
(1, 5, 'Option 1', 0, 12500.00, 1, 0.00, 0.00, 0.00, 12500.00, 0, '2026-04-20 14:40:42'),
(2, 6, 'Option 1', 0, 24500.00, 1, 0.00, 0.00, 0.00, 24500.00, 0, '2026-04-20 14:42:00'),
(3, 7, 'Option 1', 0, 24500.00, 1, 0.00, 0.00, 0.00, 24500.00, 0, '2026-04-20 14:42:11');

-- --------------------------------------------------------

--
-- Table structure for table `quotation_option_items`
--

CREATE TABLE `quotation_option_items` (
  `id` int(11) NOT NULL,
  `quotation_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `item_name` varchar(255) NOT NULL,
  `cost_price` decimal(10,2) DEFAULT 0.00,
  `original_price` decimal(10,2) DEFAULT 0.00,
  `discount_type` tinyint(4) DEFAULT 1,
  `discount_value` decimal(10,2) DEFAULT 0.00,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `selling_price` decimal(10,2) DEFAULT 0.00,
  `quantity` decimal(10,2) DEFAULT 1.00,
  `total` decimal(10,2) DEFAULT 0.00,
  `sort_order` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quotation_option_items`
--

INSERT INTO `quotation_option_items` (`id`, `quotation_id`, `option_id`, `item_id`, `item_name`, `cost_price`, `original_price`, `discount_type`, `discount_value`, `discount_amount`, `selling_price`, `quantity`, `total`, `sort_order`, `created_at`) VALUES
(1, 5, 1, 1, '1 Year Warranty Direct thermal printing, 80 mm paper width (79.5 ± 0.5 mm), print width ~72 mm, 203 DPI resolution, print speed up to 160–200 mm/s, USB/Serial/LAN connectivity (varies by model), auto cutter (~1.5 million cuts), ESC/POS support, thermal pa', 9500.00, 12500.00, 2, 0.00, 0.00, 12500.00, 1.00, 12500.00, 0, '2026-04-20 14:40:42'),
(2, 6, 2, 2, '1 Year Warranty Direct thermal printing, 203 DPI resolution, print speed up to 50.8–127 mm/s, dual mode (barcode and receipt printing), USB + Bluetooth connectivity, max print width 76 mm, media width 20–82 mm, supports thermal roll and adhesive labels, c', 17500.00, 24500.00, 2, 0.00, 0.00, 24500.00, 1.00, 24500.00, 0, '2026-04-20 14:42:00'),
(3, 7, 3, 3, '1 Year Warranty Direct thermal printing, 203 DPI resolution, print speed up to 127 mm/s (max ~220 mm/s), dual mode (barcode and receipt printing), max print width 76 mm, media width 20–82 mm, supports thermal roll and adhesive labels, USB/optional LAN/Wi-', 17500.00, 24500.00, 2, 0.00, 0.00, 24500.00, 1.00, 24500.00, 0, '2026-04-20 14:42:11');

-- --------------------------------------------------------

--
-- Table structure for table `quote_stat`
--

CREATE TABLE `quote_stat` (
  `QSID` int(11) NOT NULL,
  `color` varchar(25) DEFAULT '#000',
  `bg_color` varchar(25) DEFAULT '#fff',
  `stat_name` varchar(25) DEFAULT NULL,
  `def` int(11) DEFAULT 0,
  `completion` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quote_stat`
--

INSERT INTO `quote_stat` (`QSID`, `color`, `bg_color`, `stat_name`, `def`, `completion`) VALUES
(1, '#000', '#FFFF00', 'Pending', 1, 0),
(2, '#000', '#39ff14', 'Accepted', 1, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `quotations`
--
ALTER TABLE `quotations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `quotation_options`
--
ALTER TABLE `quotation_options`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `quotation_option_items`
--
ALTER TABLE `quotation_option_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `quote_stat`
--
ALTER TABLE `quote_stat`
  ADD PRIMARY KEY (`QSID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `quotations`
--
ALTER TABLE `quotations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `quotation_options`
--
ALTER TABLE `quotation_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `quotation_option_items`
--
ALTER TABLE `quotation_option_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `quote_stat`
--
ALTER TABLE `quote_stat`
  MODIFY `QSID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
