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
  
global $db, $xtPlugin, $language;
 
require_once(_SRV_WEBROOT."xtFramework/admin/filter/class.formFilter.php");

function setFloat($key){
    
    if( (isset($_SESSION['filters_xt_orders_invoices_templates'][$key])) && ( (float)($_SESSION['filters_xt_orders_invoices_templates'][$key])>0 ) )  return true;
    else return false;

}
function setInt($key){

    if( (isset($_SESSION['filters_xt_orders_invoices_templates'][$key])) && ( (int)($_SESSION['filters_xt_orders_invoices_templates'][$key])>0 ) )  return true;
    else return false;

}

if(FormFilter::setTxt_XT5('filter_xt_orders_invoices_templates_name', 'xt_orders_invoices_templates')){

    $where_ar[] = TABLE_PDF_MANAGER.".template_name like '%".$_SESSION['filters_xt_orders_invoices_templates']['filter_xt_orders_invoices_templates_name']."%' ";
}

if (setInt('filter_xt_orders_invoices_templates_permission') ||  setInt('filter_xt_orders_invoices_templates_shop'))
{
    $isBlacklist = _SYSTEM_GROUP_PERMISSIONS == 'blacklist' ? ' NOT ':'';

    if($isBlacklist)
    {
        $ors = array();
        if (setInt('filter_xt_orders_invoices_templates_permission'))
        {
            $ors[] = " pgroup = 'group_permission_".$_SESSION['filters_xt_orders_invoices_templates']['filter_xt_orders_invoices_templates_permission']."' ";
        }
        if (setInt('filter_xt_orders_invoices_templates_shop'))
        {
            $ors[] = " pgroup = 'shop_".$_SESSION['filters_xt_orders_invoices_templates']['filter_xt_orders_invoices_templates_shop']."' ";
        }
        $perm_where = implode(' OR ', $ors);

        $where_ar[] = " ".TABLE_PDF_MANAGER.".template_id NOT IN ( select pid from ".TABLE_CONTENT_PERMISSION." where type='invoice' AND ($perm_where)) ";
    }
    else {
        $ands = array();
        if (setInt('filter_xt_orders_invoices_templates_permission'))
        {
            $ands[] = " ".TABLE_PDF_MANAGER.".template_id  IN (select pid from ".TABLE_CONTENT_PERMISSION." where type='invoice' AND pgroup = 'group_permission_".$_SESSION['filters_xt_orders_invoices_templates']['filter_xt_orders_invoices_templates_permission']."' ) ";
        }
        if (setInt('filter_xt_orders_invoices_templates_shop'))
        {
            $ands[] = " ".TABLE_PDF_MANAGER.".template_id  IN (select pid from ".TABLE_CONTENT_PERMISSION." where type='invoice' AND pgroup = 'shop_".$_SESSION['filters_xt_orders_invoices_templates']['filter_xt_orders_invoices_templates_shop']."' ) ";
        }

        $where_ar[] = implode(' AND ', $ands);
    }
}

($plugin_code = $xtPlugin->PluginCode('class.pdf_managerPost.php:bottom')) ? eval($plugin_code) : false;
?>