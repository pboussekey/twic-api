ALTER TABLE `post_user` 
DROP FOREIGN KEY `fk_post_user_1`;
ALTER TABLE `post_user` 
ADD CONSTRAINT `fk_post_user_1`
  FOREIGN KEY (`post_id`)
  REFERENCES `post` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;
