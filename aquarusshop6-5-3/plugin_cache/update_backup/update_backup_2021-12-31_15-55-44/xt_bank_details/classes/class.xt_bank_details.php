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

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_bank_details/classes/class.bank_details.php';
require_once _SRV_WEBROOT . 'xtFramework/admin/classes/class.adminDB_DataSave.php';


class xt_bank_details extends bank_details
{

    private $_master_key = COL_BAD_BANK_DETAILS_ID_PK;
    private $_table = TABLE_BAD_BANK_DETAILS;
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
        $header[COL_BAD_BANK_DETAILS_ID_PK] = array('type' => 'textfield', 'readonly'=>true);
        $header[COL_BAD_BANK_IDENTIFIER_CODE] = array('type' => 'textfield', 'readonly'=>false);
        $header[COL_BAD_INTERNATIONAL_BANK_ACCOUNT_NUMBER] = array('type' => 'textfield', 'readonly'=>false);
        $header[COL_BAD_ACCOUNT_NAME] = array('type' => 'textfield', 'readonly'=>false);
        $header[COL_BAD_REFERENCE_NUMBER] = array('type' => 'textfield', 'readonly'=>false);

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

    public function xt_bank_details_row_fnc_1($data)
    {
        return 'result of xt_bank_details_row_fnc_1<br />data:<br />'.print_r($data,true);
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
                COL_BAD_BANK_DETAILS_ID_PK,
                COL_BAD_BANK_IDENTIFIER_CODE,
                COL_BAD_INTERNATIONAL_BANK_ACCOUNT_NUMBER,
                COL_BAD_ACCOUNT_NAME,
                COL_BAD_REFERENCE_NUMBER,
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