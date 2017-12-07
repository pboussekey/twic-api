ALTER TABLE `course` 
DROP FOREIGN KEY `fk_course_2`;
ALTER TABLE `course` 
ADD CONSTRAINT `fk_course_2`
  FOREIGN KEY (`program_id`)
  REFERENCES `program` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

ALTER TABLE `course_user_relation` 
DROP FOREIGN KEY `fk_course_user_relation_1`;
ALTER TABLE `course_user_relation` 
ADD CONSTRAINT `fk_course_user_relation_1`
  FOREIGN KEY (`course_id`)
  REFERENCES `course` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

ALTER TABLE `item` 
DROP FOREIGN KEY `fk_item_1`;
ALTER TABLE `item` 
ADD CONSTRAINT `fk_item_1`
  FOREIGN KEY (`course_id`)
  REFERENCES `course` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

ALTER TABLE `bank_question` 
CHANGE COLUMN `course_id` `course_id` INT UNSIGNED NULL DEFAULT NULL ,
ADD INDEX `fk_bank_question_11_idx` (`course_id` ASC);
ALTER TABLE `bank_question` 
ADD CONSTRAINT `fk_bank_question_11`
  FOREIGN KEY (`course_id`)
  REFERENCES `course` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `set` 
DROP FOREIGN KEY `fk_set_1`;
ALTER TABLE `set` 
ADD CONSTRAINT `fk_set_1`
  FOREIGN KEY (`course_id`)
  REFERENCES `course` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

