UPDATE `page_relation`
SET `type` = LOWER(`type`) WHERE `type` IS NOT NULL;
