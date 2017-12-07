INSERT IGNORE INTO `permission` (`libelle`) VALUES ('resume.getListId');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'resume.getListId'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('pageuser.getListByUser');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'pageuser.getListByUser'));
