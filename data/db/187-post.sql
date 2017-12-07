ALTER TABLE `submission_user_criteria` 
DROP FOREIGN KEY `fk_submission_user_criteria_3`;
ALTER TABLE `submission_user_criteria` 
ADD CONSTRAINT `fk_submission_user_criteria_3`
  FOREIGN KEY (`criteria_id`)
  REFERENCES `criteria` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;


