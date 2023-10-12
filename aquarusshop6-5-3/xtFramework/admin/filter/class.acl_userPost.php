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

if(FormFilter::setTxt_XT5('filter_name', 'acl_user')) {
           
           $where_ar[] = " ( firstname like '%".$_SESSION['filters_acl_user']['filter_name']."%'
                             OR
                             lastname like '%".$_SESSION['filters_acl_user']['filter_name']."%' 
                            )";
 }
 
if(FormFilter::setTxt_XT5('filter_username', 'acl_user')) {
    
   $where_ar[] = " handle like '%".$_SESSION['filters_acl_user']['filter_username']."%'";
   
}

if(isset($_SESSION['filters_acl_user']['filter_userstatus']) && $_SESSION['filters_acl_user']['filter_userstatus']){
         
    $where_ar[] = " status = 1 "; 
}

if($_SESSION['filters_acl_user']['filter_usergroup_id'] != ""){
    
    $where_ar[] = " group_id in (".$_SESSION['filters_acl_user']['filter_usergroup_id'].")";
    
}


       
?>