INSERT IGNORE INTO `permission` (`libelle`) VALUES ('pageuser.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'pageuser.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('pageuser.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'pageuser.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('pageuser.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'pageuser.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('pageuser.getListByUser');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'pageuser.getListByUser'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('pageuser.getListByPage');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'pageuser.getListByPage'));
