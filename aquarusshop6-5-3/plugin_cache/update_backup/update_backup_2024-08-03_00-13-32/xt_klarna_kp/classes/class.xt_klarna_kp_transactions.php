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

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_klarna_kp/classes/constants.php';


class xt_klarna_kp_transactions
{

    private $_master_key = 'fake_id';
    //public $_display_key = 'payment_reference';

    public $position = '';
    public $url_data = array();

    public $sql_limit;
    private $limit = 25;

    function __construct()
    {
        $this->sql_limit = "0,{$this->limit}";
    }

    function setPosition($position)
    {
        $this->position = $position;
    }

    function _getParams()
    {
        $header = array();
        $header[$this->_master_key] = array('type' => 'textfield', 'readonly'=>true);

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

        $params['include'] = array ('capture_id', 'amount', 'currency_code', 'short_order_id', 'type') ;
        $params['PageSize'] = $this->limit;
        //$params['GroupField'] = 'payment_reference';
        //$params['SortField'] = 'payment_reference';

        $params['header'] = $header;
        $params['master_key'] = $this->_master_key;
        $params['display_deleteBtn'] = false;
        $params['display_resetBtn'] = true;
        $params['display_editBtn'] = false;
        $params['display_newBtn'] = false;
        $params['display_searchPanel']  = false;
        $params['display_statusTrueBtn'] = false;
        $params['display_statusFalseBtn'] = false;

        $rowActionsFunctions = array();
        $rowActions = array();
        /** ROW ACTION JS
        *
        $url_backend = _SRV_WEB. "adminHandler.php?plugin=xt_klarna_kp_payouts&load_section=xt_klarna_kp_payouts&pg=xt_klarna_kp_payouts_row_fnc_1&row_fnc_1_edit_id=";
        $js_backend  = "
        var edit_id = record.data.id;
        addTab('".$url_backend."' + edit_id,'".TEXT_xt_klarna_kp_payouts_ROW_FNC_1." ' + record.data.id);
        ";
        $rowActionsFunctions['xt_klarna_kp_payouts_row_fnc_1'] = $js_backend;
        $rowActions[] = array('iconCls' => 'xt_klarna_kp_payouts_row_fnc_1', 'qtipIndex' => 'qtip1', 'tooltip' => TEXT_XT_KLARNA_KP_PAYOUTS_ROW_FNC_1);

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

    public function xt_klarna_kp_payouts_row_fnc_1($data)
    {
        return 'result of xt_klarna_kp_payouts_row_fnc_1<br />data:<br />'.print_r($data,true);
    }

    public function _get($ID = 0)
    {
        if ($this->position != 'admin') return false;

        $data = array();
        $count_data = 0;
        if ($this->url_data['get_data'])
        {
            $offset = $this->url_data['start'];// / $this->limit;

            /*
            $payouts = klarna_kp::getPayouts($offset, $this->limit);
            $data = $payouts['payouts'];
            $count_data = $payouts["pagination"]["total"];
*/
            $transactions = klarna_kp::getAllTransactions($offset, $this->limit, $this->url_data['store_id']);
            $data = $transactions['transactions'];
            $count_data = $transactions["pagination"]["total"];


            if (is_array($data) && sizeof($data)>0)
            {
                for($i=0; $i<sizeof($data); $i++)
                {

                    $data[$i]['fake_id'] = $offset + $i;

                    $data[$i]['amount'] =  strtoupper($data[$i]['currency_code']).' '.number_Format($data[$i]['amount'], 2, ',', '.');
                }
            }
        }
        elseif($ID) {
            $data = $table_data->getData($ID);
            $count_data = 1;
        }
        else {
            $data = array(
                array('capture_id')
            );
            $count_data = count($data);
        }

        if (!$this->url_data['get_data'] && false)
        {
            // rebuilding fields' order
            $defaultOrder = array(
                COL_KKP__ID_PK,
            );

            $orderedData = array();
            foreach ($defaultOrder as $key) {
                $orderedData[$key] = $data[0][$key];
            }
            $data = array($orderedData);
        }
        else if($ID==='new' && false)
        {

        }

        $obj = new stdClass;
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
