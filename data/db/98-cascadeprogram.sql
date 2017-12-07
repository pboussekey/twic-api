ALTER TABLE `program_user_relation` 
DROP FOREIGN KEY `fk_program_user_relation_1`;
ALTER TABLE `program_user_relation` 
ADD CONSTRAINT `fk_program_user_relation_1`
  FOREIGN KEY (`program_id`)
  REFERENCES `program` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

