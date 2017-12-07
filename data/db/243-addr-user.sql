ALTER TABLE `user` 
ADD COLUMN `address_id` INT NULL AFTER `email_sent`,
ADD INDEX `fk_user_2_idx` (`nationality` ASC),
ADD INDEX `fk_user_3_idx` (`origin` ASC);
ALTER TABLE `user` 
ADD CONSTRAINT `fk_user_2`
  FOREIGN KEY (`nationality`)
  REFERENCES `country` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_user_3`
  FOREIGN KEY (`origin`)
  REFERENCES `country` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;


ALTER TABLE `user`
CHANGE COLUMN `address_id` `address_id` INT(11) UNSIGNED NULL DEFAULT NULL ,
ADD INDEX `fk_user_4_idx` (`address_id` ASC);

ALTER TABLE `user`
ADD CONSTRAINT `fk_user_4`
  FOREIGN KEY (`address_id`)
  REFERENCES `address` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

