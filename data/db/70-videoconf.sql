ALTER TABLE `videoconf_opt` 
RENAME TO  `conversation_opt` ;

ALTER TABLE `conversation_opt` 
DROP FOREIGN KEY `fk_opt_assignment_10`;
ALTER TABLE `conversation_opt` 
CHANGE COLUMN `item_id` `item_id` INT(10) UNSIGNED NULL ,
DROP PRIMARY KEY;
ALTER TABLE `conversation_opt` 
ADD CONSTRAINT `fk_opt_assignment_10`
  FOREIGN KEY (`item_id`)
  REFERENCES `item` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

ALTER TABLE `conversation_opt` 
ADD COLUMN `id` INT UNSIGNED NOT NULL AUTO_INCREMENT FIRST,
ADD PRIMARY KEY (`id`);

ALTER TABLE `conversation_opt` 
ADD COLUMN `start_date` DATETIME NULL AFTER `allow_intructor`,
ADD COLUMN `duration` INT UNSIGNED NULL AFTER `start_date`;

ALTER TABLE `conversation` 
ADD COLUMN `token` VARCHAR(255) NULL AFTER `type`,
ADD COLUMN `conversation_opt_id` INT UNSIGNED NULL AFTER `token`,
ADD INDEX `fk_conversation_1_idx` (`conversation_opt_id` ASC);
ALTER TABLE `conversation` 
ADD CONSTRAINT `fk_conversation_1`
  FOREIGN KEY (`conversation_opt_id`)
  REFERENCES `conversation_opt` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('conversationopt.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'conversationopt.update'));
