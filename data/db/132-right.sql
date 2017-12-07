
INSERT IGNORE INTO `permission` (`libelle`) VALUES ('circle.addOrganizations');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (1,
(SELECT `id` FROM `permission` WHERE `libelle`= 'circle.addOrganizations'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('circle.deleteOrganizations');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (1,
(SELECT `id` FROM `permission` WHERE `libelle`= 'circle.deleteOrganizations'));

ALTER TABLE `circle_organization` 
DROP FOREIGN KEY `fk_circle_organization_1`,
DROP FOREIGN KEY `fk_circle_organization_2`;
ALTER TABLE `circle_organization` 
ADD CONSTRAINT `fk_circle_organization_1`
  FOREIGN KEY (`circle_id`)
  REFERENCES `circle` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_circle_organization_2`
  FOREIGN KEY (`organization_id`)
  REFERENCES `school` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

INSERT IGNORE INTO organization_user ( user_id, organization_id) ( SELECT id, school_id from user WHERE school_id IS NOT NULL );
