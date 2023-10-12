DELETE FROM `##_config` WHERE `config_key` = '_SYSTEM_BACKEND_THEME';

DELETE FROM `##_config` WHERE `config_key` = '_SYSTEM_MOD_REWRITE_DEFAULT';

ALTER TABLE `##_customers_addresses` CHANGE COLUMN date_added date_added  DATETIME NULL DEFAULT CURRENT_TIMESTAMP, CHANGE COLUMN last_modified last_modified TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP;

ALTER TABLE `##_config_lang` ADD COLUMN `type` varchar(255) default NULL;
UPDATE `##_config_lang` SET  `type` = 'textarea'  WHERE `config_key` = '_STORE_META_FREE_META';

ALTER TABLE `##_payment` DROP COLUMN `payment_available_tablet`;

ALTER TABLE `##_products` ADD COLUMN `products_shippingtime_nostock` INT(4) NULL DEFAULT NULL AFTER `products_shippingtime`;

ALTER TABLE `##_plugin_products` ADD COLUMN `version_available` VARCHAR(255) NULL DEFAULT NULL AFTER `version`;

ALTER TABLE `##_mail_templates` CHANGE COLUMN `tpl_type` `tpl_type` VARCHAR(255) NULL DEFAULT '';

UPDATE `##_products` SET  `products_condition` = 'NewCondition'  WHERE `products_condition` = 'new';
UPDATE `##_products` SET  `products_condition` = 'UsedCondition'  WHERE `products_condition` = 'used';
UPDATE `##_products` SET  `products_condition` = 'RefurbishedCondition'  WHERE `products_condition` = 'refurbished';
UPDATE `##_products` SET  `products_condition` = 'DamagedCondition'  WHERE `products_condition` = 'damaged';
