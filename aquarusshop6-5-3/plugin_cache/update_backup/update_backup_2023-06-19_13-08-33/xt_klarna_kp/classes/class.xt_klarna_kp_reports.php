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

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_klarna_kp_reports/classes/class.klarna_kp_reports.php';


class xt_klarna_kp_reports extends klarna_kp_reports
{

    private $_master_key = COL_KKR__ID_PK;
    private $_table = TABLE_KKR_;
    private $_table_lang = '';
    private $_table_seo = '';
    private $_sql_limit = 50;

    public $position = '';
    public $url_data = array();

    function setPosition($position)
    {
        $this->position = $position;
    }

    function _getParams()
    {
        $header = array();
        $header[COL_KKR__ID_PK] = array('type' => 'textfield', 'readonly'=>true);

        /** HEADER KEYS, see class.ExtFunctions.php for more
        *
        $header[SOME_HEADER_KEY] = array(
        'type' => 'textarea',
        'readonly'=>false,
        'value'=>'defVal',
        'text'=>'field_title',
        'width'=> '400px',
        'height' => '30px',
        'required' => true,
        'min' => 8, // min length
        'max' => 50 // max length        );
        */

        /** TYPES, see class.ExtFunctions.php
        *
        * default type => textfield
        * weiter ermittlung mit preg_match(header_key)
        *      desc,textarea => textarea
        *      price => price
        *      status && !adminActionStatus => status
        *      adminActionStatus => adminActionStatus
        *      shop,flag,permission,fsk18,startpage => status
        *      date,last_modified => date
        *      image => image
        *      file => file
        *      icon => icon
        *      url => url
        *      _html => htmleditor
        *      template => dropdown
        *      _permission_info => admininfo
        *      hidden => hidden
        */

        /** TYPE dropdown
        *
        $header['some_dropdown_field'] = array(
        'type' => 'dropdown',
        'url'  => 'DropdownData.php?get=some_dropdown_data','text'=>TEXT_SOME_DROPDOWN_FIELD);
        */
        /** TODO add hook admin_dropdown.php:dropdown, eg
        *
        case 'some_dropdown_data':
        $data=array();

        $data[] =  array(
        'id' => 'option_1',
        'name' => TEXT_SOME_DROPDOWN_FIELD_OPTION_1);
        $data[] =  array('
        id' => 'option_2',
        'name' => TEXT_SOME_DROPDOWN_FIELD_OPTION_2);
        $data[] =  array(
        'id' => 'option_3',
        'name' => TEXT_SOME_DROPDOWN_FIELD_OPTION_3);
        $result=$data;

        break;
        */

        $params = array();
        $params['header'] = $header;
        $params['master_key'] = $this->_master_key;
        $params['display_deleteBtn'] = true;
        $params['display_resetBtn'] = true;
        $params['display_editBtn'] = true;
        $params['display_newBtn'] = true;
        $params['display_searchPanel']  = false;
        $params['display_statusTrueBtn'] = false;
        $params['display_statusFalseBtn'] = false;

        $rowActionsFunctions = array();
        $rowActions = array();
        /** ROW ACTION JS
        *
        $url_backend = _SRV_WEB. "adminHandler.php?plugin=xt_klarna_kp_reports&load_section=xt_klarna_kp_reports&pg=xt_klarna_kp_reports_row_fnc_1&row_fnc_1_edit_id=";
        $js_backend  = "
        var edit_id = record.data.id;
        addTab('".$url_backend."' + edit_id,'".TEXT_xt_klarna_kp_reports_ROW_FNC_1." ' + record.data.id);
        ";
        $rowActionsFunctions['xt_klarna_kp_reports_row_fnc_1'] = $js_backend;
        $rowActions[] = array('iconCls' => 'xt_klarna_kp_reports_row_fnc_1', 'qtipIndex' => 'qtip1', 'tooltip' => TEXT_XT_KLARNA_KP_REPORTS_ROW_FNC_1);

        */

        if (count($rowActionsFunctions) > 0) {
            $params['rowActions'] = $rowActions;
            $params['rowActionsFunctions'] = $rowActionsFunctions;
        }

        if (count($rowActions) > 0) {
            $params['rowActions'] = $rowActions;
            $params['rowActionsFunctions'] = $rowActionsFunctions;
        }


        return $params;
    }

    public function xt_klarna_kp_reports_row_fnc_1($data)
    {
        return 'result of xt_klarna_kp_reports_row_fnc_1<br />data:<br />'.print_r($data,true);
    }

    public function _get($ID = 0)
    {
        if ($this->position != 'admin') return false;

        if (!$ID && !isset($this->sql_limit)) {
            $this->sql_limit = "0,25";
        }

        $table_data = new adminDB_DataRead(
        $this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key,'',$this->_sql_limit,
        /*permission*/ '', /*filter*/ '', /*sort*/ '');

        $data = array();

        if ($this->url_data['get_data'])
        {
            $data = $table_data->getData();

            /** für die view können hier werte angepasst/geändert werden */
            /*
            if (is_array($data) && sizeof($data)>0)
            {
                for($i=0; $i<sizeof($data); $i++)
                {
                    $changedVal = 'changedVal';
                    $data[$i]['SOME_KEY'] = $changedVal ;
                }
            }
            */
        }
        else if($ID==='new')
        {

        }
        elseif($ID) {
            $data = $table_data->getData($ID);
        }
        else {
            $data = $table_data->getHeader();
        }

        if (!$this->url_data['get_data'])
        {
            // rebuilding fields' order
            $defaultOrder = array(
                COL_KKR__ID_PK,
            );

            $orderedData = array();
            foreach ($defaultOrder as $key) {
                $orderedData[$key] = $data[0][$key];
            }
            $data = array($orderedData);
        }

        $obj = new stdClass;
        if ($table_data->_total_count != 0 || !$table_data->_total_count)
        {
            $count_data = $table_data->_total_count;
        }
        else
        {
            $count_data = count($data);
        }
        $obj->totalCount = $count_data;
        $obj->data = $data;

        return $obj;
    }


    function _set($data, $set_type = 'edit')
    {
        $o = new adminDB_DataSave($this->_table, $data, false, __CLASS__);
        $result = $o->saveDataSet();

        return $result;
    }


    function _unset($id = 0)
    {
        global $db;

        $delete = "DELETE FROM `".$this->_table. "` WHERE `".$this->_master_key. "`= '$id'";
        $db->Execute($delete);
        $affectedRows = $db->Affected_Rows();

        return $affectedRows >= 1;
    }

}
