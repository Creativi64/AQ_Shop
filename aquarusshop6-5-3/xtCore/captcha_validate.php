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

defined('_VALID_CALL') or die('Direct Access is not allowed.');

switch (_STORE_CAPTCHA)
{
    case 'Standard':
        include _SRV_WEBROOT.'/xtFramework/library/captcha/php-captcha.inc.php';
        if (PhpCaptcha::Validate($_POST['captcha'])) {

        } else {
            $captcha_error = true;
        }
        break;

    case 'ReCaptcha':
        $return = false;
        ($plugin_code = $xtPlugin->PluginCode('generic_recaptcha_validator')) ? eval($plugin_code) : false;
        if($return==='ERROR_CAPTCHA_INVALID')  {
            $captcha_error = true;
        }
        break;
    default:;
}