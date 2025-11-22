-- Set Hassan Yakubu as the only admin
-- Run this SQL to configure admin access

-- First, set all users to regular users (user_role = 2)
UPDATE `customer` SET `user_role` = 2;

-- Set Hassan Yakubu as admin (user_role = 1)
UPDATE `customer` 
SET `user_role` = 1, `user_type` = 'creator' 
WHERE `customer_email` = 'yhassan677@gmail.com';

-- If the admin account doesn't exist yet, create it
-- Replace 'YourPasswordHere' with your actual password
-- The password will be hashed when you register normally

-- To verify admin is set correctly:
SELECT customer_id, customer_name, customer_email, user_role, user_type 
FROM customer 
WHERE customer_email = 'yhassan677@gmail.com';
