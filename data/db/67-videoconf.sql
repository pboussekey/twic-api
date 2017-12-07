DROP TABLE `videoconf_invitation`;
DROP TABLE `videoconf_entity`;
ALTER TABLE `videoconf_admin` RENAME TO  `videoconf_user` ;

ALTER TABLE `videoconf` 
DROP FOREIGN KEY `fk_videoconf_3`;
ALTER TABLE `videoconf` 
DROP COLUMN `videoconf_opt`,
DROP COLUMN `archive_status`,
DROP COLUMN `archive_link`,
DROP COLUMN `archive_token`,
DROP INDEX `fk_videoconf_3_idx` ;