INSERT IGNORE INTO `permission` (`libelle`) VALUES ('page.m_getListByParent');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'page.m_getListByParent'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('page.m_getListByOrganization');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'page.m_getListByOrganization'));
