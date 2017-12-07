INSERT IGNORE INTO `permission` (`libelle`) VALUES ('contact.m_getListRequestByContact');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'contact.m_getListRequestByContact'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('contact.m_getListRequestByUser');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'contact.m_getListRequestByUser'));