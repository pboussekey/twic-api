CREATE TABLE IF NOT EXISTS `submission_library` (
  `submission_id` INT UNSIGNED NOT NULL,
  `library_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`submission_id`, `library_id`),
  INDEX `fk_submission_library_1_idx` (`library_id` ASC),
  CONSTRAINT `fk_submission_library_1`
    FOREIGN KEY (`library_id`)
    REFERENCES `library` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_submission_library_2`
    FOREIGN KEY (`submission_id`)
    REFERENCES `submission` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

ALTER TABLE `library`
ADD COLUMN `text` TEXT NULL AFTER `global`;

CREATE TABLE IF NOT EXISTS `quiz` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NULL,
  `attempt_count` INT NULL,
  `time_limit` INT NULL,
  `created_date` DATETIME NULL,
  `item_id` INT UNSIGNED NULL,
  `user_id` INT UNSIGNED NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_quiz_1_idx` (`item_id` ASC),
  INDEX `fk_quiz_2_idx` (`user_id` ASC),
  CONSTRAINT `fk_quiz_1`
    FOREIGN KEY (`item_id`)
    REFERENCES `item` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_quiz_2`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('submission.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'submission.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('submission.submit');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'submission.submit'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('submission.getListLibrary');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'submission.getListLibrary'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('submission.remove');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'submission.remove'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('quiz.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'quiz.add'));
