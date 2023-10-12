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

if( $_SESSION['filters_redirect_deleted']['filter_language'] != ""){
    $where_ar[] =" language_code = '".$_SESSION['filters_redirect_deleted']['filter_language']."' ";
}

if ($_SESSION['filters_redirect_deleted']['filter_keyword'] != ""){
     $where_ar[] = " (url_text LIKE '%".$_SESSION['filters_redirect_deleted']['filter_keyword']."%' or url_text_redirect LIKE '%".$_SESSION['filters_redirect_deleted']['filter_keyword']."%') ";
}

if( $_SESSION['filters_redirect_deleted']['filter_store'] != ""){
    $where_ar[] =" store_id in (".$_SESSION['filters_redirect_deleted']['filter_store'].")";
}
     
if ($_SESSION['filters_redirect_deleted']['filter_link_type'] != "") {
    $where_ar[] = " link_type ='".$_SESSION['filters_redirect_deleted']['filter_link_type']."'";
 }
           
?>