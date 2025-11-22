-- Fix collation mismatch for resources table
-- Run this SQL to fix the upload error

ALTER TABLE `resources` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Also fix other tables that might have the same issue
ALTER TABLE `categories` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `creators` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `customer` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `reviews` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Verify the changes
SHOW TABLE STATUS WHERE Name IN ('resources', 'categories', 'creators', 'customer', 'reviews');
