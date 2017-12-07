ALTER TABLE `video_archive` 
DROP FOREIGN KEY `fk_video_archive_1`;
ALTER TABLE `video_archive` 
ADD CONSTRAINT `fk_video_archive_1`
  FOREIGN KEY (`conversation_id`)
  REFERENCES `conversation` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

