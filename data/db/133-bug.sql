ALTER TABLE `conversation_text_editor` 
DROP FOREIGN KEY `fk_conversation_doc_20`;
ALTER TABLE `conversation_text_editor` 
ADD INDEX `fk_conversation_doc_20_idx` (`text_editor_id` ASC),
DROP INDEX `fk_conversation_doc_2_idx` ;
ALTER TABLE `conversation_text_editor` 
ADD CONSTRAINT `fk_conversation_doc_20`
  FOREIGN KEY (`text_editor_id`)
  REFERENCES `text_editor` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

