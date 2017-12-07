CREATE TABLE IF NOT EXISTS `sub_thread` (
  `submission_id` INT UNSIGNED NOT NULL,
  `thread_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`submission_id`, `thread_id`),
  INDEX `fk_sub_thread_1_idx` (`thread_id` ASC),
  CONSTRAINT `fk_sub_thread_1`
    FOREIGN KEY (`thread_id`)
    REFERENCES `thread` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_sub_thread_2`
    FOREIGN KEY (`submission_id`)
    REFERENCES `submission` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;