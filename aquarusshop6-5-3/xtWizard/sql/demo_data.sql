-- `##_manufacturers`

INSERT INTO `##_manufacturers` (`manufacturers_id`, `manufacturers_name`, `manufacturers_image`, `manufacturers_status`, `manufacturers_sort`) VALUES(1, 'Manufacturer A', NULL, 1, 0);
INSERT INTO `##_manufacturers` (`manufacturers_id`, `manufacturers_name`, `manufacturers_image`, `manufacturers_status`, `manufacturers_sort`) VALUES(2, 'Manufacturer B', NULL, 1, 0);
INSERT INTO `##_manufacturers` (`manufacturers_id`, `manufacturers_name`, `manufacturers_image`, `manufacturers_status`, `manufacturers_sort`) VALUES(3, 'Manufacturer C', NULL, 1, 0);
INSERT INTO `##_manufacturers` (`manufacturers_id`, `manufacturers_name`, `manufacturers_image`, `manufacturers_status`, `manufacturers_sort`) VALUES(4, 'Manufacturer D', NULL, 1, 0);

-- `##_shipping`

INSERT INTO `##_shipping` (`shipping_id`, `shipping_code`, `shipping_dir`, `shipping_icon`, `shipping_tax_class`, `status`, `sort_order`, `shipping_type`, `shipping_tpl`) VALUES(1, 'Standard', '', '', 1, 1, '1', 'price', '');

-- `##_shipping_cost`

INSERT INTO `##_shipping_cost` (`shipping_cost_id`, `shipping_id`, `shipping_geo_zone`, `shipping_country_code`, `shipping_type_value_from`, `shipping_type_value_to`, `shipping_price`, `shipping_allowed`) VALUES(1, 1, 31, '', 0.00, 5000.00, 5.0000, 1);

-- `##_products`

INSERT INTO `##_products` (`products_id`,`external_id`,`permission_id`,`products_owner`,`products_ean`,`products_quantity`,`products_average_quantity`,`products_shippingtime`,`products_model`,`price_flag_graduated_all`,`price_flag_graduated_1`,`price_flag_graduated_2`,`price_flag_graduated_3`,`price_flag_graduated_4`,`products_sort`,`products_image`,`products_price`,`date_added`,`last_modified`,`date_available`,`products_weight`,`products_status`,`products_tax_class_id`,`product_template`,`product_list_template`,`manufacturers_id`,`products_ordered`,`products_transactions`,`products_fsk18`,`products_vpe`,`products_vpe_status`,`products_vpe_value`,`products_unit`,`products_average_rating`,`products_rating_count`,`products_digital`,`flag_has_specials`,`products_serials`,`total_downloads`) VALUES (1,NULL,1,1,'ean001',50.00,20,0,'art001',0,0,0,0,0,0,'quilted-regular-fit-coat-34610-1.jpg',50.0000000000,'2016-07-15 13:36:04','2016-07-15 13:53:03',NULL,1.0000,1,1,'','',1,0,0,0,0,0,0.0000,39,0.0000,0,0,0,0,0);
INSERT INTO `##_products` (`products_id`,`external_id`,`permission_id`,`products_owner`,`products_ean`,`products_quantity`,`products_average_quantity`,`products_shippingtime`,`products_model`,`price_flag_graduated_all`,`price_flag_graduated_1`,`price_flag_graduated_2`,`price_flag_graduated_3`,`price_flag_graduated_4`,`products_sort`,`products_image`,`products_price`,`date_added`,`last_modified`,`date_available`,`products_weight`,`products_status`,`products_tax_class_id`,`product_template`,`product_list_template`,`manufacturers_id`,`products_ordered`,`products_transactions`,`products_fsk18`,`products_vpe`,`products_vpe_status`,`products_vpe_value`,`products_unit`,`products_average_rating`,`products_rating_count`,`products_digital`,`flag_has_specials`,`products_serials`,`total_downloads`) VALUES (2,NULL,1,1,'ean002',50.00,300,0,'art002',0,0,0,0,0,0,'straight-tailored-trousers-black-32607-9.jpg',150.0000000000,'2016-07-15 13:36:04','2016-07-15 13:53:04',NULL,1.0000,1,1,'','',2,0,0,0,0,0,0.0000,39,0.0000,0,0,1,0,0);
INSERT INTO `##_products` (`products_id`,`external_id`,`permission_id`,`products_owner`,`products_ean`,`products_quantity`,`products_average_quantity`,`products_shippingtime`,`products_model`,`price_flag_graduated_all`,`price_flag_graduated_1`,`price_flag_graduated_2`,`price_flag_graduated_3`,`price_flag_graduated_4`,`products_sort`,`products_image`,`products_price`,`date_added`,`last_modified`,`date_available`,`products_weight`,`products_status`,`products_tax_class_id`,`product_template`,`product_list_template`,`manufacturers_id`,`products_ordered`,`products_transactions`,`products_fsk18`,`products_vpe`,`products_vpe_status`,`products_vpe_value`,`products_unit`,`products_average_rating`,`products_rating_count`,`products_digital`,`flag_has_specials`,`products_serials`,`total_downloads`) VALUES (3,NULL,1,1,'ean003',50.00,70,0,'art003',1,0,0,0,0,0,'v-neck-slim-fit-jumper-27067-1.jpg',50.0000000000,'2016-07-15 13:36:04','2016-07-15 13:53:06',NULL,1.0000,1,1,'','',0,0,0,0,0,0,0.0000,39,0.0000,0,0,0,0,0);
INSERT INTO `##_products` (`products_id`,`external_id`,`permission_id`,`products_owner`,`products_ean`,`products_quantity`,`products_average_quantity`,`products_shippingtime`,`products_model`,`price_flag_graduated_all`,`price_flag_graduated_1`,`price_flag_graduated_2`,`price_flag_graduated_3`,`price_flag_graduated_4`,`products_sort`,`products_image`,`products_price`,`date_added`,`last_modified`,`date_available`,`products_weight`,`products_status`,`products_tax_class_id`,`product_template`,`product_list_template`,`manufacturers_id`,`products_ordered`,`products_transactions`,`products_fsk18`,`products_vpe`,`products_vpe_status`,`products_vpe_value`,`products_unit`,`products_average_rating`,`products_rating_count`,`products_digital`,`flag_has_specials`,`products_serials`,`total_downloads`) VALUES (4,NULL,2,1,'ean006',50.00,80,0,'art00',0,0,0,0,0,0,'peach-floral-print-shift-dress-27887-1.jpg',10.0000000000,'2016-07-15 13:36:04','2016-07-15 13:53:07',NULL,1.0000,1,2,'','',3,0,0,0,0,0,0.0000,39,0.0000,0,0,0,0,0);
INSERT INTO `##_products` (`products_id`,`external_id`,`permission_id`,`products_owner`,`products_ean`,`products_quantity`,`products_average_quantity`,`products_shippingtime`,`products_model`,`price_flag_graduated_all`,`price_flag_graduated_1`,`price_flag_graduated_2`,`price_flag_graduated_3`,`price_flag_graduated_4`,`products_sort`,`products_image`,`products_price`,`date_added`,`last_modified`,`date_available`,`products_weight`,`products_status`,`products_tax_class_id`,`product_template`,`product_list_template`,`manufacturers_id`,`products_ordered`,`products_transactions`,`products_fsk18`,`products_vpe`,`products_vpe_status`,`products_vpe_value`,`products_unit`,`products_average_rating`,`products_rating_count`,`products_digital`,`flag_has_specials`,`products_serials`,`total_downloads`) VALUES (5,NULL,2,1,'ean007',50.00,0,6,'art007',0,0,0,0,0,0,'contrast-floral-a-line-dress-34547-4.jpg',11.7647000000,'2016-07-15 13:08:39','2016-07-15 13:11:15',NULL,0.0000,1,1,'','',1,0,0,0,0,0,0.0000,39,0.0000,0,0,0,0,0);
INSERT INTO `##_products` (`products_id`,`external_id`,`permission_id`,`products_owner`,`products_ean`,`products_quantity`,`products_average_quantity`,`products_shippingtime`,`products_model`,`price_flag_graduated_all`,`price_flag_graduated_1`,`price_flag_graduated_2`,`price_flag_graduated_3`,`price_flag_graduated_4`,`products_sort`,`products_image`,`products_price`,`date_added`,`last_modified`,`date_available`,`products_weight`,`products_status`,`products_tax_class_id`,`product_template`,`product_list_template`,`manufacturers_id`,`products_ordered`,`products_transactions`,`products_fsk18`,`products_vpe`,`products_vpe_status`,`products_vpe_value`,`products_unit`,`products_average_rating`,`products_rating_count`,`products_digital`,`flag_has_specials`,`products_serials`,`total_downloads`) VALUES (6,NULL,2,1,'ean008',50.00,0,6,'art008',0,0,0,0,0,0,'collard-floral-skater-dress-black-lilac-36712-5.jpg',63.8655000000,'2016-07-15 13:12:07','2016-07-15 13:13:06',NULL,0.0000,1,1,'','',1,0,0,0,0,0,0.0000,39,0.0000,0,0,0,0,0);
INSERT INTO `##_products` (`products_id`,`external_id`,`permission_id`,`products_owner`,`products_ean`,`products_quantity`,`products_average_quantity`,`products_shippingtime`,`products_model`,`price_flag_graduated_all`,`price_flag_graduated_1`,`price_flag_graduated_2`,`price_flag_graduated_3`,`price_flag_graduated_4`,`products_sort`,`products_image`,`products_price`,`date_added`,`last_modified`,`date_available`,`products_weight`,`products_status`,`products_tax_class_id`,`product_template`,`product_list_template`,`manufacturers_id`,`products_ordered`,`products_transactions`,`products_fsk18`,`products_vpe`,`products_vpe_status`,`products_vpe_value`,`products_unit`,`products_average_rating`,`products_rating_count`,`products_digital`,`flag_has_specials`,`products_serials`,`total_downloads`) VALUES (7,NULL,3,1,'ean009',50.00,0,6,'art009',0,0,0,0,0,0,'striped-basket-weave-tote-bag-36825-1.jpg',4.2017000000,'2016-07-15 13:15:58','2016-07-15 13:17:34',NULL,0.0000,1,1,'','',1,0,0,0,0,0,0.0000,39,0.0000,0,0,0,0,0);
INSERT INTO `##_products` (`products_id`,`external_id`,`permission_id`,`products_owner`,`products_ean`,`products_quantity`,`products_average_quantity`,`products_shippingtime`,`products_model`,`price_flag_graduated_all`,`price_flag_graduated_1`,`price_flag_graduated_2`,`price_flag_graduated_3`,`price_flag_graduated_4`,`products_sort`,`products_image`,`products_price`,`date_added`,`last_modified`,`date_available`,`products_weight`,`products_status`,`products_tax_class_id`,`product_template`,`product_list_template`,`manufacturers_id`,`products_ordered`,`products_transactions`,`products_fsk18`,`products_vpe`,`products_vpe_status`,`products_vpe_value`,`products_unit`,`products_average_rating`,`products_rating_count`,`products_digital`,`flag_has_specials`,`products_serials`,`total_downloads`) VALUES (8,NULL,3,1,'ean010',50.00,0,6,'art010',0,0,0,0,0,0,'united-kingdom-flag-handbag-36692-1.jpg',24.3697000000,'2016-07-15 13:18:30','2016-07-15 13:19:35',NULL,0.0000,1,1,'','',1,0,0,0,0,0,0.0000,39,0.0000,0,0,0,0,0);
INSERT INTO `##_products` (`products_id`,`external_id`,`permission_id`,`products_owner`,`products_ean`,`products_quantity`,`products_average_quantity`,`products_shippingtime`,`products_model`,`price_flag_graduated_all`,`price_flag_graduated_1`,`price_flag_graduated_2`,`price_flag_graduated_3`,`price_flag_graduated_4`,`products_sort`,`products_image`,`products_price`,`date_added`,`last_modified`,`date_available`,`products_weight`,`products_status`,`products_tax_class_id`,`product_template`,`product_list_template`,`manufacturers_id`,`products_ordered`,`products_transactions`,`products_fsk18`,`products_vpe`,`products_vpe_status`,`products_vpe_value`,`products_unit`,`products_average_rating`,`products_rating_count`,`products_digital`,`flag_has_specials`,`products_serials`,`total_downloads`) VALUES (9,NULL,3,1,'ean011',50.00,0,6,'art011',0,0,0,0,0,0,'aviator-sunglasses-33988-1.jpg',56.3025000000,'2016-07-15 13:20:32','2016-07-15 13:22:06',NULL,0.0000,1,1,'','',1,0,0,0,0,0,0.0000,39,0.0000,0,0,0,0,0);
INSERT INTO `##_products` (`products_id`,`external_id`,`permission_id`,`products_owner`,`products_ean`,`products_quantity`,`products_average_quantity`,`products_shippingtime`,`products_model`,`price_flag_graduated_all`,`price_flag_graduated_1`,`price_flag_graduated_2`,`price_flag_graduated_3`,`price_flag_graduated_4`,`products_sort`,`products_image`,`products_price`,`date_added`,`last_modified`,`date_available`,`products_weight`,`products_status`,`products_tax_class_id`,`product_template`,`product_list_template`,`manufacturers_id`,`products_ordered`,`products_transactions`,`products_fsk18`,`products_vpe`,`products_vpe_status`,`products_vpe_value`,`products_unit`,`products_average_rating`,`products_rating_count`,`products_digital`,`flag_has_specials`,`products_serials`,`total_downloads`) VALUES (10,NULL,2,1,'ean012',20.00,0,6,'art012',0,0,0,0,0,0,'plunge-floral-wide-leg-jumpsuit-black-blue-34207-5.jpg',7.5630000000,'2016-07-15 13:24:30','2016-07-15 13:25:48',NULL,0.0000,1,1,'','',1,0,0,0,0,0,0.0000,39,0.0000,0,0,0,0,0);
INSERT INTO `##_products` (`products_id`,`external_id`,`permission_id`,`products_owner`,`products_ean`,`products_quantity`,`products_average_quantity`,`products_shippingtime`,`products_model`,`price_flag_graduated_all`,`price_flag_graduated_1`,`price_flag_graduated_2`,`price_flag_graduated_3`,`price_flag_graduated_4`,`products_sort`,`products_image`,`products_price`,`date_added`,`last_modified`,`date_available`,`products_weight`,`products_status`,`products_tax_class_id`,`product_template`,`product_list_template`,`manufacturers_id`,`products_ordered`,`products_transactions`,`products_fsk18`,`products_vpe`,`products_vpe_status`,`products_vpe_value`,`products_unit`,`products_average_rating`,`products_rating_count`,`products_digital`,`flag_has_specials`,`products_serials`,`total_downloads`) VALUES (11,NULL,2,1,'ean013',30.00,0,6,'art013',0,0,0,0,0,0,'black-metallic-contrast-wrap-playsuit-36225-1.jpg',15.9664000000,'2016-07-15 13:28:26','2016-07-15 13:29:22',NULL,0.0000,1,1,'','',1,0,0,0,0,0,0.0000,39,0.0000,0,0,0,0,0);
INSERT INTO `##_products` (`products_id`,`external_id`,`permission_id`,`products_owner`,`products_ean`,`products_quantity`,`products_average_quantity`,`products_shippingtime`,`products_model`,`price_flag_graduated_all`,`price_flag_graduated_1`,`price_flag_graduated_2`,`price_flag_graduated_3`,`price_flag_graduated_4`,`products_sort`,`products_image`,`products_price`,`date_added`,`last_modified`,`date_available`,`products_weight`,`products_status`,`products_tax_class_id`,`product_template`,`product_list_template`,`manufacturers_id`,`products_ordered`,`products_transactions`,`products_fsk18`,`products_vpe`,`products_vpe_status`,`products_vpe_value`,`products_unit`,`products_average_rating`,`products_rating_count`,`products_digital`,`flag_has_specials`,`products_serials`,`total_downloads`) VALUES (12,NULL,2,1,'ean014',36.00,0,6,'art014',0,0,0,0,0,0,'floral-wide-leg-jumpsuit-35143-1.jpg',24.3697000000,'2016-07-15 13:31:35','2016-07-15 13:32:25',NULL,0.0000,1,1,'','',1,0,0,0,0,0,0.0000,39,0.0000,0,0,0,0,0);
INSERT INTO `##_products` (`products_id`,`external_id`,`permission_id`,`products_owner`,`products_ean`,`products_quantity`,`products_average_quantity`,`products_shippingtime`,`products_model`,`price_flag_graduated_all`,`price_flag_graduated_1`,`price_flag_graduated_2`,`price_flag_graduated_3`,`price_flag_graduated_4`,`products_sort`,`products_image`,`products_price`,`date_added`,`last_modified`,`date_available`,`products_weight`,`products_status`,`products_tax_class_id`,`product_template`,`product_list_template`,`manufacturers_id`,`products_ordered`,`products_transactions`,`products_fsk18`,`products_vpe`,`products_vpe_status`,`products_vpe_value`,`products_unit`,`products_average_rating`,`products_rating_count`,`products_digital`,`flag_has_specials`,`products_serials`,`total_downloads`) VALUES (13,NULL,3,1,'ean015',20.00,0,6,'art015',0,0,0,0,0,0,'bow-back-contrast-hat-11643-3.jpg',32.7731000000,'2016-07-15 13:36:04','2016-07-15 13:37:09',NULL,0.0000,1,1,'','',1,0,0,0,0,0,0.0000,39,0.0000,0,0,0,0,0);
INSERT INTO `##_products` (`products_id`,`external_id`,`permission_id`,`products_owner`,`products_ean`,`products_quantity`,`products_average_quantity`,`products_shippingtime`,`products_model`,`price_flag_graduated_all`,`price_flag_graduated_1`,`price_flag_graduated_2`,`price_flag_graduated_3`,`price_flag_graduated_4`,`products_sort`,`products_image`,`products_price`,`date_added`,`last_modified`,`date_available`,`products_weight`,`products_status`,`products_tax_class_id`,`product_template`,`product_list_template`,`manufacturers_id`,`products_ordered`,`products_transactions`,`products_fsk18`,`products_vpe`,`products_vpe_status`,`products_vpe_value`,`products_unit`,`products_average_rating`,`products_rating_count`,`products_digital`,`flag_has_specials`,`products_serials`,`total_downloads`) VALUES (16,NULL,2,1,'ean016',50.00,0,6,'art016',0,0,0,0,0,0,'black-basic-bodycon-dress-28859-4.jpg',32.7731092437,'2016-07-15 13:36:04','2016-08-03 12:20:00',NULL,0.0000,1,1,'','',1,0,0,0,0,0,0.0000,39,0.0000,0,0,0,0,0);

-- `##_products_price_group_all`

INSERT INTO `##_products_price_group_all` (`id`, `products_id`, `discount_quantity`, `price`) VALUES(1, 3, 10, 40.0000);
INSERT INTO `##_products_price_group_all` (`id`, `products_id`, `discount_quantity`, `price`) VALUES(2, 3, 20, 30.0000);
INSERT INTO `##_products_price_group_all` (`id`, `products_id`, `discount_quantity`, `price`) VALUES(3, 3, 1, 50.0000);

-- `##_products_price_special`

INSERT INTO `##_products_price_special` (`id`, `products_id`, `specials_price`, `date_available`, `date_expired`, `status`, `group_permission_all`, `group_permission_1`, `group_permission_2`, `group_permission_3`) VALUES(1, 2, 70.0000, '2008-01-01 12:00:00', '2025-08-08 12:00:00', 1, 1, 0, 0, 0);

-- `##_categories`

INSERT INTO `##_categories` (`categories_id`, `external_id`, `permission_id`, `categories_owner`, `categories_image`, `categories_left`, `categories_right`, `categories_level`, `parent_id`, `categories_status`, `categories_template`, `listing_template`, `sort_order`, `products_sorting`, `products_sorting2`, `top_category`, `category_custom_link`, `category_custom_link_type`, `category_custom_link_id`) VALUES (1, NULL, 0, 1, '', 1, 2, 1, 0, 1, '', '', 0, '', '', 0, 0, 'New', 0);
INSERT INTO `##_categories` (`categories_id`, `external_id`, `permission_id`, `categories_owner`, `categories_image`, `categories_left`, `categories_right`, `categories_level`, `parent_id`, `categories_status`, `categories_template`, `listing_template`, `sort_order`, `products_sorting`, `products_sorting2`, `top_category`, `category_custom_link`, `category_custom_link_type`, `category_custom_link_id`) VALUES (2, NULL, 0, 1, '', 3, 8, 1, 0, 1, '', '', 0, '', '', 0, 0, 'New', 0);
INSERT INTO `##_categories` (`categories_id`, `external_id`, `permission_id`, `categories_owner`, `categories_image`, `categories_left`, `categories_right`, `categories_level`, `parent_id`, `categories_status`, `categories_template`, `listing_template`, `sort_order`, `products_sorting`, `products_sorting2`, `top_category`, `category_custom_link`, `category_custom_link_type`, `category_custom_link_id`) VALUES (3, NULL, 0, 1, NULL, 9, 10, 1, 0, 1, '', '', 0, '', '', 0, 0, 'New', 0);
INSERT INTO `##_categories` (`categories_id`, `external_id`, `permission_id`, `categories_owner`, `categories_image`, `categories_left`, `categories_right`, `categories_level`, `parent_id`, `categories_status`, `categories_template`, `listing_template`, `sort_order`, `products_sorting`, `products_sorting2`, `top_category`, `category_custom_link`, `category_custom_link_type`, `category_custom_link_id`) VALUES (4, NULL, 0, 1, NULL, 4, 5, 2, 2, 1, '', '', 0, '', '', 0, 0, 'New', 0);
INSERT INTO `##_categories` (`categories_id`, `external_id`, `permission_id`, `categories_owner`, `categories_image`, `categories_left`, `categories_right`, `categories_level`, `parent_id`, `categories_status`, `categories_template`, `listing_template`, `sort_order`, `products_sorting`, `products_sorting2`, `top_category`, `category_custom_link`, `category_custom_link_type`, `category_custom_link_id`) VALUES (5, NULL, 0, 1, NULL, 6, 7, 2, 2, 1, '', '', 0, '', '', 0, 0, 'New', 0);

-- `##_products_to_categories`

INSERT INTO `##_products_to_categories` (`products_id`, `categories_id`, `master_link`, `store_id`) VALUES (1, 1, 1, 1);
INSERT INTO `##_products_to_categories` (`products_id`, `categories_id`, `master_link`, `store_id`) VALUES (2, 1, 1, 1);
INSERT INTO `##_products_to_categories` (`products_id`, `categories_id`, `master_link`, `store_id`) VALUES (3, 1, 1, 1);
INSERT INTO `##_products_to_categories` (`products_id`, `categories_id`, `master_link`, `store_id`) VALUES (4, 4, 1, 1);
INSERT INTO `##_products_to_categories` (`products_id`, `categories_id`, `master_link`, `store_id`) VALUES (5, 4, 1, 1);
INSERT INTO `##_products_to_categories` (`products_id`, `categories_id`, `master_link`, `store_id`) VALUES (6, 4, 1, 1);
INSERT INTO `##_products_to_categories` (`products_id`, `categories_id`, `master_link`, `store_id`) VALUES (7, 3, 1, 1);
INSERT INTO `##_products_to_categories` (`products_id`, `categories_id`, `master_link`, `store_id`) VALUES (8, 3, 1, 1);
INSERT INTO `##_products_to_categories` (`products_id`, `categories_id`, `master_link`, `store_id`) VALUES (9, 3, 1, 1);
INSERT INTO `##_products_to_categories` (`products_id`, `categories_id`, `master_link`, `store_id`) VALUES (10, 5, 1, 1);
INSERT INTO `##_products_to_categories` (`products_id`, `categories_id`, `master_link`, `store_id`) VALUES (11, 5, 1, 1);
INSERT INTO `##_products_to_categories` (`products_id`, `categories_id`, `master_link`, `store_id`) VALUES (12, 5, 1, 1);
INSERT INTO `##_products_to_categories` (`products_id`, `categories_id`, `master_link`, `store_id`) VALUES (4, 2, 0, 1);
INSERT INTO `##_products_to_categories` (`products_id`, `categories_id`, `master_link`, `store_id`) VALUES (5, 2, 0, 1);
INSERT INTO `##_products_to_categories` (`products_id`, `categories_id`, `master_link`, `store_id`) VALUES (6, 2, 0, 1);
INSERT INTO `##_products_to_categories` (`products_id`, `categories_id`, `master_link`, `store_id`) VALUES (10, 2, 0, 1);
INSERT INTO `##_products_to_categories` (`products_id`, `categories_id`, `master_link`, `store_id`) VALUES (11, 2, 0, 1);
INSERT INTO `##_products_to_categories` (`products_id`, `categories_id`, `master_link`, `store_id`) VALUES (12, 2, 0, 1);
INSERT INTO `##_products_to_categories` (`products_id`, `categories_id`, `master_link`, `store_id`) VALUES (13, 3, 1, 1);
INSERT INTO `##_products_to_categories` (`products_id`,`categories_id`,`master_link`,`store_id`) VALUES (16,4,1,1);

-- `##_slider`

INSERT INTO `##_slider` (`slider_id`, `slide_speed`, `pagination_speed`, `auto_play_speed`, `slider_note`) VALUES (1, 800, 800, 7000, 'Startpage');

-- `##_slides`

INSERT INTO `##_slides` (`slide_id`, `slider_id`, `slide_language_code`, `slide_status`, `slide_date_from`, `slide_date_to`, `slide_image`, `slide_link`, `slide_alt_text`) VALUES (1, 1, 'de', 1, '2016-07-14 00:00:00', '2025-07-30 00:00:00', 'teaser1Example.png', '', ''), (2, 1, 'de', 1, '2016-07-11 12:12:25', '2025-07-15 12:12:25', 'teaser3Example.png', '', '');

-- `##_media`

INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (1, 'peach-floral-print-shift-dress-27887-1.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (2, 'peach-floral-print-shift-dress-27887-2.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (3, 'peach-floral-print-shift-dress-27887-3.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (4, 'peach-floral-print-shift-dress-peach-27887-4.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (5, 'contrast-floral-a-line-dress-34547-3.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (6, 'contrast-floral-a-line-dress-34547-4.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (7, 'contrast-floral-a-line-dress-34547-5.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (8, 'collard-floral-skater-dress-black-lilac-36712-5.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (9, 'collard-floral-skater-dress-36712-1.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (10, 'collard-floral-skater-dress-36712-2.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (11, 'collard-floral-skater-dress-36712-3.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (12, 'striped-basket-weave-tote-bag-36825-1.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (13, 'striped-basket-weave-tote-bag-pink-multi-36825-5.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (14, 'striped-basket-weave-tote-bag-36825-3.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (15, 'united-kingdom-flag-handbag-36692-1.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (16, 'united-kingdom-flag-handbag-36692-3.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (17, 'united-kingdom-flag-handbag-brown-36692-5.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (18, 'united-kingdom-flag-handbag-36692-4.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (19, 'aviator-sunglasses-33988-1.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (20, 'aviator-sunglasses-33988-3.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (21, 'aviator-sunglasses-33988-6.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (22, 'aviator-sunglasses-33988-8.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (23, 'plunge-floral-wide-leg-jumpsuit-black-blue-34207-5.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (24, 'plunge-floral-wide-leg-jumpsuit-34207-4.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (25, 'plunge-floral-wide-leg-jumpsuit-34207-1.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (26, 'plunge-floral-wide-leg-jumpsuit-34207-2.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (27, 'black-metallic-contrast-wrap-playsuit-36225-1.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (28, 'black-metallic-contrast-wrap-playsuit-36225-2.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (29, 'black-metallic-contrast-wrap-playsuit-black-36225-4.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (30, 'black-metallic-contrast-wrap-playsuit-36225-3.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (31, 'floral-wide-leg-jumpsuit-35143-1.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (32, 'floral-wide-leg-jumpsuit-35143-4.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (33, 'floral-wide-leg-jumpsuit-black-multi-35143-6.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (34, 'floral-wide-leg-jumpsuit-35143-3.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (35, 'bow-back-contrast-hat-11643-3.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (36, 'bow-back-contrast-hat-11643-2.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (37, 'bow-back-contrast-hat-black-white-11643-5.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (38, 'quilted-regular-fit-coat-34610-1.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (39, 'quilted-regular-fit-coat-34610-2.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (40, 'quilted-regular-fit-coat-34610-3.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (41, 'straight-tailored-trousers-black-32607-9.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (42, 'straight-tailored-trousers-32607-4.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (43, 'straight-tailored-trousers-32607-3.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (44, 'v-neck-slim-fit-jumper-27067-1.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (45, 'v-neck-slim-fit-jumper-mauve-27067-6.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`, `file`, `type`, `class`, `download_status`, `status`, `owner`, `max_dl_count`, `max_dl_days`, `total_downloads`) VALUES (46, 'v-neck-slim-fit-jumper-27067-3.jpg', 'images', 'product', 'free', 'true', 1, 0, 0, 0);
INSERT INTO `##_media` (`id`,`file`,`type`,`class`,`download_status`,`status`,`owner`,`max_dl_count`,`max_dl_days`,`total_downloads`) VALUES (47,'black-basic-bodycon-dress-28859-4.jpg','images','product','free','true',1,0,0,0);
INSERT INTO `##_media` (`id`,`file`,`type`,`class`,`download_status`,`status`,`owner`,`max_dl_count`,`max_dl_days`,`total_downloads`) VALUES (48,'black-basic-bodycon-dress-28859-1.jpg','images','product','free','true',1,0,0,0);
INSERT INTO `##_media` (`id`,`file`,`type`,`class`,`download_status`,`status`,`owner`,`max_dl_count`,`max_dl_days`,`total_downloads`) VALUES (49,'black-basic-bodycon-dress-28859-2.jpg','images','product','free','true',1,0,0,0);
INSERT INTO `##_media` (`id`,`file`,`type`,`class`,`download_status`,`status`,`owner`,`max_dl_count`,`max_dl_days`,`total_downloads`) VALUES (50,'black-basic-bodycon-dress-28859-3.jpg','images','product','free','true',1,0,0,0);
INSERT INTO `##_media` (`id`,`file`,`type`,`class`,`download_status`,`status`,`owner`,`max_dl_count`,`max_dl_days`,`total_downloads`) VALUES (51,'rust-bodycon-basic-dress-28847-4.jpg','images','product','free','true',1,0,0,0);
INSERT INTO `##_media` (`id`,`file`,`type`,`class`,`download_status`,`status`,`owner`,`max_dl_count`,`max_dl_days`,`total_downloads`) VALUES (52,'rust-bodycon-basic-dress-28847-1.jpg','images','product','free','true',1,0,0,0);
INSERT INTO `##_media` (`id`,`file`,`type`,`class`,`download_status`,`status`,`owner`,`max_dl_count`,`max_dl_days`,`total_downloads`) VALUES (53,'rust-bodycon-basic-dress-28847-2.jpg','images','product','free','true',1,0,0,0);
INSERT INTO `##_media` (`id`,`file`,`type`,`class`,`download_status`,`status`,`owner`,`max_dl_count`,`max_dl_days`,`total_downloads`) VALUES (54,'rust-bodycon-basic-dress-28847-3.jpg','images','product','free','true',1,0,0,0);

-- `##_media_link`

INSERT INTO `##_media_link` (`ml_id`, `m_id`, `link_id`, `class`, `type`, `sort_order`) VALUES (1, 2, 4, 'product', 'images', 1);
INSERT INTO `##_media_link` (`ml_id`, `m_id`, `link_id`, `class`, `type`, `sort_order`) VALUES (2, 3, 4, 'product', 'images', 2);
INSERT INTO `##_media_link` (`ml_id`, `m_id`, `link_id`, `class`, `type`, `sort_order`) VALUES (3, 4, 4, 'product', 'images', 3);
INSERT INTO `##_media_link` (`ml_id`, `m_id`, `link_id`, `class`, `type`, `sort_order`) VALUES (4, 5, 5, 'product', 'images', 1);
INSERT INTO `##_media_link` (`ml_id`, `m_id`, `link_id`, `class`, `type`, `sort_order`) VALUES (5, 7, 5, 'product', 'images', 2);
INSERT INTO `##_media_link` (`ml_id`, `m_id`, `link_id`, `class`, `type`, `sort_order`) VALUES (6, 9, 6, 'product', 'images', 1);
INSERT INTO `##_media_link` (`ml_id`, `m_id`, `link_id`, `class`, `type`, `sort_order`) VALUES (7, 10, 6, 'product', 'images', 2);
INSERT INTO `##_media_link` (`ml_id`, `m_id`, `link_id`, `class`, `type`, `sort_order`) VALUES (8, 11, 6, 'product', 'images', 3);
INSERT INTO `##_media_link` (`ml_id`, `m_id`, `link_id`, `class`, `type`, `sort_order`) VALUES (9, 13, 7, 'product', 'images', 1);
INSERT INTO `##_media_link` (`ml_id`, `m_id`, `link_id`, `class`, `type`, `sort_order`) VALUES (10, 14, 7, 'product', 'images', 2);
INSERT INTO `##_media_link` (`ml_id`, `m_id`, `link_id`, `class`, `type`, `sort_order`) VALUES (11, 16, 8, 'product', 'images', 1);
INSERT INTO `##_media_link` (`ml_id`, `m_id`, `link_id`, `class`, `type`, `sort_order`) VALUES (12, 17, 8, 'product', 'images', 2);
INSERT INTO `##_media_link` (`ml_id`, `m_id`, `link_id`, `class`, `type`, `sort_order`) VALUES (13, 18, 8, 'product', 'images', 3);
INSERT INTO `##_media_link` (`ml_id`, `m_id`, `link_id`, `class`, `type`, `sort_order`) VALUES (14, 20, 9, 'product', 'images', 1);
INSERT INTO `##_media_link` (`ml_id`, `m_id`, `link_id`, `class`, `type`, `sort_order`) VALUES (15, 21, 9, 'product', 'images', 2);
INSERT INTO `##_media_link` (`ml_id`, `m_id`, `link_id`, `class`, `type`, `sort_order`) VALUES (16, 22, 9, 'product', 'images', 3);
INSERT INTO `##_media_link` (`ml_id`, `m_id`, `link_id`, `class`, `type`, `sort_order`) VALUES (17, 24, 10, 'product', 'images', 1);
INSERT INTO `##_media_link` (`ml_id`, `m_id`, `link_id`, `class`, `type`, `sort_order`) VALUES (18, 25, 10, 'product', 'images', 2);
INSERT INTO `##_media_link` (`ml_id`, `m_id`, `link_id`, `class`, `type`, `sort_order`) VALUES (19, 26, 10, 'product', 'images', 3);
INSERT INTO `##_media_link` (`ml_id`, `m_id`, `link_id`, `class`, `type`, `sort_order`) VALUES (20, 28, 11, 'product', 'images', 1);
INSERT INTO `##_media_link` (`ml_id`, `m_id`, `link_id`, `class`, `type`, `sort_order`) VALUES (21, 29, 11, 'product', 'images', 2);
INSERT INTO `##_media_link` (`ml_id`, `m_id`, `link_id`, `class`, `type`, `sort_order`) VALUES (22, 30, 11, 'product', 'images', 3);
INSERT INTO `##_media_link` (`ml_id`, `m_id`, `link_id`, `class`, `type`, `sort_order`) VALUES (23, 32, 12, 'product', 'images', 1);
INSERT INTO `##_media_link` (`ml_id`, `m_id`, `link_id`, `class`, `type`, `sort_order`) VALUES (24, 33, 12, 'product', 'images', 2);
INSERT INTO `##_media_link` (`ml_id`, `m_id`, `link_id`, `class`, `type`, `sort_order`) VALUES (25, 34, 12, 'product', 'images', 3);
INSERT INTO `##_media_link` (`ml_id`, `m_id`, `link_id`, `class`, `type`, `sort_order`) VALUES (26, 36, 13, 'product', 'images', 1);
INSERT INTO `##_media_link` (`ml_id`, `m_id`, `link_id`, `class`, `type`, `sort_order`) VALUES (27, 37, 13, 'product', 'images', 2);
INSERT INTO `##_media_link` (`ml_id`, `m_id`, `link_id`, `class`, `type`, `sort_order`) VALUES (28, 39, 1, 'product', 'images', 1);
INSERT INTO `##_media_link` (`ml_id`, `m_id`, `link_id`, `class`, `type`, `sort_order`) VALUES (29, 40, 1, 'product', 'images', 2);
INSERT INTO `##_media_link` (`ml_id`, `m_id`, `link_id`, `class`, `type`, `sort_order`) VALUES (30, 42, 2, 'product', 'images', 1);
INSERT INTO `##_media_link` (`ml_id`, `m_id`, `link_id`, `class`, `type`, `sort_order`) VALUES (31, 43, 2, 'product', 'images', 2);
INSERT INTO `##_media_link` (`ml_id`, `m_id`, `link_id`, `class`, `type`, `sort_order`) VALUES (32, 45, 3, 'product', 'images', 1);
INSERT INTO `##_media_link` (`ml_id`, `m_id`, `link_id`, `class`, `type`, `sort_order`) VALUES (33, 46, 3, 'product', 'images', 2);
INSERT INTO `##_media_link` (`ml_id`,`m_id`,`link_id`,`class`,`type`,`sort_order`) VALUES (34,48,16,'product','images',1);
INSERT INTO `##_media_link` (`ml_id`,`m_id`,`link_id`,`class`,`type`,`sort_order`) VALUES (35,49,16,'product','images',3);
INSERT INTO `##_media_link` (`ml_id`,`m_id`,`link_id`,`class`,`type`,`sort_order`) VALUES (36,50,16,'product','images',2);

-- `##_media_to_media_gallery`

INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (1, 1, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (2, 2, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (3, 3, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (4, 4, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (5, 5, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (6, 6, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (7, 7, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (8, 8, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (9, 9, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (10, 10, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (11, 11, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (12, 12, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (13, 13, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (14, 14, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (15, 15, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (16, 16, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (17, 17, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (18, 18, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (19, 19, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (20, 20, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (21, 21, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (22, 22, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (23, 23, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (24, 24, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (25, 25, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (26, 26, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (27, 27, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (28, 28, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (29, 29, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (30, 30, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (31, 31, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (32, 32, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (33, 33, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (34, 34, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (35, 35, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (36, 36, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (37, 37, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (38, 38, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (39, 39, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (40, 40, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (41, 41, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (42, 42, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (43, 43, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (44, 44, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (45, 45, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`, `m_id`, `mg_id`) VALUES (46, 46, 2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`,`m_id`,`mg_id`) VALUES (47,47,2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`,`m_id`,`mg_id`) VALUES (48,48,2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`,`m_id`,`mg_id`) VALUES (49,49,2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`,`m_id`,`mg_id`) VALUES (50,50,2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`,`m_id`,`mg_id`) VALUES (51,51,2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`,`m_id`,`mg_id`) VALUES (52,52,2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`,`m_id`,`mg_id`) VALUES (53,53,2);
INSERT INTO `##_media_to_media_gallery` (`ml_id`,`m_id`,`mg_id`) VALUES (54,54,2);

