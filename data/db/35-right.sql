
INSERT IGNORE INTO `permission` (`libelle`) VALUES ('submission.pairRates');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'submission.pairRates'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('submission.instructorRates');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (5, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'submission.pairRates'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('submission.getUserGrades');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'submission.getUserGrades'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('submission.getPGCriterias');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'submission.getPGCriterias'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('submission.getPGGrades');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'submission.getPGGrades'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('item.getCriterias');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'item.getCriterias'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('submission.getPairGraders');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'submission.getPairGraders'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('submission.getUserCriterias');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'submission.getUserCriterias'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('submission.addComent');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'submission.addComent'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('submission.getComments');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'submission.getComments'));