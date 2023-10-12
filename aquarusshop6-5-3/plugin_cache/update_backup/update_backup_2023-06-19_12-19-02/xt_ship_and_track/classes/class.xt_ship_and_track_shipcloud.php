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

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_ship_and_track/classes/class.ship_and_track_shipcloud.php';


class xt_ship_and_track_shipcloud extends ship_and_track_shipcloud
{

    private $_master_key = COL_SHIPCLOUD_LABEL_ID_PK;
    private $_table = TABLE_SHIPCLOUD_LABELS;
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
        $header[COL_SHIPCLOUD_LABEL_ID_PK] = array('type' => 'hidden', 'readonly'=>true);
        $header[COL_SHIPCLOUD_LABEL_CARRIER] = array('type' => 'textfield', 'readonly'=>true);
        $header[COL_SHIPCLOUD_XT_ORDER_ID] = array('type' => 'textfield', 'readonly'=>true);
        $header[COL_SHIPCLOUD_LABEL_REFERENCE_NUMBER] = array('type' => 'textfield', 'readonly'=>true);
        $header[COL_SHIPCLOUD_LABEL_SERVICE] = array('type' => 'textfield', 'readonly'=>true);
        $header[COL_SHIPCLOUD_LABEL_TO] = array('type' => 'textfield', 'readonly'=>true);
        $header[COL_SHIPCLOUD_LABEL_CREATED_AT] = array('type' => 'textfield', 'readonly'=>true);
        $header[COL_SHIPCLOUD_LABEL_PICKUP] = array('type' => 'textfield', 'readonly'=>true);
        $header[COL_SHIPCLOUD_LABEL_LABEL_URL] = array('type' => 'textfield', 'readonly'=>true);
        $header[COL_SHIPCLOUD_LABEL_TRACKING_URL] = array('type' => 'textfield', 'readonly'=>true);
        $header[COL_SHIPCLOUD_LABEL_CARRIER_TRACKING_NO] = array('type' => 'textfield', 'readonly'=>true);

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
        $params['display_editBtn'] = false;
        $params['display_newBtn'] = false;
        $params['display_searchPanel']  = true;
        $params['display_statusTrueBtn'] = false;
        $params['display_statusFalseBtn'] = false;

        $params['display_checkCol']  = true;

        $rowActionsFunctions = array();
        $rowActions = array();

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

    public function xt_ship_and_track_shipcloud_row_fnc_1($data)
    {
        return 'result of xt_ship_and_track_shipcloud_row_fnc_1<br />data:<br />'.print_r($data,true);
    }

    public function _get($ID = 0)
    {
        global $filter;

        if ($this->position != 'admin') return false;

        if (!$ID && !isset($this->sql_limit)) {
            $this->sql_limit = "0,25";
        }

        $where = '';
        if($this->url_data['query'])
        {
            $q = $filter->_filter($this->url_data['query']);
            $where = "(" .COL_SHIPCLOUD_LABEL_ID." LIKE '%".$q."%'
            OR ". COL_SHIPCLOUD_LABEL_TO." LIKE '%".$q."%'
            OR ". COL_SHIPCLOUD_LABEL_ID." LIKE '%".$q."%'
            OR ". COL_SHIPCLOUD_LABEL_TRACKING_URL." LIKE '%".$q."%'
            OR ". COL_SHIPCLOUD_LABEL_CARRIER_TRACKING_NO." LIKE '%".$q."%'
            OR ". COL_SHIPCLOUD_XT_ORDER_ID." = '".$q."')";
        }

        $table_data = new adminDB_DataRead(
        $this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key, $where, $this->_sql_limit,
        /*permission*/ '', /*filter*/ '', /*sort*/ ' order by sc_xt_id desc');
        $table_data->_filterData = false;

        $data = array();

        if ($this->url_data['get_data'])
        {
            $data = $table_data->getData();

            $template = new Template();
            $template->getTemplatePath('address-be-list.tpl.html', 'xt_ship_and_track', '', 'plugin');

            if (is_array($data) && sizeof($data)>0)
            {
                for($i=0; $i<sizeof($data); $i++)
                {
                    //$this->_unset($data[$i]['sc_xt_id']);
                    $dateCreated = new DateTime($data[$i][COL_SHIPCLOUD_LABEL_CREATED_AT]);
                    $dateCreated = $dateCreated->format('Y-m-d H:i');

                    $to = unserialize($data[$i][COL_SHIPCLOUD_LABEL_TO]);
                    $tpl_data = array('addr' => $to);
                    $to = $template->getTemplate('xt_ship_and_track', 'address-be-list.tpl.html', $tpl_data). PHP_EOL;
                    $data[$i][COL_SHIPCLOUD_LABEL_TO] = $to ;


                    $pickup = unserialize($data[$i][COL_SHIPCLOUD_LABEL_PICKUP]);
                    if(!empty($pickup))
                    {
                        //error_log(print_r($pickup,true));
                        $pickupTime = (array)$pickup->pickupTime();
                        $tpl_data = array('pickupTime' => $pickupTime);
                        $pickup = $template->getTemplate('xt_ship_and_track', 'pickup-be-list.tpl.html', $tpl_data) . PHP_EOL;
                    }
                    else {
                        $pickup = '';
                    }
                    $data[$i][COL_SHIPCLOUD_LABEL_PICKUP] = $pickup ;

                    $data[$i][COL_SHIPCLOUD_LABEL_SERVICE] = $data[$i][COL_SHIPCLOUD_LABEL_CARRIER].'<br/>'.$data[$i][COL_SHIPCLOUD_LABEL_SERVICE];

                    $data[$i][COL_SHIPCLOUD_LABEL_CREATED_AT] = $dateCreated ;
                    if(!empty($data[$i][COL_SHIPCLOUD_LABEL_LABEL_URL])){
                        $data[$i][COL_SHIPCLOUD_LABEL_LABEL_URL] = '<a href="'.$data[$i][COL_SHIPCLOUD_LABEL_LABEL_URL].'" target="_blank">Label</a>';
                        $data[$i][COL_SHIPCLOUD_LABEL_LABEL_URL] .= '<br/><a href="'.$data[$i][COL_SHIPCLOUD_LABEL_TRACKING_URL].'" target="_blank">Track</a>';
                    }
                    else {
                        $data[$i][COL_SHIPCLOUD_LABEL_LABEL_URL] = '<a href="'.$data[$i][COL_SHIPCLOUD_LABEL_TRACKING_URL].'" target="_blank">Track</a>';
                    }

                }
            }
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

                COL_SHIPCLOUD_XT_ORDER_ID,
                //COL_SHIPCLOUD_LABEL_REFERENCE_NUMBER,
                //COL_SHIPCLOUD_LABEL_CARRIER,
                COL_SHIPCLOUD_LABEL_SERVICE,
                COL_SHIPCLOUD_LABEL_TO,
                COL_SHIPCLOUD_LABEL_CREATED_AT,
                COL_SHIPCLOUD_LABEL_PICKUP,
                COL_SHIPCLOUD_LABEL_LABEL_URL,
                //COL_SHIPCLOUD_LABEL_TRACKING_URL,
                COL_SHIPCLOUD_LABEL_CARRIER_TRACKING_NO,
                COL_SHIPCLOUD_LABEL_ID_PK,

                //COL_DUMMY
            );

            $orderedData = array();
            foreach ($defaultOrder as $key) {
                $orderedData[$key] = $data[0][$key];
            }
            $to = (array)(unserialize($data[0][COL_SHIPCLOUD_LABEL_TO]));
            $tpl_data = array('addr' => $to);
            $template = new Template();
            $template->getTemplatePath('address-be-edit.tpl.html', 'xt_ship_and_track', '', 'plugin');
            $to = $template->getTemplate('xt_ship_and_track', 'address-be-edit.tpl.html', $tpl_data);
            $orderedData[COL_SHIPCLOUD_LABEL_TO] = $to;
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
        /**
        COL_SHIPCLOUD_XT_ORDER_ID
        COL_SHIPCLOUD_LABEL_CARRIER
        COL_SHIPCLOUD_LABEL_CARRIER_TRACKING_NO
        COL_SHIPCLOUD_LABEL_CREATED_AT
        COL_SHIPCLOUD_LABEL_FROM
        COL_SHIPCLOUD_LABEL_ID
        COL_SHIPCLOUD_LABEL_LABEL_URL
        COL_SHIPCLOUD_LABEL_NOTIFICATION_EMAIL
        COL_SHIPCLOUD_LABEL_PACKAGES
        COL_SHIPCLOUD_LABEL_PRICE
        COL_SHIPCLOUD_LABEL_REFERENCE_NUMBER
        COL_SHIPCLOUD_LABEL_SERVICE
        COL_SHIPCLOUD_LABEL_SHIPPER_NOTIFICATION_EMAIL
        COL_SHIPCLOUD_LABEL_TO
        COL_SHIPCLOUD_LABEL_TRACKING_URL
         */

        $o = new adminDB_DataSave($this->_table, $data, false, __CLASS__);
        $result = $o->saveDataSet();

        return $result;
    }


    function _unset($id = 0)
    {
        global $db;

        $sc_id = $db->GetOne("SELECT ".COL_SHIPCLOUD_LABEL_ID. " FROM ".TABLE_SHIPCLOUD_LABELS. " WHERE ".COL_SHIPCLOUD_LABEL_ID_PK."=?", array($id));

        try
        {
            if ($sc_id)
            {
                try
                {
                    shipcloud_api::getInstance(XT_SHIPCLOUD_API_KEY)
                        ->shipmentDelete($sc_id);
                }
                catch (Exception $e)
                {

                }

                $delete = "DELETE FROM " . TABLE_TRACKING. ' WHERE '.COL_TRACKING_CODE. ' = ? ';
                $db->Execute($delete, $sc_id);

            }

            $delete = "DELETE FROM `" . $this->_table . "` WHERE `" . $this->_master_key . "`= ?";
            $db->Execute($delete, array($id));
            $affectedRows = $db->Affected_Rows();

            return $affectedRows >= 1;
        }

        catch(Exception $e)
        {
            throw $e;
        }

    }

}
