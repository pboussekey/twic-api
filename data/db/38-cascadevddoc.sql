ALTER TABLE `videoconf_doc` 
DROP FOREIGN KEY `fk_videoconf_doc_1`;
ALTER TABLE `videoconf_doc` 
ADD CONSTRAINT `fk_videoconf_doc_1`
  FOREIGN KEY (`videoconf_id`)
  REFERENCES `videoconf` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;
