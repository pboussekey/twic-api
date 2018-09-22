ALTER TABLE `user_tag` MODIFY category TEXT NOT NULL DEFAULT 'other';
UPDATE user_tag SET category = 'graduation' WHERE category = 'other';
UPDATE user_tag SET category = 'hobby' WHERE category = 'interest';
UPDATE user_tag SET category = 'skill' WHERE category = 'expertise';


CREATE TABLE IF NOT EXISTS `tag_breakdown` (
  `tag_id` INT UNSIGNED NOT NULL,
  `tag_part` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`tag_id`, `tag_part`),
  CONSTRAINT `fk_tag_breakdown_1`
    FOREIGN KEY (`tag_id`)
    REFERENCES `tag` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

INSERT INTO tag(name, weight)
SELECT name, 0 FROM (
SELECT DISTINCT country.short_name AS name
FROM user
JOIN address ON (user.address_id = address.id)
JOIN country ON (address.country_id = country.id OR user.origin = country.id)

UNION

SELECT DISTINCT division.name as name
FROM user
JOIN address ON (user.address_id = address.id)
JOIN division ON (address.division_id = division.id)

UNION

SELECT DISTINCT city.name as name
FROM user
JOIN address ON (user.address_id = address.id)
JOIN city ON (address.city_id = city.id)) AS tags
WHERE name NOT IN (SELECT name FROM tag);

INSERT INTO tag_breakdown (tag_id, tag_part)
SELECT DISTINCT * FROM (SELECT DISTINCT tag.id,
    LOWER(REPLACE(SUBSTRING_INDEX(SUBSTRING_INDEX(tag.name, ' ', numbers.n),
            ' ',
            - 1),',','')) name
FROM
    (SELECT 1 n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8) numbers
        INNER JOIN
    tag ON CHAR_LENGTH(tag.name) - CHAR_LENGTH(REPLACE(tag.name, ' ', '')) >= numbers.n - 1


	UNION
	SELECT tag.id, tag.name FROM tag
    ) as tags
WHERE id NOT IN (SELECT tag_id FROM tag_breakdown);


INSERT INTO user_tag(user_id, tag_id, category)
SELECT DISTINCT * FROM (

SELECT DISTINCT user.id as uid, tag.id as tid, 'origin' as category
FROM user
JOIN address ON (user.address_id = address.id)
JOIN country ON (user.origin = country.id)
JOIN tag ON (country.short_name = tag.name)

UNION

SELECT DISTINCT user.id as uid, tag.id as tid,  'address' as category
FROM user
JOIN address ON (user.address_id = address.id)
JOIN country ON (address.country_id = country.id)
JOIN tag ON (country.short_name = tag.name)

UNION

SELECT DISTINCT user.id as uid, tag.id as tid,  'address' as category
FROM user
JOIN address ON (user.address_id = address.id)
JOIN division ON (address.division_id = division.id)
JOIN tag ON (division.name = tag.name)

UNION

SELECT DISTINCT user.id as uid, tag.id as tid,  'address' as category
FROM user
JOIN address ON (user.address_id = address.id)
JOIN city ON (address.city_id = city.id)
JOIN tag ON (city.name = tag.name)) as tags;
