INSERT IGNORE INTO `permission` (`libelle`) VALUES ('contact.m_getListIdByUser');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'contact.m_getListIdByUser'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('school.m_get');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'school.m_get'));