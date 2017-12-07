ALTER TABLE `post_subscription` 
CHANGE COLUMN `last_date` `last_date` DATETIME NOT NULL ,
ADD COLUMN `id` INT UNSIGNED NOT NULL AUTO_INCREMENT FIRST,
ADD COLUMN `action` ENUM('create', 'update', 'com', 'like', 'tag') NOT NULL AFTER `last_date`,
ADD COLUMN `sub_post_id` INT UNSIGNED NULL AFTER `action`,
DROP PRIMARY KEY,
ADD PRIMARY KEY (`id`),
ADD INDEX `fk_post_subscription_2_idx` (`sub_post_id` ASC);
ALTER TABLE `post_subscription` 
ADD CONSTRAINT `fk_post_subscription_2`
  FOREIGN KEY (`sub_post_id`)
  REFERENCES `post` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

