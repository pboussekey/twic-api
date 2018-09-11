INSERT IGNORE INTO `permission` (`libelle`) VALUES ('event.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'event.getList'));
INSERT IGNORE INTO `permission` (`libelle`) VALUES ('event.read');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'event.read'));
