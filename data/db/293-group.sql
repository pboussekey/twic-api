ALTER TABLE `group` 
ADD COLUMN `item_id` INT UNSIGNED NULL AFTER `name`,
ADD INDEX `fk_group_1_idx` (`item_id` ASC);
ALTER TABLE `group` 
ADD CONSTRAINT `fk_group_1`
  FOREIGN KEY (`item_id`)
  REFERENCES `item` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('group.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'group.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('group.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'group.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('group.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'group.delete'));
