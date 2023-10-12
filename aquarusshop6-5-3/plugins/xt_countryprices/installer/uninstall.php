<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT. _SRV_WEB_PLUGINS.'xt_countryprices/classes/constants.php';

global $db;

$db->Execute("DROP TABLE IF EXISTS ".TABLE_PRODUCTS_PRICE_COUNTRY);
