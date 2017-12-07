INSERT IGNORE INTO `permission` (`libelle`) VALUES ('videoarchive.startRecord');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'videoarchive.startRecord'));


INSERT IGNORE INTO `permission` (`libelle`) VALUES ('videoarchive.stopRecord');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'videoarchive.stopRecord'));
