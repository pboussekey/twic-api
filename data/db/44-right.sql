INSERT IGNORE INTO `permission` (`libelle`) VALUES ('submission.addComment');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'submission.addComment'));