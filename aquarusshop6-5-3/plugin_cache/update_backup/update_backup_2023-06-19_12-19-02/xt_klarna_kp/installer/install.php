<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_klarna_kp/classes/constants.php';

global $db, $store_handler;

global $ADODB_THROW_EXCEPTIONS;
$ADODB_THROW_EXCEPTIONS = true;
try
{
    include _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_klarna_kp/installer/uninstall.php';

    $db->Execute("INSERT INTO " . TABLE_PAYMENT_COST . " (`payment_id`, `payment_geo_zone`, `payment_country_code`, `payment_type_value_from`, `payment_type_value_to`, `payment_price`,`payment_allowed`) VALUES(" . $payment_id . ", '', '', 0, 10000.00, 0, 1);");


    // ############################### West Navi

    $db->Execute("
		INSERT INTO `".TABLE_ADMIN_NAVIGATION."` (`text`,`icon`,`url_i`,`url_d`,`sortorder`,`parent`,`type`,`navtype`,`cls`,`handler`,`iconCls`, `url_h`)
        VALUES
		('xt_klarna_kp_payouts','images/partner/klarna.png','&plugin=xt_klarna_kp','adminHandler.php',9020,'ordertab','I','W',NULL,NULL,NULL, 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/573374493'); ");
        
    /*  no function, disabled so far
    $db->Execute("
		INSERT INTO `".TABLE_ADMIN_NAVIGATION."` (`text`,`icon`,`url_i`,`url_d`,`sortorder`,`parent`,`type`,`navtype`,`cls`,`handler`,`iconCls`)
		VALUES
		('xt_klarna_kp_transactions','images/partner/klarna.png','&plugin=xt_klarna_kp','adminHandler.php',9010,'ordertab','I','W',NULL,NULL,NULL); ");
	*/

	/* no function, disabled so far
	$db->Execute("
		INSERT INTO `".TABLE_ADMIN_NAVIGATION."` (`text`,`icon`,`url_i`,`url_d`,`sortorder`,`parent`,`type`,`navtype`,`cls`,`handler`,`iconCls`)
		VALUES
		('xt_klarna_kp_reports','images/partner/klarna.png','&plugin=xt_klarna_kp','adminHandler.php',9030,'ordertab','I','W',NULL,NULL,NULL); ");
*/

    if (!$this->_FieldExists('kp_order_id', TABLE_ORDERS))
    {
        $db->Execute("ALTER TABLE `" . TABLE_ORDERS . "` ADD COLUMN `kp_order_id` VARCHAR(36) NULL DEFAULT NULL ;");
    }
    if (!$this->_FieldExists('kp_order_test_mode', TABLE_ORDERS))
    {
        $db->Execute("ALTER TABLE `" . TABLE_ORDERS . "` ADD COLUMN `kp_order_test_mode` INT(1) NULL AFTER `kp_order_id` ;");
    }

    $db->Execute("ALTER TABLE " . TABLE_ORDERS . "
        ADD COLUMN `kp_order_ref` VARCHAR(36) NULL DEFAULT NULL ,
        ADD COLUMN `kp_order_status` VARCHAR(36) NULL DEFAULT NULL ,
        ADD COLUMN `kp_order_fraud_status` VARCHAR(36) NULL DEFAULT NULL ,
        ADD COLUMN `kp_order_data` TEXT NULL DEFAULT NULL,
        ADD INDEX `idx_kp_order_id` (`kp_order_id`),
        ADD INDEX `idx_kp_order_ref` (`kp_order_ref`),
        ADD INDEX `idx_kp_order_status` (`kp_order_status`),
        ADD INDEX `idx_kp_order_fraud_status` (`kp_order_fraud_status`)
    ");
}
catch(Exception $e){
    error_log($e->getMessage());
    // ignore or update ?
    $output .= "<div style='border:1px solid red; background:#f0bcb8;padding:10px;'>Irgendetwas hat nicht funktioniert!<br/>Something went wrong!<br/>&nbsp<br/>".$e->getMessage().'</div>';
    throw $e;
}
finally {

}


/**
_KLARNA_CONFIG_KP_MID_TEST
_KLARNA_CONFIG_KP_PWD_TEST
_KLARNA_CONFIG_KP_MID
_KLARNA_CONFIG_KP_PWD
_KLARNA_CONFIG_KP_TESTMODE
 *
_KLARNA_CONFIG_KP_LOGO_DESIGN
_KLARNA_CONFIG_WIDGET_DESIGN
_KLARNA_CONFIG_KP_RADIUS_BORDER
_KLARNA_CONFIG_KP_COLOR_LINK
 *
_KLARNA_CONFIG_ACCOUNT_STATUS
_KLARNA_CONFIG_ACCOUNT_REASON
_KLARNA_CONFIG_ACCOUNT_TYPE
 */

$store_config = array
(
    '_KLARNA_CONFIG_KP_MID_TEST' => array(
        'config_value' => 'K501691_c9c98ef92786',
        'group_id' => 101,
        'sort_order' => 0,
        'type' => 'text',
        'url' => ''
    ),
    '_KLARNA_CONFIG_KP_PWD_TEST' => array(
        'config_value' => 'EkyuJeMCVH7tW6Wu',
        'group_id' => 101,
        'sort_order' => 0,
        'type' => 'password',
        'url' => ''
    ),
    '_KLARNA_CONFIG_KP_TESTMODE' => array(
        'config_value' => '1',
        'group_id' => 101,
        'sort_order' => 0,
        'type' => 'status',
        'url' => ''
    ),
    '_KLARNA_CONFIG_KP_MID' => array(
        'config_value' => '',
        'group_id' => 101,
        'sort_order' => 0,
        'type' => 'text',
        'url' => ''
    ),
    '_KLARNA_CONFIG_KP_PWD' => array(
        'config_value' => '',
        'group_id' => 101,
        'sort_order' => 0,
        'type' => 'password',
        'url' => ''
    ),
    '_KLARNA_CONFIG_KP_TESTMODE_CUSTOMER_GROUP' => [
        'config_value' => '',
        'group_id' => 101,
        'sort_order' => 0,
        'type' => 'dropdown',
        'url' => 'customers_status&add_empty'
    ],

    '_KLARNA_CONFIG_SEND_EXTENDED_PRODUCT_INFO' => array(
        'config_value' => '1',
        'group_id' => 101,
        'sort_order' => 0,
        'type' => 'status',
        'url' => ''
    ),
    '_KLARNA_CONFIG_KP_LOGO_DESIGN' => array(
        'config_value' => 'badge-short-white',
        'group_id' => 101,
        'sort_order' => 0,
        'type' => 'hidden', /*'dropdown',*/
        'url' => 'klarna_kp_logo_design'
    ),
    '_KLARNA_CONFIG_WIDGET_DESIGN' => array(
        'config_value' => 'pale-v2',
        'group_id' => 101,
        'sort_order' => 0,
        'type' => 'hidden', //'dropdown',
        'url' => 'klarna_widget_design'
    ),


    '_KLARNA_CONFIG_KP_PAYMENT_METHODS' => array(
        'config_value' => 'pay_now,pay_later,pay_over_time',
        'group_id' => 101,
        'sort_order' => -10,
        'type' => 'itemselect',
        'url' => 'DropdownData.php?get=klarna_kp_payment_methods&store_id={{store_id}}'
    ),

    '_KLARNA_CONFIG_KP_RADIUS_BORDER' => array(
        'config_value' => '0',
        'group_id' => 101,
        'sort_order' => 0,
        'type' => 'text',
        'url' => ''
    ),
    '_KLARNA_CONFIG_KP_COLOR_BORDER' => array(
        'config_value' => '#cccccc',
        'group_id' => 101,
        'sort_order' => 0,
        'type' => 'text',
        'url' => ''
    ),
    '_KLARNA_CONFIG_KP_COLOR_BORDER_SELECTED' => array(
        'config_value' => '#FF9900',
        'group_id' => 101,
        'sort_order' => 0,
        'type' => 'text',
        'url' => ''
    ),

    '_KLARNA_CONFIG_KP_COLOR_BUTTON' => array(
        'config_value' => '#313131',
        'group_id' => 101,
        'sort_order' => 0,
        'type' => 'text',
        'url' => ''
    ),
    '_KLARNA_CONFIG_KP_COLOR_BUTTON_TEXT' => array(
        'config_value' => '#FFFFFF',
        'group_id' => 101,
        'sort_order' => 0,
        'type' => 'text',
        'url' => ''
    ),
    '_KLARNA_CONFIG_KP_COLOR_CHECKBOX' => array(
        'config_value' => '#313131',
        'group_id' => 101,
        'sort_order' => 0,
        'type' => 'text',
        'url' => ''
    ),
    '_KLARNA_CONFIG_KP_COLOR_CHECKBOX_CHECKMARK' => array(
        'config_value' => '#313131',
        'group_id' => 101,
        'sort_order' => 0,
        'type' => 'text',
        'url' => ''
    ),

    '_KLARNA_CONFIG_KP_COLOR_HEADER' => array(
        'config_value' => '#313131',
        'group_id' => 101,
        'sort_order' => 0,
        'type' => 'text',
        'url' => ''
    ),
    '_KLARNA_CONFIG_KP_COLOR_LINK' => array(
        'config_value' => '#FF9900',
        'group_id' => 101,
        'sort_order' => 0,
        'type' => 'text',
        'url' => ''
    ),
    '_KLARNA_CONFIG_KP_COLOR_DETAILS' => array(
        'config_value' => '#313131',
        'group_id' => 101,
        'sort_order' => 0,
        'type' => 'text',
        'url' => ''
    ),

    '_KLARNA_CONFIG_KP_COLOR_TEXT' => array(
        'config_value' => '#313131',
        'group_id' => 101,
        'sort_order' => 0,
        'type' => 'text',
        'url' => ''
    ),
    '_KLARNA_CONFIG_KP_COLOR_TEXT_SECONDARY' => array(
        'config_value' => '#eeeeee',
        'group_id' => 101,
        'sort_order' => 0,
        'type' => 'text',
        'url' => ''
    ),

    '_KLARNA_CONFIG_STATUS_ACCEPTED' => array(
        'config_value' => '23',
        'group_id' => 101,
        'sort_order' => 0,
        'type' => 'dropdown',
        'url' => 'order_status'
    ),
    '_KLARNA_CONFIG_STATUS_PENDING' => array(
        'config_value' => '62',
        'group_id' => 101,
        'sort_order' => 0,
        'type' => 'dropdown',
        'url' => 'order_status'
    ),
    '_KLARNA_CONFIG_STATUS_REJECTED' => array(
        'config_value' => '63',
        'group_id' => 101,
        'sort_order' => 0,
        'type' => 'dropdown',
        'url' => 'order_status'
    ),
    '_KLARNA_CONFIG_STATUS_CANCELED' => array(
        'config_value' => '34',
        'group_id' => 101,
        'sort_order' => 0,
        'type' => 'dropdown',
        'url' => 'order_status'
    ),

    '_KLARNA_CONFIG_TRIGGER_FULL_CAPTURE' => array(
        'config_value' => '',
        'group_id' => 101,
        'sort_order' => 0,
        'type' => 'dropdown',
        'url' => 'order_status_empty'
    ),
    '_KLARNA_CONFIG_TRIGGER_FULL_CAPTURE_FORCE_STATUS_UPDATE' => array(
        'config_value' => '0',
        'group_id' => 101,
        'sort_order' => 0,
        'type' => 'status',
    ),
    '_KLARNA_CONFIG_TRIGGER_CANCEL' => array(
        'config_value' => '',
        'group_id' => 101,
        'sort_order' => 0,
        'type' => 'dropdown',
        'url' => 'order_status_empty'
    ),
    '_KLARNA_CONFIG_TRIGGER_CANCEL_FORCE_STATUS_UPDATE' => array(
        'config_value' => '0',
        'group_id' => 101,
        'sort_order' => 0,
        'type' => 'status',
    ),
    '_KLARNA_CONFIG_TRIGGER_ESCALATION_EMAIL' => array(
        'config_value' => '',
        'group_id' => 101,
        'sort_order' => 0,
        'type' => 'text',
    ),
    '_KLARNA_CONFIG_TRIGGER_ALLOWED_TRIGGER' => array(
        'config_value' => '',
        'group_id' => 101,
        'sort_order' => 0,
        'type' => 'text',
    ),

	'_KLARNA_CONFIG_ACCOUNT_STATUS' => array(
		'config_value' => 'DEMO',
		'group_id' => 101,
		'sort_order' => 0,
		'type' => 'hidden',
		'url' => ''
	),
	'_KLARNA_CONFIG_ACCOUNT_REASON' => array(
		'config_value' => '',
		'group_id' => 101,
		'sort_order' => 0,
		'type' => 'hidden',
		'url' => ''
	),
	'_KLARNA_CONFIG_ACCOUNT_TYPE' => array(
		'config_value' => '1',
		'group_id' => 101,
		'sort_order' => 0,
		'type' => 'hidden',
		'url' => ''
		),

    '_KLARNA_CONFIG_KP_B2B_INFO' => array(
        'config_value' => '{{info}}',
        'group_id' => 101,
        'sort_order' => 0,
        'type' => 'admininfo',
        'url' => ''
    ),

    '_KLARNA_CONFIG_KP_B2B_ENABLED' => array(
        'config_value' => '1',
        'group_id' => 101,
        'sort_order' => 5,
        'type' => 'hidden',
        'url' => ''
    ),
    '_KLARNA_CONFIG_KP_B2B_GROUPS' => array(
        'config_value' => '0',
        'group_id' => 101,
        'sort_order' => 10,
        'type' => 'itemselect',
        'url' => 'DropdownData.php?get=klarna_kp_b2b_groups&store_id={{store_id}}'
    ),
);

$stores = $store_handler->getStores();
foreach ($stores as $store)
{
    $i = 5;
    foreach ($store_config as $k => $c)
    {
        $keyExists = $db->GetOne(" SELECT 1 FROM ". DB_PREFIX . "_config_" . $store['id'] . " WHERE config_key=?", array($k));
        if(!$keyExists)
        {
            $db->Execute("INSERT INTO " . DB_PREFIX . "_config_" . $store['id'] . " (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) 
            VALUES 
            (?, ?, ?, ?, ?, ?);", array($k, $c['config_value'], $c['group_id'], $i, $c['type'], $c['url'],));
        }
        {
            // update ?
        }
        $i = $i + 5;
    }
}

if(version_compare(constant('_SYSTEM_VERSION'), '6.1.2', '<'))
{
    require_once(_SRV_WEBROOT . _SRV_WEB_FRAMEWORK . 'admin/classes/class.adminDB_DataSave.php');
    // C R O N
    $data = array(
        "cron_id" => "",
        "cron_note" => "Klarna account status update",
        "cron_value" => "1",
        "cron_type" => "i",
        "hour" => "",
        "minute" => "",
        "cron_action" => "file:cron.klarna_kp.php",
        "cron_parameter" => "",
        "active_status" => 1
    );
    $cron = new xt_cron();
    $cron->setPosition('admin');
    $cron->_set($data, 'add');


    $f_source = _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_klarna_kp/cronjobs/cron.klarna_kp.php';
    $f_target = _SRV_WEBROOT . _SRV_WEB_CRONJOBS . 'cron.klarna_kp.php';
    copy($f_source, $f_target);

    if (!file_exists($f_target) && XT_WIZARD_STARTED !== true)
    {
        $hintPath = _SRV_WEB_CORE . 'cronjobs/cron.klarna_kp.php';
        $hint = "Installer tried to copy cron file but failed.<br />You have to copy manually plugins/xt_klarna_kp/cronjobs/cron.klarna_kp.php to <br/> $hintPath";
        $output .= "<br /><div style='border:solid 1px #fecf43;background: #ffe086; padding:10px;'>";
        $output .= $hint;
        $output .= "</div>";
    }
}


