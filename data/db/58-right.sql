INSERT IGNORE INTO `permission` (`libelle`) VALUES ('videoconf.getListId');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (5, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'videoconf.getListId'));