DROP TABLE `user_language`;
DROP TABLE `feed`;
DROP TABLE `sub_thread`;
DROP TABLE `thread_message`;
DROP TABLE `thread`;
DROP TABLE `mail`;

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('message.send');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'message.send'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('conversation.getIdByUser');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'conversation.getIdByUser'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('conversation.get');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'conversation.get'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('conversation.getListId');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'conversation.getListId'));

