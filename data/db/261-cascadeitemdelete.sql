INSERT IGNORE INTO `permission` (`libelle`) VALUES ('item.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'item.delete'));

