INSERT IGNORE INTO `permission` (`libelle`) VALUES ('videoarchive.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'videoarchive.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('videoarchive.startRecord');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'videoarchive.startRecord'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('videoarchive.stopRecord');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'videoarchive.stopRecord'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('videoarchive.validTransfertVideo');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'videoarchive.validTransfertVideo'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('videoarchive.getListVideoUpload');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'videoarchive.getListVideoUpload'));

