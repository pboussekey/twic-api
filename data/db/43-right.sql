INSERT IGNORE INTO `permission` (`libelle`) VALUES ('questionnaire.getByItem');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'questionnaire.getByItem'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('submissionuser.start');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'submissionuser.start'));

INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'submission.instructorRates'));