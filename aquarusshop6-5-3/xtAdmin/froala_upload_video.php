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
    $defaultUploadOptions = array(
        'fieldname' => 'file',
        'validation' => array(
            'allowedExts' => array('mp4', 'webm', 'ogg'),
            'allowedMimeTypes' => array('video/mp4','video/webm', 'video/ogg')
        ),
        'resize' => NULL
    );
    $useUploadSubFolders = true;
    $uploadFolder = 'media/u/videos/';

    if(file_exists(_SRV_WEBROOT.'conf/config_froala.php'))
    {
        include_once _SRV_WEBROOT.'conf/config_froala.php';

        if(isset($froala_videoUploadOptions) && is_array($froala_videoUploadOptions))
        {
            $defaultUploadOptions = array_merge($defaultUploadOptions, $froala_videoUploadOptions);
        }

        if(isset($froala_useUploadSubFolders))
        {
            $useUploadSubFolders = $froala_useUploadSubFolders;
        }

        if(isset($froala_videosUploadFolder))
        {
            $uploadFolder = $froala_videosUploadFolder;
        }
    }


    if(!is_dir(_SRV_WEBROOT. $uploadFolder))
    {
        $created = mkdir(_SRV_WEBROOT. $uploadFolder, 0755, true);
        if(!$created)
        {
            echo ('1) could not create folder '._SRV_WEBROOT. $uploadFolder);
            error_log('1) froala_upload_video could not create folder '._SRV_WEBROOT. $uploadFolder);
            http_response_code(404);
            die();
        }
    }

    if($useUploadSubFolders && !empty($_POST['xtClass']))
    {
        $uploadFolder .= $_POST['xtClass'].'/';
    }

    if(!is_dir(_SRV_WEBROOT. $uploadFolder))
    {
        $created = mkdir(_SRV_WEBROOT. $uploadFolder, 0755, true);
        if(!$created)
        {
            echo ('2) could not create folder '._SRV_WEBROOT. $uploadFolder);
            error_log('2) froala_upload_video could not create folder '._SRV_WEBROOT. $uploadFolder);
            http_response_code(404);
            die();
        }
    }
    $uploadFolder = '/'.$uploadFolder;

    $response = FroalaEditor_Video::upload($uploadFolder, $defaultUploadOptions);
    echo stripslashes(json_encode($response));
}
catch (Exception $e) {
    echo '<pre>';
    print_r($e);
    echo '</pre>';
    error_log('froala_upload_video: '.$e->getMessage());
    http_response_code(503);
}
die();
