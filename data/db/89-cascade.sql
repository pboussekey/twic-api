ALTER TABLE `pg_user_grade` 
DROP FOREIGN KEY `fk_pg_user_grade_3`;
ALTER TABLE `pg_user_grade` 
ADD CONSTRAINT `fk_pg_user_grade_3`
  FOREIGN KEY (`submission_id`)
  REFERENCES `submission` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;


ALTER TABLE `pg_user_criteria` 
DROP FOREIGN KEY `fk_pg_user_criteria_4`;
ALTER TABLE `pg_user_criteria` 
ADD CONSTRAINT `fk_pg_user_criteria_4`
  FOREIGN KEY (`submission_id`)
  REFERENCES `submission` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;
