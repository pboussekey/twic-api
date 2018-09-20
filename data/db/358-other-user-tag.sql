ALTER TABLE `user_tag` MODIFY category ENUM('expertise', 'interest', 'language', 'other') NOT NULL DEFAULT 'other';
