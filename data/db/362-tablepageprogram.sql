CREATE TABLE IF NOT EXISTS `page_program` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `user_id` INT UNSIGNED NULL,
  `page_id` INT UNSIGNED NOT NULL,
  `created_date` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `user_id_idx` (`user_id` ASC),
  INDEX `fk_page_program_2_idx` (`page_id` ASC),
  CONSTRAINT `fk_page_program_1`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_page_program_2`
    FOREIGN KEY (`page_id`)
    REFERENCES `page` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


CREATE TABLE IF NOT EXISTS `page_program_user` (
  `user_id` INT UNSIGNED NOT NULL,
  `page_program_id` INT UNSIGNED NOT NULL,
  `created_date` DATETIME NULL,
  PRIMARY KEY (`user_id`, `page_program_id`),
  INDEX `fk_page_program_user_2_idx` (`page_program_id` ASC),
  CONSTRAINT `fk_page_program_user_1`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_page_program_user_2`
    FOREIGN KEY (`page_program_id`)
    REFERENCES `page_program` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;
