ALTER TABLE `submission` 
DROP FOREIGN KEY `item_id`;
ALTER TABLE `submission` 
ADD COLUMN `post_id` INT UNSIGNED NULL AFTER `is_graded`,
ADD INDEX `fk_submission_2_idx` (`post_id` ASC);
ALTER TABLE `submission` 
ADD CONSTRAINT `fk_submission_1`
  FOREIGN KEY (`item_id`)
  REFERENCES `item` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_submission_2`
  FOREIGN KEY (`post_id`)
  REFERENCES `post` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

