<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT._SRV_WEB_PLUGINS.'xt_google_product_categories/classes/class.xt_google_product_categories.php';

google_product_categories::parseLine($data['google_product_cat'], $id);
if ($id > 0 )
{
    $data['google_product_cat'] = $id;
}
else {
    unset($data['google_product_cat']);
}