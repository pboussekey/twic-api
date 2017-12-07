ALTER TABLE `user` 
CHANGE COLUMN `birth_date` `birth_date` DATETIME NULL ;

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.auth');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (7, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.auth'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.create');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (7, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.create'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.listing');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (7, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.listing'));
