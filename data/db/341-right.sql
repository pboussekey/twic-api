INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.sendEmailUpdateConf');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.sendEmailUpdateConf'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.confirmEmailUpdate');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.confirmEmailUpdate'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.cancelEmailUpdate');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.cancelEmailUpdate'));