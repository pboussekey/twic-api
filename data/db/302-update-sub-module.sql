UPDATE item AS si
JOIN item AS i ON i.id = si.parent_id
SET si.parent_id = i.parent_id,
    si.order = i.order
WHERE i.parent_id IS NOT NULL AND si.parent_id IS NOT NULL;
