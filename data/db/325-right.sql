INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.checkEmail');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.checkEmail'));