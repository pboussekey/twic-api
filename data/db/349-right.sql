INSERT IGNORE INTO `permission` (`libelle`) VALUES ('page.getListByEmail');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'page.getListByEmail'));
