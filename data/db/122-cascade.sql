ALTER TABLE `sub_answer` 
DROP FOREIGN KEY `fk_sub_answer_1`;
ALTER TABLE `sub_answer` 
ADD CONSTRAINT `fk_sub_answer_1`
  FOREIGN KEY (`bank_question_item_id`)
  REFERENCES `bank_question_item` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

