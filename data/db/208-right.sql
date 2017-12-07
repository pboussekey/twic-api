INSERT IGNORE INTO `permission` (`libelle`) VALUES ('pageuser.m_getListByPage');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'pageuser.m_getListByPage'));
