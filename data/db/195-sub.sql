INSERT IGNORE INTO `subscription`
(`libelle`, `user_id`, `created_date`) 
SELECT CONCAT('PU',contact.contact_id), contact.user_id, UTC_TIMESTAMP() FROM contact
LEFT JOIN subscription ON subscription.user_id=contact.user_id AND subscription.libelle=CONCAT('PU',contact.contact_id) 
WHERE contact.accepted_date IS NOT NULL 
AND subscription.user_id IS NULL
AND contact.deleted_date IS NULL;
