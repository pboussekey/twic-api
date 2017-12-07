ALTER TABLE `submission_user_criteria` 
DROP FOREIGN KEY `fk_submission_user_criteria_1`;
ALTER TABLE `submission_user_criteria` 
ADD CONSTRAINT `fk_submission_user_criteria_1`
  FOREIGN KEY (`submission_id`)
  REFERENCES `submission` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;
