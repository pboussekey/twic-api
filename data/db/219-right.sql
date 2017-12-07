INSERT IGNORE INTO `permission` (`libelle`) VALUES ('page.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'page.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('page.get');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'page.get'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('page.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'page.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('page.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'page.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.login');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0,
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.login'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.get');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0,
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.get'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.getListId');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0,
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.getListId'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0,
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('contact.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0,
(SELECT `id` FROM `permission` WHERE `libelle`= 'contact.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('contact.getListRequestId');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0,
(SELECT `id` FROM `permission` WHERE `libelle`= 'contact.getListRequestId'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('contact.accept');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0,
(SELECT `id` FROM `permission` WHERE `libelle`= 'contact.accept'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('contact.remove');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0,
(SELECT `id` FROM `permission` WHERE `libelle`= 'contact.remove'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('contact.getListId');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0,
(SELECT `id` FROM `permission` WHERE `libelle`= 'contact.getListId'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('post.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'post.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('post.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'post.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('post.get');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'post.get'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('post.getListId');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'post.getListId'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('post.like');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'post.like'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('post.unlike');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'post.unlike'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('post.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'post.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('post.reactivate');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (1, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'post.reactivate'));

SET SQL_SAFE_UPDATES=0;
UPDATE `role_permission`, `permission`
SET
`role_permission`.`role_id` = 2
WHERE role_permission.permission_id=permission.id AND role_permission.role_id=0 AND permission.libelle <> "user.login";

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

