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

if ($_SESSION['filters_redirect_404']['filter_keyword'] != ""){
     $where_ar[] = " (url_text LIKE '%".$_SESSION['filters_redirect_404']['filter_keyword']."%' or url_text_redirect LIKE '%".$_SESSION['filters_redirect_404']['filter_keyword']."%') ";
}

if( $_SESSION['filters_redirect_404']['filter_store'] != ""){
    $where_ar[] =" store_id in (".$_SESSION['filters_redirect_404']['filter_store'].")";
}

if( $_SESSION['filters_redirect_404']['filter_language'] != ""){
    $where_ar[] =" language_code = '".$_SESSION['filters_redirect_404']['filter_language']."' ";
}
            
if( FormFilter::setTxt_XT5('filter_date_from', 'redirect_404')){

    $where_ar[] = " last_access >= '". FormFilter::date_trans($_SESSION['filters_redirect_404']['filter_date_from'])."'";
}

if( FormFilter::setTxt_XT5('filter_date_to', 'redirect_404'))
{
    $date = DateTime::createFromFormat('m/d/y', $_SESSION['filters_redirect_404']['filter_date_to']);
    if($date)
    {
        $date->add(new DateInterval('P1D'));
        $dates_s = $date->format('Y-m-d');
        $where_ar[] = " last_access <= '". $dates_s."'";
    }
}

?>