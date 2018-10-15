INSERT IGNORE INTO `permission` (`libelle`) VALUES ('pageprogram.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'pageprogram.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('pageprogram.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'pageprogram.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('pageprogram.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'pageprogram.delete'));

