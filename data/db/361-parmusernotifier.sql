ALTER TABLE `user`
ADD COLUMN `has_email_contact_request_notifier` TINYINT NOT NULL DEFAULT 1 AFTER `linkedin_url`;

