<?php
/*
 #########################################################################
 #                       xt:Commerce Shopsoftware
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # Copyright 2021 xt:Commerce GmbH All Rights Reserved.
 # This file may not be redistributed in whole or significant part.
 # Content of this file is Protected By International Copyright Laws.
 #
 # ~~~~~~ xt:Commerce Shopsoftware IS NOT FREE SOFTWARE ~~~~~~~
 #
 # https://www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # @copyright xt:Commerce GmbH, www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # xt:Commerce GmbH, Maximilianstrasse 9, 6020 Innsbruck
 #
 # office@xt-commerce.com
 #
 #########################################################################
 */

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $xtPlugin, $xtLink;

/** @var $params array */
/** @var $manufacturer manufacturer */

if($params['order_by'])
	$order_by = $params['order_by'];
else 	
	$order_by = 'm.manufacturers_name';

$type = 'default';
$position = 'box';

($plugin_code = $xtPlugin->PluginCode('box_manufacturer:getManufacturerList')) ? eval($plugin_code) : false;

$manufacturers_list = $manufacturer->getManufacturerList($type,$position, $order_by);


if($manufacturers_list){
	$tpl_data = array('_manufacturers'=> $manufacturers_list);
	if(isset($xtPlugin->active_modules['xt_manufacturer_listing']))
    {
        $tpl_data['link'] = $xtLink->_link(array('page' =>'xt_manufacturer_listing'));
    }
	$show_box = true;
}else{
	$show_box = false;
}
