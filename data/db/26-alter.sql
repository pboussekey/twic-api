ALTER TABLE `pg_user_criteria` 
ADD COLUMN `submission_id` INT UNSIGNED NOT NULL AFTER `criteria_id`,
DROP PRIMARY KEY,
ADD PRIMARY KEY (`pg_id`, `user_id`, `criteria_id`, `submission_id`),
ADD INDEX `fk_pg_user_criteria_4_idx` (`submission_id` ASC);
ALTER TABLE `pg_user_criteria` 
ADD CONSTRAINT `fk_pg_user_criteria_4`
  FOREIGN KEY (`submission_id`)
  REFERENCES `submission` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;
  
ALTER TABLE `submission_user_criteria` 
ADD COLUMN `overwritten` TINYINT NOT NULL DEFAULT 0 AFTER `points`;

ALTER TABLE `pg_user_grade` 
ADD COLUMN `submission_id` INT UNSIGNED NOT NULL AFTER `user_id`,
DROP PRIMARY KEY,
ADD PRIMARY KEY (`pg_id`, `user_id`, `submission_id`),
ADD INDEX `fk_pg_user_grade_3_idx` (`submission_id` ASC);
ALTER TABLE `pg_user_grade` 
ADD CONSTRAINT `fk_pg_user_grade_3`
  FOREIGN KEY (`submission_id`)
  REFERENCES `submission` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `submission_pg` 
ADD COLUMN `has_graded` TINYINT NOT NULL DEFAULT 0 AFTER `date`;
