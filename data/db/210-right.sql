INSERT IGNORE INTO `permission` (`libelle`) VALUES ('library.m_getListByPage');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'library.m_getListByPage'));
