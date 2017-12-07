ALTER TABLE `user` 
ADD COLUMN `suspension_date` DATETIME NULL AFTER `created_date`,
ADD COLUMN `suspension_reason` VARCHAR(45) NULL AFTER `suspension_date`;

