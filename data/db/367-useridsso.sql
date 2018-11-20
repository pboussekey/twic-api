ALTER TABLE `user` 
ADD COLUMN `sso_uid` VARCHAR(512) NULL AFTER `has_email_contact_request_notifier`;

ALTER TABLE `user`
CHANGE COLUMN `email` `email` VARCHAR(128) NULL DEFAULT NULL;

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('saml.login');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0,
(SELECT `id` FROM `permission` WHERE `libelle`= 'saml.login'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('saml.acs');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0,
(SELECT `id` FROM `permission` WHERE `libelle`= 'saml.acs'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('saml.logout');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'saml.logout'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('saml.sls');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'saml.sls'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('saml.slsr');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'saml.slsr'));
