ALTER TABLE `submission` 
DROP COLUMN `group_name`,
DROP COLUMN `group_id`;

CREATE TABLE IF NOT EXISTS `item_user` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NULL,
  `item_id` INT UNSIGNED NULL,
  `rate` VARCHAR(45) NULL,
  `group_id` VARCHAR(45) NULL,
  `submission_id` INT UNSIGNED NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_item_user_1_idx` (`user_id` ASC),
  INDEX `fk_item_user_2_idx` (`item_id` ASC),
  INDEX `fk_item_user_3_idx` (`submission_id` ASC),
  CONSTRAINT `fk_item_user_1`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_item_user_2`
    FOREIGN KEY (`item_id`)
    REFERENCES `item` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_item_user_3`
    FOREIGN KEY (`submission_id`)
    REFERENCES `submission` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

ALTER TABLE `item`
ADD COLUMN `participants` ENUM('user', 'group', 'all') NOT NULL DEFAULT 'all' AFTER `points`;

ALTER TABLE `item_user` 
ADD COLUMN `deleted_date` DATETIME NULL AFTER `submission_id`;

ALTER TABLE `group`
DROP COLUMN `uid`;

ALTER TABLE `item_user`
CHANGE COLUMN `group_id` `group_id` INT UNSIGNED NULL DEFAULT NULL ,
ADD INDEX `fk_item_user_4_idx` (`group_id` ASC);
ALTER TABLE `item_user`
ADD CONSTRAINT `fk_item_user_4`
  FOREIGN KEY (`group_id`)
  REFERENCES `group` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `item_user`
DROP FOREIGN KEY `fk_item_user_2`,
DROP FOREIGN KEY `fk_item_user_3`;
ALTER TABLE `item_user`
ADD CONSTRAINT `fk_item_user_2`
  FOREIGN KEY (`item_id`)
  REFERENCES `item` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_item_user_3`
  FOREIGN KEY (`submission_id`)
  REFERENCES `submission` (`id`)
  ON DELETE SET NULL
  ON UPDATE NO ACTION;

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('item.deleteUsers');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'item.deleteUsers'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('item.getListItemUser');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'item.getListItemUser'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('item.addUsers');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'item.addUsers'));
