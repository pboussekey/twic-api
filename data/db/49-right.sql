INSERT IGNORE INTO `permission` (`libelle`) VALUES ('submissionuser.setGrade');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (5, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'submissionuser.setGrade'));


DELETE FROM `role_permission`  WHERE `id` IN (SELECT `id` FROM `permission` WHERE `libelle`= 'submissioncomments.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'submissioncomments.getList'));