ALTER TABLE `material` 
DROP FOREIGN KEY `fk_material_1`,
DROP FOREIGN KEY `fk_material_2`;
ALTER TABLE `material` 
ADD CONSTRAINT `fk_material_1`
  FOREIGN KEY (`library_id`)
  REFERENCES `library` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_material_2`
  FOREIGN KEY (`course_id`)
  REFERENCES `course` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

