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

require_once _SRV_WEBROOT . 'conf/database.php';

global $db;
$sql = "SELECT `version` FROM ".TABLE_PLUGIN_PRODUCTS." WHERE `code`='xt_it_recht_kanzlei'";
$itrk_module_version = $db->GetOne($sql);

define('_ITRK_MODULE_VERSION', $itrk_module_version);
if (!defined('_ITRK_ALLOWED_HOSTS'))
    define('_ITRK_ALLOWED_HOSTS', ['www.it-recht-kanzlei.de']);


