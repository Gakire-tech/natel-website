-- SQL Migration Script to Add Multilingual Support (French) to All Content Tables
-- This script adds French language columns to all content tables while preserving existing data

-- Add French language columns to services table
ALTER TABLE `services` 
ADD COLUMN `title_fr` TEXT DEFAULT NULL,
ADD COLUMN `description_fr` TEXT DEFAULT NULL;

-- Add French language columns to projects table
ALTER TABLE `projects` 
ADD COLUMN `name_fr` TEXT DEFAULT NULL,
ADD COLUMN `description_fr` TEXT DEFAULT NULL;

-- Add French language columns to about_page table
ALTER TABLE `about_page` 
ADD COLUMN `main_content_fr` TEXT DEFAULT NULL,
ADD COLUMN `mission_fr` TEXT DEFAULT NULL,
ADD COLUMN `vision_fr` TEXT DEFAULT NULL,
ADD COLUMN `values_content_fr` TEXT DEFAULT NULL;

-- Add French language columns to team_members table
ALTER TABLE `team_members` 
ADD COLUMN `name_fr` TEXT DEFAULT NULL,
ADD COLUMN `position_fr` TEXT DEFAULT NULL,
ADD COLUMN `bio_fr` TEXT DEFAULT NULL;

-- Add French language columns to testimonials table
ALTER TABLE `testimonials` 
ADD COLUMN `name_fr` TEXT DEFAULT NULL,
ADD COLUMN `testimonial_fr` TEXT DEFAULT NULL;

-- Add French language columns to settings table
ALTER TABLE `settings` 
ADD COLUMN `site_title_fr` VARCHAR(255) DEFAULT NULL,
ADD COLUMN `footer_text_fr` TEXT DEFAULT NULL,
ADD COLUMN `working_hours_fr` TEXT DEFAULT NULL,
ADD COLUMN `meta_description_fr` TEXT DEFAULT NULL,
ADD COLUMN `meta_keywords_fr` TEXT DEFAULT NULL,
ADD COLUMN `site_keywords_fr` TEXT DEFAULT NULL,
ADD COLUMN `confirmation_message_fr` TEXT DEFAULT NULL;

-- Add French language columns to gallery table
ALTER TABLE `gallery` 
ADD COLUMN `title_fr` TEXT DEFAULT NULL,
ADD COLUMN `description_fr` TEXT DEFAULT NULL;

-- Optional: Copy existing English content to French fields as placeholders
-- Uncomment the following lines if you want to copy existing content as placeholders
/*
UPDATE `services` SET `title_fr` = `title`, `description_fr` = `description` WHERE `title_fr` IS NULL AND `description_fr` IS NULL;
UPDATE `projects` SET `name_fr` = `name`, `description_fr` = `description` WHERE `name_fr` IS NULL AND `description_fr` IS NULL;
UPDATE `about_page` SET `main_content_fr` = `main_content`, `mission_fr` = `mission`, `vision_fr` = `vision`, `values_content_fr` = `values_content` WHERE `main_content_fr` IS NULL;
UPDATE `team_members` SET `name_fr` = `name`, `position_fr` = `position`, `bio_fr` = `bio` WHERE `name_fr` IS NULL AND `position_fr` IS NULL AND `bio_fr` IS NULL;
UPDATE `testimonials` SET `name_fr` = `name`, `testimonial_fr` = `testimonial` WHERE `name_fr` IS NULL AND `testimonial_fr` IS NULL;
UPDATE `settings` SET `site_title_fr` = `site_title`, `footer_text_fr` = `footer_text`, `working_hours_fr` = `working_hours`, `meta_description_fr` = `meta_description`, `meta_keywords_fr` = `meta_keywords`, `site_keywords_fr` = `site_keywords`, `confirmation_message_fr` = `confirmation_message` WHERE `site_title_fr` IS NULL;
UPDATE `gallery` SET `title_fr` = `title`, `description_fr` = `description` WHERE `title_fr` IS NULL AND `description_fr` IS NULL;
*/