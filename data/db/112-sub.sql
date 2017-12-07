CREATE TABLE IF NOT EXISTS `sub_whiteboard` (
  `submission_id` INT UNSIGNED NOT NULL,
  `whiteboard_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`submission_id`, `whiteboard_id`),
  INDEX `fk_sub_whiteboard_1_idx` (`whiteboard_id` ASC),
  CONSTRAINT `fk_sub_whiteboard_1`
    FOREIGN KEY (`whiteboard_id`)
    REFERENCES `whiteboard` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_sub_whiteboard_2`
    FOREIGN KEY (`submission_id`)
    REFERENCES `submission` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `sub_text_editor` (
  `submission_id` INT UNSIGNED NOT NULL,
  `text_editor_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`submission_id`, `text_editor_id`),
  INDEX `fk_sub_text_editor_1_idx` (`text_editor_id` ASC),
  CONSTRAINT `fk_sub_text_editor_10`
    FOREIGN KEY (`text_editor_id`)
    REFERENCES `text_editor` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_sub_text_editor_20`
    FOREIGN KEY (`submission_id`)
    REFERENCES `submission` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

ALTER TABLE `text_editor` 
DROP FOREIGN KEY `fk_assignment_1`;
ALTER TABLE `text_editor` 
DROP COLUMN `submission_id`,
DROP INDEX `fk_assignment_1_idx` ;

