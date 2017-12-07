CREATE TABLE IF NOT EXISTS `quiz_question` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `quiz_id` INT(10) UNSIGNED NULL DEFAULT NULL,
  `point` INT NULL,
  `text` TEXT NULL DEFAULT NULL,
  `type` ENUM('simple', 'multiple', 'text') NULL,
  `order` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_quiz_question_1_idx` (`quiz_id` ASC),
  CONSTRAINT `fk_quiz_question_1`
    FOREIGN KEY (`quiz_id`)
    REFERENCES `quiz` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `quiz_answer` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `quiz_question_id` INT(10) UNSIGNED NOT NULL,
  `text` TEXT NULL DEFAULT NULL,
  `is_correct` TINYINT(1) NULL DEFAULT 0,
  `order` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_answer_1_idx` (`quiz_question_id` ASC),
  CONSTRAINT `fk_quiz_answer_1`
    FOREIGN KEY (`quiz_question_id`)
    REFERENCES `quiz_question` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `quiz_user` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `quiz_id` INT UNSIGNED NULL,
  `quiz_question_id` INT UNSIGNED NULL,
  `quiz_answer_id` INT UNSIGNED NULL,
  `user_id` INT UNSIGNED NULL,
  `text` TEXT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_quiz_user_1_idx` (`quiz_id` ASC),
  INDEX `fk_quiz_user_2_idx` (`quiz_question_id` ASC),
  INDEX `fk_quiz_user_4_idx` (`user_id` ASC),
  INDEX `fk_quiz_user_3_idx` (`quiz_answer_id` ASC),
  CONSTRAINT `fk_quiz_user_1`
    FOREIGN KEY (`quiz_id`)
    REFERENCES `quiz` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_quiz_user_2`
    FOREIGN KEY (`quiz_question_id`)
    REFERENCES `quiz_question` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_quiz_user_3`
    FOREIGN KEY (`quiz_answer_id`)
    REFERENCES `quiz_answer` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_quiz_user_4`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;
