ALTER TABLE `page` 
ADD COLUMN `address_id` INT UNSIGNED NULL AFTER `page_id`,
ADD INDEX `fk_page_4_idx` (`address_id` ASC);
ALTER TABLE `page` 
ADD CONSTRAINT `fk_page_4`
  FOREIGN KEY (`address_id`)
  REFERENCES `address` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `page_user` 
CHANGE COLUMN `role` `role` ENUM('admin', 'user') NOT NULL DEFAULT 'user' ,
CHANGE COLUMN `state` `state` ENUM('pending', 'member', 'invited', 'rejected') NOT NULL DEFAULT 'member' ;

