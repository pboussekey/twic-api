INSERT IGNORE INTO `permission` (`libelle`) VALUES ('set.get');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'set.get'));