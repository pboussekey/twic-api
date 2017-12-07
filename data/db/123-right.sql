INSERT IGNORE INTO `permission` (`libelle`) VALUES ('videoarchive.validTransfertVideo');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0,
(SELECT `id` FROM `permission` WHERE `libelle`= 'videoarchive.validTransfertVideo'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('videoarchive.getListVideoUpload');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0,
(SELECT `id` FROM `permission` WHERE `libelle`= 'videoarchive.getListVideoUpload'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('item.getListTmp');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4,
(SELECT `id` FROM `permission` WHERE `libelle`= 'item.getListTmp'));

