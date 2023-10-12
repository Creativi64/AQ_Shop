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

class checkout extends checkout_base {

    function __construct()
    {
        global $page_data, $xtLink, $info;

        if(USER_POSITION == 'store' && $page_data['page'] == 'checkout' && $page_data['page_action'] == 'shipping')
        {
            $info->_addInfoSession('Klarna Checkout not found', 'error');
            $xtLink->_redirect($xtLink->_link(array('page'=>'cart')));
        }

        parent::__construct();
    }

	function process_pageAction_shipping()
    {
        global $page_data, $xtLink;

        if(USER_POSITION == 'store' && $page_data['page'] == 'checkout' && $page_data['page_action'] == 'shipping')
        {
            $xtLink->_redirect($xtLink->_link(array('page'=>'cart')));
        }

        parent::process_pageAction_shipping();
    }
}