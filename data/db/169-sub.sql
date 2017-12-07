INSERT IGNORE INTO `subscription` (`user_id`, `libelle`,`created_date`) SELECT * FROM 
(SELECT user_id, CONCAT('EU',contact_id) libelle, UTC_TIMESTAMP created_date FROM contact WHERE accepted_date IS NOT NULL
UNION 
SELECT user_id, CONCAT('PU',contact_id) libelle, UTC_TIMESTAMP created_date FROM contact WHERE accepted_date IS NOT NULL
UNION
SELECT id user_id, CONCAT('SU',id) libelle, UTC_TIMESTAMP created_date FROM user
UNION
SELECT user_id, CONCAT('EO',organization_id) libelle, UTC_TIMESTAMP FROM organization_user
UNION
SELECT user_id, CONCAT('PO',organization_id) libelle, UTC_TIMESTAMP FROM organization_user
) as T;
