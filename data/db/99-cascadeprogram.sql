ALTER TABLE `grading_policy` 
DROP FOREIGN KEY `fk_grading_policy_1`;
ALTER TABLE `grading_policy` 
ADD CONSTRAINT `fk_grading_policy_1`
  FOREIGN KEY (`course_id`)
  REFERENCES `course` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

