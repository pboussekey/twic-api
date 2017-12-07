CREATE TABLE IF NOT EXISTS `material` (
  `library_id` INT UNSIGNED NOT NULL,
  `course_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`library_id`, `course_id`),
  INDEX `fk_material_2_idx` (`course_id` ASC),
  CONSTRAINT `fk_material_1`
    FOREIGN KEY (`library_id`)
    REFERENCES `library` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_material_2`
    FOREIGN KEY (`course_id`)
    REFERENCES `course` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('material.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (5,
(SELECT `id` FROM `permission` WHERE `libelle`= 'material.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('material.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (5,
(SELECT `id` FROM `permission` WHERE `libelle`= 'material.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('material.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4,
(SELECT `id` FROM `permission` WHERE `libelle`= 'material.getList'));
