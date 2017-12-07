ALTER TABLE `user` 
ADD COLUMN `email_sent` TINYINT NOT NULL DEFAULT 1 AFTER `suspension_reason`;
