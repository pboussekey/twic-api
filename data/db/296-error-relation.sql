ALTER TABLE `preregistration`
ADD INDEX `fk_preregistration_1_idx` (`organization_id` ASC);
ALTER TABLE `preregistration`
ADD CONSTRAINT `fk_preregistration_1`
  FOREIGN KEY (`organization_id`)
  REFERENCES `page` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;
