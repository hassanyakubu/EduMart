-- Fix purchases table to accept proper invoice numbers
-- Run this SQL to fix the invoice_no column type

ALTER TABLE `purchases` 
MODIFY COLUMN `invoice_no` VARCHAR(50) NOT NULL;

-- Create payments table if it doesn't exist
CREATE TABLE IF NOT EXISTS `payments` (
  `payment_id` int NOT NULL AUTO_INCREMENT,
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
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`payment_id`),
  KEY `purchase_id` (`purchase_id`),
  KEY `customer_id` (`customer_id`),
  KEY `payment_reference` (`payment_reference`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`purchase_id`) ON DELETE CASCADE,
  CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
