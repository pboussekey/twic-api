REPLACE INTO subscription SELECT "PP59" as libelle, id as user_id, UTC_TIMESTAMP() as created_date from user WHERE organization_id <> 112;
