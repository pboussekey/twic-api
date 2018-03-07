ALTER TABLE `user` ADD COLUMN
  `cgu_accepted` TINYINT(4) NOT NULL DEFAULT 0;

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.acceptCgu');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.acceptCgu'));