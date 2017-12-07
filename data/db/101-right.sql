INSERT IGNORE INTO `permission` (`libelle`) VALUES ('conversation.addVideo');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (3, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'conversation.addVideo'));

