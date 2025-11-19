-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 19, 2025 at 05:42 PM
-- Server version: 8.0.43-0ubuntu0.24.04.2
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ecommerce_2025A_hassan_yakubu`
--

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `cart_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `item_id` int NOT NULL,
  `cart_id` int NOT NULL,
  `resource_id` int NOT NULL,
  `qty` int DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `cat_id` int NOT NULL,
  `cat_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`cat_id`, `cat_name`) VALUES
(1, 'SHS Mathematics'),
(2, 'SHS Science'),
(3, 'BECE English'),
(4, 'University MIS'),
(5, 'General Knowledge');

-- --------------------------------------------------------

--
-- Table structure for table `creators`
--

CREATE TABLE `creators` (
  `creator_id` int NOT NULL,
  `creator_name` varchar(100) NOT NULL,
  `created_by` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `creators`
--

INSERT INTO `creators` (`creator_id`, `creator_name`, `created_by`) VALUES
(1, 'Mr. Opoku (Math Tutor)', 11),
(2, 'Approachers Series', 11),
(3, 'Dr. K. Mensah (CS Dept)', 11);

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customer_id` int NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(50) NOT NULL,
  `customer_pass` varchar(150) NOT NULL,
  `customer_country` varchar(30) NOT NULL,
  `customer_city` varchar(30) NOT NULL,
  `customer_contact` varchar(15) NOT NULL,
  `customer_image` varchar(100) DEFAULT NULL,
  `user_role` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_id`, `customer_name`, `customer_email`, `customer_pass`, `customer_country`, `customer_city`, `customer_contact`, `customer_image`, `user_role`) VALUES
(1, 'Victor Quagraine', 'vqaugs@gmail.com', '$2y$10$3Z5qwXbYU0ojteUR9AhWgOY4SzF.dEYYcn/pWBD/5tsRye1T7E3lW', 'Ghana', 'Accra', '0599259809', NULL, 2),
(4, 'Nana Brown', 'brown@gmail.com', '$2y$10$bDgc0qRHG70Hj2.sT2ZDQehFixFehU77IdGmqb.rR.5wFn0ZIOvDC', 'Italy', 'Venice', '0204367589', NULL, 2),
(6, 'Michelle Afari', 'mafari@gmail.com', '$2y$10$5Yk2PeSbJ5tLCrq4FIFJ1Ov4jP/MvExk0M7odi/KE637i/jy8FyuW', 'South Africa', 'Durban', '0303333099', NULL, 2),
(8, 'Nana Yaw Badu', 'nyaw@gmail.com', '$2y$10$3BXt351HYwv6TJUWBcJXluGWRr/XjmYyaPYDHI7D/sZrM8KFIBZ26', 'Ghana', 'Kumasi', '0541204098', NULL, 2),
(9, 'Jeff Dahmer', 'jdahm@gmail.com', '$2y$10$TzKnbR3rwW8fvpm1YmQNquQ5Tv379PvoEc.o5eG73eeXLC9kTSJru', 'Ghana', 'Takoradi', '0505550575', NULL, 2),
(10, 'Angie Quaye', 'angieq@gmail.com', '$2y$10$cpKHi2.2nDV17cVPEQJeSOMMkPfG7WFupa6rGm03l8rVmcYqbReKK', 'Ghana', 'Accra', '0507750091', NULL, 2),
(11, 'Hassan Yakubu', 'yhassan677@gmail.com', '$2y$10$DxAbi1SoXj/T3oEuQ45Bp.gwrPrVTP4yKhA30ZaoXr.ATRhXGM5da', 'Ghana', 'Accra', '0204200934', NULL, 1),
(12, 'Jeffery Adei', 'jeffadei@gmail.com', '$2y$10$bONMyw7yxPEfFXJyd3gccur.vxyfEAMobdY6Xl2wtbegiuFxiAEmO', 'Ghana', 'Akosombo', '0249446578', NULL, 2),
(13, 'Gina Davis', 'ginadavis@gmail.com', '$2y$10$NQTqXFU79VvX3Mw4OW7R9uNI10p8s1mmUrdcnhtDSrwsgN4snY4yy', 'Ghana', 'Accra', '0244412853', NULL, 2),
(14, 'John Doe', 'johndoe@gmail.com', '$2y$10$MUurGF15UqcSzuP3cb1TQeRkiEIIXVqAP9IaH05HZX27yH8j5wyEK', 'Ghana', 'Kumasi', '0264412853', NULL, 2);

-- --------------------------------------------------------

--
-- Table structure for table `downloads`
--

CREATE TABLE `downloads` (
  `download_id` int NOT NULL,
  `customer_id` int NOT NULL,
  `resource_id` int NOT NULL,
  `purchase_id` int NOT NULL,
  `download_date` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `purchase_id` int NOT NULL,
  `customer_id` int NOT NULL,
  `invoice_no` int NOT NULL,
  `purchase_date` date NOT NULL,
  `order_status` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `resources`
--

CREATE TABLE `resources` (
  `resource_id` int NOT NULL,
  `cat_id` int NOT NULL,
  `creator_id` int NOT NULL,
  `resource_title` varchar(200) NOT NULL,
  `resource_price` double NOT NULL,
  `resource_desc` varchar(500) DEFAULT NULL,
  `resource_image` varchar(100) DEFAULT NULL,
  `resource_keywords` varchar(100) DEFAULT NULL,
  `resource_file` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `resources`
--

INSERT INTO `resources` (`resource_id`, `cat_id`, `creator_id`, `resource_title`, `resource_price`, `resource_desc`, `resource_image`, `resource_keywords`, `resource_file`) VALUES
(1, 1, 1, 'Core Math Formulas', 10, 'Essential formulas for WASSCE', 'math_cover.jpg', 'math, wassce', 'files/math_formulas.pdf'),
(2, 3, 2, 'BECE English Past Questions', 15, '2020-2024 Past Questions', 'english_cover.jpg', 'english, bece', 'files/english_pq.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int NOT NULL,
  `user_id` int NOT NULL,
  `resource_id` int NOT NULL,
  `rating` int NOT NULL,
  `comment` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('momo_api_key', 'dummy_key_123'),
('site_name', 'EduMart');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`cart_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `resource_id` (`resource_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`cat_id`);

--
-- Indexes for table `creators`
--
ALTER TABLE `creators`
  ADD PRIMARY KEY (`creator_id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `customer_email` (`customer_email`);

--
-- Indexes for table `downloads`
--
ALTER TABLE `downloads`
  ADD PRIMARY KEY (`download_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `resource_id` (`resource_id`),
  ADD KEY `purchase_id` (`purchase_id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`purchase_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `resources`
--
ALTER TABLE `resources`
  ADD PRIMARY KEY (`resource_id`),
  ADD KEY `fk_resource_cat` (`cat_id`),
  ADD KEY `fk_resource_creator` (`creator_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `resource_id` (`resource_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `cart_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `item_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `cat_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `creators`
--
ALTER TABLE `creators`
  MODIFY `creator_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `downloads`
--
ALTER TABLE `downloads`
  MODIFY `download_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `purchase_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `resources`
--
ALTER TABLE `resources`
  MODIFY `resource_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`cart_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`resource_id`) ON DELETE CASCADE;

--
-- Constraints for table `downloads`
--
ALTER TABLE `downloads`
  ADD CONSTRAINT `downloads_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`),
  ADD CONSTRAINT `downloads_ibfk_2` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`resource_id`),
  ADD CONSTRAINT `downloads_ibfk_3` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`purchase_id`);

--
-- Constraints for table `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `purchases_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`);

--
-- Constraints for table `resources`
--
ALTER TABLE `resources`
  ADD CONSTRAINT `fk_resource_cat` FOREIGN KEY (`cat_id`) REFERENCES `categories` (`cat_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_resource_creator` FOREIGN KEY (`creator_id`) REFERENCES `creators` (`creator_id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`resource_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
