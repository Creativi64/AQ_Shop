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

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_bank_details/classes/constants.php';

global $db;

if(!defined('DB_STORAGE_ENGINE'))
{
    $sel_engine = 'innodb';
    $sql_version = $db->GetOne("SELECT VERSION() AS Version");
    if(version_compare($sql_version, '5.6') == -1)
    {
        $sel_engine = 'myisam';
    }
    define('DB_STORAGE_ENGINE', $sel_engine);
}

// ############################### West Navi
/*
$db->Execute("
INSERT INTO `".TABLE_ADMIN_NAVIGATION."` (`text`,`icon`,`url_i`,`url_d`,`sortorder`,`parent`,`type`,`navtype`,`cls`,`handler`,`iconCls`)
VALUES
('xt_bank_details','images/icons/plugin.png','&plugin=xt_bank_details','adminHandler.php',9000,'shop','I','W',NULL,NULL,NULL); ");
*/


// ############################### Table bank_details
$db->Execute("
CREATE TABLE IF NOT EXISTS ".TABLE_BAD_BANK_DETAILS." (
    ".COL_BAD_BANK_DETAILS_ID_PK." INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    ".COL_BAD_BANK_IDENTIFIER_CODE." VARCHAR(256)  NOT NULL,
    ".COL_BAD_INTERNATIONAL_BANK_ACCOUNT_NUMBER." VARCHAR(256)  NOT NULL,
    ".COL_BAD_ACCOUNT_NAME." VARCHAR(256)  NOT NULL,
    ".COL_BAD_REFERENCE_NUMBER." VARCHAR(256)  NOT NULL,
    ".COL_BAD_DUE_DATE." DATE DEFAULT NULL,
    ".COL_BAD_TECH_ISSUER." VARCHAR(256)  NULL, /* ie core , xt_paypal_plus ... */
    ".COL_BAD_ADD_DATA." TEXT NULL,

    PRIMARY KEY(".COL_BAD_BANK_DETAILS_ID_PK.")
    ) ENGINE=".DB_STORAGE_ENGINE."  DEFAULT CHARSET=utf8;
");


// ############################### Table order_bank_details
$db->Execute("
CREATE TABLE IF NOT EXISTS ".TABLE_BAD_ORDER_BANK_DETAILS." (
    ".COL_BAD_ORDERS_ID." INTEGER(11)  NOT NULL,
    ".COL_BAD_BANK_DETAILS_ID." INTEGER(11)  NOT NULL,

    PRIMARY KEY(".COL_BAD_ORDERS_ID.",".COL_BAD_BANK_DETAILS_ID.")
    ) ENGINE=".DB_STORAGE_ENGINE."  DEFAULT CHARSET=utf8;
");


