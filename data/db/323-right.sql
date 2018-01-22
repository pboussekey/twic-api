INSERT IGNORE INTO `permission` (`libelle`) VALUES ('activity.getDocumentsOpeningPrc');
INSERT IGNORE INTO `permission` (`libelle`) VALUES ('activity.getDocumentsOpeningPrc');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'activity.getDocumentsOpeningPrc'));
