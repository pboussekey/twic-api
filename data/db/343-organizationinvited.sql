SET SQL_SAFE_UPDATES=0;
UPDATE page_user SET state = 'invited' WHERE state = 'pending' AND page_id IN (SELECT id FROM page WHERE type = 'organization');
