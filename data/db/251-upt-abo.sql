SET foreign_key_checks = 0;
truncate subscription;
DELETE FROM `contact` WHERE deleted = 0 and accepted = 0 and requested = 0;
SET foreign_key_checks = 1;

INSERT IGNORE INTO `subscription` (`user_id`, `libelle`,`created_date`) SELECT * FROM
(SELECT user_id, CONCAT('PP',page_id) libelle, UTC_TIMESTAMP created_date FROM page_user WHERE state='member'
UNION
SELECT user_id, CONCAT('PU',contact_id) libelle, UTC_TIMESTAMP created_date FROM contact WHERE accepted_date IS NOT NULL
UNION
SELECT id user_id, CONCAT('SU',id) libelle, UTC_TIMESTAMP created_date FROM user 
) as T;
