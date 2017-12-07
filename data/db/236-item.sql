SET foreign_key_checks = 0;
TRUNCATE `item`;
SET foreign_key_checks = 1;
ALTER TABLE `item` 
ADD COLUMN `type` VARCHAR(45) NOT NULL AFTER `description`,
ADD COLUMN `is_published` TINYINT NULL,
ADD COLUMN `order` INT NULL AFTER `is_published`,
ADD COLUMN `created_date` DATETIME NULL AFTER `updated_date`,
CHANGE COLUMN `is_complete` `is_available` TINYINT(4) NULL DEFAULT '0' AFTER `type`,
CHANGE COLUMN `parent_id` `parent_id` INT(10) UNSIGNED NULL DEFAULT NULL AFTER `created_date`,
CHANGE COLUMN `describe` `description` TEXT NULL DEFAULT NULL ,
CHANGE COLUMN `start` `start_date` DATETIME NULL DEFAULT NULL ,
CHANGE COLUMN `end` `end_date` DATETIME NULL DEFAULT NULL;


ALTER TABLE `item`
CHANGE COLUMN `is_published` `is_published` TINYINT NULL AFTER `is_available`,
CHANGE COLUMN `order` `order` INT NULL AFTER `is_published`,
ADD COLUMN `page_id` INT NULL AFTER `parent_id`,
ADD COLUMN `user_id` INT NULL AFTER `page_id`;

ALTER TABLE `item`
CHANGE COLUMN `is_published` `is_published` TINYINT(4) NULL DEFAULT 0 ,
CHANGE COLUMN `page_id` `page_id` INT(11) UNSIGNED NOT NULL ,
CHANGE COLUMN `user_id` `user_id` INT(11) UNSIGNED NOT NULL ;

ALTER TABLE `item`
ADD INDEX `fk_item_2_idx1` (`page_id` ASC),
ADD INDEX `fk_item_3_idx` (`user_id` ASC);
ALTER TABLE `item`
ADD CONSTRAINT `fk_item_1`
  FOREIGN KEY (`parent_id`)
  REFERENCES `item` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_item_2`
  FOREIGN KEY (`page_id`)
  REFERENCES `page` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_item_3`
  FOREIGN KEY (`user_id`)
  REFERENCES `user` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

