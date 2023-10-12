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

$root_dir = dirname(__FILE__);
$root_dir= str_ireplace("xtadmin","",$root_dir);
include ($root_dir.'/xtFramework/admin/main.php');

include $root_dir.'/xtFramework/library/captcha/php-captcha.inc.php';

$aFonts = array($root_dir.'/media/fonts/VeraBd.ttf', $root_dir.'/media/fonts/VeraIt.ttf', $root_dir.'/media/fonts/Vera.ttf');
$oVisualCaptcha = new PhpCaptcha($aFonts, 200, 60);
$oVisualCaptcha->Create();
?>