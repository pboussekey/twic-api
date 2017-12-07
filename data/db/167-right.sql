INSERT IGNORE INTO `permission` (`libelle`) VALUES ('page.getLite');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4,
(SELECT `id` FROM `permission` WHERE `libelle`= 'page.getLite'));
