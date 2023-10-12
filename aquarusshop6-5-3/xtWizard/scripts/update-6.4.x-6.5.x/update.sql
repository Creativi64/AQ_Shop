ALTER TABLE `##_feed` ADD COLUMN `feed_ftp_tls` TINYINT UNSIGNED NOT NULL DEFAULT 1 AFTER `feed_ftp_server`;

ALTER TABLE `##_acl_user` ADD UNIQUE INDEX `handle_unique` (`handle` ASC);

ALTER TABLE `##_feed` CHANGE COLUMN `feed_date_from_orders` `feed_date_from_orders` datetime NULL DEFAULT NULL, CHANGE COLUMN `feed_date_to_orders` `feed_date_to_orders` datetime NULL DEFAULT NULL;

ALTER TABLE `##_media` ADD COLUMN `copyright_holder` VARCHAR(255) CHARACTER SET 'utf8' NULL;
