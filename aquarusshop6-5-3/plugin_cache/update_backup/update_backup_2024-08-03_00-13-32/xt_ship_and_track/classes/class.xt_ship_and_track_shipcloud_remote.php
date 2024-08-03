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


class xt_ship_and_track_shipcloud_remote extends ship_and_track_shipcloud
{

    private $_master_key = COL_SHIPCLOUD_LABEL_ID_PK;
    private $_pageSize = 3;

    public $position = '';
    public $url_data = array();

    function setPosition($position)
    {
        $this->position = $position;
    }

    function _getParams()
    {
        $header = array();
        $header[COL_SHIPCLOUD_LABEL_TO] = array('type' => 'hidden', 'readonly'=>true);

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

        $params['PageSize'] = $this->_pageSize;

        return $params;
    }

    public function xt_ship_and_track_shipcloud_row_fnc_1($data)
    {
        return 'result of xt_ship_and_track_shipcloud_row_fnc_1<br />data:<br />'.print_r($data,true);
    }

    public function _get($ID = 0)
    {
        if ($this->position != 'admin') return false;

        if (!$ID && !isset($this->sql_limit)) {
            $this->sql_limit = "0,25";
        }

        $defaultOrder = array(
            COL_SHIPCLOUD_LABEL_CARRIER_TRACKING_NO,
            COL_SHIPCLOUD_LABEL_CREATED_AT,
            COL_SHIPCLOUD_LABEL_TO,
            COL_SHIPCLOUD_LABEL_ID_PK
        );

        $data = array();

        if ($this->url_data['get_data'])
        {
            $page = ($this->url_data['start'] / $this->_pageSize)+1;

            $reqData = Shipcloud\ShipmentIndexRequest::create();
            $sc_data = shipcloud_api::getInstance(XT_SHIPCLOUD_API_KEY)
                ->shipmentIndex($reqData, $this->_pageSize, $page);

            $template = new Template();
            $template->getTemplatePath('address-be-list.tpl.html', 'xt_ship_and_track', '', 'plugin');

            foreach($sc_data as $k => $v)
            {
                $dateCreated = new DateTime($v->createdAt());
                $dateCreated = $dateCreated->format('Y-m-d H:i');

                $to = (array) $v->to();
                $tpl_data = array('addr' => $to);
                $to = $template->getTemplate('xt_ship_and_track', 'address-be-list.tpl.html', $tpl_data). PHP_EOL;

                $data[] =array(
                    COL_SHIPCLOUD_LABEL_ID_PK => $v->id(),
                    COL_SHIPCLOUD_LABEL_CARRIER_TRACKING_NO => $v->carrierTrackingNo(),
                    COL_SHIPCLOUD_LABEL_CREATED_AT => $dateCreated,
                    COL_SHIPCLOUD_LABEL_TO => $to
                );
            }
            $count_data = $this->_pageSize;

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
            $data = array();
        }

        if (!$this->url_data['get_data'])
        {
            // rebuilding fields' order
            $orderedData = array();
            foreach ($defaultOrder as $key) {
                $orderedData[$key] = $data[0][$key];
            }
            $data = array($orderedData);

            $count_data = 10000000;
        }

        $obj = new stdClass;
        $obj->totalCount = 10000000;//$count_data;
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
