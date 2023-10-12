<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT. _SRV_WEB_PLUGINS.'xt_countryprices/classes/constants.php';

$rowActions[] = array('iconCls' => 'products_country_price', 'qtipIndex' => 'qtip1', 'tooltip' => __define('TEXT_PRODUCTS_COUNTRY_PRICE'));
if ($this->url_data['edit_id'])
{
    $js = "var edit_id = " . $this->url_data['edit_id'] . "; var edit_name = '';";
}
else
{
    $js = "var edit_id = record.id; var edit_name=record.get('products_model');";
}
$js .= "addTab('adminHandler.php?load_section=xt_countryprices&plugin=xt_countryprices&pg=overview&products_id='+edit_id,'" . __define('TEXT_PRODUCTS_COUNTRY_PRICE') . " ' + edit_id , 'products_country_price'+edit_id)";

$rowActionsFunctions['products_country_price'] = $js;
