INSERT IGNORE INTO `permission` (`libelle`) VALUES ('library.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'library.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('pageuser.getListByuser');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'pageuser.getListByuser'));
