CREATE TABLE IF NOT EXISTS `conversation_text_editor` (
  `conversation_id` INT(10) UNSIGNED NOT NULL,
  `text_editor_id` INT UNSIGNED NOT NULL,
  INDEX `fk_conversation_doc_1_idx` (`conversation_id` ASC),
  PRIMARY KEY (`conversation_id`, `text_editor_id`),
  INDEX `fk_conversation_doc_2_idx` (`text_editor_id` ASC),
  CONSTRAINT `fk_conversation_doc_10`
    FOREIGN KEY (`conversation_id`)
    REFERENCES `conversation` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_conversation_doc_20`
    FOREIGN KEY (`text_editor_id`)
    REFERENCES `library` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `whiteboard` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `conversation_whiteboard` (
  `conversation_id` INT(10) UNSIGNED NOT NULL,
  `whiteboard_id` INT UNSIGNED NOT NULL,
  INDEX `fk_conversation_doc_1_idx` (`conversation_id` ASC),
  PRIMARY KEY (`conversation_id`, `whiteboard_id`),
  INDEX `fk_conversation_doc_200_idx` (`whiteboard_id` ASC),
  CONSTRAINT `fk_conversation_doc_100`
    FOREIGN KEY (`conversation_id`)
    REFERENCES `conversation` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_conversation_doc_200`
    FOREIGN KEY (`whiteboard_id`)
    REFERENCES `whiteboard` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `conversation_doc` (
  `conversation_id` INT(10) UNSIGNED NOT NULL,
  `library_id` INT UNSIGNED NOT NULL,
  INDEX `fk_conversation_doc_1_idx` (`conversation_id` ASC),
  PRIMARY KEY (`conversation_id`, `library_id`),
  INDEX `fk_conversation_doc_2_idx` (`library_id` ASC),
  CONSTRAINT `fk_conversation_doc_1`
    FOREIGN KEY (`conversation_id`)
    REFERENCES `conversation` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_conversation_doc_2`
    FOREIGN KEY (`library_id`)
    REFERENCES `library` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `sub_conversation` (
  `conversation_id` INT(10) UNSIGNED NOT NULL,
  `submission_id` INT UNSIGNED NOT NULL,
  INDEX `fk_conversation_1_idx` (`submission_id` ASC),
  PRIMARY KEY (`conversation_id`, `submission_id`),
  CONSTRAINT `fk_sub_conversation_2`
    FOREIGN KEY (`submission_id`)
    REFERENCES `submission` (`id`)
    ON DELETE SET NULL
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_sub_conversation_1`
    FOREIGN KEY (`conversation_id`)
    REFERENCES `conversation` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `video_archive` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `archive_token` TEXT NULL DEFAULT NULL,
  `archive_link` TEXT NULL DEFAULT NULL,
  `archive_status` VARCHAR(128) NULL DEFAULT NULL,
  `archive_duration` INT(11) NULL DEFAULT NULL,
  `conversation_id` INT(10) UNSIGNED NOT NULL,
  `created_date` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_video_archive_1_idx` (`conversation_id` ASC),
  CONSTRAINT `fk_video_archive_1`
    FOREIGN KEY (`conversation_id`)
    REFERENCES `conversation` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

DROP TABLE `videoconf_archive`;

CREATE TABLE IF NOT EXISTS `conversation_conversation` (
  `id` INT(10) UNSIGNED NOT NULL,
  `conversation_id` INT(10) UNSIGNED NOT NULL,
  INDEX `fk_conversation_conversation_1_idx` (`conversation_id` ASC),
  CONSTRAINT `fk_conversation_conversation_1`
    FOREIGN KEY (`conversation_id`)
    REFERENCES `conversation` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;






