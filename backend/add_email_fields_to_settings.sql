-- Script to add missing email configuration fields to the settings table
-- This script adds the necessary fields for email configuration in the admin panel

ALTER TABLE `settings` 
ADD COLUMN `email_enabled` TINYINT(1) DEFAULT 1,
ADD COLUMN `email_sender_address` VARCHAR(191) DEFAULT NULL,
ADD COLUMN `email_sender_name` VARCHAR(255) DEFAULT NULL;

-- Update the default confirmation message
UPDATE `settings` SET `confirmation_message` = 'Thank you for contacting us. We have received your message and will respond shortly.' WHERE `id` = 1;