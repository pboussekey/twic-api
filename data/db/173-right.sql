INSERT IGNORE INTO `permission` (`libelle`) VALUES ('post.reactivate');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (1,
(SELECT `id` FROM `permission` WHERE `libelle`= 'post.reactivate'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('page.reactivate');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (1,
(SELECT `id` FROM `permission` WHERE `libelle`= 'page.reactivate'));
