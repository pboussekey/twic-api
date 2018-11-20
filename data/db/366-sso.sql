ALTER TABLE `page` 
ADD COLUMN `sso_entity_id` TEXT NULL AFTER `domaine`,
ADD COLUMN `single_sign_on_service` TEXT NULL AFTER `sso_entity_id`,
ADD COLUMN `single_logout_service` TEXT NULL AFTER `single_sign_on_service`,
ADD COLUMN `sso_x509cert` TEXT NULL AFTER `single_logout_service`;

