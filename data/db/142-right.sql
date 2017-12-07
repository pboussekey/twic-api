INSERT IGNORE INTO `permission` (`libelle`) VALUES ('report.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'report.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('report.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (1, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'report.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('report.treat');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (1, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'report.treat'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('eventcomment.get');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (1, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'eventcomment.get'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('feed.reactivate');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (1, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'feed.reactivate'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('eventcomment.reactivate');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (1, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'eventcomment.reactivate'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.suspend');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (1, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.suspend'));

