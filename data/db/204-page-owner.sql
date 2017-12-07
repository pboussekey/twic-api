ALTER TABLE `page` 
ADD COLUMN `owner_id` INT UNSIGNED AFTER `deleted_date`,
ADD INDEX `fk_page_5_idx` (`owner_id` ASC);
ALTER TABLE `page` 
ADD CONSTRAINT `fk_page_5`
  FOREIGN KEY (`owner_id`)
  REFERENCES `user` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

UPDATE page SET owner_id = user_id;