ALTER TABLE `quiz_user` 
DROP FOREIGN KEY `fk_quiz_user_1`,
DROP FOREIGN KEY `fk_quiz_user_2`,
DROP FOREIGN KEY `fk_quiz_user_3`,
DROP FOREIGN KEY `fk_quiz_user_4`;
ALTER TABLE `quiz_user` 
ADD CONSTRAINT `fk_quiz_user_1`
  FOREIGN KEY (`quiz_id`)
  REFERENCES `quiz` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_quiz_user_2`
  FOREIGN KEY (`quiz_question_id`)
  REFERENCES `quiz_question` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_quiz_user_3`
  FOREIGN KEY (`quiz_answer_id`)
  REFERENCES `quiz_answer` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_quiz_user_4`
  FOREIGN KEY (`user_id`)
  REFERENCES `user` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

