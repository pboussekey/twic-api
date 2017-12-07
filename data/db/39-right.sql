
INSERT IGNORE INTO `permission` (`libelle`) VALUES ('submission.forceSubmitByItem');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'submission.forceSubmitByItem'));


INSERT IGNORE INTO `permission` (`libelle`) VALUES ('submission.forceSubmit');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'submission.forceSubmit'));


INSERT IGNORE INTO `permission` (`libelle`) VALUES ('submission.forceSubmitBySubmission');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'submission.forceSubmitBySubmission'));