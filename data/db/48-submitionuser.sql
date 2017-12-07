ALTER TABLE `submission_user` 
ADD COLUMN `end_date` DATETIME NULL DEFAULT NULL AFTER `start_date`;
