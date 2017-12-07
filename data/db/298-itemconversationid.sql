ALTER TABLE `item` 
ADD COLUMN `conversation_id` INT UNSIGNED NULL AFTER `is_grade_published`,
ADD INDEX `fk_item_5_idx` (`conversation_id` ASC);
ALTER TABLE `item` 
ADD CONSTRAINT `fk_item_5`
  FOREIGN KEY (`conversation_id`)
  REFERENCES `conversation` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;
