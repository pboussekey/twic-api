CREATE TABLE IF NOT EXISTS `report` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `reporter_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NULL,
  `post_id` INT UNSIGNED NULL,
  `comment_id` INT UNSIGNED NULL,
  `created_date` DATETIME NOT NULL,
  `treatment_date` DATETIME NULL,
  `treated` TINYINT(1) NOT NULL DEFAULT 0,
  `reason` VARCHAR(45) NOT NULL,
  `description` TEXT NULL,
  `validate` TINYINT(1) NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `index2` (`reporter_id` ASC, `user_id` ASC, `post_id` ASC, `comment_id` ASC),
  CONSTRAINT `fk_Report_1`
    FOREIGN KEY (`reporter_id`)
    REFERENCES `user` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Report_2`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Report_3`
    FOREIGN KEY (`post_id`)
    REFERENCES `event` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Report_4`
    FOREIGN KEY (`comment_id`)
    REFERENCES `event_comment` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;
