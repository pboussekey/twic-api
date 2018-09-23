ALTER TABLE `hashtag` 
ADD COLUMN  `user_id` INT(10) UNSIGNED NULL DEFAULT NULL;

ALTER TABLE `hashtag`
ADD INDEX `fk_hashtag_2_idx` (`user_id` ASC);
ALTER TABLE `hashtag`
ADD CONSTRAINT `fk_hashtag_2`
  FOREIGN KEY (`user_id`)
  REFERENCES `user` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

/*ALTER TABLE `hashtag`
ADD CONSTRAINT`fk_hashtag_1`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION;*/
