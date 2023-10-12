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

global $_content, $store_handler, $customers_status;


$currentData = $formattedData = array();
$show_box = false;
$show_box_for_store = false;
$show_box_for_group = false;
// get data

if (!isset($params['sliderid']) || empty($params['sliderid']))
{
    throw new Exception("Missing parameter 'sliderid' for box '{$params['name']}'.");
}
if (isset($params['shopids']))
{
    $shopIds = explode(',',$params['shopids']);
    if(in_array($store_handler->shop_id, $shopIds))
    {
        $show_box_for_store = true;
    }
} else {
    $show_box_for_store = true;
}

if (isset($params['groupids']))
{
    $groupIds = explode(',',$params['groupids']);
    if(in_array($customers_status->customers_status_id, $groupIds))
    {
        $show_box_for_group = true;
    }
} else {
    $show_box_for_group = true;
}

if($show_box_for_store && $show_box_for_group)
{
$slider = new slider();
$data = $slider->_getSlider($params['sliderid']);

    if (is_array($data))
    {
        $show_box = true;
    }

$tpl_data = array(
	'params'=>$params,
    'data'   => $data
    );
}