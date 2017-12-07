INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.getListByItem');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.getListByItem'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('videoconf.getByItem');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'videoconf.getByItem'));