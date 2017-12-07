ALTER TABLE `submission` 
DROP FOREIGN KEY `item_id`;
ALTER TABLE `submission` 
ADD CONSTRAINT `item_id`
  FOREIGN KEY (`item_id`)
  REFERENCES `item` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

ALTER TABLE `submission_user` 
DROP FOREIGN KEY `fk_submission_user_1`;
ALTER TABLE `submission_user` 
ADD CONSTRAINT `fk_submission_user_1`
  FOREIGN KEY (`submission_id`)
  REFERENCES `submission` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;
