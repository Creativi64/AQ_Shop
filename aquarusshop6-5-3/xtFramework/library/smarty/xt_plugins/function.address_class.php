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

function smarty_function_address_class($params, &$smarty) {

	if(empty($params['address']) && empty($params['class']))
		return '';

	$class = false;
	if($params['class'])
    {
        $class = $params['type'];
    }
	else if($params['address'] && is_array($params['address']) && $params['address']['address_class'])
    {
        $class = $params['address']['address_class'];
    }

    switch($class)
    {
        case 'default':
            return TEXT_DEFAULT_ADDRESS;
        case 'shipping':
            return TEXT_SHIPPING_ADDRESS;
        case 'payment':
            return TEXT_PAYMENT_ADDRESS;
        default:
            return '';
    }
}
