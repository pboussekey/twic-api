UPDATE post 
SET 
    page_id = REGEXP_REPLACE(uid, '(_[0-9]*)|PPM|PP', '')
WHERE
    page_id IS NULL 
    AND user_id IS NULL
    AND type = 'page'
    AND REGEXP_REPLACE(uid, '(_[0-9]*)|PPM|PP', '') IN (SELECT DISTINCT id FROM page);
