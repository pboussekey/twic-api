INSERT IGNORE INTO `permission` (`libelle`) VALUES ('page.getGrades');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'page.getGrades'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('page.getUsersGrades');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'page.getUsersGrades'));
