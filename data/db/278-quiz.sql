ALTER TABLE `quiz` 
DROP FOREIGN KEY `fk_quiz_1`;
ALTER TABLE `quiz` 
ADD CONSTRAINT `fk_quiz_1`
  FOREIGN KEY (`item_id`)
  REFERENCES `item` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

ALTER TABLE `quiz_question`
DROP FOREIGN KEY `fk_quiz_question_1`;
ALTER TABLE `quiz_question`
ADD CONSTRAINT `fk_quiz_question_1`
  FOREIGN KEY (`quiz_id`)
  REFERENCES `quiz` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;


ALTER TABLE `quiz_answer` 
DROP FOREIGN KEY `fk_quiz_answer_1`;
ALTER TABLE `quiz_answer` 
ADD CONSTRAINT `fk_quiz_answer_1`
  FOREIGN KEY (`quiz_question_id`)
  REFERENCES `quiz_question` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('quiz.addQuestions');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'quiz.addQuestions'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('quiz.addAnswers');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'quiz.addAnswers'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('quiz.updateQuestion');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'quiz.updateQuestion'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('quiz.updateAnswers');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'quiz.updateAnswers'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('quiz.removeQuestions');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'quiz.removeQuestions'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('quiz.removeAnswers');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'quiz.removeAnswers'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('quiz.get');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'quiz.get'));


