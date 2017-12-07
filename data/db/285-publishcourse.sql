ALTER TABLE `page` 
ADD COLUMN `is_published` TINYINT NULL DEFAULT 0 AFTER `created_date`;

