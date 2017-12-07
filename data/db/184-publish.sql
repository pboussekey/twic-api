ALTER TABLE `course` 
ADD COLUMN `is_published` TINYINT NOT NULL DEFAULT 0 AFTER `sis`;

ALTER TABLE `message_doc` 
DROP FOREIGN KEY `fk_message_doc_1`;
ALTER TABLE `message_doc` 
ADD CONSTRAINT `fk_message_doc_1`
  FOREIGN KEY (`message_id`)
  REFERENCES `message` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

