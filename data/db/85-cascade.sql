ALTER TABLE `questionnaire_user` 
DROP FOREIGN KEY `fk_questionnaire_user_2`,
DROP FOREIGN KEY `fk_questionnaire_user_3`;
ALTER TABLE `questionnaire_user` 
ADD CONSTRAINT `fk_questionnaire_user_2`
  FOREIGN KEY (`questionnaire_id`)
  REFERENCES `questionnaire` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_questionnaire_user_3`
  FOREIGN KEY (`submission_id`)
  REFERENCES `submission` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

ALTER TABLE `school` 
DROP COLUMN `programme`;

ALTER TABLE `user` 
ADD COLUMN `created_date` DATETIME NULL AFTER `sis`;
