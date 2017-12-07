UPDATE `role_permission` SET `role_id`=0 WHERE `id`=(SELECT `id` FROM `permission` WHERE `libelle`= 'videoarchive.validTransfertVideo');
UPDATE `role_permission` SET `role_id`=0 WHERE `id`=(SELECT `id` FROM `permission` WHERE `libelle`= 'videoarchive.getListVideoUpload');
