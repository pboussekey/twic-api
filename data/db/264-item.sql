ALTER TABLE `post` 
ADD COLUMN `item_id` INT UNSIGNED NULL AFTER `uid`,
ADD INDEX `fk_post_3_idx` (`item_id` ASC);
ALTER TABLE `post` 
ADD CONSTRAINT `fk_post_3`
  FOREIGN KEY (`item_id`)
  REFERENCES `item` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `item` 
ADD COLUMN `text` TEXT NULL AFTER `points`,
ADD COLUMN `library_id` INT UNSIGNED NULL AFTER `text`,
ADD INDEX `fk_item_4_idx` (`library_id` ASC);
ALTER TABLE `item` 
ADD CONSTRAINT `fk_item_4`
  FOREIGN KEY (`library_id`)
  REFERENCES `library` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

