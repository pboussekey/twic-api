CREATE TABLE IF NOT EXISTS `sub_conversation` (
  `conversation_id` INT UNSIGNED NOT NULL,
  `submission_id` INT UNSIGNED NOT NULL,
  INDEX `fk_conversation_1_idx` (`submission_id` ASC),
  PRIMARY KEY (`conversation_id`, `submission_id`),
  CONSTRAINT `fk_sub_conversation_2`
    FOREIGN KEY (`submission_id`)
    REFERENCES `submission` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_sub_conversation_1`	
    FOREIGN KEY (`conversation_id`)
    REFERENCES `conversation` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

ALTER TABLE `conversation` 
DROP FOREIGN KEY `fk_conversation_1`;

ALTER TABLE `conversation` 
DROP COLUMN `submission_id`,
DROP INDEX `fk_conversation_1_idx` ;
	

ALTER TABLE `sub_answer` 
DROP FOREIGN KEY `fk_poll_answer_item_2`;
ALTER TABLE `sub_answer` 
ADD INDEX `fk_poll_answer_item_2_idx` (`sub_question_id` ASC),
ADD INDEX `fk_sub_answer_1_idx` (`bank_question_item_id` ASC),
DROP INDEX `fk_poll_answer_items_1_idx` ;
ALTER TABLE `sub_answer` 
ADD CONSTRAINT `fk_poll_answer_item_2`
  FOREIGN KEY (`sub_question_id`)
  REFERENCES `sub_question` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_sub_answer_1`
  FOREIGN KEY (`bank_question_item_id`)
  REFERENCES `bank_question_item` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;