ALTER TABLE `user` 
ADD COLUMN `background` VARCHAR(80) NULL AFTER `deleted_date`,
ADD COLUMN `timezone` VARCHAR(128) NULL AFTER `background`;
