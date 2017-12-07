ALTER TABLE `criteria` 
DROP FOREIGN KEY `fk_criteria_1`;
ALTER TABLE `criteria` 
ADD CONSTRAINT `fk_criteria_1`
  FOREIGN KEY (`grading_policy_id`)
  REFERENCES `grading_policy` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;
