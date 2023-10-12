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

// SHIPPER
define ('TABLE_SHIPPER',  DB_PREFIX . '_shipper');
define ('COL_SHIPPER_ID_PK',  'id');
define ('COL_SHIPPER_CODE',  'shipper_code');
define ('COL_SHIPPER_NAME',  'shipper_name');
define ('COL_SHIPPER_TRACKING_URL',  'shipper_tracking_url');
define ('COL_SHIPPER_API_ENABLED',  'shipper_api_enabled');
define ('COL_SHIPPER_ENABLED',  'shipper_enabled');

// tracking
define ('TABLE_TRACKING',  DB_PREFIX . '_tracking');
define ('COL_TRACKING_ID_PK',  'id');
define ('COL_TRACKING_CODE',  'tracking_code');
define ('COL_CARRIER_TRACKING_CODE',  'carrier_tracking_code');
define ('COL_TRACKING_ORDER_ID',  'tracking_order_id');
define ('COL_TRACKING_STATUS_ID',  'tracking_status_id');
define ('COL_TRACKING_SHIPPER_ID',  'tracking_shipper_id');
define ('COL_TRACKING_ADDED',  'tracking_added');

define ('VIEW_TRACKING',  DB_PREFIX . '_v_tracking');
// trackin status
define ('TABLE_TRACKING_STATUS',  DB_PREFIX . '_tracking_status');
define ('COL_TRACKING_STATUS_ID_PK',  'id');
//define ('COL_TRACKING_SHIPPER_ID',  'tracking_shipper_id');
define ('COL_TRACKING_STATUS_CODE',  'tracking_status_code');
define ('COL_TRACKING_STATUS_DESC_SHORT',  'tracking_status_desc_short');
define ('COL_TRACKING_STATUS_DESC_LONG',  'tracking_status_desc_long');

// hermes orders
define ('TABLE_HERMES_ORDER',  DB_PREFIX . '_hermes_order');
define ('COL_HERMES_ID_PK',  'id');
define ('COL_HERMES_ORDER_NO',  'hermes_order_no'); // entspricht tracking code
define ('COL_HERMES_XT_ORDER_ID',  'xt_orders_id'); //
define ('COL_HERMES_SHIPPING_ID',  'hermes_shipping_id');
define ('COL_HERMES_STATUS',  'hermes_status');
define ('COL_HERMES_PARCEL_CLASS',  'parcel_class');
define ('COL_HERMES_AMOUNT_CASH_ON_DELIVERY',  'hermes_amount_cash_on_delivery');
define ('COL_HERMES_BULK_GOOD',  'hermes_bulk_good');
define ('COL_HERMES_COLLECT_DATE',  'collect_date');
define ('COL_HERMES_TS_CREATED',  'hermes_ts_created');

// hermes settings
define ('TABLE_HERMES_SETTINGS',  DB_PREFIX . '_hermes_settings');
define ('KEY_HERMES_USER',  'hermes_user');
define ('KEY_HERMES_PWD',  'hermes_pwd');
define ('KEY_HERMES_SANDBOX',  'hermes_sandbox');
define ('KEY_HERMES_LAST_ORDER_BEFORE_v6',  'last_order_before_v6');

// hermes collect request
define ('TABLE_HERMES_COLLECT',  DB_PREFIX . '_hermes_collect');
define ('COL_HERMES_COLLECT_ID_PK',  'id');
define ('COL_HERMES_COLLECT_NO',  'collect_request_no');


// shipcloud orders
define ('TABLE_SHIPCLOUD_LABELS',  DB_PREFIX . '_shipcloud_labels');
define ('COL_SHIPCLOUD_LABEL_ID_PK',  'sc_xt_id');
define ('COL_SHIPCLOUD_XT_ORDER_ID',  'xt_orders_id'); //

/*
define ('COL_SHIPCLOUD_ORDER_NO',  'shipcloud_order_no'); // entspricht tracking code
define ('COL_SHIPCLOUD_SHIPPING_ID',  'shipcloud_shipping_id');
define ('COL_SHIPCLOUD_STATUS',  'shipcloud_status');
define ('COL_SHIPCLOUD_PARCEL_CLASS',  'parcel_class');
define ('COL_SHIPCLOUD_AMOUNT_CASH_ON_DELIVERY',  'shipcloud_amount_cash_on_delivery');
define ('COL_SHIPCLOUD_BULK_GOOD',  'shipcloud_bulk_good');
define ('COL_SHIPCLOUD_COLLECT_DATE',  'collect_date');
define ('COL_SHIPCLOUD_TS_CREATED',  'shipcloud_ts_created');
*/

define ('COL_SHIPCLOUD_LABEL_CARRIER',  'sc_carrier');
define ('COL_SHIPCLOUD_LABEL_CARRIER_TRACKING_NO',  'sc_carrier_tracking_no');
define ('COL_SHIPCLOUD_LABEL_CREATED_AT',  'sc_created_at');
define ('COL_SHIPCLOUD_LABEL_FROM',  'sc_from_address'); // address
    /*
        [COL_SHIPCLOUD_LABEL_COMPANY] => XT:COMMERCE GMBH
        [COL_SHIPCLOUD_LABEL_FIRST_NAME] => MAX
        [COL_SHIPCLOUD_LABEL_LAST_NAME] => MUSTERMANN
        [COL_SHIPCLOUD_LABEL_CARE_OF] =>
        [COL_SHIPCLOUD_LABEL_STREET] => TEMPELHOFER UFER
        [COL_SHIPCLOUD_LABEL_STREET_NO] => 11
        [COL_SHIPCLOUD_LABEL_ZIP_CODE] => 10963
        [COL_SHIPCLOUD_LABEL_CITY] => BERLIN
        [COL_SHIPCLOUD_LABEL_STATE] => BERLIN
        [COL_SHIPCLOUD_LABEL_COUNTRY] => DE
        [COL_SHIPCLOUD_LABEL_PHONE] => 030-123456789
        [COL_SHIPCLOUD_LABEL_ID] => B7DE92B4-A43A-497F-B1A7-EA24B4C9A5CF
    */
define ('COL_SHIPCLOUD_LABEL_ID',  'sc_id');
define ('COL_SHIPCLOUD_LABEL_LABEL_URL',  'sc_label_url');
define ('COL_SHIPCLOUD_LABEL_NOTIFICATION_EMAIL',  'sc_notification_email');
define ('COL_SHIPCLOUD_LABEL_PACKAGES',  'sc_packages'); // array
    /*
        [COL_SHIPCLOUD_LABEL_0] => STDCLASS OBJECT
        [COL_SHIPCLOUD_LABEL_ID] => D0B6BE71A393EC3E45B7E3A16E001D998CC0FDFA
        [COL_SHIPCLOUD_LABEL_LENGTH] => 12
        [COL_SHIPCLOUD_LABEL_WIDTH] => 23
        [COL_SHIPCLOUD_LABEL_HEIGHT] => 12
        [COL_SHIPCLOUD_LABEL_WEIGHT] => 2
        [COL_SHIPCLOUD_LABEL_TYPE] => PARCEL
    */
define ('COL_SHIPCLOUD_LABEL_PRICE',  'sc_price');
define ('COL_SHIPCLOUD_LABEL_REFERENCE_NUMBER',  'sc_reference_number');
define ('COL_SHIPCLOUD_LABEL_SERVICE',  'sc_service'); // array
define ('COL_SHIPCLOUD_LABEL_SHIPPER_NOTIFICATION_EMAIL',  'sc_shipper_notification_email');
define ('COL_SHIPCLOUD_LABEL_TO',  'sc_to_address'); // address
define ('COL_SHIPCLOUD_LABEL_PICKUP',  'sc_pickup'); // address
    /*
        [COL_SHIPCLOUD_LABEL_COMPANY] =>
        [COL_SHIPCLOUD_LABEL_FIRST_NAME] => FRANK
        [COL_SHIPCLOUD_LABEL_LAST_NAME] => BÖHME
        [COL_SHIPCLOUD_LABEL_CARE_OF] =>
        [COL_SHIPCLOUD_LABEL_STREET] => DIETRICH-BONHOEFFER-STR.
        [COL_SHIPCLOUD_LABEL_STREET_NO] => 15
        [COL_SHIPCLOUD_LABEL_ZIP_CODE] => 10407
        [COL_SHIPCLOUD_LABEL_CITY] => BERLIN
        [COL_SHIPCLOUD_LABEL_STATE] => DEUTSCHLAND
        [COL_SHIPCLOUD_LABEL_COUNTRY] => DE
        [COL_SHIPCLOUD_LABEL_PHONE] => 01723236725
        [COL_SHIPCLOUD_LABEL_ID] => A42CAD72-03BC-4360-AD08-83279402E7FD
    */
define ('COL_SHIPCLOUD_LABEL_TRACKING_URL',  'sc_tracking_url'); //
define('COL_DUMMY', 'col_dummy');
define('TEXT_COL_DUMMY', '');


// shipcloud settings
define ('TABLE_SHIPCLOUD_SETTINGS',  DB_PREFIX . '_shipcloud_settings');
define ('KEY_SHIPCLOUD_SETTINGS_PK',  'id');
define ('KEY_SHIPCLOUD_API_KEY_LIVE',  'shipcloud_api_key_live');
define ('KEY_SHIPCLOUD_API_KEY_SANDBOX',  'shipcloud_api_key_sandbox');
define ('KEY_SHIPCLOUD_SANDBOX',  'shipcloud_sandbox');
define ('KEY_SHIPCLOUD_SETTINGS_HINT',  'shipcloud_settings_hint');
define ('KEY_SHIPCLOUD_BANK_ACCOUNT_HOLDER',  'shipcloud_settings_bank_account_holder');
define ('KEY_SHIPCLOUD_BANK_NAME',  'shipcloud_settings_bank_name');
define ('KEY_SHIPCLOUD_BANK_ACCOUNT_NUMBER',  'shipcloud_settings_bank_account_number');
define ('KEY_SHIPCLOUD_BANK_CODE',  'shipcloud_settings_bank_code');

define ('KEY_SHIPCLOUD_FROM_FIRSTNAME',  'shipcloud_from_firstname');
define ('KEY_SHIPCLOUD_FROM_LASTNAME',  'shipcloud_from_lastname');
define ('KEY_SHIPCLOUD_FROM_COMPANY',  'shipcloud_from_company');
define ('KEY_SHIPCLOUD_FROM_CAREOF',  'shipcloud_from_careof');
define ('KEY_SHIPCLOUD_FROM_STREET',  'shipcloud_from_street');
define ('KEY_SHIPCLOUD_FROM_HOUSENO',  'shipcloud_from_houseno');
define ('KEY_SHIPCLOUD_FROM_CITY',  'shipcloud_from_city');
define ('KEY_SHIPCLOUD_FROM_ZIP',  'shipcloud_from_zip');
define ('KEY_SHIPCLOUD_FROM_STATE',  'shipcloud_from_state');
define ('KEY_SHIPCLOUD_FROM_COUNTRY',  'shipcloud_from_country');
define ('KEY_SHIPCLOUD_FROM_PHONE',  'shipcloud_from_phone');

define ('KEY_SHIPCLOUD_DIFFERENT_RETOURE_ADDRESS',  'shipcloud_different_retoure');

define ('KEY_SHIPCLOUD_RETOURE_FIRSTNAME',  'shipcloud_retoure_firstname');
define ('KEY_SHIPCLOUD_RETOURE_LASTNAME',  'shipcloud_retoure_lastname');
define ('KEY_SHIPCLOUD_RETOURE_COMPANY',  'shipcloud_retoure_company');
define ('KEY_SHIPCLOUD_RETOURE_CAREOF',  'shipcloud_retoure_careof');
define ('KEY_SHIPCLOUD_RETOURE_STREET',  'shipcloud_retoure_street');
define ('KEY_SHIPCLOUD_RETOURE_HOUSENO',  'shipcloud_retoure_houseno');
define ('KEY_SHIPCLOUD_RETOURE_CITY',  'shipcloud_retoure_city');
define ('KEY_SHIPCLOUD_RETOURE_ZIP',  'shipcloud_retoure_zip');
define ('KEY_SHIPCLOUD_RETOURE_STATE',  'shipcloud_retoure_state');
define ('KEY_SHIPCLOUD_RETOURE_COUNTRY',  'shipcloud_retoure_country');
define ('KEY_SHIPCLOUD_RETOURE_PHONE',  'shipcloud_retoure_phone');


// shipcloud collect request
define ('TABLE_SHIPCLOUD_COLLECT',  DB_PREFIX . '_shipcloud_collect');
define ('COL_SHIPCLOUD_COLLECT_ID_PK',  'id');
define ('COL_SHIPCLOUD_COLLECT_DATE',  'collect_date');
define ('COL_SHIPCLOUD_COLLECT_NO',  'collect_request_no');

// shipcloud carriers
define ('TABLE_SHIPCLOUD_CARRIERS',  DB_PREFIX . '_shipcloud_carriers');
define ('COL_SHIPCLOUD_CARRIER_PK_ID', 'shipcloud_carrier_id');
define ('COL_SHIPCLOUD_CARRIER_NAME',  'shipcloud_carrier_name');
define ('COL_SHIPCLOUD_CARRIER_DATA',  'shipcloud_carrier_data');
define ('COL_SHIPCLOUD_CARRIER_STATUS',  'shipcloud_carrier_status');

// shipcloud paket vorlagen
define ('TABLE_SHIPCLOUD_PACKAGES',  DB_PREFIX . '_shipcloud_packages');
define ('COL_SHIPCLOUD_PACKAGE_PK_ID', 'shipcloud_package_id');
define ('COL_SHIPCLOUD_PACKAGE_LENGHT',  'shipcloud_package_length');
define ('COL_SHIPCLOUD_PACKAGE_WIDTH',  'shipcloud_package_width');
define ('COL_SHIPCLOUD_PACKAGE_HEIGHT',  'shipcloud_package_height');
define ('COL_SHIPCLOUD_PACKAGE_WEIGHT',  'shipcloud_package_weight');
define ('COL_SHIPCLOUD_PACKAGE_STATUS',  'shipcloud_package_status');
define ('COL_SHIPCLOUD_PACKAGE_CODE',  'shipcloud_package_code');
define ('COL_SHIPCLOUD_SETTINGS_HINT_LABEL', 'shipcloud_settings_hint_label'); // pseudo col

spl_autoload_register(function ($class) {
    $class_file = _SRV_WEBROOT._SRV_WEB_PLUGINS.'xt_ship_and_track/classes/class.' . $class . '.php';
    if (file_exists($class_file))
    {
        include_once $class_file;
        return;
    }
    $class_file = _SRV_WEBROOT._SRV_WEB_PLUGINS.'xt_ship_and_track/classes/api/shipcloud/class.' . $class . '.php';
    if (file_exists($class_file))
    {
        include_once  $class_file;
        return;
    }
    $class = preg_replace("/(.*\\\\)/", "$2", $class);
    $class_file = _SRV_WEBROOT._SRV_WEB_PLUGINS.'xt_ship_and_track/classes/api/shipcloud/class.' . $class . '.php';
    if (file_exists($class_file))
    {
        include_once  $class_file;
        return;
    }
});

