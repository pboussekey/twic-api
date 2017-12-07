ALTER TABLE `sub_thread` 
DROP FOREIGN KEY `fk_sub_thread_1`,
DROP FOREIGN KEY `fk_sub_thread_2`;
ALTER TABLE `sub_thread` 
ADD CONSTRAINT `fk_sub_thread_1`
  FOREIGN KEY (`thread_id`)
  REFERENCES `thread` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_sub_thread_2`
  FOREIGN KEY (`submission_id`)
  REFERENCES `submission` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

