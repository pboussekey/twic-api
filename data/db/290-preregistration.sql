DROP TABLE IF EXISTS `preregistration` ;

CREATE TABLE IF NOT EXISTS `preregistration` (
  `email` VARCHAR(128) NOT NULL,
  `firstname` VARCHAR(128) NOT NULL,
  `lastname` VARCHAR(128) NOT NULL,
  `organization_id` INT UNSIGNED NULL,
  `account_token` VARCHAR(128) NOT NULL,
  `user_id` INT UNSIGNED NULL,
  INDEX `fk_preregistration_1_idx` (`organization_id` ASC),
  INDEX `fk_preregistration_2_idx` (`user_id` ASC),
  PRIMARY KEY (`account_token`),
  CONSTRAINT `fk_preregistration_1`
    FOREIGN KEY (`organization_id`)
    REFERENCES `page` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_preregistration_2`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;
