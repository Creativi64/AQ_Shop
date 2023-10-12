
ALTER TABLE `##_payment` DROP COLUMN `payment_available_mobile`;

ALTER TABLE `##_content` DROP COLUMN `link_ssl`;

DELETE FROM `##_config` WHERE `config_key` = '_SYSTEM_SEARCH_SPLIT';

ALTER TABLE `##_products` ADD COLUMN `products_mpn` VARCHAR(512) CHARACTER SET utf8 NULL AFTER `manufacturers_id`;
ALTER TABLE `##_products` ADD COLUMN `products_condition` VARCHAR(64) CHARACTER SET utf8 NULL DEFAULT 'new' AFTER `products_weight`;
ALTER TABLE `##_products` DROP COLUMN `google_products_cat`;

ALTER TABLE `##_products` DROP INDEX `idx_products_condition` ;
ALTER TABLE `##_products` ADD INDEX `idx_products_condition` (`products_condition` ASC);

ALTER TABLE `##_products_description` CHANGE COLUMN `reload_st` `reload_st` INT(11) NULL DEFAULT '0' ;

INSERT INTO `##_image_type` (`folder`, `width`, `height`, `watermark`, `process`, `class`) VALUES('category/listingTop', 1200, 600, 'false', 'true', 'category');

ALTER TABLE `##_acl_user` DROP COLUMN `default_language_code`;

DELETE FROM `##_config_1` WHERE `config_key` = '_STORE_ACCOUNT_MIN_AGE';
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_ACCOUNT_MIN_AGE', '', 5, -5, NULL, NULL);

UPDATE `##_config` SET `config_value` = 'xt_responsive' WHERE `config_value` IN ('xt_responsive_kco', 'xt_responsive_klarna');

ALTER TABLE `##_federal_states` CHANGE COLUMN `status` `status` TINYINT(1) NULL DEFAULT 1 ;

