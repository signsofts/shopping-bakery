-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 03, 2025 at 10:52 PM
-- Server version: 10.6.17-MariaDB
-- PHP Version: 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `signsoft_bakery`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_carts`
--

CREATE TABLE `tbl_carts` (
  `CART_ID` bigint(20) NOT NULL,
  `USER_ID` bigint(20) NOT NULL,
  `PRO_ID` bigint(20) NOT NULL,
  `CART_NUMBER` int(11) NOT NULL DEFAULT 1,
  `CART_STAMP` timestamp NOT NULL DEFAULT current_timestamp(),
  `CART_PRICE` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_carts`
--

INSERT INTO `tbl_carts` (`CART_ID`, `USER_ID`, `PRO_ID`, `CART_NUMBER`, `CART_STAMP`, `CART_PRICE`) VALUES
(5, 2, 1, 1, '2025-02-01 04:18:19', '50.00');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_orders`
--

CREATE TABLE `tbl_orders` (
  `ORDER_ID` bigint(20) NOT NULL,
  `USER_ID` bigint(20) DEFAULT NULL,
  `ORDER_STAMP` timestamp NULL DEFAULT current_timestamp(),
  `ORDER_STATUS` int(1) DEFAULT 1,
  `ORDER_CANCEL` int(1) DEFAULT NULL,
  `ORDER_PRICE` decimal(10,2) NOT NULL DEFAULT 0.00,
  `ORDER_PAYMENT_IMAGE` varchar(100) DEFAULT NULL,
  `ORDER_PAYMENT_PRICE` decimal(10,2) NOT NULL DEFAULT 0.00,
  `ORDER_CUS_NAME` varchar(100) DEFAULT NULL,
  `ORDER_CUS_PHONE` varchar(10) DEFAULT NULL,
  `ORDER_CUS_ADDRESS` text DEFAULT NULL,
  `ORDER_PAYMENT_CONFIRM` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_orders`
--

INSERT INTO `tbl_orders` (`ORDER_ID`, `USER_ID`, `ORDER_STAMP`, `ORDER_STATUS`, `ORDER_CANCEL`, `ORDER_PRICE`, `ORDER_PAYMENT_IMAGE`, `ORDER_PAYMENT_PRICE`, `ORDER_CUS_NAME`, `ORDER_CUS_PHONE`, `ORDER_CUS_ADDRESS`, `ORDER_PAYMENT_CONFIRM`) VALUES
(1, 2, '2025-01-28 19:43:12', 1, NULL, '30.00', 'image_6799335030fa23.66039223.png', '30.00', 'USER USER', '0987654123', ' ggggg', 1),
(2, 2, '2025-02-01 01:51:31', 1, NULL, '160.00', 'image_679d7e23374e48.17088306.png', '33333.00', 'USER USER', '0987654123', ' ggggg', 1),
(3, 2, '2025-02-01 02:00:23', 1, NULL, '30.00', 'image_679d8037a1cdf2.86594878.png', '66.00', 'USER USER', '0987654123', ' ffff', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_order_lists`
--

CREATE TABLE `tbl_order_lists` (
  `OLIST_ID` bigint(20) NOT NULL,
  `ORDER_ID` bigint(20) DEFAULT NULL,
  `PRO_ID` bigint(20) DEFAULT NULL,
  `OLIST_NUMBER` int(11) NOT NULL DEFAULT 0,
  `OLIST_PRICE` decimal(10,2) NOT NULL DEFAULT 0.00,
  `OLIST_CANCEL` int(1) DEFAULT NULL,
  `OLIST_STATUS` int(1) DEFAULT NULL,
  `PRO_NAME` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_order_lists`
--

INSERT INTO `tbl_order_lists` (`OLIST_ID`, `ORDER_ID`, `PRO_ID`, `OLIST_NUMBER`, `OLIST_PRICE`, `OLIST_CANCEL`, `OLIST_STATUS`, `PRO_NAME`) VALUES
(1, 1, 2, 1, '80.00', NULL, NULL, 'ขนมปัง 2'),
(2, 1, 3, 1, '30.00', NULL, NULL, 'ขนมปัง 3'),
(3, 2, 2, 2, '80.00', NULL, NULL, 'ขนมปัง 2'),
(4, 3, 3, 1, '30.00', NULL, NULL, 'ขนมปัง 3');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_products`
--

CREATE TABLE `tbl_products` (
  `PRO_ID` bigint(20) NOT NULL,
  `PRO_NAME` varchar(50) DEFAULT NULL,
  `PRO_PRICE` decimal(10,2) DEFAULT NULL,
  `PRO_STAMP` timestamp NOT NULL DEFAULT current_timestamp(),
  `PRO_DETAILS` text DEFAULT NULL,
  `PRO_PHOTO` varchar(100) DEFAULT NULL,
  `USER_ID` bigint(20) DEFAULT NULL,
  `PRO_DELETE` enum('1') DEFAULT NULL,
  `PRO_STATUS` enum('1','0') NOT NULL DEFAULT '1',
  `PRO_GROUP_NAME` varchar(50) DEFAULT NULL,
  `PRO_STOCK` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='สินค้า';

--
-- Dumping data for table `tbl_products`
--

INSERT INTO `tbl_products` (`PRO_ID`, `PRO_NAME`, `PRO_PRICE`, `PRO_STAMP`, `PRO_DETAILS`, `PRO_PHOTO`, `USER_ID`, `PRO_DELETE`, `PRO_STATUS`, `PRO_GROUP_NAME`, `PRO_STOCK`) VALUES
(1, 'ขนมปัง 1', '50.00', '2025-01-28 19:40:46', 'ขนมปัง 1', 'image_679932be8e7f30.11107014.jpg', 1, NULL, '1', 'ขนมปัง', 90),
(2, 'ขนมปัง 2', '80.00', '2025-01-28 19:41:06', 'ขนมปัง 1', 'image_679932d29aa224.74119681.jpg', 1, NULL, '1', 'ขนมปัง', 47),
(3, 'ขนมปัง 3', '30.00', '2025-01-28 19:41:30', 'ขนมปัง 3', 'image_679932ea7f1695.29615590.jpg', 1, NULL, '1', 'ขนมปัง', 88),
(4, 'ขนมปัง 4', '50.00', '2025-01-28 19:41:52', 'ขนมปัง 4', 'image_679933006dd0f5.16310445.jpg', 1, NULL, '1', 'ขนมปัง', 90),
(5, 'เสื้อ', '100.00', '2025-02-02 12:57:59', 'กหเกเดเกด', 'image_679f6bd704f192.88351049.jpg', 1, NULL, '1', 'เสื้อ', 20);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_users`
--

CREATE TABLE `tbl_users` (
  `USER_ID` bigint(20) NOT NULL,
  `USER_FNAME` varchar(50) DEFAULT NULL,
  `USER_LNAME` varchar(50) DEFAULT NULL,
  `USER_PHONE` varchar(10) DEFAULT NULL,
  `USER_ADDRESS` text DEFAULT NULL,
  `USER_USERNAME` varchar(50) DEFAULT NULL,
  `USER_PASSWORD` varchar(50) DEFAULT NULL,
  `USER_STAMP` timestamp NOT NULL DEFAULT current_timestamp(),
  `USER_ROLE` enum('USER','ADMIN') NOT NULL DEFAULT 'USER',
  `USER_DELETE` enum('1') DEFAULT NULL,
  `USER_DELETE_TIME` datetime DEFAULT NULL,
  `USER_DELETE_USER` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='ผู้ใช้';

--
-- Dumping data for table `tbl_users`
--

INSERT INTO `tbl_users` (`USER_ID`, `USER_FNAME`, `USER_LNAME`, `USER_PHONE`, `USER_ADDRESS`, `USER_USERNAME`, `USER_PASSWORD`, `USER_STAMP`, `USER_ROLE`, `USER_DELETE`, `USER_DELETE_TIME`, `USER_DELETE_USER`) VALUES
(1, 'ADMIN', 'ADMIN', '0987654123', '-', 'ADMIN', 'ADMIN', '2025-01-13 18:34:46', 'ADMIN', NULL, NULL, NULL),
(2, 'USER', 'USER', '0987654123', 'ffff', 'USER', 'USER', '2025-01-13 19:09:22', 'USER', NULL, NULL, NULL),
(3, 'ggg', 'ggg', 'ggg', 'ggg', 'ggg', 'ggh', '2025-01-15 15:25:19', 'USER', '1', NULL, NULL),
(4, 'ggg', 'ggg', 'ggg', 'USER', 'USER', 'USER', '2025-01-15 15:39:42', 'USER', '1', NULL, NULL),
(5, 'ggg', 'ggg', 'ggg', 'root', 'root', 'root', '2025-01-15 15:42:39', 'USER', NULL, NULL, NULL),
(6, 'ggg', 'ggg', 'ggg', 'f', 'rootf', '12345678', '2025-01-15 15:46:12', 'ADMIN', NULL, NULL, NULL),
(7, 'กา', 'ก', '0987654321', 'fggg', 'USER1', 'USER1', '2025-01-16 23:47:13', 'USER', NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_carts`
--
ALTER TABLE `tbl_carts`
  ADD PRIMARY KEY (`CART_ID`),
  ADD KEY `cPR_ID` (`PRO_ID`),
  ADD KEY `cUSER_ID` (`USER_ID`);

--
-- Indexes for table `tbl_orders`
--
ALTER TABLE `tbl_orders`
  ADD PRIMARY KEY (`ORDER_ID`),
  ADD KEY `PUSER_IDs` (`USER_ID`);

--
-- Indexes for table `tbl_order_lists`
--
ALTER TABLE `tbl_order_lists`
  ADD PRIMARY KEY (`OLIST_ID`),
  ADD KEY `OLIST_ORER` (`ORDER_ID`),
  ADD KEY `OLIST_PRO` (`PRO_ID`);

--
-- Indexes for table `tbl_products`
--
ALTER TABLE `tbl_products`
  ADD PRIMARY KEY (`PRO_ID`),
  ADD KEY `PUSER_ID` (`USER_ID`);

--
-- Indexes for table `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD PRIMARY KEY (`USER_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_carts`
--
ALTER TABLE `tbl_carts`
  MODIFY `CART_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_orders`
--
ALTER TABLE `tbl_orders`
  MODIFY `ORDER_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbl_order_lists`
--
ALTER TABLE `tbl_order_lists`
  MODIFY `OLIST_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_products`
--
ALTER TABLE `tbl_products`
  MODIFY `PRO_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_users`
--
ALTER TABLE `tbl_users`
  MODIFY `USER_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_carts`
--
ALTER TABLE `tbl_carts`
  ADD CONSTRAINT `cPR_ID` FOREIGN KEY (`PRO_ID`) REFERENCES `tbl_products` (`PRO_ID`),
  ADD CONSTRAINT `cUSER_ID` FOREIGN KEY (`USER_ID`) REFERENCES `tbl_users` (`USER_ID`);

--
-- Constraints for table `tbl_orders`
--
ALTER TABLE `tbl_orders`
  ADD CONSTRAINT `PUSER_IDs` FOREIGN KEY (`USER_ID`) REFERENCES `tbl_users` (`USER_ID`);

--
-- Constraints for table `tbl_order_lists`
--
ALTER TABLE `tbl_order_lists`
  ADD CONSTRAINT `OLIST_ORER` FOREIGN KEY (`ORDER_ID`) REFERENCES `tbl_orders` (`ORDER_ID`),
  ADD CONSTRAINT `OLIST_PRO` FOREIGN KEY (`PRO_ID`) REFERENCES `tbl_products` (`PRO_ID`);

--
-- Constraints for table `tbl_products`
--
ALTER TABLE `tbl_products`
  ADD CONSTRAINT `PUSER_ID` FOREIGN KEY (`USER_ID`) REFERENCES `tbl_users` (`USER_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
