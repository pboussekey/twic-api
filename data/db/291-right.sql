INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.signIn');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.signIn'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.lindekinSignIn');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.lindekinSignIn'));

