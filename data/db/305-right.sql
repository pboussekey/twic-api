INSERT IGNORE INTO `permission` (`libelle`) VALUES ('activity.getConnectionCount');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'activity.getConnectionCount'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('postlike.getCount');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'postlike.getCount'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('page.getCount');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'page.getCount'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('post.getCount');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'post.getCount'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('contact.getCounts');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'contact.getCounts'));
