-- Add user_type column to customer table
-- Run this SQL to update your database

ALTER TABLE `customer` 
ADD COLUMN `user_type` ENUM('student', 'creator') DEFAULT 'student' AFTER `user_role`;

-- Update existing users to be students by default
UPDATE `customer` SET `user_type` = 'student' WHERE `user_type` IS NULL;
