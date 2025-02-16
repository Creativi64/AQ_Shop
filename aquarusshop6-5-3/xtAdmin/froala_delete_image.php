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

include_once '../xtFramework/admin/main.php';

require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'admin/classes/Xt_Froala_Image.php';

global $xtc_acl;

if (!$xtc_acl->isLoggedIn()) {
    die('login required');
}

if (CSRF_PROTECTION!='false')
{
    $xtc_acl->checkAdminKey();
}

include (_SRV_WEBROOT_ADMIN.'page_includes.php');

try
{
    $useUploadSubFolders = true;
    $uploadFolder = 'media/u/images/';

    if(file_exists(_SRV_WEBROOT.'conf/config_froala.php'))
    {
        include_once _SRV_WEBROOT.'conf/config_froala.php';

        if(isset($froala_useUploadSubFolders))
        {
            $useUploadSubFolders = $froala_useUploadSubFolders;
        }

        if(isset($froala_imagesUploadFolder))
        {
            $uploadFolder = $froala_imagesUploadFolder;
        }
    }

    if($useUploadSubFolders && !empty($_POST['xtClass']))
    {
        $uploadFolder .= $_POST['xtClass'].'/';
    }

    $src = '/'.$uploadFolder.$_REQUEST["data-name"];
    $response = false;
    if($src == $_REQUEST["src"])
        $response = FroalaEditor_Image::delete($src);
    echo stripslashes(json_encode($response));
}
catch (Exception $e) {
    echo '<pre>';
    print_r($e);
    echo '</pre>';
    error_log('froala_upload_image: '.$e->getMessage());
    http_response_code(503);
}
die();
