INSERT IGNORE INTO `permission` (`libelle`) VALUES ('activity.getVisitsPrc');
INSERT IGNORE INTO `permission` (`libelle`) VALUES ('activity.getVisitsPrc');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'activity.getVisitsPrc'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('item.getCountByPage');
INSERT IGNORE INTO `permission` (`libelle`) VALUES ('item.getCountByPage');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'item.getCountByPage'));
