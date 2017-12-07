INSERT IGNORE INTO `permission` (`libelle`) VALUES ('circle.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (1,
(SELECT `id` FROM `permission` WHERE `libelle`= 'circle.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('circle.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (1,
(SELECT `id` FROM `permission` WHERE `libelle`= 'circle.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('circle.get');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (1,
(SELECT `id` FROM `permission` WHERE `libelle`= 'circle.get'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('circle.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (1,
(SELECT `id` FROM `permission` WHERE `libelle`= 'circle.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('circle.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (1,
(SELECT `id` FROM `permission` WHERE `libelle`= 'circle.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('circle.addOrganizations');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (1,
(SELECT `id` FROM `permission` WHERE `libelle`= 'circle.addOrganizations'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('circle.deleteOrganizations');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (1,
(SELECT `id` FROM `permission` WHERE `libelle`= 'circle.deleteOrganizations'));
