CREATE TABLE IF NOT EXISTS `event_subscription` (
  `libelle` VARCHAR(80) NOT NULL,
  `event_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`libelle`, `event_id`),
  INDEX `idx_event_subscription` (`libelle` ASC),
  INDEX `fk_event_subscription_1_idx` (`event_id` ASC),
  CONSTRAINT `fk_event_subscription_1`
    FOREIGN KEY (`event_id`)
    REFERENCES `event` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;
