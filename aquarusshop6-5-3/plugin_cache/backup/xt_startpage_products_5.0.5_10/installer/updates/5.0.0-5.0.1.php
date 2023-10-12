<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;
$sql = "UPDATE ".TABLE_PLUGIN_CONFIGURATION." set config_value = 'product_listing_slider.html' WHERE config_key = 'XT_STARTPAGE_PRODUCTS_PAGE_TPL' ";
$db->Execute($sql);