<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT._SRV_WEB_PLUGINS.'xt_google_product_categories/classes/class.xt_google_product_categories.php';

if ($ID)
{
    global $db, $language;
    $name = $db->GetOne("SELECT `category_path` FROM ".TABLE_GOOGLE_CATEGORIES." WHERE `google_category_id`=? AND `language`=?",
        array($data[0]['google_product_cat'], $language->content_language));
    $data[0]['google_product_cat'] = google_product_categories::formatName($data[0]['google_product_cat'], $name);

}