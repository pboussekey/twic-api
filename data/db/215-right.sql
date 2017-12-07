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

