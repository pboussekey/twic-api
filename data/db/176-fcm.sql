CREATE TABLE IF NOT EXISTS `gcm_group` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `notification_key_name` VARCHAR(300) NOT NULL,
  `notification_key` VARCHAR(300) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `gcm_registration` (
  `gcm_group_id` INT UNSIGNED NOT NULL,
  `registration_id` VARCHAR(512) NOT NULL,
  `uuid` VARCHAR(512) NOT NULL,
  CONSTRAINT `fk_gcm_registration_1`
    FOREIGN KEY (`gcm_group_id`)
    REFERENCES `gcm_group` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.registerFcm');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4,
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.registerFcm'));

