INSERT IGNORE INTO `permission` (`libelle`) VALUES ('language.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'language.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('conversation.addVideo');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'conversation.addVideo'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('conversation.getToken');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'conversation.getToken'));

