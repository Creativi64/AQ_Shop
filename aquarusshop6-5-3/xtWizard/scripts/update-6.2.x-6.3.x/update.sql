DROP INDEX `popuptrigger` ON `##_system_log`;
CREATE INDEX `popuptrigger` ON `##_system_log`(`popuptrigger`);

ALTER TABLE `##_products` ADD COLUMN `show_stock` TINYINT(1) NULL DEFAULT 0  AFTER `products_quantity`;

ALTER TABLE `##_products_to_categories` DROP PRIMARY KEY;
ALTER TABLE `##_products_to_categories` ADD PRIMARY KEY (`products_id`, `categories_id`, `store_id`, `master_link`);

ALTER TABLE `##_products_to_categories` DROP KEY `p2k_unique`;
ALTER TABLE `##_products_to_categories` ADD CONSTRAINT `p2k_unique` UNIQUE (`products_id` ASC, `categories_id` ASC, `master_link` ASC, `store_id` ASC);

ALTER TABLE `##_image_type` DROP COLUMN `watermark`;

ALTER TABLE `##_seo_url` CHANGE COLUMN `url_text` `url_text` VARCHAR(2048) NOT NULL;
