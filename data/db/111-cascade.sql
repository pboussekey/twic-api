ALTER TABLE `conversation` 
DROP FOREIGN KEY `fk_conversation_1`;
ALTER TABLE `conversation` 
ADD CONSTRAINT `fk_conversation_1`
  FOREIGN KEY (`conversation_opt_id`)
  REFERENCES `conversation_opt` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

