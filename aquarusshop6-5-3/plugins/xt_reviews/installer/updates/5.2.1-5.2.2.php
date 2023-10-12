<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS.'xt_reviews/hooks/page_registry_php_bottom.php';

global $db;

$db->Execute("update ".TABLE_PRODUCTS_REVIEWS." set review_text = REPLACE(review_text,'\\\\n', CHAR(10))");