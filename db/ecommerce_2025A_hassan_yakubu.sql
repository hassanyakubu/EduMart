-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 27, 2025 at 06:49 PM
-- Server version: 8.0.44-0ubuntu0.24.04.1
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

--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`cart_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 15, '2025-11-22 20:03:12', '2025-11-22 20:03:12'),
(3, 18, '2025-11-22 21:32:50', '2025-11-22 21:32:50'),
(4, 20, '2025-11-24 15:51:15', '2025-11-24 15:51:15'),
(5, 21, '2025-11-26 20:38:30', '2025-11-26 20:38:30'),
(6, 22, '2025-11-27 17:00:16', '2025-11-27 17:00:16'),
(7, 23, '2025-11-27 17:05:12', '2025-11-27 17:05:12'),
(8, 25, '2025-11-27 17:50:25', '2025-11-27 17:50:25');

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

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`item_id`, `cart_id`, `resource_id`, `qty`) VALUES
(17, 6, 5, 1),
(18, 6, 4, 1);

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
(1, 'SHS Core Mathematics'),
(2, 'SHS Integrated Science'),
(3, 'JHS English Language'),
(6, 'SHS Social Studies'),
(7, 'JHS French'),
(8, 'SHS Physics'),
(9, 'JHS ICT');

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
(4, 'Seth Tekper', 18),
(5, 'Kwame Nkrumah', 19),
(6, 'Hassan Yakubu', 11),
(7, 'Aki Ola', 24);

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
  `customer_contact` varchar(30) NOT NULL,
  `customer_image` varchar(100) DEFAULT NULL,
  `user_role` int NOT NULL,
  `user_type` enum('student','creator') DEFAULT 'student'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_id`, `customer_name`, `customer_email`, `customer_pass`, `customer_country`, `customer_city`, `customer_contact`, `customer_image`, `user_role`, `user_type`) VALUES
(11, 'Hassan Yakubu', 'yhassan677@gmail.com', '$2y$10$DxAbi1SoXj/T3oEuQ45Bp.gwrPrVTP4yKhA30ZaoXr.ATRhXGM5da', 'Ghana', 'Accra', '0204200934', NULL, 1, 'creator'),
(18, 'Seth Tekper', 'stek@gmail.com', '$2y$10$MNgZr1p1biMRzDtfF6gf4e21J0DzqlHg8kRNQWPUJPcryLdwTgBxa', 'Ghana', 'Accra', '055189345', NULL, 2, 'creator'),
(19, 'Kwame Nkrumah', 'nkrumah@gmail.com', '$2y$10$CBtod1xiev8y6BQqT2mnX.3TxzO89toi/14h.K9/J6S/Qiyyei4Ja', 'Ghana', 'Kumasi', '0244569123', NULL, 2, 'creator'),
(20, 'Senam Dzomeku', 'senam@gmail.com', '$2y$10$Z60oHZD/nMhpIYMumYoEJeSqfApsMaw1GbOPCoNwRl8qedVrizB1W', 'Ghana', 'Accra', '0500098121', NULL, 2, 'creator'),
(21, 'Reginald Tetteh', 'reggie@gmail.com', '$2y$10$a0qHgTsu.iHfCTjPeh.dOe.hdeUoQlO7ZEhbL4PU6GJcnutKogIci', 'Ghana', 'Kumasi', '0500002341', NULL, 2, 'student'),
(22, 'Salma Yakubu', 'salma@gmail.com', '$2y$10$kJE1vB9JwnDI/avk/SKsOut8.PYnY9M9fPkOsX.wDbPYEzkvhfb8u', 'Canada', 'Ontario', '+1 (519) 317-5817', NULL, 3, 'creator'),
(23, 'Ama Brown', 'amab@gmail.com', '$2y$10$ofZZ5XwppWh26AIm.mxEJ.JikGZ3cWYfYG7JBs8Ljs/i5ztpLooRi', 'Ghana', 'Takoradi', '0592349177', NULL, 3, 'student'),
(24, 'Aki Ola', 'akiola@gmail.com', '$2y$10$PwBnztm5PpowER.l3jo29.ytxAjLZPLrM5/a.b1H6tv2xI1OovCOu', 'Ghana', 'Sunyani', '0204200671', NULL, 2, 'creator'),
(25, 'Peter Griffin', 'pgriff@gmail.com', '$2y$10$w6QJxPynb7MqWcTW8uORPeDSVht/FGiXLmhkpUl7ydxMauhO0B7x6', 'USA', 'Rhode Island', '+1 (401) 555-3890', NULL, 3, 'student');

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

--
-- Dumping data for table `downloads`
--

INSERT INTO `downloads` (`download_id`, `customer_id`, `resource_id`, `purchase_id`, `download_date`) VALUES
(1, 18, 3, 3, '2025-11-22 22:02:26'),
(2, 20, 4, 4, '2025-11-24 15:51:33'),
(3, 20, 3, 5, '2025-11-24 16:18:01'),
(4, 20, 5, 6, '2025-11-25 15:32:06'),
(5, 20, 3, 7, '2025-11-25 16:07:28'),
(6, 20, 4, 7, '2025-11-25 16:07:28'),
(7, 20, 5, 7, '2025-11-25 16:07:28'),
(8, 21, 3, 8, '2025-11-26 20:39:04'),
(9, 21, 4, 8, '2025-11-26 20:39:04'),
(10, 21, 5, 8, '2025-11-26 20:39:04'),
(11, 21, 5, 9, '2025-11-27 16:41:41'),
(12, 23, 5, 10, '2025-11-27 17:05:53'),
(13, 23, 4, 10, '2025-11-27 17:05:53'),
(14, 23, 3, 11, '2025-11-27 17:07:29'),
(15, 25, 3, 12, '2025-11-27 17:51:17'),
(16, 25, 4, 12, '2025-11-27 17:51:17'),
(17, 25, 5, 12, '2025-11-27 17:51:17'),
(18, 25, 6, 12, '2025-11-27 17:51:17'),
(19, 25, 7, 12, '2025-11-27 17:51:17'),
(20, 25, 8, 12, '2025-11-27 17:51:17'),
(21, 25, 6, 13, '2025-11-27 18:00:35'),
(22, 25, 6, 14, '2025-11-27 18:02:53'),
(23, 25, 3, 15, '2025-11-27 18:03:51'),
(24, 25, 3, 16, '2025-11-27 18:26:53'),
(25, 25, 4, 16, '2025-11-27 18:26:53'),
(26, 25, 5, 16, '2025-11-27 18:26:53'),
(27, 25, 6, 16, '2025-11-27 18:26:53'),
(28, 25, 7, 16, '2025-11-27 18:26:53'),
(29, 25, 8, 16, '2025-11-27 18:26:53'),
(30, 25, 3, 17, '2025-11-27 18:48:40'),
(31, 25, 4, 17, '2025-11-27 18:48:40'),
(32, 25, 5, 17, '2025-11-27 18:48:40'),
(33, 25, 6, 17, '2025-11-27 18:48:40'),
(34, 25, 7, 17, '2025-11-27 18:48:40'),
(35, 25, 8, 17, '2025-11-27 18:48:40');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int NOT NULL,
  `purchase_id` int NOT NULL,
  `resource_id` int NOT NULL,
  `qty` int DEFAULT '1',
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `purchase_id`, `resource_id`, `qty`, `price`, `created_at`) VALUES
(1, 3, 3, 1, 15.00, '2025-11-22 00:00:00'),
(2, 5, 3, 1, 15.00, '2025-11-24 00:00:00'),
(3, 7, 3, 1, 15.00, '2025-11-25 00:00:00'),
(4, 8, 3, 1, 15.00, '2025-11-26 00:00:00'),
(5, 4, 4, 1, 25.00, '2025-11-24 00:00:00'),
(6, 7, 4, 1, 25.00, '2025-11-25 00:00:00'),
(7, 8, 4, 1, 25.00, '2025-11-26 00:00:00'),
(8, 6, 5, 1, 25.00, '2025-11-25 00:00:00'),
(9, 7, 5, 1, 25.00, '2025-11-25 00:00:00'),
(10, 8, 5, 1, 25.00, '2025-11-26 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int NOT NULL,
  `purchase_id` int NOT NULL,
  `customer_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(10) DEFAULT 'GHS',
  `payment_method` varchar(50) DEFAULT 'paystack',
  `payment_reference` varchar(255) NOT NULL,
  `authorization_code` varchar(255) DEFAULT NULL,
  `payment_channel` varchar(50) DEFAULT NULL,
  `payment_date` date NOT NULL,
  `payment_status` varchar(50) DEFAULT 'success',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `purchase_id` int NOT NULL,
  `customer_id` int NOT NULL,
  `invoice_no` varchar(50) NOT NULL,
  `purchase_date` date NOT NULL,
  `order_status` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `purchases`
--

INSERT INTO `purchases` (`purchase_id`, `customer_id`, `invoice_no`, `purchase_date`, `order_status`) VALUES
(1, 18, 'INV-20251122-000001', '2025-11-22', '15'),
(2, 18, 'INV-20251122-000002', '2025-11-22', 'completed'),
(3, 18, 'INV-20251122-000003', '2025-11-22', 'completed'),
(4, 20, 'INV-20251124-000004', '2025-11-24', 'completed'),
(5, 20, 'INV-20251124-000005', '2025-11-24', 'completed'),
(6, 20, 'INV-20251125-000006', '2025-11-25', 'completed'),
(7, 20, 'INV-20251125-000007', '2025-11-25', 'completed'),
(8, 21, 'INV-20251126-000008', '2025-11-26', 'completed'),
(9, 21, 'INV-20251127-000009', '2025-11-27', 'completed'),
(10, 23, 'INV-20251127-000010', '2025-11-27', 'completed'),
(11, 23, 'INV-20251127-000011', '2025-11-27', 'completed'),
(12, 25, 'INV-20251127-000012', '2025-11-27', 'completed'),
(13, 25, 'INV-20251127-000013', '2025-11-27', 'completed'),
(14, 25, 'INV-20251127-000014', '2025-11-27', 'completed'),
(15, 25, 'INV-20251127-000015', '2025-11-27', 'completed'),
(16, 25, '0', '2025-11-27', 'completed'),
(17, 25, '0', '2025-11-27', 'completed');

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `quiz_id` int NOT NULL,
  `user_id` int NOT NULL,
  `quiz_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` int NOT NULL,
  `time_limit` int NOT NULL COMMENT 'Time limit in minutes',
  `is_published` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`quiz_id`, `user_id`, `quiz_title`, `category_id`, `time_limit`, `is_published`, `created_at`) VALUES
(7, 18, 'BECE ENGLISH LANGUAGE QUIZ 1', 3, 10, 1, '2025-11-26 21:44:50'),
(8, 24, 'WASSCE SOCIAL STUDIES QUIZ 1 ', 6, 10, 1, '2025-11-27 17:47:58');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_answers`
--

CREATE TABLE `quiz_answers` (
  `answer_id` int NOT NULL,
  `attempt_id` int NOT NULL,
  `question_id` int NOT NULL,
  `user_answer` enum('A','B','C','D') COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_correct` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quiz_answers`
--

INSERT INTO `quiz_answers` (`answer_id`, `attempt_id`, `question_id`, `user_answer`, `is_correct`) VALUES
(6, 2, 31, 'B', 0),
(7, 2, 32, 'B', 1),
(8, 2, 33, 'B', 1),
(9, 2, 34, 'A', 1),
(10, 2, 35, 'D', 1);

-- --------------------------------------------------------

--
-- Table structure for table `quiz_attempts`
--

CREATE TABLE `quiz_attempts` (
  `attempt_id` int NOT NULL,
  `quiz_id` int NOT NULL,
  `user_id` int NOT NULL,
  `score` int NOT NULL,
  `total_questions` int NOT NULL,
  `time_taken` int DEFAULT NULL COMMENT 'Time taken in seconds',
  `completed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quiz_attempts`
--

INSERT INTO `quiz_attempts` (`attempt_id`, `quiz_id`, `user_id`, `score`, `total_questions`, `time_taken`, `completed_at`) VALUES
(2, 7, 21, 4, 5, 19, '2025-11-26 21:45:42');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_questions`
--

CREATE TABLE `quiz_questions` (
  `question_id` int NOT NULL,
  `quiz_id` int NOT NULL,
  `question_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `option_a` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `option_b` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `option_c` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `option_d` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `correct_answer` enum('A','B','C','D') COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quiz_questions`
--

INSERT INTO `quiz_questions` (`question_id`, `quiz_id`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_answer`) VALUES
(31, 7, 'Which word has the same vowel sound as the word join?', 'cold', 'joke ', 'toy ', 'tool', 'C'),
(32, 7, 'Our friends _____ meet us at the lorry station this afternoon.', 'are', 'are going to', 'go to', 'will be to', 'B'),
(33, 7, 'The woman, as well as her uncles, ______ been given free accommodation.', 'have', 'has', 'are', 'is', 'B'),
(34, 7, '(i) Ben goes to his farms daily until it is late in the evening. \r\nThis is a example of what type of sentence?', 'complex', 'compound', 'compound complex', 'simple', 'A'),
(35, 7, 'Choose the word which is nearest in meaning (synonyms) to the\r\nword in bracket.\r\nThe spectators (abandoned) their seats during the match.', 'inhibit', 'occupied', 'stayed in', 'vacated', 'D'),
(36, 8, 'The administrative head of a public corporation is the', 'Speaker ', 'Chairman', 'Director-general', 'Managing Director', 'B'),
(37, 8, 'A constitutional rule ensures that the', 'citizens can act in any manner.', 'executive acts within the law.', 'legislature is above the other organs o f government.', 'judiciary cannot be checked', 'B'),
(38, 8, 'The symbol that best helps to foster allegiance to the state of Ghana is', 'Gye Nyame', 'Sankofa', 'Independence Ark', 'National Flag', 'D'),
(39, 8, 'Which of the following options is not an agency of socialization?', 'Family', 'Mosque', 'Youth Clubs', 'Peers', 'C'),
(40, 8, 'Which of the following elements of our culture is common to all the ethnic groups in Ghana?', 'Language', 'Music and dance', 'Names', 'Chieftancy', 'D');

-- --------------------------------------------------------

--
-- Table structure for table `resources`
--

CREATE TABLE `resources` (
  `resource_id` int NOT NULL,
  `cat_id` int NOT NULL,
  `creator_id` int NOT NULL,
  `resource_title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `resource_price` double NOT NULL,
  `resource_desc` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resource_image` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resource_keywords` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resource_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `resources`
--

INSERT INTO `resources` (`resource_id`, `cat_id`, `creator_id`, `resource_title`, `resource_price`, `resource_desc`, `resource_image`, `resource_keywords`, `resource_file`) VALUES
(3, 3, 4, '2024', 15, 'BECE English sample questions from 2024', NULL, 'english, sample, sample questions, bece, 2024', 'uploads/files/692229fc10f33_BECE-English-Sample-SET-2.pdf'),
(4, 2, 5, '2020', 25, 'WASSCE Integrated Science paper written in 2020', 'uploads/images/6922376d88fce_Screenshot 2025-11-22 at 10.11.49 PM.png', 'IS, science, integrated science, WASSCE, 2020', 'uploads/files/6922376d891f4_wassce 2020 science.pdf'),
(5, 1, 6, '2023', 25, 'WASSCE Core Math paper written in 2023', 'uploads/images/6925c98fd696d_Screenshot 2025-11-25 at 3.21.28 PM.png', 'math, core math, WASSCE, 2023', 'uploads/files/6925c98fd6cd7_wassce 2023 mathematics.pdf'),
(6, 6, 7, '2023', 15, 'WASSCE Social Studies paper written in 2023', 'uploads/images/69288b21f23f6_Screenshot 2025-11-27 at 5.20.44 PM.png', 'WASSCE, SHS, social studies, 2023', 'uploads/files/69288b21f2695_wassce 2023 social studies.pdf'),
(7, 7, 7, '2022', 10, 'BECE French paper written in 2022', 'uploads/images/69288bbbea04b_Screenshot 2025-11-27 at 5.34.22 PM.png', 'BECE, JHS, french, 2022', 'uploads/files/69288bbbea329_bece 2022 french.pdf'),
(8, 8, 7, '2020', 25, 'WASSCE Physics paper written in 2020', 'uploads/images/69288c71ba22d_Screenshot 2025-11-27 at 5.37.22 PM.png', 'WASSCE, SHS, physics, 2020', 'uploads/files/69288c71ba41c_wassce 2020 physics.pdf');

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
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `purchase_id` (`purchase_id`),
  ADD KEY `resource_id` (`resource_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `purchase_id` (`purchase_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `payment_reference` (`payment_reference`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`purchase_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`quiz_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `quizzes_category_fk` (`category_id`);

--
-- Indexes for table `quiz_answers`
--
ALTER TABLE `quiz_answers`
  ADD PRIMARY KEY (`answer_id`),
  ADD KEY `idx_attempt_id` (`attempt_id`),
  ADD KEY `idx_question_id` (`question_id`);

--
-- Indexes for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD PRIMARY KEY (`attempt_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_quiz_id` (`quiz_id`),
  ADD KEY `idx_completed_at` (`completed_at`);

--
-- Indexes for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD PRIMARY KEY (`question_id`),
  ADD KEY `idx_quiz_id` (`quiz_id`);

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
  MODIFY `cart_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `item_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `cat_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `creators`
--
ALTER TABLE `creators`
  MODIFY `creator_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `downloads`
--
ALTER TABLE `downloads`
  MODIFY `download_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `purchase_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `quiz_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `quiz_answers`
--
ALTER TABLE `quiz_answers`
  MODIFY `answer_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  MODIFY `attempt_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  MODIFY `question_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `resources`
--
ALTER TABLE `resources`
  MODIFY `resource_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`purchase_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`resource_id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`purchase_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE;

--
-- Constraints for table `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `purchases_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`);

--
-- Constraints for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `quizzes_category_fk` FOREIGN KEY (`category_id`) REFERENCES `categories` (`cat_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_answers`
--
ALTER TABLE `quiz_answers`
  ADD CONSTRAINT `quiz_answers_ibfk_1` FOREIGN KEY (`attempt_id`) REFERENCES `quiz_attempts` (`attempt_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quiz_answers_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `quiz_questions` (`question_id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD CONSTRAINT `quiz_attempts_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`quiz_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quiz_attempts_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD CONSTRAINT `quiz_questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`quiz_id`) ON DELETE CASCADE;

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
