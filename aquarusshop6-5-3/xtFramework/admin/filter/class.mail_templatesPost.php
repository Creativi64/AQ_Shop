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
    
    if( (isset($_SESSION['filters_email_manager'][$key])) && ( (float)($_SESSION['filters_email_manager'][$key])>0 ) )  return true;
    else return false;
    
}
function setInt($key){
    
    if( (isset($_SESSION['filters_email_manager'][$key])) && ( (int)($_SESSION['filters_email_manager'][$key])>0 ) )  return true;
    else return false;
    
}

$ad_table = '';
if(FormFilter::setTxt_XT5('filter_email_manager_name', 'email_manager')){

   $where_ar[] = TABLE_MAIL_TEMPLATES.".tpl_type like '%".$_SESSION['filters_email_manager']['filter_email_manager_name']."%' ";
}

if (setInt('filter_email_manager_permission') ||  setInt('filter_email_manager_shop'))
{
    $isBlacklist = _SYSTEM_GROUP_PERMISSIONS == 'blacklist' ? ' NOT ':'';

    if($isBlacklist)
    {
        $ors = array();
        if (setInt('filter_email_manager_permission'))
        {
            $ors[] = " pgroup = 'group_permission_".$_SESSION['filters_email_manager']['filter_email_manager_permission']."' ";
        }
        if (setInt('filter_email_manager_shop'))
        {
            $ors[] = " pgroup = 'shop_".$_SESSION['filters_email_manager']['filter_email_manager_shop']."' ";
        }
        $perm_where = implode(' OR ', $ors);

        $where_ar[] = " ".TABLE_MAIL_TEMPLATES.".tpl_id NOT IN ( select pid from ".TABLE_CONTENT_PERMISSION." where type='email' AND ($perm_where)) ";
    }
    else {
        $ands = array();
        if (setInt('filter_email_manager_permission'))
        {
            $ands[] = " ".TABLE_MAIL_TEMPLATES.".tpl_id  IN (select pid from ".TABLE_CONTENT_PERMISSION." where type='email' AND pgroup = 'group_permission_".$_SESSION['filters_email_manager']['filter_email_manager_permission']."' ) ";
        }
        if (setInt('filter_email_manager_shop'))
        {
            $ands[] = " ".TABLE_MAIL_TEMPLATES.".tpl_id  IN (select pid from ".TABLE_CONTENT_PERMISSION." where type='email' AND pgroup = 'shop_".$_SESSION['filters_email_manager']['filter_email_manager_shop']."' ) ";
        }

        $where_ar[] = implode(' AND ', $ands);
    }
}

($plugin_code = $xtPlugin->PluginCode('class.mail_templatesPost.php:bottom')) ? eval($plugin_code) : false;
?>