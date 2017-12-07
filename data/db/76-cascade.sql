ALTER TABLE `sub_quiz` 
DROP FOREIGN KEY `fk_poll_answer_6`;
ALTER TABLE `sub_quiz` 
ADD CONSTRAINT `fk_poll_answer_6`
  FOREIGN KEY (`submission_id`)
  REFERENCES `submission` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;
