INSERT IGNORE INTO `permission` (`libelle`) VALUES ('mail.addTpl');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (1, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'mail.addTpl'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('mail.getListTpl');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (1, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'mail.getListTpl'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('mail.getTpl');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (1, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'mail.getTpl'));
