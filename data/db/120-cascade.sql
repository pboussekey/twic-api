ALTER TABLE `message_user` 
DROP FOREIGN KEY `fk_message_user_2`;
ALTER TABLE `message_user` 
ADD CONSTRAINT `fk_message_user_2`
  FOREIGN KEY (`conversation_id`)
  REFERENCES `conversation` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

