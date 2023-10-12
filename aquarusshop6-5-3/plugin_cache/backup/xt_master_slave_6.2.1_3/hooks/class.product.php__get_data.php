<?php


defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db, $productGetParamsIsMainProduct, $productGetParamsIsVariant;
if(!isset($productGetParamsIsMainProduct)) $productGetParamsIsMainProduct = $db->GetOne("SELECT 1 FROM ".TABLE_PRODUCTS." WHERE products_id=? AND products_master_flag=1", $_REQUEST['edit_id']) ? true : false;
if(!isset($productGetParamsIsVariant)) $productGetParamsIsVariant = $db->GetOne("SELECT 1 FROM ".TABLE_PRODUCTS." WHERE products_id=? AND products_master_model IS NOT NULL AND products_master_model >''",  $_REQUEST['edit_id']) ? true : false;

$header['products_master_model'] = array(
        'type' => 'dropdown', // you can modyfy the auto type
        'url' => 'DropdownData.php?get=products_model&plugin_code=xt_master_slave');

$header['products_model_old'] = array('type' => 'hidden');

$dd_appendix = '';
if($productGetParamsIsVariant) $dd_appendix = '&is_variant=true';
else if($productGetParamsIsMainProduct) $dd_appendix = '&is_variant=false';


//$header['products_image_from_master'] = array('type' => 'status');
$header['products_description_from_master'] = array('type' => 'dropdown', 'url' => 'DropdownData.php?get=products_description_from_master&plugin_code=xt_master_slave'.$dd_appendix, 'store_autoLoad' => true);
$header['products_short_description_from_master'] = array('type' => 'dropdown', 'url' => 'DropdownData.php?get=products_short_description_from_master&plugin_code=xt_master_slave'.$dd_appendix, 'store_autoLoad' => true);
$header['products_keywords_from_master'] = array('type' => 'dropdown', 'url' => 'DropdownData.php?get=products_keywords_from_master&plugin_code=xt_master_slave'.$dd_appendix, 'store_autoLoad' => true);

$header['ms_load_masters_main_img'] = array('type' => 'dropdown', 'url' => 'DropdownData.php?get=ms_load_masters_main_img&plugin_code=xt_master_slave'.$dd_appendix, 'store_autoLoad' => true);
$header['load_mains_imgs'] = array('type' => 'dropdown', 'url' => 'DropdownData.php?get=load_mains_imgs&plugin_code=xt_master_slave'.$dd_appendix, 'store_autoLoad' => true);
$header['ms_load_masters_free_downloads'] = array('type' => 'dropdown', 'url' => 'DropdownData.php?get=ms_load_masters_free_downloads&plugin_code=xt_master_slave'.$dd_appendix, 'store_autoLoad' => true);

$header['products_master_slave_order'] = array('type' => 'text', 'width' => 100);

$header['products_option_master_price'] = array('type' => 'dropdown', 'url' => 'DropdownData.php?get=master_price_view&plugin_code=xt_master_slave');
$header['ms_open_first_slave'] = array('type' => 'dropdown', 'url' => 'DropdownData.php?get=ms_open_first_slave&plugin_code=xt_master_slave');
$header['ms_show_slave_list'] = array('type' => 'dropdown', 'url' => 'DropdownData.php?get=ms_show_slave_list&plugin_code=xt_master_slave');
$header['ms_filter_slave_list'] = array('type' => 'dropdown', 'url' => 'DropdownData.php?get=ms_filter_slave_list&plugin_code=xt_master_slave');
$header['ms_filter_slave_list_hide_on_product'] = array('type' => 'dropdown', 'url' => 'DropdownData.php?get=ms_filter_slave_list_hide_on_product&plugin_code=xt_master_slave');

$header['products_canonical_master'] = array('type' => 'status');

$groupingMaster_slave = 'master_slave';
$grouping['products_master_model'] = array('position' => $groupingMaster_slave);
$grouping['products_master_flag'] = array('position' => $groupingMaster_slave);
$grouping['products_image_from_master'] = array('position' => $groupingMaster_slave);
$grouping['products_description_from_master'] = array('position' => $groupingMaster_slave);
$grouping['products_short_description_from_master'] = array('position' => $groupingMaster_slave);
$grouping['products_keywords_from_master'] = array('position' => $groupingMaster_slave);
$grouping['products_image_from_master'] = array('position' => $groupingMaster_slave);
$grouping['products_master_slave_order'] = array('position' => $groupingMaster_slave);
$grouping['products_option_master_price'] = array('position' => $groupingMaster_slave);

$grouping['ms_open_first_slave'] = array('position' => $groupingMaster_slave);
$grouping['ms_show_slave_list'] = array('position' => $groupingMaster_slave);
$grouping['ms_filter_slave_list'] = array('position' => $groupingMaster_slave);
$grouping['ms_filter_slave_list_hide_on_product'] = array('position' => $groupingMaster_slave);
$grouping['ms_load_masters_main_img'] = array('position' => $groupingMaster_slave);
$grouping['load_mains_imgs'] = array('position' => $groupingMaster_slave);
$grouping['ms_load_masters_free_downloads'] = array('position' => $groupingMaster_slave);

$grouping['products_canonical_master'] = array('position' => $groupingMaster_slave);

