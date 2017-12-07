INSERT IGNORE INTO `permission` (`libelle`) VALUES ('resume.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'resume.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('resume.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'resume.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('resume.get');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'resume.get'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('resume.getListId');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'resume.getListId'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('resume.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'resume.delete'));




INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('post.linkPreview');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'post.linkPreview'));

