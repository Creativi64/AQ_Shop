
ALTER TABLE `##_categories_custom_link_url` ADD INDEX categories_id_store_id (categories_id, store_id);

ALTER TABLE `##_system_log` ADD COLUMN popuptrigger INT(1) NOT NULL DEFAULT 0;

ALTER TABLE `##_payment_description`  DROP PRIMARY KEY;
ALTER TABLE `##_payment_description` ADD PRIMARY KEY(language_code, payment_id, payment_description_store_id);

ALTER TABLE `##_config_group` ADD COLUMN `iconCls` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `visible`;

INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('themes', '' , '&pg=overview' , 'adminHandler.php' , 4050, 'config', 'I', 'W', NULL , NULL , 'fab fa-css3-alt', 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/624656385');

INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_SHOPOWNER_REGISTERID','', 26, 11, NULL, NULL);
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_SHOPOWNER_REGISTER_COURT','', 26, 12, NULL, NULL);


ALTER TABLE `##_products_serials` MODIFY COLUMN serial_number VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '';
ALTER TABLE `##_products_serials` MODIFY COLUMN orders_id INT(11) NULL DEFAULT 0;
ALTER TABLE `##_products_serials` MODIFY COLUMN orders_products_id INT(11) NULL DEFAULT 0;
ALTER TABLE `##_products_serials` MODIFY COLUMN status TINYINT(1) NULL DEFAULT 0;

UPDATE `##_acl_area` SET `area_description` = 'Erlaubt den Zugriff auf die xt:Commerce Produktdokumentation.' WHERE `area_name` = 'documentation';

ALTER TABLE `##_products_description` CHANGE COLUMN `reload_st` `reload_st` INT(11) NULL DEFAULT '0' ;
