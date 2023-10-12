<?php
/*
 #########################################################################
 #                       xt:Commerce Shopsoftware
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # Copyright 2007-2018 xt:Commerce International Ltd. All Rights Reserved.
 # This file may not be redistributed in whole or significant part.
 # Content of this file is Protected By International Copyright Laws.
 #
 # ~~~~~~ xt:Commerce Shopsoftware IS NOT FREE SOFTWARE ~~~~~~~
 #
 # http://www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # @copyright xt:Commerce International Ltd., www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # xt:Commerce International Ltd., Kafkasou 9, Aglantzia, CY-2112 Nicosia
 #
 # office@xt-commerce.com
 #
 #########################################################################
 */

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . 'conf/database.php';

define ('TABLE_BAD_BANK_DETAILS', DB_PREFIX . '_bank_details');
define ('COL_BAD_BANK_DETAILS_ID_PK', 'bad_id');
define ('COL_BAD_BANK_IDENTIFIER_CODE', 'bad_bank_identifier_code');
define ('COL_BAD_INTERNATIONAL_BANK_ACCOUNT_NUMBER', 'bad_international_bank_account_number');
define ('COL_BAD_ACCOUNT_NAME', 'bad_account_name');
define ('COL_BAD_REFERENCE_NUMBER', 'bad_reference_number');
define ('COL_BAD_DUE_DATE', 'bad_due_date');
define ('COL_BAD_TECH_ISSUER', 'bad_tech_issuer');
define ('COL_BAD_ADD_DATA', 'bad_add_data');

define ('TABLE_BAD_ORDER_BANK_DETAILS', DB_PREFIX . '_order_bank_details');
define ('COL_BAD_ORDERS_ID', 'bad_orders_id');
define ('COL_BAD_BANK_DETAILS_ID', 'bad_bank_details_id');

