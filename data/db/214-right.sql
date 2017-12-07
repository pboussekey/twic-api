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

