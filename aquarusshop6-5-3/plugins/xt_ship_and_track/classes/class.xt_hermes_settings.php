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

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_ship_and_track/classes/constants.php';
require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_ship_and_track/classes/class.xt_tracking.php';
require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_ship_and_track/classes/api/hermes/Hermes.php';

class xt_hermes_settings {

    public $position;
    private $_master_key = 'na';
    private $_table = TABLE_HERMES_SETTINGS;

    function setPosition($position)
    {
        $this->position = $position;
    }

    function _getParams()
    {
        $header = array();
        $header['COL_HERMES_SETTINGS_HINT_LABEL'] = array('type' => 'textarea', 'readonly' => true, 'width' => 310);
        $header[KEY_HERMES_USER] = array('type' => 'textfield');
        $header[KEY_HERMES_PWD] = array('type' => 'textfield');
        $header[KEY_HERMES_SANDBOX] = array('type' => 'status');


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


        if($ID) {
            $data = array();
            $data[0] = array(
                'COL_HERMES_SETTINGS_HINT_LABEL' =>  constant('TEXT_HERMES_SETTINGS_HINT_TEXT'),
                KEY_HERMES_SANDBOX => XT_HERMES_SANDBOX,
                KEY_HERMES_USER => XT_HERMES_USER,
                KEY_HERMES_PWD => XT_HERMES_PWD
            );
            $count_data = 1;

        } else {
            $data = array();
            $data[] = array(
                'COL_HERMES_SETTINGS_HINT_LABEL' => '',
                KEY_HERMES_SANDBOX => '',
                KEY_HERMES_USER => '',
                KEY_HERMES_PWD => ''
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

        $query = "SELECT * FROM " . $this->_table;
        /** @var ADORecordSet $record */
        $record = $db->Execute($query);

        while (!$record->EOF)
        {
            $conf_value = $filter->_filter($data[$record->fields['key']]);
            $db->Execute(
                "UPDATE " . $this->_table . " SET `value`=? WHERE `key`=?",
                array($conf_value, $record->fields['key'])
            );
            $record->MoveNext();
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
}