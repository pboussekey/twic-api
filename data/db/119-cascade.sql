ALTER TABLE `message` 
DROP FOREIGN KEY `fk_message_1`;
ALTER TABLE `message` 
ADD CONSTRAINT `fk_message_1`
  FOREIGN KEY (`conversation_id`)
  REFERENCES `conversation` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

