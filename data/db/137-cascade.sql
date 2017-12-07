ALTER TABLE `thread_message` 
DROP FOREIGN KEY `fk_thread_message_3`;
ALTER TABLE `thread_message` 
ADD CONSTRAINT `fk_thread_message_3`
  FOREIGN KEY (`parent_id`)
  REFERENCES `thread_message` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

