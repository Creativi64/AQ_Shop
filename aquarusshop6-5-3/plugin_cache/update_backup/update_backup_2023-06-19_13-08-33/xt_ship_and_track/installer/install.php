<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_ship_and_track/classes/constants.php';

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

// ############################### Table Shippers
$db->Execute("
        CREATE TABLE IF NOT EXISTS ".TABLE_SHIPPER." (
          ".COL_SHIPPER_ID_PK." INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
          ".COL_SHIPPER_CODE." VARCHAR(128) NOT NULL,
          ".COL_SHIPPER_NAME." VARCHAR(128),
          ".COL_SHIPPER_TRACKING_URL." VARCHAR(256),
          ".COL_SHIPPER_API_ENABLED." INTEGER(1) UNSIGNED DEFAULT 0,
          ".COL_SHIPPER_ENABLED." INTEGER(1) UNSIGNED DEFAULT 1,

          PRIMARY KEY(".COL_SHIPPER_ID_PK.")
        ) ENGINE=".DB_STORAGE_ENGINE."  DEFAULT CHARSET=utf8;
        ");

// ############################### Table Tracking
$db->Execute("
        CREATE TABLE IF NOT EXISTS ".TABLE_TRACKING." (
          ".COL_TRACKING_ID_PK." INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
          ".COL_TRACKING_CODE." VARCHAR(256) NOT NULL,
          ".COL_TRACKING_ORDER_ID." INTEGER UNSIGNED NOT NULL,
          ".COL_TRACKING_STATUS_ID." INTEGER UNSIGNED NOT NULL,
          ".COL_TRACKING_SHIPPER_ID." INTEGER UNSIGNED NOT NULL,
          ".COL_TRACKING_ADDED." TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

          PRIMARY KEY(".COL_TRACKING_ID_PK.")
        ) ENGINE=".DB_STORAGE_ENGINE."  DEFAULT CHARSET=utf8;
        ");

// ############################### Table Tracking status
$db->Execute("
        CREATE TABLE IF NOT EXISTS ".TABLE_TRACKING_STATUS." (
          ".COL_TRACKING_STATUS_ID_PK." INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
          ".COL_TRACKING_SHIPPER_ID." INTEGER UNSIGNED NOT NULL,
          ".COL_TRACKING_STATUS_CODE." INTEGER NOT NULL,

          PRIMARY KEY(".COL_TRACKING_STATUS_ID_PK.")
        ) ENGINE=".DB_STORAGE_ENGINE."  DEFAULT CHARSET=utf8;
        ");

// ############################### Table HERMES
$db->Execute("
        CREATE TABLE IF NOT EXISTS ".TABLE_HERMES_ORDER." (
          ".COL_HERMES_ID_PK." INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
          ".COL_HERMES_ORDER_NO." VARCHAR(256) NOT NULL,
          ".COL_HERMES_XT_ORDER_ID." INTEGER(11) NOT NULL,
          ".COL_HERMES_SHIPPING_ID." VARCHAR(256) DEFAULT '',
          ".COL_HERMES_PARCEL_CLASS." VARCHAR(256) DEFAULT '',
          ".COL_HERMES_STATUS." INTEGER DEFAULT 1,
          ".COL_HERMES_AMOUNT_CASH_ON_DELIVERY." INTEGER DEFAULT 0,
          ".COL_HERMES_BULK_GOOD." INTEGER(1) DEFAULT 0,
          ".COL_HERMES_COLLECT_DATE." DATETIME DEFAULT NULL,
          ".COL_HERMES_TS_CREATED." TIMESTAMP DEFAULT CURRENT_TIMESTAMP ,

          PRIMARY KEY(".COL_HERMES_ID_PK.")
        ) ENGINE=".DB_STORAGE_ENGINE."  DEFAULT CHARSET=utf8;
        ");

// ############################### Table HERMES COLLECT
$db->Execute("
        CREATE TABLE IF NOT EXISTS ".TABLE_HERMES_COLLECT." (
          ".COL_HERMES_COLLECT_ID_PK." INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
          ".COL_HERMES_COLLECT_DATE." DATETIME NOT NULL,
          ".COL_HERMES_COLLECT_NO." VARCHAR(256) NOT NULL,

          PRIMARY KEY(".COL_HERMES_COLLECT_ID_PK.")
        ) ENGINE=".DB_STORAGE_ENGINE."  DEFAULT CHARSET=utf8;
        ");

// ############################### Table HERMES
$db->Execute("
        CREATE TABLE IF NOT EXISTS ".TABLE_HERMES_SETTINGS." (
          `key` VARCHAR(256) NOT NULL,
          `value` VARCHAR(8000) DEFAULT ''
        ) ENGINE=".DB_STORAGE_ENGINE."  DEFAULT CHARSET=utf8;
        ");
$db->Execute("TRUNCATE ".TABLE_HERMES_SETTINGS);
$db->Execute('INSERT INTO '.TABLE_HERMES_SETTINGS." values ('".KEY_HERMES_USER."',''),('".KEY_HERMES_PWD."',''),('".KEY_HERMES_SANDBOX."','1')");

// ############################### West Navi
$db->Execute("DELETE FROM ".TABLE_ADMIN_NAVIGATION." WHERE text in ('xt_hermes_settings','xt_ship_and_track','xt_hermes_collect')");

// west menu bestellungen/kunden -> Hermes
$db->Execute("
        INSERT INTO `".TABLE_ADMIN_NAVIGATION."` (`text`,`icon`,`url_i`,`url_d`,`sortorder`,`parent`,`type`,`navtype`,`cls`,`handler`,`iconCls`)
        VALUES
        ('xt_ship_and_track','../"._SRV_WEB_PLUGINS."xt_ship_and_track/images/icons/hermes16.png','&plugin=xt_ship_and_track','adminHandler.php',9000,'ordertab','G','W',NULL,NULL,NULL); ");

// west menu bestellungen/kunden -> Hermes -> Abholungen
$db->Execute("
        INSERT INTO `".TABLE_ADMIN_NAVIGATION."` (`text`,`icon`,`url_i`,`url_d`,`sortorder`,`parent`,`type`,`navtype`,`cls`,`handler`,`iconCls`)
        VALUES
        ('xt_hermes_collect','../"._SRV_WEB_ADMIN."images/icons/lorry.png','&plugin=xt_ship_and_track&load_section=xt_hermes_collect&pg=overview','adminHandler.php',9000,'xt_ship_and_track','I','W',NULL,NULL,NULL); ");


// west menu bestellungen/kunden -> Hermes -> Enstellungen
$db->Execute("
        INSERT INTO `".TABLE_ADMIN_NAVIGATION."` (`text`,`icon`,`url_i`,`url_d`,`sortorder`,`parent`,`type`,`navtype`,`cls`,`handler`,`iconCls`)
        VALUES
        ('xt_hermes_settings','../"._SRV_WEB_ADMIN."images/icons/wrench.png','&plugin=xt_ship_and_track&load_section=xt_hermes_settings&edit_id=1','adminHandler.php',9001,'xt_ship_and_track','I','W',NULL,NULL,NULL); ");


// ############################### Table SHIPCLOUD
$db->Execute("
        CREATE TABLE IF NOT EXISTS ".TABLE_SHIPCLOUD_LABELS." (
          ".COL_SHIPCLOUD_LABEL_ID_PK." INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
          ".COL_SHIPCLOUD_XT_ORDER_ID." INTEGER(11) NOT NULL,
          ".COL_SHIPCLOUD_LABEL_CARRIER." VARCHAR(64) NOT NULL,
          ".COL_SHIPCLOUD_LABEL_CARRIER_TRACKING_NO." VARCHAR(256) DEFAULT '',
          ".COL_SHIPCLOUD_LABEL_CREATED_AT." TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          ".COL_SHIPCLOUD_LABEL_FROM." TEXT DEFAULT NULL,
          ".COL_SHIPCLOUD_LABEL_ID." VARCHAR(256) NOT NULL,
          ".COL_SHIPCLOUD_LABEL_LABEL_URL." VARCHAR(512) DEFAULT NULL,
          ".COL_SHIPCLOUD_LABEL_NOTIFICATION_EMAIL." VARCHAR(512) DEFAULT NULL,
          ".COL_SHIPCLOUD_LABEL_PACKAGES." TEXT DEFAULT NULL,
          ".COL_SHIPCLOUD_LABEL_PRICE." DECIMAL(5,2) DEFAULT 0,
          ".COL_SHIPCLOUD_LABEL_REFERENCE_NUMBER." VARCHAR(512) DEFAULT NULL,
          ".COL_SHIPCLOUD_LABEL_SERVICE." VARCHAR(64) DEFAULT NULL,
          ".COL_SHIPCLOUD_LABEL_SHIPPER_NOTIFICATION_EMAIL." VARCHAR(512) DEFAULT NULL,
          `".COL_SHIPCLOUD_LABEL_TO."` TEXT DEFAULT '',
          ".COL_SHIPCLOUD_LABEL_TRACKING_URL." VARCHAR(256) DEFAULT '',

          PRIMARY KEY(".COL_SHIPCLOUD_LABEL_ID_PK.")
        ) ENGINE=".DB_STORAGE_ENGINE."  DEFAULT CHARSET=utf8;
        ");


// ############################### Table SHIPCLOUD COLLECT
$db->Execute("
        CREATE TABLE IF NOT EXISTS ".TABLE_SHIPCLOUD_COLLECT." (
          ".COL_SHIPCLOUD_COLLECT_ID_PK." INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
          ".COL_SHIPCLOUD_COLLECT_DATE." DATETIME NOT NULL,
          ".COL_SHIPCLOUD_COLLECT_NO." VARCHAR(256) NOT NULL,

          PRIMARY KEY(".COL_SHIPCLOUD_COLLECT_ID_PK.")
        ) ENGINE=".DB_STORAGE_ENGINE."  DEFAULT CHARSET=utf8;
        ");

// ############################### Table SHIPCLOUD settings
$db->Execute("
        CREATE TABLE IF NOT EXISTS ".TABLE_SHIPCLOUD_SETTINGS." (
          `key` VARCHAR(256) NOT NULL,
          `value` VARCHAR(8000) DEFAULT ''
        ) ENGINE=".DB_STORAGE_ENGINE."  DEFAULT CHARSET=utf8;
        ");
$db->Execute("TRUNCATE ".TABLE_SHIPCLOUD_SETTINGS);
$db->Execute('INSERT INTO '.TABLE_SHIPCLOUD_SETTINGS." values 
    ('".KEY_SHIPCLOUD_SANDBOX."','1'),
    ('".KEY_SHIPCLOUD_API_KEY_SANDBOX."'    ,''),
    ('".KEY_SHIPCLOUD_API_KEY_LIVE."'       ,''),
    ('".KEY_SHIPCLOUD_BANK_ACCOUNT_HOLDER."',''),
    ('".KEY_SHIPCLOUD_BANK_NAME."'          ,''),
    ('".KEY_SHIPCLOUD_BANK_ACCOUNT_NUMBER."',''),
    ('".KEY_SHIPCLOUD_BANK_CODE."'          ,''),
    ('".KEY_SHIPCLOUD_FROM_FIRSTNAME."'     ,''),
    ('".KEY_SHIPCLOUD_FROM_LASTNAME."'      ,'')
    ");
$db->Execute('INSERT INTO '.TABLE_SHIPCLOUD_SETTINGS." values 
    ('".KEY_SHIPCLOUD_FROM_FIRSTNAME."'        ,''),
    ('".KEY_SHIPCLOUD_FROM_LASTNAME."'        ,''),
    ('".KEY_SHIPCLOUD_FROM_COMPANY."'        ,''),
    ('".KEY_SHIPCLOUD_FROM_CAREOF."'        ,''),
    ('".KEY_SHIPCLOUD_FROM_STREET."'        ,''),
    ('".KEY_SHIPCLOUD_FROM_HOUSENO."'       ,''),
    ('".KEY_SHIPCLOUD_FROM_CITY."'          ,''),
    ('".KEY_SHIPCLOUD_FROM_ZIP."'           ,''),
    ('".KEY_SHIPCLOUD_FROM_STATE."'         ,''),
    ('".KEY_SHIPCLOUD_FROM_COUNTRY."'       ,''),
    ('".KEY_SHIPCLOUD_FROM_PHONE."'         ,''),
    
    ('".KEY_SHIPCLOUD_DIFFERENT_RETOURE_ADDRESS."'         ,'0'),
    
    ('".KEY_SHIPCLOUD_RETOURE_FIRSTNAME."'        ,''),
    ('".KEY_SHIPCLOUD_RETOURE_LASTNAME."'        ,''),
    ('".KEY_SHIPCLOUD_RETOURE_COMPANY."'        ,''),
    ('".KEY_SHIPCLOUD_RETOURE_CAREOF."'        ,''),
    ('".KEY_SHIPCLOUD_RETOURE_STREET."'        ,''),
    ('".KEY_SHIPCLOUD_RETOURE_HOUSENO."'       ,''),
    ('".KEY_SHIPCLOUD_RETOURE_CITY."'          ,''),
    ('".KEY_SHIPCLOUD_RETOURE_ZIP."'           ,''),
    ('".KEY_SHIPCLOUD_RETOURE_STATE."'         ,''),
    ('".KEY_SHIPCLOUD_RETOURE_COUNTRY."'       ,''),
    ('".KEY_SHIPCLOUD_RETOURE_PHONE."'         ,'')
    ");




// ############################### Table SHIPCLOUD carriers
$db->Execute("
        CREATE TABLE IF NOT EXISTS ".TABLE_SHIPCLOUD_CARRIERS." (
          ".COL_SHIPCLOUD_CARRIER_PK_ID." INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
          ".COL_SHIPCLOUD_CARRIER_NAME." VARCHAR(256) NOT NULL,
          ".COL_SHIPCLOUD_CARRIER_DATA." TEXT NOT NULL,
          ".COL_SHIPCLOUD_CARRIER_STATUS." INT(1) DEFAULT 1,

          PRIMARY KEY(".COL_SHIPCLOUD_CARRIER_PK_ID.")
        ) ENGINE=".DB_STORAGE_ENGINE."  DEFAULT CHARSET=utf8;
        ");

$db->Execute("
        CREATE TABLE IF NOT EXISTS ".TABLE_SHIPCLOUD_PACKAGES." (
          ".COL_SHIPCLOUD_PACKAGE_PK_ID." INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
          ".COL_SHIPCLOUD_PACKAGE_LENGHT." INTEGER(6) UNSIGNED DEFAULT 30,
          ".COL_SHIPCLOUD_PACKAGE_WIDTH." INTEGER(6) UNSIGNED DEFAULT 30,
          ".COL_SHIPCLOUD_PACKAGE_HEIGHT." INTEGER(7) UNSIGNED DEFAULT 1,
          ".COL_SHIPCLOUD_PACKAGE_WEIGHT." DOUBLE(10,2) UNSIGNED DEFAULT 1,
          ".COL_SHIPCLOUD_PACKAGE_STATUS." INTEGER(1) UNSIGNED DEFAULT 1,
          ".COL_SHIPCLOUD_PACKAGE_CODE." VARCHAR(256) NULL DEFAULT NULL,

          PRIMARY KEY(".COL_SHIPCLOUD_PACKAGE_PK_ID.")
        ) ENGINE=".DB_STORAGE_ENGINE."  DEFAULT CHARSET=utf8;
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






// west menu einstellungen -> konfiguration -> Versender
// wird im hook get_node gemacht

$langs = array('de', 'en');
$tpls = array('links');
_installMailTemplatesShipTrack($langs, $tpls);

_installShipper();

function _installShipper()
{
    global $db;

    $dir = _SRV_WEBROOT.'plugins/xt_ship_and_track/installer/shipper/';

    $csv = file_exists($dir.'shipper.csv') ?  _getFileContentShipTrack($dir.'shipper.csv') : '';


    foreach(preg_split("/((\r?\n)|(\r\n?))/", $csv) as $line)
    {
        $data = explode(';', $line);
        $inserData = array(
            COL_SHIPPER_CODE => $data[0],
            COL_SHIPPER_NAME => $data[1],
            COL_SHIPPER_TRACKING_URL => $data[2],
        );
        if ( $data[0]=='hermes' || $data[0]=='shipcloud')
        {
            $inserData[COL_SHIPPER_API_ENABLED] = 1;
        }
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

                $db->Execute('DELETE FROM '.TABLE_LANGUAGE_CONTENT.' WHERE language_key=? AND language_code=?', array($key, $lang));

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

                $db->Execute('DELETE FROM '.TABLE_LANGUAGE_CONTENT.' WHERE language_key=? AND language_code=?', array($key, $lang));

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
            $db->Execute('DELETE FROM '.TABLE_LANGUAGE_CONTENT.' WHERE language_key=? AND language_code=?', array($key, $lang));
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
            $db->Execute('DELETE FROM '.TABLE_LANGUAGE_CONTENT.' WHERE language_key=? AND language_code=?', array($key, $lang));
            $db->AutoExecute(TABLE_LANGUAGE_CONTENT ,$insertData);
        } catch (exception $e) {
            return $e->msg;
        }
    }

}


function _installMailTemplatesShipTrack($langs, $tpls) {
    global $db;

    $mail_dir = _SRV_WEBROOT.'plugins/xt_ship_and_track/installer/mails/';

    foreach($tpls as $tpl)
    {
        $data = array(
            'tpl_type' => 'tracking_'.$tpl,
            'tpl_special' => '-1',
        );
        $c = (int) $db->GetOne("SELECT count(tpl_id) FROM ".TABLE_MAIL_TEMPLATES." where `tpl_type` = '".$data['tpl_type']."'");
        if ($c>0)
        {
            continue;
        }
        try {
            $db->AutoExecute(TABLE_MAIL_TEMPLATES ,$data);
        } catch (exception $e) {
            return $e->msg;
        }
        $tplId = $db->GetOne("SELECT `tpl_id` FROM `".TABLE_MAIL_TEMPLATES."` WHERE `tpl_type`='".$data['tpl_type']."'");

        foreach($langs as $lang)
        {
            $html = file_exists($mail_dir.$lang.'/'.$tpl.'_html.txt') ?  _getFileContentShipTrack($mail_dir.$lang.'/'.$tpl.'_html.txt') : '';
            $txt  = file_exists($mail_dir.$lang.'/'.$tpl.'_txt.txt')  ?  _getFileContentShipTrack($mail_dir.$lang.'/'.$tpl.'_txt.txt') : '';
            $subj = file_exists($mail_dir.$lang.'/'.$tpl.'_subject.txt')  ?  _getFileContentShipTrack($mail_dir.$lang.'/'.$tpl.'_subject.txt') : '';

            $data = array(
                'tpl_id' => $tplId,
                'language_code' => $lang,
                'mail_body_html' => $html,
                'mail_body_txt' => $txt,
                'mail_subject' => $subj
            );
            try {
                $db->AutoExecute(TABLE_MAIL_TEMPLATES_CONTENT ,$data);
            } catch (exception $e) {
                return $e->msg;
            }
        }
    }
}

function _getFileContentShipTrack($filename) {
    $handle = fopen($filename, 'rb');
    $content = fread($handle, filesize($filename));
    fclose($handle);
    return $content;

}
