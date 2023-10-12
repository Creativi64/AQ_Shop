<?php


defined('_VALID_CALL') or die('Direct Access is not allowed.');

$db->CacheExecute("DELETE FROM ".TABLE_ADMIN_NAVIGATION." WHERE text = 'xt_master_slave'");
$db->CacheExecute("DELETE FROM ".TABLE_CONFIGURATION." WHERE config_key = '_SYSTEM_ADMIN_PAGE_SIZE_SLAVE_PRODUCT'");

$db->CacheExecute("DROP TABLE IF EXISTS ".DB_PREFIX."_plg_products_attributes");
$db->CacheExecute("DROP TABLE IF EXISTS ".DB_PREFIX."_plg_products_attributes_description");
$db->CacheExecute("DROP TABLE IF EXISTS ".DB_PREFIX."_plg_products_to_attributes");

$drop_columns = array(
    'products_master_model',
    'products_master_flag',
    'products_option_template',
    'products_option_list_template',
    'products_option_master_price',
    'products_image_from_master',
    'products_master_slave_order',
    'ms_load_masters_free_downloads',
    'ms_load_masters_main_img',
    'load_mains_imgs',
    'products_keywords_from_master',
    'products_short_description_from_master',
    'products_description_from_master',
    'ms_filter_slave_list_hide_on_product',
    'ms_filter_slave_list',
    'ms_show_slave_list',
    'ms_open_first_slave'
);

foreach($drop_columns as $column)
{
    if ($this->_FieldExists($column, TABLE_PRODUCTS))
    {
        $db->CacheExecute("ALTER TABLE ".TABLE_PRODUCTS." DROP `$column`");
    }
}

$db->CacheExecute("DROP TABLE IF EXISTS ".DB_PREFIX."_plg_products_attributes_templates");
$db->CacheExecute("DROP TABLE IF EXISTS ".DB_PREFIX."_tmp_products");
$db->CacheExecute("DROP TABLE IF EXISTS ".DB_PREFIX."_tmp_plg_products_to_attributes");
