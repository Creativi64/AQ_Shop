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

/**
 * @return cart
 */
function sessionCart()
{
    if (!is_object($_SESSION['cart']) || !is_a($_SESSION['cart'], 'cart'))
    {
        $_SESSION['cart'] = new cart();
    }
    return $_SESSION['cart'];
}

/**
 * @return customer
 */
function sessionCustomer()
{
    if (!is_object($_SESSION['customer']) || !is_a($_SESSION['customer'], 'customer'))
    {
        $_SESSION['customer'] = new customer();
    }
    return $_SESSION['customer'];
}