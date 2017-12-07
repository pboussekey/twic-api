ALTER TABLE `page` 
ADD COLUMN `conversation_id` INT UNSIGNED NULL AFTER `subtype`,
ADD INDEX `fk_page_2_idx` (`conversation_id` ASC);
ALTER TABLE `page` 
ADD CONSTRAINT `fk_page_2`
  FOREIGN KEY (`conversation_id`)
  REFERENCES `conversation` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

