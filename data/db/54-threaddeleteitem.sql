ALTER TABLE `thread` 
DROP FOREIGN KEY `fk_thread_3`;

ALTER TABLE `thread` 
ADD CONSTRAINT `fk_thread_3`
  FOREIGN KEY (`item_id`)
  REFERENCES `item` (`id`)
  ON DELETE SET NULL
  ON UPDATE NO ACTION;

  
ALTER TABLE `thread_message` 
DROP FOREIGN KEY `fk_thread_message_2`;
ALTER TABLE `thread_message` 
ADD CONSTRAINT `fk_thread_message_2`
  FOREIGN KEY (`thread_id`)
  REFERENCES `thread` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

  