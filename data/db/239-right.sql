INSERT IGNORE INTO `permission` (`libelle`) VALUES ('item.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'item.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('item.get');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'item.get'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('item.getListId');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'item.getListId'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('item.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'item.update'));
