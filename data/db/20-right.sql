INSERT IGNORE INTO `permission` (`libelle`) VALUES ('subquiz.start');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'subquiz.start'));
