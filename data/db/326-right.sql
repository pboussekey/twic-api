ALTER TABLE `user` 
ADD COLUMN  `welcome_date` DATETIME NULL;
ALTER TABLE `user` 
ADD COLUMN  `welcome_delay` INT NULL;

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.closeWelcome');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.closeWelcome'));