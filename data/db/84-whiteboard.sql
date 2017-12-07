ALTER TABLE `whiteboard` 
ADD COLUMN `name` VARCHAR(255) NULL AFTER `id`,
ADD COLUMN `owner_id` INT UNSIGNED NULL AFTER `name`,
ADD INDEX `fk_whiteboard_1_idx` (`owner_id` ASC);
ALTER TABLE `whiteboard` 
ADD CONSTRAINT `fk_whiteboard_1`
  FOREIGN KEY (`owner_id`)
  REFERENCES `user` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

