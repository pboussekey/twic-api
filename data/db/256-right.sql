INSERT IGNORE INTO `permission` (`libelle`) VALUES ('report.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'report.add'));
