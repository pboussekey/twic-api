ALTER TABLE `event`
ADD COLUMN  `text` TEXT NULL DEFAULT NULL;

ALTER TABLE `event`
ADD COLUMN  `picture` VARCHAR(255) NULL DEFAULT NULL;

ALTER TABLE `event`
ADD COLUMN  `target_id` INT(11) UNSIGNED NULL DEFAULT NULL;

ALTER TABLE `event`
ADD COLUMN  `academic` TINYINT(1) NOT NULL DEFAULT 0;

ALTER TABLE `event`
ADD COLUMN  `uid` VARCHAR(45) NULL DEFAULT NULL;

ALTER TABLE `event`
ADD COLUMN  `previous_id` INT(11) UNSIGNED NULL DEFAULT NULL;

ALTER TABLE `event`
MODIFY  `user_id` INT(11) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `event`
ADD INDEX `fk_event_1_idx` (`target_id` ASC),
ADD INDEX `fk_event_2_idx` (`user_id` ASC);

ALTER TABLE user CHANGE has_email_notifier has_academic_notifier TINYINT(1) DEFAULT 1;
ALTER TABLE user CHANGE has_email_contact_request_notifier has_social_notifier TINYINT(1) DEFAULT 1;


INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.getSettings');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0,
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.getSettings'));


INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.updateSettings');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0,
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.updateSettings'));

ALTER TABLE `event`
ADD CONSTRAINT `fk_event_1`
    FOREIGN KEY (`target_id`)
    REFERENCES `user` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION;

ALTER TABLE `event`
ADD CONSTRAINT `fk_event_2`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION;


ALTER TABLE `event`
ADD CONSTRAINT `fk_event_3`
    FOREIGN KEY (`previous_id`)
    REFERENCES `event` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION;
