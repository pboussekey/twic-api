ALTER TABLE `item` 
DROP FOREIGN KEY `fk_item_4`;
ALTER TABLE `item` 
ADD CONSTRAINT `fk_item_4`
  FOREIGN KEY (`grading_policy_id`)
  REFERENCES `grading_policy` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

