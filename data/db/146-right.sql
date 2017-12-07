INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.m_get');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.m_get'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('resume.m_getListIdByUser');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'resume.m_getListIdByUser'));