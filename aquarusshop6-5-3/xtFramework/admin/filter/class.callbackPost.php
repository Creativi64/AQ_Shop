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

require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK."admin/filter/class.formFilter.php";

if (FormFilter::setTxt_XT5('filter_date_from', 'LogHandler'))
{
    $where_ar[] = " created >= '" . FormFilter::date_trans($_SESSION['filters_callback']['filter_date_from']) . "'";
}

if (FormFilter::setTxt_XT5('filter_date_to', 'LogHandler'))
{
    $where_ar[] = " created <= '" . FormFilter::date_trans($_SESSION['filters_callback']['filter_date_to']) . "'";
}


if (isset($_SESSION['filters_callback']['filter_orders_id']) && $_SESSION['filters_callback']['filter_orders_id']!=='')
{
    $where_ar[] = " orders_id = '".$_SESSION['filters_callback']['filter_orders_id']. "'";
}

if (isset($_SESSION['filters_callback']['filter_transaction_id']) && $_SESSION['filters_callback']['filter_transaction_id']!=='')
{
    $where_ar[] = " transaction_id LIKE '%".$_SESSION['filters_callback']['filter_transaction_id']."%'";
}

if (isset($_SESSION['filters_callback']['filter_module']) && $_SESSION['filters_callback']['filter_module']!=='')
{
    $where_ar[] = " module = '".$_SESSION['filters_callback']['filter_module']. "'";
}

if (isset($_SESSION['filters_callback']['filter_class']) && $_SESSION['filters_callback']['filter_class']!=='')
{
    $where_ar[] = " class LIKE '%".$_SESSION['filters_callback']['filter_class']."%'";
}

if (isset($_SESSION['filters_callback']['filter_error_msg']) && $_SESSION['filters_callback']['filter_error_msg']!=='')
{
    $where_ar[] = " ( error_msg LIKE '%".$_SESSION['filters_callback']['filter_error_msg']."%' OR error_data LIKE '%".$_SESSION['filters_callback']['filter_error_msg']."%' )";
}