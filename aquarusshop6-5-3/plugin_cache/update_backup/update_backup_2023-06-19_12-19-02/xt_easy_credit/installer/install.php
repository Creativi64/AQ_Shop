<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

define('TABLE_EASY_CREDIT_FINANCING', DB_PREFIX.'_easy_credit_financing');

$db->Execute("INSERT INTO " . TABLE_PAYMENT_COST . " (`payment_id`, `payment_geo_zone`, `payment_country_code`, `payment_type_value_from`, `payment_type_value_to`, `payment_price`,`payment_allowed`) VALUES(" . $payment_id . ", 0, 'DE', 200, 10000.00, 0, 1);");

$db->Execute("CREATE TABLE IF NOT EXISTS " . TABLE_EASY_CREDIT_FINANCING . " (
  `orders_id` INT(11) NOT NULL,
  `rehash` TEXT NOT NULL,
  PRIMARY KEY (`orders_id`))");

copy(_SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_easy_credit/images/easy_credit.png', _SRV_WEBROOT .'media/payment/easy_credit.png');
