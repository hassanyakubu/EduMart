-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 26, 2025 at 09:21 PM
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
(5, 21, '2025-11-26 20:38:30', '2025-11-26 20:38:30');

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
(4, 'Seth Tekper', 18),
(5, 'Kwame Nkrumah', 19),
(6, 'Hassan Yakubu', 11);

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
  `user_role` int NOT NULL,
  `user_type` enum('student','creator') DEFAULT 'student'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_id`, `customer_name`, `customer_email`, `customer_pass`, `customer_country`, `customer_city`, `customer_contact`, `customer_image`, `user_role`, `user_type`) VALUES
(1, 'Victor Quagraine', 'vqaugs@gmail.com', '$2y$10$3Z5qwXbYU0ojteUR9AhWgOY4SzF.dEYYcn/pWBD/5tsRye1T7E3lW', 'Ghana', 'Accra', '0599259809', NULL, 2, 'creator'),
(4, 'Nana Brown', 'brown@gmail.com', '$2y$10$bDgc0qRHG70Hj2.sT2ZDQehFixFehU77IdGmqb.rR.5wFn0ZIOvDC', 'Italy', 'Venice', '0204367589', NULL, 2, 'creator'),
(6, 'Michelle Afari', 'mafari@gmail.com', '$2y$10$5Yk2PeSbJ5tLCrq4FIFJ1Ov4jP/MvExk0M7odi/KE637i/jy8FyuW', 'South Africa', 'Durban', '0303333099', NULL, 2, 'creator'),
(8, 'Nana Yaw Badu', 'nyaw@gmail.com', '$2y$10$3BXt351HYwv6TJUWBcJXluGWRr/XjmYyaPYDHI7D/sZrM8KFIBZ26', 'Ghana', 'Kumasi', '0541204098', NULL, 2, 'creator'),
(9, 'Jeff Dahmer', 'jdahm@gmail.com', '$2y$10$TzKnbR3rwW8fvpm1YmQNquQ5Tv379PvoEc.o5eG73eeXLC9kTSJru', 'Ghana', 'Takoradi', '0505550575', NULL, 2, 'creator'),
(10, 'Angie Quaye', 'angieq@gmail.com', '$2y$10$cpKHi2.2nDV17cVPEQJeSOMMkPfG7WFupa6rGm03l8rVmcYqbReKK', 'Ghana', 'Accra', '0507750091', NULL, 2, 'creator'),
(11, 'Hassan Yakubu', 'yhassan677@gmail.com', '$2y$10$DxAbi1SoXj/T3oEuQ45Bp.gwrPrVTP4yKhA30ZaoXr.ATRhXGM5da', 'Ghana', 'Accra', '0204200934', NULL, 1, 'creator'),
(12, 'Jeffery Adei', 'jeffadei@gmail.com', '$2y$10$bONMyw7yxPEfFXJyd3gccur.vxyfEAMobdY6Xl2wtbegiuFxiAEmO', 'Ghana', 'Akosombo', '0249446578', NULL, 2, 'creator'),
(13, 'Gina Davis', 'ginadavis@gmail.com', '$2y$10$NQTqXFU79VvX3Mw4OW7R9uNI10p8s1mmUrdcnhtDSrwsgN4snY4yy', 'Ghana', 'Accra', '0244412853', NULL, 2, 'creator'),
(14, 'John Doe', 'johndoe@gmail.com', '$2y$10$MUurGF15UqcSzuP3cb1TQeRkiEIIXVqAP9IaH05HZX27yH8j5wyEK', 'Ghana', 'Kumasi', '0264412853', NULL, 2, 'creator'),
(15, 'Test1', 'test1@gmail.com', '$2y$10$YNpWlcTg3.n2ra1HtH7a.etlS66./0RNlpjFUpknEfzBboY1iAjQW', 'Ghana', 'Accra', '0507151939', NULL, 2, 'creator'),
(18, 'Seth Tekper', 'stek@gmail.com', '$2y$10$MNgZr1p1biMRzDtfF6gf4e21J0DzqlHg8kRNQWPUJPcryLdwTgBxa', 'Ghana', 'Accra', '055189345', NULL, 2, 'creator'),
(19, 'Kwame Nkrumah', 'nkrumah@gmail.com', '$2y$10$CBtod1xiev8y6BQqT2mnX.3TxzO89toi/14h.K9/J6S/Qiyyei4Ja', 'Ghana', 'Kumasi', '0244569123', NULL, 2, 'creator'),
(20, 'Senam Dzomeku', 'senam@gmail.com', '$2y$10$Z60oHZD/nMhpIYMumYoEJeSqfApsMaw1GbOPCoNwRl8qedVrizB1W', 'Ghana', 'Accra', '0500098121', NULL, 2, 'creator'),
(21, 'Reginald Tetteh', 'reggie@gmail.com', '$2y$10$a0qHgTsu.iHfCTjPeh.dOe.hdeUoQlO7ZEhbL4PU6GJcnutKogIci', 'Ghana', 'Kumasi', '0500002341', NULL, 2, 'student');

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
(10, 21, 5, 8, '2025-11-26 20:39:04');

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
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `purchase_id` int NOT NULL,
  `customer_id` int NOT NULL,
  `invoice_no` int NOT NULL,
  `purchase_date` date NOT NULL,
  `order_status` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `purchases`
--

INSERT INTO `purchases` (`purchase_id`, `customer_id`, `invoice_no`, `purchase_date`, `order_status`) VALUES
(1, 18, 0, '2025-11-22', '15'),
(2, 18, 0, '2025-11-22', 'completed'),
(3, 18, 0, '2025-11-22', 'completed'),
(4, 20, 0, '2025-11-24', 'completed'),
(5, 20, 0, '2025-11-24', 'completed'),
(6, 20, 0, '2025-11-25', 'completed'),
(7, 20, 0, '2025-11-25', 'completed'),
(8, 21, 0, '2025-11-26', 'completed');

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `quiz_id` int NOT NULL,
  `user_id` int NOT NULL,
  `quiz_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` int DEFAULT NULL,
  `resource_filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `resource_path` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `time_limit` int NOT NULL COMMENT 'Time limit in minutes',
  `is_published` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`quiz_id`, `user_id`, `quiz_title`, `category_id`, `resource_filename`, `resource_path`, `time_limit`, `is_published`, `created_at`) VALUES
(6, 21, 'BECE English', NULL, '692229fc10f33_BECE-English-Sample-SET-2.txt', 'uploads/quiz_resources/69276b35a62b8_692229fc10f33_BECE-English-Sample-SET-2.txt', 10, 0, '2025-11-26 21:03:49');

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
(26, 6, 'Complete the statement: SET 2 ENGLISH LANGUAGE PAPER 1 INSTRUCTIONS Paper 1 has two parts: Part A and ______', 'B', 'I', 'Questions', 'None of the above', 'A'),
(27, 6, 'Complete the statement: Part A has 50 questions on Lexis an______ Structure', 'D', 'C', 'Word', 'None of the above', 'A'),
(28, 6, 'Complete the statement: Part ______ has 4 questions on Literature-in-English', 'B', 'You', 'Word', 'None of the above', 'A'),
(29, 6, 'Which of the following is a key concept discussed in the document?', 'B', 'You', 'Irrelevant topic', 'Unrelated subject', 'A'),
(30, 6, 'Which of the following is a key concept discussed in the document?', 'D', 'He', 'Irrelevant topic', 'Unrelated subject', 'A');

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
(5, 1, 6, '2023', 25, 'WASSCE Core Math paper written in 2023', 'uploads/images/6925c98fd696d_Screenshot 2025-11-25 at 3.21.28 PM.png', 'math, core math, WASSCE, 2023', 'uploads/files/6925c98fd6cd7_wassce 2023 mathematics.pdf');

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
  ADD KEY `idx_created_at` (`created_at`);

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
  MODIFY `cart_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `item_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `cat_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `creators`
--
ALTER TABLE `creators`
  MODIFY `creator_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `downloads`
--
ALTER TABLE `downloads`
  MODIFY `download_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `purchase_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `quiz_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `quiz_answers`
--
ALTER TABLE `quiz_answers`
  MODIFY `answer_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  MODIFY `attempt_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  MODIFY `question_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `resources`
--
ALTER TABLE `resources`
  MODIFY `resource_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
-- Constraints for table `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `purchases_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`);

--
-- Constraints for table `quizzes`
--
ALTER TABLE `quizzes`
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
