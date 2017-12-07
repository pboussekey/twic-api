ALTER TABLE `submission_pg` 
DROP FOREIGN KEY `fk_submission_pg_2`;
ALTER TABLE `submission_pg` 
ADD CONSTRAINT `fk_submission_pg_2`
  FOREIGN KEY (`submission_id`)
  REFERENCES `submission` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;
  