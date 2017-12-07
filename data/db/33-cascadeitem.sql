ALTER TABLE `text_editor` 
DROP FOREIGN KEY `fk_assignment_1`;
ALTER TABLE `text_editor` 
ADD CONSTRAINT `fk_assignment_1`
  FOREIGN KEY (`submission_id`)
  REFERENCES `submission` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

  ALTER TABLE `document` 
DROP FOREIGN KEY `fk_document_2`,
DROP FOREIGN KEY `fk_document_3`;
ALTER TABLE `document` 
ADD CONSTRAINT `fk_document_2`
  FOREIGN KEY (`library_id`)
  REFERENCES `library` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_document_3`
  FOREIGN KEY (`submission_id`)
  REFERENCES `submission` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

  ALTER TABLE `sub_conversation` 
DROP FOREIGN KEY `fk_sub_conversation_1`,
DROP FOREIGN KEY `fk_sub_conversation_2`;
ALTER TABLE `sub_conversation` 
ADD CONSTRAINT `fk_sub_conversation_1`
  FOREIGN KEY (`conversation_id`)
  REFERENCES `conversation` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_sub_conversation_2`
  FOREIGN KEY (`submission_id`)
  REFERENCES `submission` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

 ALTER TABLE `videoconf` 
DROP FOREIGN KEY `fk_videoconf_1`,
DROP FOREIGN KEY `fk_videoconf_2`,
DROP FOREIGN KEY `fk_videoconf_3`;
ALTER TABLE `videoconf` 
ADD CONSTRAINT `fk_videoconf_1`
  FOREIGN KEY (`conversation_id`)
  REFERENCES `conversation` (`id`)
  ON DELETE SET NULL
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_videoconf_2`
  FOREIGN KEY (`submission_id`)
  REFERENCES `submission` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_videoconf_3`
  FOREIGN KEY (`videoconf_opt`)
  REFERENCES `videoconf_opt` (`item_id`)
  ON DELETE SET NULL
  ON UPDATE NO ACTION;
  
  ALTER TABLE `videoconf_admin` 
DROP FOREIGN KEY `fk_videoconf_admin_vodeoconf`;
ALTER TABLE `videoconf_admin` 
ADD CONSTRAINT `fk_videoconf_admin_vodeoconf`
  FOREIGN KEY (`videoconf_id`)
  REFERENCES `videoconf` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

 ALTER TABLE `videoconf_archive` 
DROP FOREIGN KEY `fk_videoconf_archive_1`;
ALTER TABLE `videoconf_archive` 
ADD CONSTRAINT `fk_videoconf_archive_1`
  FOREIGN KEY (`videoconf_id`)
  REFERENCES `videoconf` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

  ALTER TABLE `videoconf_conversation` 
DROP FOREIGN KEY `fk_videoconf_conversation_1`,
DROP FOREIGN KEY `fk_videoconf_conversation_2`;
ALTER TABLE `videoconf_conversation` 
ADD CONSTRAINT `fk_videoconf_conversation_1`
  FOREIGN KEY (`conversation_id`)
  REFERENCES `conversation` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_videoconf_conversation_2`
  FOREIGN KEY (`videoconf_id`)
  REFERENCES `videoconf` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;
