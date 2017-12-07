INSERT IGNORE INTO `permission` (`libelle`) VALUES ('country.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'country.getList'));


INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.lostPassword');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.lostPassword'));
