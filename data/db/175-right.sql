INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.m_getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4,
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.m_getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('contact.m_getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4,
(SELECT `id` FROM `permission` WHERE `libelle`= 'contact.m_getList'));

