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

include '../xtFramework/admin/main.php';

global $is_pro_version;

    if (!$xtc_acl->isLoggedIn()) {
        die('login required');
    }
    
$keys =['company','key','license'];
if($is_pro_version === true) $keys[] = 'updatesuntil';
$lic_parms = $store_handler->getLicenseFileInfo($keys);

    $s = array();
    foreach($lic_parms as $k=>$lic)
    {
       array_push($s,array("text"=>$k,"value"=>$lic));
    }


    $tpl_data = array('lic_parms' => $s);
    $template = new Template();
    $template->_setTemplate('__xtAdmin');
    $tpl = 'license.html';

    $page_data = $template->getTemplate('smarty', '/'._SRV_WEB_CORE.'pages/'.$tpl, $tpl_data);
    echo $page_data;   