-- Rename columns to match signup.php
ALTER TABLE users CHANGE COLUMN pseudo username VARCHAR(255) NOT NULL;
ALTER TABLE users CHANGE COLUMN mail email VARCHAR(255) NOT NULL UNIQUE;
ALTER TABLE users CHANGE COLUMN motdepasse password_hash VARCHAR(255) NOT NULL;

-- Add the phone column
ALTER TABLE users ADD COLUMN phone VARCHAR(20) UNIQUE AFTER username;

-- Add the created_at column
ALTER TABLE users ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP; 