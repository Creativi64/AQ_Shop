ALTER TABLE `##_sales_stats` ADD INDEX `idx_type` (`sales_stat_type` ASC);
ALTER TABLE `##_sales_stats` ADD INDEX `idx_shop` (`shop_id` ASC);
ALTER TABLE `##_sales_stats` ADD INDEX `idx_customer` (`customers_id` ASC);
ALTER TABLE `##_sales_stats` ADD INDEX `idx_customers_status` (`customers_status` ASC);
ALTER TABLE `##_sales_stats` ADD INDEX `idx_date` (`date_added` ASC);
