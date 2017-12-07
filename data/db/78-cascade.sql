ALTER TABLE `sub_question` 
DROP FOREIGN KEY `fk_poll_answer_50`,
DROP FOREIGN KEY `fk_sub_question_1`;
ALTER TABLE `sub_question` 
ADD CONSTRAINT `fk_poll_answer_50`
  FOREIGN KEY (`group_question_id`)
  REFERENCES `group_question` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_sub_question_1`
  FOREIGN KEY (`sub_quiz_id`)
  REFERENCES `sub_quiz` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

