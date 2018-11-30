INSERT IGNORE INTO `permission` (`libelle`) VALUES ('contact.follow');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'contact.follow'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('contact.unfollow');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'contact.unfollow'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('contact.getListFollowersId');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'contact.getListFollowersId'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('contact.getListFollowingsId');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'contact.getListFollowingsId'));
