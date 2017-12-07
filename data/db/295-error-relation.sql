ALTER TABLE `preregistration`
DROP FOREIGN KEY `fk_preregistration_1`;
ALTER TABLE `preregistration`
CHANGE COLUMN `email` `email` VARCHAR(128) NULL ,
CHANGE COLUMN `firstname` `firstname` VARCHAR(128) NULL ,
CHANGE COLUMN `lastname` `lastname` VARCHAR(128) NULL ,
DROP INDEX `fk_preregistration_1_idx` ;
