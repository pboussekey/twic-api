ALTER TABLE `page_user` ADD COLUMN
  `created_date` DATETIME NULL;

ALTER TABLE `user` ADD COLUMN
  `invitation_date` DATETIME NULL;