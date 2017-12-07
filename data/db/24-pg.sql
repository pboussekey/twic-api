CREATE TABLE IF NOT EXISTS  `criteria` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NULL,
  `points` INT NULL,
  `description` TEXT NULL,
  `grading_policy_id` INT UNSIGNED NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_criteria_1_idx` (`grading_policy_id` ASC),
  CONSTRAINT `fk_criteria_1`
    FOREIGN KEY (`grading_policy_id`)
    REFERENCES  `grading_policy` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS  `submission_pg` (
  `user_id` INT UNSIGNED NOT NULL,
  `submission_id` INT UNSIGNED NOT NULL,
  `date` DATETIME NULL,
  PRIMARY KEY (`user_id`, `submission_id`),
  INDEX `fk_submission_pg_2_idx` (`submission_id` ASC),
  CONSTRAINT `fk_submission_pg_1`
    FOREIGN KEY (`user_id`)
    REFERENCES  `user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_submission_pg_2`
    FOREIGN KEY (`submission_id`)
    REFERENCES  `submission` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS  `pg_user_grade` (
  `pg_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `grade` INT NULL,
  PRIMARY KEY (`pg_id`, `user_id`),
  INDEX `fk_pg_user_grade_2_idx` (`user_id` ASC),
  CONSTRAINT `fk_pg_user_grade_1`
    FOREIGN KEY (`pg_id`)
    REFERENCES  `submission_pg` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_pg_user_grade_2`
    FOREIGN KEY (`user_id`)
    REFERENCES  `user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS  `submission_user_criteria` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `submission_id` INT UNSIGNED NULL,
  `user_id` INT UNSIGNED NULL,
  `criteria_id` INT UNSIGNED NULL,
  `points` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_submission_user_criteria_1_idx` (`submission_id` ASC),
  INDEX `fk_submission_user_criteria_2_idx` (`user_id` ASC),
  INDEX `fk_submission_user_criteria_3_idx` (`criteria_id` ASC),
  CONSTRAINT `fk_submission_user_criteria_1`
    FOREIGN KEY (`submission_id`)
    REFERENCES  `submission` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_submission_user_criteria_2`
    FOREIGN KEY (`user_id`)
    REFERENCES  `user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_submission_user_criteria_3`
    FOREIGN KEY (`criteria_id`)
    REFERENCES  `criteria` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS  `pg_user_criteria` (
  `pg_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `criteria_id` INT UNSIGNED NOT NULL,
  `points` INT NULL,
  PRIMARY KEY (`pg_id`, `user_id`, `criteria_id`),
  INDEX `fk_pg_user_criteria_1_idx` (`user_id` ASC),
  INDEX `fk_pg_user_criteria_2_idx` (`criteria_id` ASC),
  CONSTRAINT `fk_pg_user_criteria_1`
    FOREIGN KEY (`user_id`)
    REFERENCES  `user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_pg_user_criteria_2`
    FOREIGN KEY (`criteria_id`)
    REFERENCES  `criteria` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_pg_user_criteria_3`
    FOREIGN KEY (`pg_id`)
    REFERENCES  `submission_pg` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


























