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

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_ship_and_track/classes/class.xt_ship_and_track.php';

class xt_shipcloud_settings {

    public $position;
    private $_master_key = 'na';
    private $_table = TABLE_SHIPCLOUD_SETTINGS;

    function setPosition($position)
    {
        $this->position = $position;
    }

    function _getParams()
    {
        $header = array();
        $header['COL_HERMES_SETTINGS_HINT_LABEL'] = array('type' => 'textarea', 'readonly' => true, 'width' => 315, 'height' => '75px');

        $header[KEY_SHIPCLOUD_SANDBOX] = array('type' => 'status');
        $header['COL_SHIPCLOUD_SETTINGS_HINT_BANK_ACCOUNT'] = array('type' => 'textarea', 'readonly' => true, 'width' => 315, 'height' => '75px');
        $header['COL_SHIPCLOUD_SETTINGS_HINT_DEFAULT_ADDRESS'] = array('type' => 'textarea', 'readonly' => true, 'width' => 315, 'height' => '60px');
        $header['COL_SHIPCLOUD_SETTINGS_HINT_RETOURE_ADDRESS'] = array('type' => 'textarea', 'readonly' => true, 'width' => 315, 'height' => '60px');
        $header['COL_SHIPCLOUD_SETTINGS_HINT_CALLBACK'] = array('type' => 'textarea', 'readonly' => true, 'width' => 315, 'height' => '200px');

        /*
        $header[KEY_SHIPCLOUD_API_KEY_LIVE] = array('type' => 'textfield');
        $header[KEY_SHIPCLOUD_API_KEY_SANDBOX] = array('type' => 'textfield');
        $header[KEY_SHIPCLOUD_SANDBOX] = array('type' => 'status');

        $header[KEY_SHIPCLOUD_BANK_ACCOUNT_HOLDER] = array('type' => 'textfield');
        $header[KEY_SHIPCLOUD_BANK_NAME] = array('type' => 'textfield');
        $header[KEY_SHIPCLOUD_BANK_ACCOUNT_NUMBER] = array('type' => 'textfield');
        $header[KEY_SHIPCLOUD_BANK_CODE] = array('type' => 'textfield');
        */

        $header[KEY_SHIPCLOUD_DIFFERENT_RETOURE_ADDRESS] = array('type' => 'status');
        $header[KEY_SHIPCLOUD_FROM_COUNTRY] =  array('type' => 'dropdown', 'readonly'=>false, 'url' => 'DropdownData.php?get=countries');
        $header[KEY_SHIPCLOUD_RETOURE_COUNTRY] =  array('type' => 'dropdown', 'readonly'=>false, 'url' => 'DropdownData.php?get=countries');
        $header['SHIPCLOUD_CALLBACK_URL'] = array();

        $params = array();
        $params['header'] = $header;
        $params['master_key'] = $this->_master_key;
        $params['display_deleteBtn'] = false;
        $params['display_resetBtn'] = false;
        $params['display_editBtn'] = true;
        $params['display_newBtn'] = false;
        $params['display_searchPanel']  = false;

        $params['display_checkCol']  = false;

        return $params;
    }

    function _get($ID = 0)
    {
        if ($this->position != 'admin') return false;


        if($ID)
        {
            global $xtLink;

            self::readSettings();

            $data = array();
            $data[0] = array(
               'COL_HERMES_SETTINGS_HINT_LABEL' =>  constant('TEXT_SHIPCLOUD_SETTINGS_HINT_TEXT'),
                KEY_SHIPCLOUD_SANDBOX => XT_SHIPCLOUD_SANDBOX,
                KEY_SHIPCLOUD_API_KEY_LIVE => XT_SHIPCLOUD_API_KEY_LIVE,
                KEY_SHIPCLOUD_API_KEY_SANDBOX => XT_SHIPCLOUD_API_KEY_SANDBOX,

                'COL_SHIPCLOUD_SETTINGS_HINT_BANK_ACCOUNT' =>  constant('TEXT_SHIPCLOUD_SETTINGS_HINT_BANK_ACCOUNT'),

                KEY_SHIPCLOUD_BANK_ACCOUNT_HOLDER => XT_SHIPCLOUD_BANK_ACCOUNT_HOLDER,
                KEY_SHIPCLOUD_BANK_NAME => XT_SHIPCLOUD_BANK_NAME,
                KEY_SHIPCLOUD_BANK_ACCOUNT_NUMBER => XT_SHIPCLOUD_BANK_ACCOUNT_NUMBER,
                KEY_SHIPCLOUD_BANK_CODE => XT_SHIPCLOUD_BANK_CODE,

                'COL_SHIPCLOUD_SETTINGS_HINT_DEFAULT_ADDRESS' =>  constant('TEXT_SHIPCLOUD_SETTINGS_HINT_DEFAULT_ADDRESS'),

                KEY_SHIPCLOUD_FROM_FIRSTNAME => defined('XT_SHIPCLOUD_FROM_FIRSTNAME') ? XT_SHIPCLOUD_FROM_FIRSTNAME : '',
                KEY_SHIPCLOUD_FROM_LASTNAME => defined('XT_SHIPCLOUD_FROM_LASTNAME') ? XT_SHIPCLOUD_FROM_LASTNAME : '',
                KEY_SHIPCLOUD_FROM_COMPANY => defined('XT_SHIPCLOUD_FROM_COMPANY') ? XT_SHIPCLOUD_FROM_COMPANY : '',
                KEY_SHIPCLOUD_FROM_CAREOF => defined('XT_SHIPCLOUD_FROM_CAREOF') ? XT_SHIPCLOUD_FROM_CAREOF : '',
                KEY_SHIPCLOUD_FROM_STREET => defined('XT_SHIPCLOUD_FROM_STREET') ? XT_SHIPCLOUD_FROM_STREET : '',
                KEY_SHIPCLOUD_FROM_HOUSENO => defined('XT_SHIPCLOUD_FROM_HOUSENO') ? XT_SHIPCLOUD_FROM_HOUSENO : '',
                KEY_SHIPCLOUD_FROM_CITY =>  defined('XT_SHIPCLOUD_FROM_CITY') ? XT_SHIPCLOUD_FROM_CITY : '',
                KEY_SHIPCLOUD_FROM_ZIP => defined('XT_SHIPCLOUD_FROM_ZIP') ? XT_SHIPCLOUD_FROM_ZIP : '',
                KEY_SHIPCLOUD_FROM_STATE => defined('XT_SHIPCLOUD_FROM_STATE') ? XT_SHIPCLOUD_FROM_STATE : '',
                KEY_SHIPCLOUD_FROM_COUNTRY => defined('XT_SHIPCLOUD_FROM_COUNTRY') ? XT_SHIPCLOUD_FROM_COUNTRY : '',
                KEY_SHIPCLOUD_FROM_PHONE => defined('XT_SHIPCLOUD_FROM_PHONE') ? XT_SHIPCLOUD_FROM_PHONE : '',


                'COL_SHIPCLOUD_SETTINGS_HINT_RETOURE_ADDRESS' =>  constant('TEXT_SHIPCLOUD_SETTINGS_HINT_RETOURE_ADDRESS'),

                KEY_SHIPCLOUD_DIFFERENT_RETOURE_ADDRESS => defined('XT_SHIPCLOUD_DIFFERENT_RETOURE') ? XT_SHIPCLOUD_DIFFERENT_RETOURE : '',

                KEY_SHIPCLOUD_RETOURE_FIRSTNAME => defined('XT_SHIPCLOUD_RETOURE_FIRSTNAME') ? XT_SHIPCLOUD_RETOURE_FIRSTNAME : '',
                KEY_SHIPCLOUD_RETOURE_LASTNAME => defined('XT_SHIPCLOUD_RETOURE_LASTNAME') ? XT_SHIPCLOUD_RETOURE_LASTNAME : '',
                KEY_SHIPCLOUD_RETOURE_COMPANY => defined('XT_SHIPCLOUD_RETOURE_COMPANY') ? XT_SHIPCLOUD_RETOURE_COMPANY : '',
                KEY_SHIPCLOUD_RETOURE_CAREOF => defined('XT_SHIPCLOUD_RETOURE_CAREOF') ? XT_SHIPCLOUD_RETOURE_CAREOF : '',
                KEY_SHIPCLOUD_RETOURE_STREET => defined('XT_SHIPCLOUD_RETOURE_STREET') ? XT_SHIPCLOUD_RETOURE_STREET : '',
                KEY_SHIPCLOUD_RETOURE_HOUSENO => defined('XT_SHIPCLOUD_RETOURE_HOUSENO') ? XT_SHIPCLOUD_RETOURE_HOUSENO : '',
                KEY_SHIPCLOUD_RETOURE_CITY =>  defined('XT_SHIPCLOUD_RETOURE_CITY') ? XT_SHIPCLOUD_RETOURE_CITY : '',
                KEY_SHIPCLOUD_RETOURE_ZIP => defined('XT_SHIPCLOUD_RETOURE_ZIP') ? XT_SHIPCLOUD_RETOURE_ZIP : '',
                KEY_SHIPCLOUD_RETOURE_STATE => defined('XT_SHIPCLOUD_RETOURE_STATE') ? XT_SHIPCLOUD_RETOURE_STATE : '',
                KEY_SHIPCLOUD_RETOURE_COUNTRY => defined('XT_SHIPCLOUD_RETOURE_COUNTRY') ? XT_SHIPCLOUD_RETOURE_COUNTRY : '',
                KEY_SHIPCLOUD_RETOURE_PHONE => defined('XT_SHIPCLOUD_RETOURE_PHONE') ? XT_SHIPCLOUD_RETOURE_PHONE : '',

                'COL_SHIPCLOUD_SETTINGS_HINT_CALLBACK' =>  constant('TEXT_SHIPCLOUD_SETTINGS_HINT_CALLBACK'),

                'SHIPCLOUD_CALLBACK_URL' => $xtLink->_link(array('page' => 'callback', 'paction' => 'xt_ship_and_track', 'params'=>'target=shipcloud', 'conn' => 'SSL'), 'xtAdmin/')

            );
            $count_data = 1;

        } else {
            $data = array();
            $data[] = array(
                'COL_HERMES_SETTINGS_HINT_LABEL' => '',
                KEY_SHIPCLOUD_SANDBOX => '',
                KEY_SHIPCLOUD_API_KEY_LIVE => '',
                KEY_SHIPCLOUD_API_KEY_SANDBOX => ''
            );
            $count_data = 0;
        }

        $obj = new stdClass;
        $obj->totalCount = $count_data;
        $obj->data = $data;
        return $obj;
    }


    function _set($data, $set_type = 'edit')
    {
        global $db, $filter;
        $r = new stdClass();
        $r->success = false;
        $r->msg = false;
        $r->errorMsg = false;

        $checkBoxes = array('shipcloud_sandbox', 'shipcloud_different_retoure');
        foreach ($checkBoxes as $key)
        {
            if(!array_key_exists($key, $data))
            {
                $data[$key] = 0;
            }
        }

        $query = "SELECT * FROM " . $this->_table;
        $record = $db->Execute($query);

        if($record->RecordCount()==0)
        {
            foreach($data as $k => $v)
            {
                $db->Execute(
                    "INSERT INTO " . $this->_table . " values(?,?)",
                    array($k, $v)
                );
            }
        }
        else
        {
            while (!$record->EOF)
            {
                $conf_value = $filter->_filter($data[$record->fields['key']]);
                $db->Execute(
                    "UPDATE " . $this->_table . " SET `value`=? WHERE `key`=?",
                    array($conf_value, $record->fields['key'])
                );
                $record->MoveNext();
            }
        }

        $r->success = true;
        $r->msg = __define('TEXT_SUCCESS');
        $r->errorMsg = __define('TEXT_SUCCESS');

        return $r;
    }

    function _unset($id = 0)
    {
        return false;
    }

    public static function readSettings()
    {
        global $db;

        static $done = false;
        if($done) return;

        $sql = 'SELECT * FROM '.TABLE_SHIPCLOUD_SETTINGS;
        $result = $db->GetArray($sql);
        foreach ($result as $item)
        {
            if (!defined('XT_'.strtoupper($item['key'])))
            {
                define('XT_'.strtoupper($item['key']), $item['value']);
            }
        }

        $done = true;
    }
}