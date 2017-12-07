ALTER TABLE `post` 
DROP FOREIGN KEY `fk_post_3`;
ALTER TABLE `post` 
ADD CONSTRAINT `fk_post_3`
  FOREIGN KEY (`item_id`)
  REFERENCES `item` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('quiz.getUserAnswer');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'quiz.getUserAnswer'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('quiz.addUserAnswer');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'quiz.addUserAnswer'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('quiz.removeUserAnswer');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'quiz.removeUserAnswer'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('quiz.updateUserAnswer');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'quiz.updateUserAnswer'));
