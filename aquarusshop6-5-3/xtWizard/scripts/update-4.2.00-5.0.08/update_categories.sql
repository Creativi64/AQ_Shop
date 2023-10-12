ALTER TABLE `##_categories` ADD COLUMN `categories_level` TINYINT UNSIGNED NOT NULL DEFAULT '0';

ALTER TABLE `##_categories` ADD INDEX `bb_nested_set_performance_categories_left` (`categories_left`);

  
