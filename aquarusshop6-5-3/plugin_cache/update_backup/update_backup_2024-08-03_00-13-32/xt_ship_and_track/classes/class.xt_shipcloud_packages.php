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

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_ship_and_track/classes/class.xt_ship_and_track.php';

class xt_shipcloud_packages {

    private $_master_key = COL_SHIPCLOUD_PACKAGE_PK_ID;

    function setPosition($position)
    {
        $this->position = $position;
    }

    function _getParams()
    {
        $header = array();
        $header[COL_SHIPCLOUD_SETTINGS_HINT_LABEL] = array('type' => 'textarea', 'readonly' => true, 'width' => 310, 'height' => 50);
        $header[COL_SHIPCLOUD_PACKAGE_PK_ID] = array('readonly'=>true);
        $header[COL_SHIPCLOUD_PACKAGE_LENGHT] = array('type' => 'textfield', 'required' => true);
        $header[COL_SHIPCLOUD_PACKAGE_WIDTH] = array('type' => 'textfield', 'required' => true);
        $header[COL_SHIPCLOUD_PACKAGE_HEIGHT] = array('type' => 'textfield', 'required' => true);
        $header[COL_SHIPCLOUD_PACKAGE_WEIGHT] = array('type' => 'textfield', 'required' => true);
        $header[COL_SHIPCLOUD_PACKAGE_STATUS] = array('type' => 'status');
        $header[COL_SHIPCLOUD_PACKAGE_CODE] = array('type' => 'textfield');

        $params = array();
        $params['header'] = $header;
        $params['master_key'] = $this->_master_key;
        $params['display_deleteBtn'] = true;
        $params['display_resetBtn'] = true;
        $params['display_editBtn'] = true;
        $params['display_newBtn'] = true;
        $params['display_searchPanel']  = false;
        $params['display_adminActionStatus']  = false;
        $params['display_checkCol']  = false;
        $params['display_statusTrueBtn']  = true;
        $params['display_statusFalseBtn']  = true;

        return $params;
    }

    function _get($ID = 0, $enabled = -1)
    {
        if ($this->position != 'admin') return false;

        $where = '';
        if($enabled > -1)
        {
            $where = COL_SHIPCLOUD_PACKAGE_STATUS.' = '.(int) $enabled. ' ';
        }

        if (!$ID && !isset($this->sql_limit)) {
            $this->sql_limit = "0,50";
        }

        $defaultOrder = array(
            COL_SHIPCLOUD_SETTINGS_HINT_LABEL,
            COL_SHIPCLOUD_PACKAGE_PK_ID,
            COL_SHIPCLOUD_PACKAGE_LENGHT,
            COL_SHIPCLOUD_PACKAGE_WIDTH,
            COL_SHIPCLOUD_PACKAGE_HEIGHT,
            COL_SHIPCLOUD_PACKAGE_WEIGHT,
            COL_SHIPCLOUD_PACKAGE_STATUS,
            COL_SHIPCLOUD_PACKAGE_CODE
        );

        $table_data = new adminDB_DataRead(TABLE_SHIPCLOUD_PACKAGES, '', '', $this->_master_key, $where , '', '', '',  '');
        $table_data->_filterData = false;
        if ($this->url_data['get_data']) {
            $data = $table_data->getData();
            foreach($data as $k => &$v)
            {
            }
        }
        elseif($ID==='new')
        {
            $orderedData = array();
            foreach ($defaultOrder as $key) {
                $orderedData[$key] = '';
            }
            $orderedData[COL_SHIPCLOUD_SETTINGS_HINT_LABEL] =  constant('TEXT_SHIPCLOUD_PACKAGES_HINT_TEXT');
            $data = array($orderedData);
        }
        elseif($ID) {
            $data = $table_data->getData($ID);
            $orderedData = array();
            foreach ($defaultOrder as $key) {
                $orderedData[$key] = $data[0][$key];
            }
            $orderedData[COL_SHIPCLOUD_SETTINGS_HINT_LABEL] =  constant('TEXT_SHIPCLOUD_PACKAGES_HINT_TEXT');
            $data = array($orderedData);

        } else {
            $data = $table_data->getHeader();
            $orderedData = array();
            foreach ($defaultOrder as $key) {
                $orderedData[$key] = $data[0][$key];
            }
            $data = array($orderedData);
        }

        $obj = new stdClass;
        if ($table_data->_total_count != 0 || !$table_data->_total_count)
            $count_data = $table_data->_total_count;
        else
            $count_data = count($data);
        $obj->totalCount = $count_data;
        $obj->data = $data;
        return $obj;
    }

    function _set($data, $set_type = 'edit')
    {
        $result = new stdClass();
        $result->success = 'true';
        $data[COL_SHIPCLOUD_PACKAGE_CODE] = trim($data[COL_SHIPCLOUD_PACKAGE_CODE]);
        $data[COL_SHIPCLOUD_PACKAGE_WEIGHT] = str_replace(',','.', $data[COL_SHIPCLOUD_PACKAGE_WEIGHT]);
        if(empty($data[COL_SHIPCLOUD_PACKAGE_CODE]))
        {
            $data[COL_SHIPCLOUD_PACKAGE_CODE] =
                self::getPackageShorthand($data);
        }
        $o = new adminDB_DataSave(TABLE_SHIPCLOUD_PACKAGES, $data, false, __CLASS__);
        try {
            $r = $o->saveDataSet();
        }
        catch(Exception $e){
            $result->success = false;
            $result->msg = $e->getMessage();
        }

        $result->totalCount = 1;
        return $result;
    }

    function _unset($id = 0)
    {
        global $db;
        return false;
    }

    function _setStatus($id, $status) {
        global $db,$xtPlugin;

        $id = (int)$id;
        if (!is_int($id)) return false;

        $db->Execute(
            "update " . TABLE_SHIPCLOUD_PACKAGES . " set " . COL_SHIPCLOUD_PACKAGE_STATUS . " = ? where ".$this->_master_key." = ?",
            array((int)$status, (int)$id)
        );
    }

    /**
     *
     * @return array die verfügbaren shipcloud versender (dhl,tnt,...)
     */
    public static function getShipcloudPackages($enabled = -1)
    {
        global $db;

        $sc = new xt_shipcloud_packages();
        $sc->setPosition('admin');
        $sc->url_data['get_data'] = true;
        $xt_data = $sc->_get(0, $enabled);
        $data = array();

        foreach ($xt_data->data as $k => $c)
        {
            $name = self::getPackageShorthand($c);
            $data[] =  array(
                'id' => $c['shipcloud_package_code'],
                'name' => $name
            );
        }
        if(count($data) == 0)
        {
            $data[] =  array(
                'id' => 0,
                'name' => __define('TEXT_SHIPCLOUD_NO_ACTIVE_PACKAGES')
            );
        }

        return $data;
    }

    public static function getPackageShorthand($data)
    {
        return $data[COL_SHIPCLOUD_PACKAGE_LENGHT].'x'.$data[COL_SHIPCLOUD_PACKAGE_WIDTH].'x'.$data[COL_SHIPCLOUD_PACKAGE_HEIGHT].' / '.$data[COL_SHIPCLOUD_PACKAGE_WEIGHT].'kg';
    }

}