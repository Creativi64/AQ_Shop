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
    
    if( (isset($_SESSION['filters_content'][$key])) && ( (float)($_SESSION['filters_content'][$key])>0 ) )  return true;
    else return false;
    
}
function setInt($key){
    
    if( (isset($_SESSION['filters_content'][$key])) && ( (int)($_SESSION['filters_content'][$key])>0 ) )  return true;
    else return false;
    
}

/*
if(isset($_SESSION['filters_content']["filter_content_status"]))
{
    $_SESSION['filters_content']["filter_content_status"] = 1;
}
else {
    $_SESSION['filters_content']["filter_content_status"] = 0;
}
$where_ar[] = " content_status = ".$_SESSION['filters_content']['filter_content_status'];
*/
if(isset($_SESSION['filters_content']['filter_content_status']) && (int)$_SESSION['filters_content']['filter_content_status'] === 1 )
    $where_ar[] = " content_status = 1";
else if(isset($_SESSION['filters_content']['filter_content_status']) && (int)$_SESSION['filters_content']['filter_content_status'] === 0  && $_SESSION['filters_content']['filter_content_status']!="" )
    $where_ar[] = " content_status = 0";


$ad_table = '';
if(FormFilter::setTxt_XT5('filter_content_name', 'content')){

   $ad_table .= ", ".TABLE_CONTENT_ELEMENTS;
   $where_ar[] = TABLE_CONTENT_ELEMENTS.".content_id = ".TABLE_CONTENT.".content_id";
   $where_ar[] = " (content_title like '%".$_SESSION['filters_content']['filter_content_name']."%' OR content_heading like '%".$_SESSION['filters_content']['filter_content_name']."%') AND language_code = '".$language->content_language."' ";
}

if (setInt('filter_content_permission') ||  setInt('filter_content_shop'))
{
    $isBlacklist = _SYSTEM_GROUP_PERMISSIONS == 'blacklist' ? ' NOT ':'';

    if($isBlacklist)
    {
        $ors = array();
        if (setInt('filter_content_permission'))
        {
            $ors[] = " pgroup = 'group_permission_".$_SESSION['filters_content']['filter_content_permission']."' ";
        }
        if (setInt('filter_content_shop'))
        {
            $ors[] = " pgroup = 'shop_".$_SESSION['filters_content']['filter_content_shop']."' ";
        }
        $perm_where = implode(' OR ', $ors);

        $where_ar[] = " ".TABLE_CONTENT.".content_id NOT IN ( select pid from ".TABLE_CONTENT_PERMISSION." where type='content' AND ($perm_where)) ";
    }
    else {
        $ands = array();
        if (setInt('filter_content_permission'))
        {
            $ands[] = " ".TABLE_CONTENT.".content_id  IN (select pid from ".TABLE_CONTENT_PERMISSION." where type='content' AND pgroup = 'group_permission_".$_SESSION['filters_content']['filter_content_permission']."' ) ";
        }
        if (setInt('filter_content_shop'))
        {
            $ands[] = " ".TABLE_CONTENT.".content_id  IN (select pid from ".TABLE_CONTENT_PERMISSION." where type='content' AND pgroup = 'shop_".$_SESSION['filters_content']['filter_content_shop']."' ) ";
        }

        $where_ar[] = implode(' AND ', $ands);
    }
}

($plugin_code = $xtPlugin->PluginCode('class.contentPost.php:bottom')) ? eval($plugin_code) : false;
?>