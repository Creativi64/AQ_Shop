<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_ship_and_track/classes/constants.php';

// alter shipper
$db->Execute("ALTER TABLE ".TABLE_SHIPPER." 
CHANGE COLUMN ".COL_SHIPPER_CODE." ".COL_SHIPPER_CODE." VARCHAR(128) NOT NULL ;");

// add liefery url
$exists = $db->GetOne("SELECT 1 FROM ".TABLE_SHIPPER." WHERE ".COL_SHIPPER_CODE."='liefery'");
if(!$exists)
{
    $insertData = array(
        COL_SHIPPER_CODE => 'liefery',
        COL_SHIPPER_NAME => 'Liefery',
        COL_SHIPPER_TRACKING_URL => "https://www.liefery.com/#/order/[TRACKING_CODE]",
    );
    try {
        $db->AutoExecute(TABLE_SHIPPER ,$insertData);
    } catch (exception $e) {
        error_log($e->getMessage());
    }
}


// ############################### Table SHIPCLOUD

$db->Execute("
        CREATE TABLE IF NOT EXISTS ".TABLE_SHIPCLOUD_LABELS." (
          ".COL_SHIPCLOUD_LABEL_ID_PK." INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
          ".COL_SHIPCLOUD_XT_ORDER_ID." INTEGER(11) NOT NULL,
          ".COL_SHIPCLOUD_LABEL_CARRIER." VARCHAR(64) NOT NULL,
          ".COL_SHIPCLOUD_LABEL_CARRIER_TRACKING_NO." VARCHAR(256) DEFAULT '',
          ".COL_SHIPCLOUD_LABEL_CREATED_AT." TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          `".COL_SHIPCLOUD_LABEL_FROM."` TEXT DEFAULT NULL,
          ".COL_SHIPCLOUD_LABEL_ID." VARCHAR(256) NOT NULL,
          ".COL_SHIPCLOUD_LABEL_LABEL_URL." VARCHAR(512) DEFAULT NULL,
          ".COL_SHIPCLOUD_LABEL_NOTIFICATION_EMAIL." VARCHAR(512) DEFAULT NULL,
          ".COL_SHIPCLOUD_LABEL_PACKAGES." TEXT DEFAULT NULL,
          ".COL_SHIPCLOUD_LABEL_PRICE." DECIMAL(5,2) DEFAULT 0,
          ".COL_SHIPCLOUD_LABEL_REFERENCE_NUMBER." VARCHAR(512) DEFAULT NULL,
          ".COL_SHIPCLOUD_LABEL_SERVICE." VARCHAR(64) DEFAULT NULL,
          ".COL_SHIPCLOUD_LABEL_SHIPPER_NOTIFICATION_EMAIL." VARCHAR(512) DEFAULT NULL,
          ".COL_SHIPCLOUD_LABEL_TO." TEXT DEFAULT '',
          ".COL_SHIPCLOUD_LABEL_PICKUP." TEXT DEFAULT '',
          ".COL_SHIPCLOUD_LABEL_TRACKING_URL." VARCHAR(256) DEFAULT '',

          PRIMARY KEY(".COL_SHIPCLOUD_LABEL_ID_PK.")
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
        ");


// ############################### Table SHIPCLOUD COLLECT
$db->Execute("
        CREATE TABLE IF NOT EXISTS ".TABLE_SHIPCLOUD_COLLECT." (
          ".COL_SHIPCLOUD_COLLECT_ID_PK." INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
          ".COL_SHIPCLOUD_COLLECT_DATE." DATETIME NOT NULL,
          ".COL_SHIPCLOUD_COLLECT_NO." VARCHAR(256) NOT NULL,

          PRIMARY KEY(".COL_SHIPCLOUD_COLLECT_ID_PK.")
        );
        ");

// ############################### Table SHIPCLOUD settings
$db->Execute("
        CREATE TABLE IF NOT EXISTS ".TABLE_SHIPCLOUD_SETTINGS." (
          `key` VARCHAR(256) NOT NULL,
          `value` VARCHAR(8000) DEFAULT ''
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
        ");

// ############################### Table SHIPCLOUD carriers
$db->Execute("
        CREATE TABLE IF NOT EXISTS ".TABLE_SHIPCLOUD_CARRIERS." (
          ".COL_SHIPCLOUD_CARRIER_PK_ID." INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
          ".COL_SHIPCLOUD_CARRIER_NAME." VARCHAR(256) NOT NULL,
          ".COL_SHIPCLOUD_CARRIER_DATA." TEXT NOT NULL,
          ".COL_SHIPCLOUD_CARRIER_STATUS." INT(1) DEFAULT 1,

          PRIMARY KEY(".COL_SHIPCLOUD_CARRIER_PK_ID.")
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
        ");

$db->Execute("
        CREATE TABLE IF NOT EXISTS ".TABLE_SHIPCLOUD_PACKAGES." (
          ".COL_SHIPCLOUD_PACKAGE_PK_ID." INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
          ".COL_SHIPCLOUD_PACKAGE_LENGHT." INTEGER(6) UNSIGNED DEFAULT 30,
          ".COL_SHIPCLOUD_PACKAGE_WIDTH." INTEGER(6) UNSIGNED DEFAULT 30,
          ".COL_SHIPCLOUD_PACKAGE_HEIGHT." INTEGER(7) UNSIGNED DEFAULT 1,
          ".COL_SHIPCLOUD_PACKAGE_WEIGHT." INTEGER(7) UNSIGNED DEFAULT 1000,
          ".COL_SHIPCLOUD_PACKAGE_STATUS." INTEGER(1) UNSIGNED DEFAULT 1,
          ".COL_SHIPCLOUD_PACKAGE_CODE." VARCHAR(256) NULL DEFAULT NULL,

          PRIMARY KEY(".COL_SHIPCLOUD_PACKAGE_PK_ID.")
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
        ");


// ############################### West Navi
$db->Execute("DELETE FROM ".TABLE_ADMIN_NAVIGATION." WHERE text in ('xt_shipcloud_settings','xt_ship_and_track_shipcloud','xt_shipcloud_collect', 'xt_shipcloud_carriers', 'xt_shipcloud_packages')");

// west menu bestellungen/kunden -> shipcloud
$db->Execute("
        INSERT INTO `".TABLE_ADMIN_NAVIGATION."` (`text`,`icon`,`url_i`,`url_d`,`sortorder`,`parent`,`type`,`navtype`,`cls`,`handler`,`iconCls`)
        VALUES
        ('xt_ship_and_track_shipcloud','../"._SRV_WEB_PLUGINS."xt_ship_and_track/images/icons/shipcloud16.png','&plugin=xt_ship_and_track','adminHandler.php',9000,'ordertab','G','W',NULL,NULL,NULL); ");

// west menu bestellungen/kunden -> shipcloud -> Abholungen
/*
$db->Execute("
        INSERT INTO `".TABLE_ADMIN_NAVIGATION."` (`text`,`icon`,`url_i`,`url_d`,`sortorder`,`parent`,`type`,`navtype`,`cls`,`handler`,`iconCls`)
        VALUES
        ('xt_shipcloud_collect','../"._SRV_WEB_ADMIN."images/icons/lorry.png','&plugin=xt_ship_and_track&load_section=xt_shipcloud_collect&pg=overview','adminHandler.php',9000,'xt_ship_and_track_shipcloud','I','W',NULL,NULL,NULL); ");
*/

// west menu bestellungen/kunden -> shipcloud -> Packetvorlagen
$db->Execute("
        INSERT INTO `".TABLE_ADMIN_NAVIGATION."` (`text`,`icon`,`url_i`,`url_d`,`sortorder`,`parent`,`type`,`navtype`,`cls`,`handler`,`iconCls`)
        VALUES
        ('xt_shipcloud_packages','../"._SRV_WEB_ADMIN."images/icons/table_gear.png','&plugin=xt_ship_and_track&load_section=xt_shipcloud_packages&pg=overview','adminHandler.php',9000,'xt_ship_and_track_shipcloud','I','W',NULL,NULL,NULL); ");

// west menu bestellungen/kunden -> shipcloud -> Versender
$db->Execute("
        INSERT INTO `".TABLE_ADMIN_NAVIGATION."` (`text`,`icon`,`url_i`,`url_d`,`sortorder`,`parent`,`type`,`navtype`,`cls`,`handler`,`iconCls`)
        VALUES
        ('xt_shipcloud_carriers','../"._SRV_WEB_ADMIN."images/icons/lorry.png','&plugin=xt_ship_and_track&load_section=xt_shipcloud_carriers&pg=overview','adminHandler.php',9000,'xt_ship_and_track_shipcloud','I','W',NULL,NULL,NULL); ");


// west menu bestellungen/kunden -> shipcloud -> Enstellungen
$db->Execute("
        INSERT INTO `".TABLE_ADMIN_NAVIGATION."` (`text`,`icon`,`url_i`,`url_d`,`sortorder`,`parent`,`type`,`navtype`,`cls`,`handler`,`iconCls`)
        VALUES
        ('xt_shipcloud_settings','../"._SRV_WEB_ADMIN."images/icons/wrench.png','&plugin=xt_ship_and_track&load_section=xt_shipcloud_settings&edit_id=1','adminHandler.php',9001,'xt_ship_and_track_shipcloud','I','W',NULL,NULL,NULL); ");



if($this->_FieldExists(KEY_HERMES_USER, TABLE_HERMES_SETTINGS))
{
    // struktur hermes settings ändern, vorher werte merken, falls noch vorhanden....
    $sql = 'SELECT '.KEY_HERMES_USER.' FROM '.TABLE_HERMES_SETTINGS. ' WHERE 1';
    $a = $db->GetOne($sql);
    define('XT_HERMES_USER', $a ? $a:'');

    $sql = 'SELECT '.KEY_HERMES_PWD.' FROM '.TABLE_HERMES_SETTINGS. ' WHERE 1';
    $a = $db->GetOne($sql);
    define('XT_HERMES_PWD',  $a ? $a:'');

    $sql = 'SELECT '.KEY_HERMES_SANDBOX.' FROM '.TABLE_HERMES_SETTINGS. ' WHERE 1';
    $a = $db->GetOne($sql);
    define('XT_HERMES_SANDBOX',  $a==1 ? 1:0);
}

$db->Execute('DROP TABLE IF EXISTS '.TABLE_HERMES_SETTINGS);
$db->Execute("
        CREATE TABLE IF NOT EXISTS ".TABLE_HERMES_SETTINGS." (
          `key` VARCHAR(256) NOT NULL,
          `value` VARCHAR(256) DEFAULT ''
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
        ");
$db->Execute('INSERT INTO '.TABLE_HERMES_SETTINGS." values ('".KEY_HERMES_USER."','".XT_HERMES_USER."'),('".KEY_HERMES_PWD."','".XT_HERMES_PWD."'),('".KEY_HERMES_SANDBOX."','".XT_HERMES_SANDBOX."')");

// hermes referenze nummer hat nun neues format {shop_ssl_domain}#{order_id}
// für abfrage zb des status älterer bestellungen merken wir uns die momentan höchste order_id um das alte format zu nutzen
$maxOrderId = $db->GetOne("SELECT max(orders_id) FROM ".TABLE_ORDERS);
$db->Execute('INSERT INTO '.TABLE_HERMES_SETTINGS." values ('".KEY_HERMES_LAST_ORDER_BEFORE_v6."',?)", array($maxOrderId));


_installShipperShipcloud();

function _installShipperShipcloud()
{
    global $db;

    $csv = 'shipcloud;shipcloud;https://track.shipcloud.io/[TRACKING_CODE]';

    foreach(preg_split("/((\r?\n)|(\r\n?))/", $csv) as $line)
    {
        $data = explode(';', $line);
        $inserData = array(
            COL_SHIPPER_CODE => $data[0],
            COL_SHIPPER_NAME => $data[1],
            COL_SHIPPER_TRACKING_URL => $data[2],
            COL_SHIPPER_API_ENABLED => 1
        );
        try {
            $db->AutoExecute(TABLE_SHIPPER ,$inserData);
        } catch (exception $e) {
            return $e->msg;
        }
        $shipperId = $db->Insert_ID();
        $langs = array('de');
        _installStatusCodesShipTrack($shipperId, $data[0], $langs);
    }

}


function _installStatusCodesShipTrack($shipperId, $shipperCode, $langs) {
    global $db;

    $dir = _SRV_WEBROOT.'plugins/xt_ship_and_track/installer/status_codes/';

    foreach($langs as $lang)
    {
        if (file_exists($dir.$lang.'/status_codes_'.$shipperCode.'.csv'))
        {
            $csv =  _getFileContentShipTrack($dir.$lang.'/status_codes_'.$shipperCode.'.csv');

            foreach(preg_split("/((\r?\n)|(\r\n?))/", $csv) as $line)
            {

                $data = explode(';', $line);
                $insertData = array(
                    COL_TRACKING_STATUS_CODE => $data[0],
                    COL_TRACKING_SHIPPER_ID => $shipperId
                );
                try {
                    $db->AutoExecute(TABLE_TRACKING_STATUS ,$insertData);
                } catch (exception $e) {
                    return $e->msg;
                }

                $key = 'TEXT_'.strtoupper($shipperCode).'_'.strtoupper(str_replace('-','_',$data[0])).'_SHORT';

                $insertData = array(
                    'translated' => 0,
                    'language_code' => $lang,
                    'language_key' => $key,
                    'language_value' => $data[1],
                    'class' => 'both',
                    'plugin_key' => 'xt_ship_and_track'
                );
                try {
                    $db->AutoExecute(TABLE_LANGUAGE_CONTENT ,$insertData);
                } catch (exception $e) {
                    return $e->msg;
                }

                $key = 'TEXT_'.strtoupper($shipperCode).'_'.strtoupper(str_replace('-','_',$data[0])).'_LONG';

                $insertData = array(
                    'translated' => 0,
                    'language_code' => $lang,
                    'language_key' => $key,
                    'language_value' => $data[2],
                    'class' => 'both',
                    'plugin_key' => 'xt_ship_and_track'
                );
                try {
                    $db->AutoExecute(TABLE_LANGUAGE_CONTENT ,$insertData);
                } catch (exception $e) {
                    return $e->msg;
                }
            }

        }

        $insertData = array(
            COL_TRACKING_STATUS_CODE => 0,
            COL_TRACKING_SHIPPER_ID => $shipperId
        );
        try {
            $db->AutoExecute(TABLE_TRACKING_STATUS ,$insertData);
        } catch (exception $e) {
            return $e->msg;
        }

        $key = 'TEXT_'.strtoupper($shipperCode).'_0_SHORT';
        switch($lang)
        {
            case 'de':
                $value = 'manuell hinzugefügt';
                break;
            case 'en':
                $value = 'added manually';
                break;
            default:
                $value = 'added manually';
        }
        $insertData = array(
            'translated' => 0,
            'language_code' => $lang,
            'language_key' => $key,
            'language_value' => $value,
            'class' => 'both',
            'plugin_key' => 'xt_ship_and_track'
        );
        try {
            $db->AutoExecute(TABLE_LANGUAGE_CONTENT ,$insertData);
        } catch (exception $e) {
            return $e->msg;
        }

        $key = 'TEXT_'.strtoupper($shipperCode).'_0_LONG';

        $insertData = array(
            'translated' => 0,
            'language_code' => $lang,
            'language_key' => $key,
            'language_value' => $value,
            'class' => 'both',
            'plugin_key' => 'xt_ship_and_track'
        );
        try {
            $db->AutoExecute(TABLE_LANGUAGE_CONTENT ,$insertData);
        } catch (exception $e) {
            return $e->msg;
        }
    }

}

function _getFileContentShipTrack($filename) {
    $handle = fopen($filename, 'rb');
    $content = fread($handle, filesize($filename));
    fclose($handle);
    return $content;

}
