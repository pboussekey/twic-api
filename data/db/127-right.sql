INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.getListAttendees');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4,
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.getListAttendees'));

