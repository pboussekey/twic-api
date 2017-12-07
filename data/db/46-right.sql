INSERT IGNORE INTO `permission` (`libelle`) VALUES ('submission.assignGraders');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (5, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'submission.assignGraders'));