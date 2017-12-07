INSERT IGNORE INTO `permission` (`libelle`) VALUES ('pageuser.m_getListByUser');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'pageuser.m_getListByUser'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('pageuser.m_getInvitationListByUser');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'pageuser.m_getInvitationListByUser'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('pageuser.m_getApplicationListByUser');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'pageuser.m_getApplicationListByUser'));
