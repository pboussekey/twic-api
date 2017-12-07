ALTER TABLE `item` 
DROP FOREIGN KEY `fk_item_1`;
ALTER TABLE `item` 
ADD CONSTRAINT `fk_item_1`
  FOREIGN KEY (`parent_id`)
  REFERENCES `item` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;


INSERT IGNORE INTO `permission` (`libelle`) VALUES ('item.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'item.delete'));

