ALTER TABLE `post_subscription` ADD COLUMN `user_id` INT NULL AFTER `sub_post_id`;


INSERT IGNORE INTO `permission` (`libelle`) VALUES ('post.m_get');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'post.m_get'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('post.getListId');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'post.getListId'));
