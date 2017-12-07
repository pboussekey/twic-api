ALTER TABLE `item` DROP FOREIGN KEY `fk_item_5`;
ALTER TABLE `item` DROP INDEX `fk_item_5_idx` ;
ALTER TABLE `item` ADD INDEX `fk_item_5_idx` (`conversation_id` ASC);
ALTER TABLE `item` ADD CONSTRAINT `fk_item_5`
  FOREIGN KEY (`conversation_id`)
  REFERENCES `conversation` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;
