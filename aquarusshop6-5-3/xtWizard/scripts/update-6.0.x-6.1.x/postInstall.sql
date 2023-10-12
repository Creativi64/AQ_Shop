
UPDATE `##_stores` SET `shop_ssl` = 1 WHERE `shop_ssl` = 'default_ssl' OR `shop_ssl` = 'full_ssl';
UPDATE `##_stores` SET `shop_ssl` = 0 WHERE `shop_ssl` = 'no_ssl' OR `shop_ssl` IS NULL;

ALTER TABLE `##_stores` MODIFY COLUMN `shop_ssl` INT(1) NULL DEFAULT 0;
ALTER TABLE `##_stores` DROP COLUMN `admin_ssl`;
ALTER TABLE `##_stores` DROP COLUMN `shop_domain`;
ALTER TABLE `##_stores` DROP COLUMN `shop_http`;
ALTER TABLE `##_stores` DROP COLUMN `shop_https`;
ALTER TABLE `##_stores` DROP COLUMN `virtual_folder`;

DELETE FROM `##_config` WHERE config_key = '_SYSTEM_USE_DB_HOOKS';
