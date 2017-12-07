INSERT IGNORE INTO `permission` (`libelle`) VALUES ('page.addDocument');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'page.addDocument'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('page.getListDocument');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'page.getListDocument'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('page.deleteDocument');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'page.deleteDocument'));
