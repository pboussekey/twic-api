INSERT IGNORE INTO `permission` (`libelle`) VALUES ('program.get');
DELETE FROM `role_permission` WHERE `permission_id`=(SELECT `id` FROM `permission` WHERE `libelle`= 'program.get');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, (SELECT `id` FROM `permission` WHERE `libelle`= 'program.get'));
