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
 
 if( FormFilter::setTxt_XT5('filter_date_from', 'xt_reviews')){
           
           $where_ar[] = " review_date >= '". FormFilter::date_trans($_SESSION['filters_xt_reviews']['filter_date_from'])."'"; 
       }
       
   if( FormFilter::setTxt_XT5('filter_date_to', 'xt_reviews')) {
           
           $where_ar[] = " review_date <= '". FormFilter::date_trans($_SESSION['filters_xt_reviews']['filter_date_to'])."'"; 
   }
       
   if(FormFilter::setTxt_XT5('filter_product', 'xt_reviews')) {
       
           $ad_table = ", ".TABLE_PRODUCTS_DESCRIPTION;
           $where_ar[] = TABLE_PRODUCTS_DESCRIPTION.".products_id = ".TABLE_PRODUCTS_REVIEWS.".products_id"; 
           $where_ar[] = " products_name like '%".$_SESSION['filters_xt_reviews']['filter_product']."%'"; 
           
    }
       
   if(isset($_SESSION['filters_xt_reviews']['filter_active']) && $_SESSION['filters_xt_reviews']['filter_active'] ){
       
          $where_ar[] = " review_status = 1"; 
   }
   
   if( FormFilter::setTxt_XT5('filter_title', 'xt_reviews')) {
       
        $where_ar[] = " review_title like '%".$_SESSION['filters_xt_reviews']['filter_title']."%'";
        
   }
   
   
    if($_SESSION['filters_xt_reviews']['filter_language_id'] != ""){
        
        $l = explode(",", $_SESSION['filters_xt_reviews']['filter_language_id']);
        foreach($l as $l_l){
            $ls[] = "'".$l_l."'";
        }
        
        $where_ar[] = " language_code in (".implode(",",$ls).")";
        
    }
    
     if($_SESSION['filters_xt_reviews']['filter_rating_id'] != ""){
        
        $where_ar[] = " review_rating in (".$_SESSION['filters_xt_reviews']['filter_rating_id'].")";
        
    }
   
?>