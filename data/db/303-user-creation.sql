INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.getListIdByEmail');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.getListIdByEmail'));

ALTER TABLE `user` ADD `is_active` TINYINT(4) NOT NULL DEFAULT 0;
UPDATE user SET is_active = 1 WHERE is_active = 0;