INSERT IGNORE INTO `permission` (`libelle`) VALUES ('pagedoc.delete');
DELETE FROM `role_permission` WHERE `permission_id`=(SELECT `id` FROM `permission` WHERE `libelle`= 'pagedoc.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, (SELECT `id` FROM `permission` WHERE `libelle`= 'pagedoc.delete'));

