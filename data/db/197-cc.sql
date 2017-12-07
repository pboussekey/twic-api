ALTER TABLE `page` 
CHANGE COLUMN `type` `type` ENUM('group', 'event', 'course') NULL DEFAULT NULL ;
