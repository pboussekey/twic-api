ALTER TABLE `post_subscription` 
DROP FOREIGN KEY `fk_post_subscription_2`;
ALTER TABLE `post_subscription` 
ADD CONSTRAINT `fk_post_subscription_2`
  FOREIGN KEY (`sub_post_id`)
  REFERENCES `post` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

