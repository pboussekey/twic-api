INSERT IGNORE INTO `permission` (`libelle`) VALUES ('page.addTag');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'page.addTag'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('page.removeTag');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'page.removeTag'));

