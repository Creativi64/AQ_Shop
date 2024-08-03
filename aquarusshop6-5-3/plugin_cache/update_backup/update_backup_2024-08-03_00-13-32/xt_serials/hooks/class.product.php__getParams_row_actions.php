<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

/** @var $products_model string*/

$rowActions[] = array('iconCls' => 'products_serials', 'qtipIndex' => 'qtip1', 'tooltip' => constant('TEXT_PRODUCTS_SERIALS'));
if ($this->url_data['edit_id'])
    $js = "var edit_id = ".$this->url_data['edit_id']."; var edit_name = '".htmlentities($products_model)."';";
else
    $js = "var edit_id = record.id; var edit_name=record.get('products_model');";
$js .= "addTab('adminHandler.php?load_section=product_serials&plugin=xt_serials&pg=overview&products_id='+edit_id,'".constant('TEXT_PRODUCTS_SERIALS')." ('+edit_name+')', 'product_serials'+edit_id)";

$rowActionsFunctions['products_serials'] = $js;