ALTER TABLE `message` 
ADD COLUMN `user_id` INT UNSIGNED NULL AFTER `created_date`,
ADD INDEX `fk_message_2_idx` (`user_id` ASC);
ALTER TABLE `message` 
ADD CONSTRAINT `fk_message_2`
  FOREIGN KEY (`user_id`)
  REFERENCES `user` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `message`
CHANGE COLUMN `token` `library_id` INT UNSIGNED NULL DEFAULT NULL ,
ADD INDEX `fk_message_3_idx` (`library_id` ASC);
ALTER TABLE `message`
ADD CONSTRAINT `fk_message_3`
  FOREIGN KEY (`library_id`)
  REFERENCES `library` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;
