CREATE TABLE IF NOT EXISTS `organization_user` (
  `organization_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`organization_id`, `user_id`),
  INDEX `fk_organization_user_2_idx` (`user_id` ASC),
  CONSTRAINT `fk_organization_user_1`
    FOREIGN KEY (`organization_id`)
    REFERENCES `school` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_organization_user_2`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `organization_relation` (
  `organization_id` INT UNSIGNED NOT NULL,
  `parent_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`organization_id`, `parent_id`),
  INDEX `fk_organization_relation_2_idx` (`parent_id` ASC),
  CONSTRAINT `fk_organization_relation_1`
    FOREIGN KEY (`organization_id`)
    REFERENCES `school` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_organization_relation_2`
    FOREIGN KEY (`parent_id`)
    REFERENCES `school` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `circle` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `circle_organization` (
  `circle_id` INT UNSIGNED NOT NULL,
  `organization_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`circle_id`, `organization_id`),
  INDEX `fk_circle_organization_2_idx` (`organization_id` ASC),
  CONSTRAINT `fk_circle_organization_1`
    FOREIGN KEY (`circle_id`)
    REFERENCES `circle` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_circle_organization_2`
    FOREIGN KEY (`organization_id`)
    REFERENCES `school` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

ALTER TABLE `school` 
ADD COLUMN `libelle` VARCHAR(45) NULL AFTER `address_id`,
ADD COLUMN `custom` TEXT NULL AFTER `libelle`;

