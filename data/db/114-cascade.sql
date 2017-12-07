ALTER TABLE `conversation_doc` 
DROP FOREIGN KEY `fk_conversation_doc_1`,
DROP FOREIGN KEY `fk_conversation_doc_2`;
ALTER TABLE `conversation_doc` 
ADD CONSTRAINT `fk_conversation_doc_1`
  FOREIGN KEY (`conversation_id`)
  REFERENCES `conversation` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_conversation_doc_2`
  FOREIGN KEY (`library_id`)
  REFERENCES `library` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

ALTER TABLE `bank_question` 
DROP FOREIGN KEY `fk_bank_question_11`;
ALTER TABLE `bank_question` 
ADD CONSTRAINT `fk_bank_question_11`
  FOREIGN KEY (`course_id`)
  REFERENCES `course` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

