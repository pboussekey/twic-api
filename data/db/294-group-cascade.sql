ALTER TABLE `group` 
DROP FOREIGN KEY `fk_group_1`;
ALTER TABLE `group` 
ADD CONSTRAINT `fk_group_1`
  FOREIGN KEY (`item_id`)
  REFERENCES `item` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

ALTER TABLE `item_user`
DROP FOREIGN KEY `fk_item_user_4`;
ALTER TABLE `item_user`
ADD CONSTRAINT `fk_item_user_4`
  FOREIGN KEY (`group_id`)
  REFERENCES `group` (`id`)
  ON DELETE SET NULL
  ON UPDATE NO ACTION;

