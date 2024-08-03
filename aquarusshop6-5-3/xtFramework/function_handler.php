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

global $htmlPurifier;
$config = HTMLPurifier_Config::createDefault();
$config->set('HTML.Allowed', '');
$config->set('Cache.SerializerPath', _SRV_WEBROOT.'templates_c');
$htmlPurifier = new HTMLPurifier($config);

require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'functions/debug.php';
require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'functions/show_debug.php';
require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'functions/is_data.inc.php';
require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'functions/build_define.inc.php';
require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'functions/date_short.inc.php';
require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'functions/merge_arrays.inc.php';
require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'functions/getPath.inc.php';
require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'functions/filter_text.inc.php';
require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'functions/strip_slashes.inc.php';
require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'functions/getSingleValue.inc.php';
require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'functions/get_array_with_keys.php';
require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'functions/evaluate_smarty.php';
require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'functions/session_vars.php';
require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'functions/checkHTTPS.php';
require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'functions/array_value.inc.php';

if(USER_POSITION=='admin'){
require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'functions/get_table_fields.inc.php';
require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'functions/filter_text.inc.php';
require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'functions/empty_dataset.inc.php';
}
