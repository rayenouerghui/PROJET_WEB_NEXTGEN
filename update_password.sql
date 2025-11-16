-- Step 1: First, go to http://localhost/PROJET_WEB_NEXTGEN/generate_password_hash.php
-- Step 2: Copy the hash it shows
-- Step 3: Replace YOUR_HASH_HERE below with that hash
-- Step 4: Run this SQL in phpMyAdmin

UPDATE `utilisateur` 
SET `mot_de_passe` = 'YOUR_HASH_HERE'
WHERE `email` = 'dhia@gmail.com';

-- After running, login with:
-- Email: dhia@gmail.com  
-- Password: dhia
