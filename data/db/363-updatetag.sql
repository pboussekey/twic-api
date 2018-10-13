INSERT IGNORE INTO `tag`(`name`, `weight`) 
SELECT user.firstname as name, 1 as weight FROM user 
WHERE NOT EXISTS (SELECT name from tag where user.firstname=tag.name OR user.firstname IS NULL);

INSERT IGNORE INTO `tag`(`name`, `weight`) 
SELECT user.lastname as name, 1 as weight FROM user 
WHERE NOT EXISTS (SELECT name from tag where user.lastname=tag.name OR user.lastname IS NULL);

INSERT IGNORE INTO `tag`(`name`, `weight`) 
SELECT user.email as name, 1 as weight FROM user 
WHERE NOT EXISTS (SELECT name from tag where user.email=tag.name OR user.email IS NULL);

INSERT IGNORE INTO `tag`(`name`, `weight`) 
SELECT user.initial_email as name, 1 as weight FROM user 
WHERE NOT EXISTS (SELECT name from tag where user.initial_email=tag.name OR user.initial_email IS NULL);

INSERT IGNORE INTO `user_tag`(`user_id`, `tag_id`, `category`)
SELECT user.id as user_id, tag.id as tag_id, 'profile' as category 
FROM user LEFT JOIN tag ON tag.name = user.firstname WHERE NOT EXISTS 
(SELECT `user_id`, `tag_id`, `category` FROM `user_tag` as ut
WHERE ut.user_id=user.id and ut.tag_id=tag.id and ut.category='profile');

INSERT IGNORE INTO `user_tag`(`user_id`, `tag_id`, `category`)
SELECT user.id as user_id, tag.id as tag_id, 'profile' as category 
FROM user LEFT JOIN tag ON tag.name = user.lastname WHERE NOT EXISTS 
(SELECT `user_id`, `tag_id`, `category` FROM `user_tag` as ut
WHERE ut.user_id=user.id and ut.tag_id=tag.id and ut.category='profile');

INSERT IGNORE INTO `user_tag`(`user_id`, `tag_id`, `category`)
SELECT user.id as user_id, tag.id as tag_id, 'profile' as category 
FROM user LEFT JOIN tag ON tag.name = user.email WHERE NOT EXISTS 
(SELECT `user_id`, `tag_id`, `category` FROM `user_tag` as ut
WHERE ut.user_id=user.id and ut.tag_id=tag.id and ut.category='profile');

INSERT IGNORE INTO `user_tag`(`user_id`, `tag_id`, `category`)
SELECT user.id as user_id, tag.id as tag_id, 'profile' as category 
FROM user LEFT JOIN tag ON tag.name = user.initial_email WHERE NOT EXISTS 
(SELECT `user_id`, `tag_id`, `category` FROM `user_tag` as ut
WHERE ut.user_id=user.id and ut.tag_id=tag.id and ut.category='profile');


INSERT IGNORE INTO `tag_breakdown`(`tag_id`, `tag_part`)
SELECT tag.id as tag_id, SUBSTRING_INDEX(SUBSTRING_INDEX(tag.name,' ',1), ' ',-1) as tag_part from tag WHERE NOT EXISTS 
(SELECT * FROM tag_breakdown WHERE tag_breakdown.tag_id=tag.id AND tag_breakdown.tag_part=SUBSTRING_INDEX(SUBSTRING_INDEX(tag.name,' ',1), ' ',-1))
AND  LTRIM(RTRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(tag.name,' ',1), ' ',-1))) <> '';

INSERT IGNORE INTO `tag_breakdown`(`tag_id`, `tag_part`)
SELECT tag.id as tag_id, SUBSTRING_INDEX(SUBSTRING_INDEX(tag.name,' ',2), ' ',-1) as tag_part from tag WHERE NOT EXISTS 
(SELECT * FROM tag_breakdown WHERE tag_breakdown.tag_id=tag.id AND tag_breakdown.tag_part=SUBSTRING_INDEX(SUBSTRING_INDEX(tag.name,' ',2), ' ',-1))
AND  LTRIM(RTRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(tag.name,' ',2), ' ',-1))) <> '';

INSERT IGNORE INTO `tag_breakdown`(`tag_id`, `tag_part`)
SELECT tag.id as tag_id, SUBSTRING_INDEX(SUBSTRING_INDEX(tag.name,' ',3), ' ',-1) as tag_part from tag WHERE NOT EXISTS 
(SELECT * FROM tag_breakdown WHERE tag_breakdown.tag_id=tag.id AND tag_breakdown.tag_part=SUBSTRING_INDEX(SUBSTRING_INDEX(tag.name,' ',3), ' ',-1))
AND  LTRIM(RTRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(tag.name,' ',3), ' ',-1))) <> '';

INSERT IGNORE INTO `tag_breakdown`(`tag_id`, `tag_part`)
SELECT tag.id as tag_id, SUBSTRING_INDEX(SUBSTRING_INDEX(tag.name,' ',4), ' ',-1) as tag_part from tag WHERE NOT EXISTS 
(SELECT * FROM tag_breakdown WHERE tag_breakdown.tag_id=tag.id AND tag_breakdown.tag_part=SUBSTRING_INDEX(SUBSTRING_INDEX(tag.name,' ',4), ' ',-1))
AND  LTRIM(RTRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(tag.name,' ',4), ' ',-1))) <> '';

INSERT IGNORE INTO `tag_breakdown`(`tag_id`, `tag_part`)
SELECT tag.id as tag_id, SUBSTRING_INDEX(SUBSTRING_INDEX(tag.name,' ',5), ' ',-1) as tag_part from tag WHERE NOT EXISTS 
(SELECT * FROM tag_breakdown WHERE tag_breakdown.tag_id=tag.id AND tag_breakdown.tag_part=SUBSTRING_INDEX(SUBSTRING_INDEX(tag.name,' ',5), ' ',-1))
AND  LTRIM(RTRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(tag.name,' ',5), ' ',-1))) <> '';

INSERT IGNORE INTO `tag_breakdown`(`tag_id`, `tag_part`)
SELECT tag.id as tag_id, SUBSTRING_INDEX(SUBSTRING_INDEX(tag.name,' ',6), ' ',-1) as tag_part from tag WHERE NOT EXISTS 
(SELECT * FROM tag_breakdown WHERE tag_breakdown.tag_id=tag.id AND tag_breakdown.tag_part=SUBSTRING_INDEX(SUBSTRING_INDEX(tag.name,' ',6), ' ',-1))
AND  LTRIM(RTRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(tag.name,' ',6), ' ',-1))) <> '';

INSERT IGNORE INTO `tag_breakdown`(`tag_id`, `tag_part`)
SELECT tag.id as tag_id, SUBSTRING_INDEX(SUBSTRING_INDEX(tag.name,' ',7), ' ',-1) as tag_part from tag WHERE NOT EXISTS 
(SELECT * FROM tag_breakdown WHERE tag_breakdown.tag_id=tag.id AND tag_breakdown.tag_part=SUBSTRING_INDEX(SUBSTRING_INDEX(tag.name,' ',7), ' ',-1))
AND  LTRIM(RTRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(tag.name,' ',7), ' ',-1))) <> '';

INSERT IGNORE INTO `tag_breakdown`(`tag_id`, `tag_part`)
SELECT tag.id as tag_id, SUBSTRING_INDEX(SUBSTRING_INDEX(tag.name,' ',8), ' ',-1) as tag_part from tag WHERE NOT EXISTS 
(SELECT * FROM tag_breakdown WHERE tag_breakdown.tag_id=tag.id AND tag_breakdown.tag_part=SUBSTRING_INDEX(SUBSTRING_INDEX(tag.name,' ',8), ' ',-1))
AND  LTRIM(RTRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(tag.name,' ',8), ' ',-1))) <> '';

