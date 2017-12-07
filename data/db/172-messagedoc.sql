ALTER TABLE `message_doc` 
ADD COLUMN `library_id` INT UNSIGNED NULL AFTER `message_id`,
ADD COLUMN `type` VARCHAR(45) NULL AFTER `library_id`;

