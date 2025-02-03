-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 28, 2025 at 08:01 PM
-- Server version: 11.4.2-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
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
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `CART_ID` bigint(20) NOT NULL,
  `USER_ID` bigint(20) NOT NULL,
  `PRO_ID` bigint(20) NOT NULL,
  `CART_NUMBER` int(11) NOT NULL DEFAULT 1,
  `CART_STAMP` timestamp NOT NULL DEFAULT current_timestamp(),
  `CART_PRICE` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
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

-- --------------------------------------------------------

--
-- Table structure for table `order_lists`
--

CREATE TABLE `order_lists` (
  `OLIST_ID` bigint(20) NOT NULL,
  `ORDER_ID` bigint(20) DEFAULT NULL,
  `PRO_ID` bigint(20) DEFAULT NULL,
  `OLIST_NUMBER` int(11) NOT NULL DEFAULT 0,
  `OLIST_PRICE` decimal(10,2) NOT NULL DEFAULT 0.00,
  `OLIST_CANCEL` int(1) DEFAULT NULL,
  `OLIST_STATUS` int(1) DEFAULT NULL,
  `PRO_NAME` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
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

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
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
-- Dumping data for table `users`
--

INSERT INTO `users` (`USER_ID`, `USER_FNAME`, `USER_LNAME`, `USER_PHONE`, `USER_ADDRESS`, `USER_USERNAME`, `USER_PASSWORD`, `USER_STAMP`, `USER_ROLE`, `USER_DELETE`, `USER_DELETE_TIME`, `USER_DELETE_USER`) VALUES
(1, 'ADMIN', 'ADMIN', '0987654123', '-', 'ADMIN', 'ADMIN', '2025-01-13 18:34:46', 'ADMIN', NULL, NULL, NULL),
(2, 'USER', 'USER', '0987654123', 'ggggg', 'USER', 'USER', '2025-01-13 19:09:22', 'USER', NULL, NULL, NULL),
(3, 'ggg', 'ggg', 'ggg', 'ggg', 'ggg', 'ggh', '2025-01-15 15:25:19', 'USER', '1', NULL, NULL),
(4, 'ggg', 'ggg', 'ggg', 'USER', 'USER', 'USER', '2025-01-15 15:39:42', 'USER', '1', NULL, NULL),
(5, 'ggg', 'ggg', 'ggg', 'root', 'root', 'root', '2025-01-15 15:42:39', 'USER', NULL, NULL, NULL),
(6, 'ggg', 'ggg', 'ggg', 'f', 'rootf', '12345678', '2025-01-15 15:46:12', 'ADMIN', NULL, NULL, NULL),
(7, 'กา', 'ก', '0987654321', 'fggg', 'USER1', 'USER1', '2025-01-16 23:47:13', 'USER', NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`CART_ID`),
  ADD KEY `cPR_ID` (`PRO_ID`),
  ADD KEY `cUSER_ID` (`USER_ID`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`ORDER_ID`),
  ADD KEY `PUSER_IDs` (`USER_ID`);

--
-- Indexes for table `order_lists`
--
ALTER TABLE `order_lists`
  ADD PRIMARY KEY (`OLIST_ID`),
  ADD KEY `OLIST_ORER` (`ORDER_ID`),
  ADD KEY `OLIST_PRO` (`PRO_ID`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`PRO_ID`),
  ADD KEY `PUSER_ID` (`USER_ID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`USER_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `CART_ID` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `ORDER_ID` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_lists`
--
ALTER TABLE `order_lists`
  MODIFY `OLIST_ID` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `PRO_ID` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `USER_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `cPR_ID` FOREIGN KEY (`PRO_ID`) REFERENCES `products` (`PRO_ID`),
  ADD CONSTRAINT `cUSER_ID` FOREIGN KEY (`USER_ID`) REFERENCES `users` (`USER_ID`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `PUSER_IDs` FOREIGN KEY (`USER_ID`) REFERENCES `users` (`USER_ID`);

--
-- Constraints for table `order_lists`
--
ALTER TABLE `order_lists`
  ADD CONSTRAINT `OLIST_ORER` FOREIGN KEY (`ORDER_ID`) REFERENCES `orders` (`ORDER_ID`),
  ADD CONSTRAINT `OLIST_PRO` FOREIGN KEY (`PRO_ID`) REFERENCES `products` (`PRO_ID`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `PUSER_ID` FOREIGN KEY (`USER_ID`) REFERENCES `users` (`USER_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
