INSERT IGNORE INTO `permission` (`libelle`) VALUES ('contact.getAcceptedCount');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'contact.getAcceptedCount'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('contact.getRequestsCount');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'contact.getRequestsCount'));