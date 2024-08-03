<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if ($data['products_master_flag'] == '1')
{
    $data['products_master_model'] = '';
    $data['products_master_slave_order'] = 0;
}
else if ($data['products_master_model'] != '')
{
    $data['products_master_flag'] = 0;
    $data['ms_open_first_slave'] = 0;
    $data['ms_show_slave_list'] = 0;
    $data['ms_filter_slave_list'] = 0;
    $data['ms_filter_slave_list_hide_on_product'] = 0;
}
else {
    /**
    $data['products_master_model'] = '';
    $data['products_master_flag'] = 0;
    $data['products_image_from_master'] = 0;
    $data['products_description_from_master'] = 0;
    $data['products_short_description_from_master'] = 0;
    $data['products_keywords_from_master'] = 0;
    $data['products_image_from_master'] = 0;
    $data['products_master_slave_order'] = 0;
    $data['products_option_master_price'] = '';
    $data['ms_open_first_slave'] = 0;
    $data['ms_show_slave_list'] = 0;
    $data['ms_filter_slave_list'] = 0;
    $data['ms_filter_slave_list_hide_on_product'] = 0;
    $data['ms_load_masters_main_img'] = 0;
    $data['load_mains_imgs'] = 0;
    $data['ms_load_masters_free_downloads'] = 0;
     * */
}


