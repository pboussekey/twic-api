ALTER TABLE `hashtag`
ADD COLUMN  `tag_id` INT(10) UNSIGNED NULL DEFAULT NULL;

ALTER TABLE `hashtag`
ADD INDEX `fk_hashtag_3_idx` (`tag_id` ASC);
ALTER TABLE `hashtag`
ADD CONSTRAINT `fk_hashtag_3`
  FOREIGN KEY (`tag_id`)
  REFERENCES `tag` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;
