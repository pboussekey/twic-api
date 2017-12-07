ALTER TABLE `conversation_text_editor` 
DROP FOREIGN KEY `fk_conversation_doc_10`,
DROP FOREIGN KEY `fk_conversation_doc_20`;
ALTER TABLE `conversation_text_editor` 
ADD CONSTRAINT `fk_conversation_doc_10`
  FOREIGN KEY (`conversation_id`)
  REFERENCES `conversation` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_conversation_doc_20`
  FOREIGN KEY (`text_editor_id`)
  REFERENCES `library` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

ALTER TABLE `conversation_whiteboard` 
DROP FOREIGN KEY `fk_conversation_doc_100`,
DROP FOREIGN KEY `fk_conversation_doc_200`;
ALTER TABLE `conversation_whiteboard` 
ADD CONSTRAINT `fk_conversation_doc_100`
  FOREIGN KEY (`conversation_id`)
  REFERENCES `conversation` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_conversation_doc_200`
  FOREIGN KEY (`whiteboard_id`)
  REFERENCES `whiteboard` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

DROP TABLE `videoconf_conversation`;
DROP TABLE `videoconf_doc`;
DROP TABLE `videoconf_user`;
DROP TABLE `videoconf`;

ALTER TABLE `thread` 
DROP FOREIGN KEY `fk_thread_1`,
DROP FOREIGN KEY `fk_thread_3`;
ALTER TABLE `thread` 
ADD CONSTRAINT `fk_thread_1`
  FOREIGN KEY (`course_id`)
  REFERENCES `course` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_thread_3`
  FOREIGN KEY (`item_id`)
  REFERENCES `item` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

