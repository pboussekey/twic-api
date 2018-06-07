CREATE TABLE IF NOT EXISTS `apilms`.`user_tag` (
  `user_id` INT(10) UNSIGNED NOT NULL,
  `tag_id` INT(10) UNSIGNED NOT NULL,
  INDEX `fk_sgroup_tag_1_idx` (`tag_id` ASC),
  INDEX `fk_sgroup_tag_2_idx` (`user_id` ASC),
  CONSTRAINT `fk_user_tag_1`
    FOREIGN KEY (`tag_id`)
    REFERENCES `apilms`.`tag` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_tag_2`
    FOREIGN KEY (`user_id`)
    REFERENCES `apilms`.`user` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.addTag');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.addTag'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.removeTag');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.removeTag'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('tag.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'tag.getList'));
