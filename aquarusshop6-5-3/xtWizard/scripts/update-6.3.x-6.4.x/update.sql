ALTER TABLE `##_products` CHANGE COLUMN `products_price` `products_price` DECIMAL(15,4) NULL DEFAULT NULL ;

ALTER TABLE `##_seo_url` CHANGE COLUMN `url_text` `url_text` VARCHAR(2048) NOT NULL;

ALTER TABLE `##_shipping` CHANGE COLUMN `shipping_code` `shipping_code` VARCHAR(32) NULL DEFAULT '' ;

ALTER TABLE `##_products`
    CHANGE COLUMN `products_ean` `products_ean` VARCHAR(128) CHARACTER SET 'utf8mb4' NULL DEFAULT NULL ,
    CHANGE COLUMN `products_model` `products_model` VARCHAR(255) CHARACTER SET 'utf8mb4' NULL DEFAULT NULL ;

ALTER TABLE `##_products_description`
    CHANGE COLUMN `products_name` `products_name` VARCHAR(255) CHARACTER SET 'utf8mb4' NULL DEFAULT NULL ,
    CHANGE COLUMN `products_description` `products_description` TEXT CHARACTER SET 'utf8mb4' NULL DEFAULT NULL ,
    CHANGE COLUMN `products_short_description` `products_short_description` TEXT CHARACTER SET 'utf8mb4' NULL DEFAULT NULL ,
    CHANGE COLUMN `products_keywords` `products_keywords` VARCHAR(255) CHARACTER SET 'utf8mb4' NULL DEFAULT NULL ;

UPDATE `##_language_content` SET `class` = 'both' WHERE `language_key` = 'TEXT_FROM';
UPDATE `##_language_content` SET `class` = 'both' WHERE `language_key` = 'TEXT_DSGVO_SHIPPING_CONSENT';
UPDATE `##_language_content` SET `class` = 'both' WHERE `language_key` = 'ERROR_FILE_NOT_ALLOWED';

INSERT IGNORE INTO `##_language_content` (`language_code`, `language_key`, `language_value`, `class`) VALUES ('de', 'ERROR_FILE_NOT_ALLOWED', 'Datei nicht erlaubt', 'both');
INSERT IGNORE INTO `##_language_content` (`language_code`, `language_key`, `language_value`, `class`) VALUES ('en', 'ERROR_FILE_NOT_ALLOWED', 'File not allowed', 'both');

ALTER TABLE `##_manufacturers` MODIFY `date_added`  datetime  NULL DEFAULT current_timestamp;
ALTER TABLE `##_manufacturers` MODIFY `last_modified`  timestamp NULL DEFAULT NULL ON UPDATE current_timestamp;
