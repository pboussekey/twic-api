ALTER TABLE `conversation_user` 
DROP FOREIGN KEY `fk_conversation_user_1`,
DROP FOREIGN KEY `fk_conversation_user_2`;
ALTER TABLE `conversation_user` 
ADD CONSTRAINT `fk_conversation_user_1`
  FOREIGN KEY (`conversation_id`)
  REFERENCES `conversation` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_conversation_user_2`
  FOREIGN KEY (`user_id`)
  REFERENCES `user` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

