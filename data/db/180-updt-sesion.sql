ALTER TABLE `session` 
ADD COLUMN `registration_id` VARCHAR(300) NULL AFTER `uid`,
ADD COLUMN `uuid` VARCHAR(45) NULL AFTER `registration_id`;
