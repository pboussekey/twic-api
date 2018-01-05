ALTER TABLE `user_role` 
DROP FOREIGN KEY `fk_user_role_2`;
ALTER TABLE `user_role` 
ADD CONSTRAINT `fk_user_role_2`
    FOREIGN KEY (`role_id`)
    REFERENCES `role` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;