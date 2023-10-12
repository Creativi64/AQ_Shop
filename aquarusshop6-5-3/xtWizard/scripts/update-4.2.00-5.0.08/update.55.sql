
DELETE FROM `##_acl_nav` WHERE `text` = 'stores';

INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`) VALUES ('slider', 'images/icons/layers.png' , '&pg=overview' , 'adminHandler.php' , 200000, 'content_manager', 'I', 'W', NULL , NULL , NULL);

ALTER TABLE `##_acl_user` CHANGE COLUMN `user_password` `user_password` VARCHAR(255) NOT NULL, ADD COLUMN `ip_restriction` VARCHAR(255) NULL DEFAULT NULL AFTER `default_language_code`;

UPDATE `##_config` SET `config_value`='xt_responsive' WHERE `config_key`='_SYSTEM_TEMPLATE';
DELETE FROM `##_config` WHERE config_key = '_SYSTEM_MOBILE_TEMPLATE';

UPDATE `##_config` SET `config_value`='ckeditor' WHERE config_key = '_SYSTEM_USE_WYSIWYG' AND config_value = 'TinyMce';

INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_ADMIN_PAGE_SIZE', '50', 25, 25, NULL, NULL);
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_ADMIN_PAGE_SIZE_PRODUCT',       '50', 25, 30, NULL, NULL);
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_ADMIN_PAGE_SIZE_MANUFACTURER',  '25', 25, 35, NULL, NULL);
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_ADMIN_PAGE_SIZE_ORDER',         '25', 25, 40, NULL, NULL);
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_ADMIN_PAGE_SIZE_CUSTOMER',      '25', 25, 45, NULL, NULL);
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_ADMIN_PAGE_SIZE_PLUGIN',        '25', 25, 50, NULL, NULL);
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_ADMIN_PAGE_SIZE_SEO_DELETED',   '50', 25, 55, NULL, NULL);
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_ADMIN_PAGE_SIZE_SEO_404',       '50', 25, 60, NULL, NULL);
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_ADMIN_PAGE_SIZE_SEO_REGENERATE','24', 25, 65, NULL, NULL);

DELETE FROM `##_config_group` WHERE `group_title`='TEXT_MOBILE_TEMPLATE';

ALTER TABLE `##_cron` ADD COLUMN  `running_status` tinyint(1) unsigned DEFAULT NULL AFTER `cron_note`;

ALTER TABLE `##_currencies` CHANGE COLUMN `dec_point` `dec_point` CHAR(1) NULL DEFAULT ',' , CHANGE COLUMN `thousands_sep` `thousands_sep` CHAR(1) NULL DEFAULT '.' , CHANGE COLUMN `decimals` `decimals` TINYINT(1) NULL DEFAULT 2 , CHANGE COLUMN `value_multiplicator` `value_multiplicator` FLOAT(13,8) NULL DEFAULT 1 ;

ALTER TABLE `##_customers` CHANGE COLUMN `customers_password` `customers_password` VARCHAR(255) NULL DEFAULT NULL , ADD COLUMN `password_type` INT(1) NULL DEFAULT 1 AFTER `customers_password`;
ALTER TABLE `##_customers` CHANGE COLUMN `password_type` `password_type` INT(1) NULL DEFAULT 0;

ALTER TABLE `##_customers_status` DROP COLUMN `customers_status_mobile_template`;


UPDATE `##_image_type` SET `width`='290', `height`='400' WHERE `folder`='thumb';
UPDATE `##_image_type` SET `width`='725', `height`='725' WHERE `folder`='info';
UPDATE `##_image_type` SET `width`='352', `height`='174' WHERE `folder`='category/thumb';
DELETE FROM `##_image_type` WHERE `folder`IN ('sidebar','smallproduct','mobile/thumb','mobile/info','mobile/popup','mobile/pslider','mobile/islider');


UPDATE `##_media_file_types` SET `file_type`='images' WHERE  `file_ext`='jpeg';

ALTER TABLE `##_orders` CHANGE COLUMN `customers_ip` `customers_ip` VARCHAR(128) NULL DEFAULT NULL;

ALTER TABLE `##_plugin_code` ADD COLUMN `plugin_status` INT(1) NULL DEFAULT 0 AFTER `code_status`,ADD KEY `hook_code_status_plugin_status_sortorder` (`hook`, `code_status`, `plugin_status`, `sortorder`);
UPDATE `##_plugin_code` pc INNER JOIN `##_plugin_products` pp ON pc.plugin_id = pp.plugin_id SET pc.plugin_status = pp.plugin_status;

ALTER TABLE `##_products` CHANGE COLUMN `products_price` `products_price` DECIMAL(15,10) NULL DEFAULT NULL ;
ALTER TABLE `##_products` DROP COLUMN `products_startpage`;
ALTER TABLE `##_products` DROP COLUMN `products_startpage_sort`;

ALTER TABLE `##_seo_url` DROP PRIMARY KEY, ADD PRIMARY KEY(`url_md5`,`store_id`,`language_code`);

ALTER TABLE `##_shipping_cost` CHANGE COLUMN `shipping_type_value_from` `shipping_type_value_from` DECIMAL(15,4) NULL DEFAULT '0.00' , CHANGE COLUMN `shipping_type_value_to` `shipping_type_value_to` DECIMAL(15,4) NULL DEFAULT '0.00' ;

ALTER TABLE `##_stores` DROP COLUMN `shop_title`;

INSERT INTO `##_system_status` (`status_class`, `status_values`) VALUES ('order_status', 'a:1:{s:4:"data";a:5:{s:15:"enable_download";i:0;s:7:"visible";s:1:"1";s:13:"visible_admin";i:1;s:19:"calculate_statistic";i:0;s:12:"reduce_stock";i:1;}}');
INSERT INTO `##_system_status` (status_class, status_values) VALUES ('order_status', 'a:1:{s:4:"data";a:5:{s:15:"enable_download";i:0;s:7:"visible";s:1:"1";s:13:"visible_admin";i:1;s:19:"calculate_statistic";i:0;s:12:"reduce_stock";i:1;}}');
INSERT INTO `##_system_status` (status_class, status_values) VALUES ('order_status', 'a:1:{s:4:"data";a:5:{s:15:"enable_download";i:0;s:7:"visible";s:1:"1";s:13:"visible_admin";i:1;s:19:"calculate_statistic";i:0;s:12:"reduce_stock";i:1;}}');
INSERT INTO `##_system_status` (status_class, status_values) VALUES ('order_status', 'a:1:{s:4:"data";a:5:{s:15:"enable_download";i:0;s:7:"visible";s:1:"1";s:13:"visible_admin";i:1;s:19:"calculate_statistic";i:0;s:12:"reduce_stock";i:1;}}');
INSERT INTO `##_system_status` (status_class, status_values) VALUES ('order_status', 'a:1:{s:4:"data";a:5:{s:15:"enable_download";i:0;s:7:"visible";s:1:"1";s:13:"visible_admin";i:1;s:19:"calculate_statistic";i:0;s:12:"reduce_stock";i:1;}}');
INSERT INTO `##_system_status` (status_class, status_values) VALUES ('order_status', 'a:1:{s:4:"data";a:5:{s:15:"enable_download";i:0;s:7:"visible";s:1:"1";s:13:"visible_admin";i:1;s:19:"calculate_statistic";i:0;s:12:"reduce_stock";i:1;}}');
INSERT INTO `##_system_status` (status_class, status_values) VALUES ('order_status', 'a:1:{s:4:"data";a:5:{s:15:"enable_download";i:0;s:7:"visible";s:1:"1";s:13:"visible_admin";i:1;s:19:"calculate_statistic";i:0;s:12:"reduce_stock";i:1;}}');

UPDATE `##_acl_area` SET `area_description`='Erlaubt den Zugriff auf die xt:Commerce Produktdokumentation.' WHERE `area_name`='documentation';

INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'é', 'e', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'é', 'e', 1);

CREATE TABLE IF NOT EXISTS `##_slider` (
  `slider_id` int(11) NOT NULL AUTO_INCREMENT,
  `slide_speed` int(11) NOT NULL DEFAULT '800',
  `pagination_speed` int(11) NOT NULL DEFAULT '800',
  `auto_play_speed` int(11) NOT NULL DEFAULT '7000',
  `slider_note` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`slider_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `##_slides` (
  `slide_id` int(11) NOT NULL AUTO_INCREMENT,
  `slider_id` int(11) NOT NULL DEFAULT '0',
  `slide_language_code` char(2) DEFAULT NULL,
  `slide_status` int(11) DEFAULT '1',
  `slide_date_from` timestamp NULL DEFAULT NULL,
  `slide_date_to` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `slide_image` varchar(255) DEFAULT NULL,
  `slide_link` varchar(255) DEFAULT NULL,
  `slide_alt_text` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`slide_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

UPDATE `##_language_content` SET `class`='both' WHERE `language_key`='TEXT_REVIEW_RATING';

