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
  
global $db, $xtPlugin;
 
require_once(_SRV_WEBROOT."xtFramework/admin/filter/class.formFilter.php");

function setFloat($key){
    
    if( (isset($_SESSION['filters_xt_master_slave'][$key])) && ( (float)($_SESSION['filters_product'][$key])>0 ) )  return true;
    else return false;
    
}
function setInt($key){
    
    if( (isset($_SESSION['filters_xt_master_slave'][$key])) && ( (int)($_SESSION['filters_xt_master_slave'][$key])>0 ) )  return true;
    else return false;
    
}
       


if (setInt('filter_attributes_parent'))
{
    $where_ar[] = " attributes_parent = ".(int)$_SESSION['filters_xt_master_slave']['filter_attributes_parent'];
}

($plugin_code = $xtPlugin->PluginCode('class.xt_master_slavePost.php:bottom')) ? eval($plugin_code) : false;
