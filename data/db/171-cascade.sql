ALTER TABLE `post_subscription` 
DROP FOREIGN KEY `fk_post_subscription_1`;
ALTER TABLE `post_subscription` 
ADD CONSTRAINT `fk_post_subscription_1`
  FOREIGN KEY (`post_id`)
  REFERENCES `post` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

ALTER TABLE `post_like` 
DROP FOREIGN KEY `fk_post_like_1`,
DROP FOREIGN KEY `fk_post_like_2`;
ALTER TABLE `post_like` 
ADD CONSTRAINT `fk_post_like_1`
  FOREIGN KEY (`user_id`)
  REFERENCES `user` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_post_like_2`
  FOREIGN KEY (`post_id`)
  REFERENCES `post` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

ALTER TABLE `post_doc` 
DROP FOREIGN KEY `fk_page_doc_10`,
DROP FOREIGN KEY `fk_page_doc_20`,
DROP FOREIGN KEY `fk_page_doc_30`;
ALTER TABLE `post_doc` 
ADD CONSTRAINT `fk_page_doc_10`
  FOREIGN KEY (`post_id`)
  REFERENCES `post` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_page_doc_20`
  FOREIGN KEY (`library_id`)
  REFERENCES `library` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_page_doc_30`
  FOREIGN KEY (`user_id`)
  REFERENCES `user` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

ALTER TABLE `page_user` 
DROP FOREIGN KEY `fk_sgroup_user_2`;
ALTER TABLE `page_user` 
ADD CONSTRAINT `fk_sgroup_user_2`
  FOREIGN KEY (`user_id`)
  REFERENCES `user` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

