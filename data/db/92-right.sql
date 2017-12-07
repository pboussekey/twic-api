INSERT IGNORE INTO `permission` (`libelle`) VALUES ('conversation.removeTextEditor');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'conversation.removeTextEditor'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('conversation.removeWhiteboard');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'conversation.removeWhiteboard'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('conversation.removeDocument');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'conversation.removeDocument'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('conversation.removeConversation');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'conversation.removeConversation'));

