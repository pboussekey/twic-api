INSERT IGNORE INTO `permission` (`libelle`) VALUES ('page.getListOrg');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'page.getListOrg'));
