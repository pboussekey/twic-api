ALTER TABLE `item` 
ADD COLUMN `is_complete` TINYINT NULL DEFAULT 0 AFTER `is_grouped`;
