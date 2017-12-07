INSERT IGNORE INTO `permission` (`libelle`) VALUES ('gradingpolicy.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (5, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'gradingpolicy.add'));