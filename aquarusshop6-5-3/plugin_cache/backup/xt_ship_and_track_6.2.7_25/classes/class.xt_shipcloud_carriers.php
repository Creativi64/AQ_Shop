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

class xt_shipcloud_carriers extends xt_backend_cls {


    public $_master_key = COL_SHIPCLOUD_CARRIER_PK_ID;

    var $master_id = COL_SHIPCLOUD_CARRIER_PK_ID;

    function _getParams()
    {
        $header = array();
        $header[COL_SHIPCLOUD_CARRIER_PK_ID] = array('readonly'=>true);
        $header[COL_SHIPCLOUD_CARRIER_NAME] = array('type' => 'textfield', 'readonly'=>true);
        $header[COL_SHIPCLOUD_CARRIER_DATA.'_services'] = array('type' => 'textfield', 'readonly'=>true);
        $header[COL_SHIPCLOUD_CARRIER_DATA.'_package_types'] = array('type' => 'textfield', 'readonly'=>true);
        $header[COL_SHIPCLOUD_CARRIER_STATUS] = array('type' => 'status');

        $params = array();
        $params['header'] = $header;
        $params['master_key'] = $this->_master_key;
        $params['default_sort']   = $this->_master_key;
        $params['display_deleteBtn'] = true;
        $params['display_resetBtn'] = true;
        $params['display_editBtn'] = false;
        $params['display_newBtn'] = false;
        $params['display_searchPanel']  = false;
        $params['display_adminActionStatus']  = false;
        $params['display_checkCol']  = true;
        $params['display_statusTrueBtn']  = true;
        $params['display_statusFalseBtn']  = true;

        $js = "
            var mask = new Ext.LoadMask(Ext.getBody(), {msg:'Please wait...'});
            mask.show();
            var conn = new Ext.data.Connection();
            conn.request({
                url: 'adminHandler.php',
                method:'GET',
                params: {
                    pg:             'fetchCarriers',
                    load_section:   'xt_shipcloud_carriers',
                    plugin:         'xt_ship_and_track',
                    sec:            csrf_key
                },
                success: function(responseObject)
                {
                    record_ids = '';
                    var r = Ext.decode(responseObject.responseText);
                    if (!r.success || null != r.msg)
                    {
                        Ext.MessageBox.alert('Error', r.msg);
                    }
                    contentTabs.getActiveTab().getUpdater().refresh();
                    mask.hide();
                    Ext.MessageBox.alert('Info', 'Um Änderungen in der Liste zu übernehmen, laden Sie die Seite neu.<br/>To apply changes to the list, reload the page.');
                },
                failure: function(responseObject)
                {
                    var title = responseObject.statusText ? 'Error '+responseObject.status : 'Error ';
                    var msg = responseObject.statusText ? responseObject.statusText : 'No Details available';
                    Ext.MessageBox.alert(title,msg);
                    console.log(responseObject);
                    mask.hide();
                }
            });";

        $userButtons['SHIPCLOUD_FETCH_CARRIERS'] = array('text' => 'TEXT_SHIPCLOUD_FETCH_CARRIERS', 'style' => 'SHIPCLOUD_FETCH_CARRIERS', 'icon' => 'lorry_go.png', 'stm' => $js);
        $params['display_SHIPCLOUD_FETCH_CARRIERSBtn'] = true;
        $params['UserButtons'] = $userButtons;

        return $params;
    }

    function _get($ID = 0, $enabled = -1)
    {
        if ($this->position != 'admin') return false;

        $where = '';
        if($enabled > -1)
        {
            $where = COL_SHIPCLOUD_CARRIER_STATUS.' = '.(int) $enabled. ' ';
        }

        if (!$ID && !isset($this->sql_limit)) {
            $this->sql_limit = "0,50";
        }

        $table_data = new adminDB_DataRead(TABLE_SHIPCLOUD_CARRIERS, '', '', $this->_master_key, $where , '', '', '',  'ORDER BY '.COL_SHIPCLOUD_CARRIER_NAME. ' ');
        $table_data->_filterData = false;
        if ($this->url_data['get_data']) {
            $data = $table_data->getData();
            foreach($data as $k => &$v)
            {
                $carrier = shipcloud_helper::clzFactory('carrier', $v[COL_SHIPCLOUD_CARRIER_DATA]);
                $v[COL_SHIPCLOUD_CARRIER_DATA.'_services'] = implode('<br/>', $carrier->getServices());
                $v[COL_SHIPCLOUD_CARRIER_DATA.'_package_types'] = implode('<br/>', $carrier->getPackageTypes());
                $v[COL_SHIPCLOUD_CARRIER_NAME] = $carrier->getDisplayName();
                $v['code'] = $carrier->getName();
            }
        }
        elseif($ID==='new'){
            $defaultOrder = array(
            );

            $orderedData = array();
            foreach ($defaultOrder as $key) {
                $orderedData[$key] = '';
            }
            $data = array($orderedData);
        }
        elseif($ID) {
            $data = $table_data->getData($ID);
            $defaultOrder = array(
                COL_SHIPCLOUD_CARRIER_PK_ID,
                COL_SHIPCLOUD_CARRIER_NAME,
                COL_SHIPCLOUD_CARRIER_DATA.'_services',
                COL_SHIPCLOUD_CARRIER_DATA.'_package_types',
                COL_SHIPCLOUD_CARRIER_STATUS
            );

            $orderedData = array();
            foreach ($defaultOrder as $key) {
                $orderedData[$key] = $data[0][$key];
            }
            $
            $data = array($orderedData);

        } else {
            $data = $table_data->getHeader();
            $defaultOrder = array(
                COL_SHIPCLOUD_CARRIER_PK_ID,
                COL_SHIPCLOUD_CARRIER_NAME,
                COL_SHIPCLOUD_CARRIER_DATA.'_services',
                COL_SHIPCLOUD_CARRIER_DATA.'_package_types',
                COL_SHIPCLOUD_CARRIER_STATUS
            );
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
        $o = new adminDB_DataSave(TABLE_SHIPCLOUD_CARRIERS, $data, false, __CLASS__);
        try {
            $result = $o->saveDataSet();
        }
        catch(Exception $e){
            return false;
        }

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
            "update " . TABLE_SHIPCLOUD_CARRIERS . " set " . COL_SHIPCLOUD_CARRIER_STATUS . " = ? where ".$this->_master_key." = ?",
            array((int)$status, (int)$id)
        );
    }

    function fetchCarriers($data)
    {
        global $db;
        $r = new stdClass();
        $r->success = true;
        try {
            $sc = shipcloud_api::getInstance(XT_SHIPCLOUD_API_KEY)->getCarriers();


            $this->url_data['get_data'] = true;
            $current_carriers = $this->_get()->data;

            $current_carriers_2 = array();
            foreach($current_carriers as $k => &$v)
            {
                $current_carriers_2[$v['code']] = $v;
            }
            unset($current_carriers);
            $count = $db->GetOne("SELECT count(*) FROM ".TABLE_SHIPCLOUD_CARRIERS);
            $db->Execute("TRUNCATE ".TABLE_SHIPCLOUD_CARRIERS);
            $db->Execute("ALTER TABLE ".TABLE_SHIPCLOUD_CARRIERS." AUTO_INCREMENT = 1");
            foreach($sc as $c)
            {
                $c = shipcloud_helper::dismount($c);
                if($count==0 ||
                    (
                        array_key_exists($c['name'], $current_carriers_2)
                        && $current_carriers_2[$c['name']][COL_SHIPCLOUD_CARRIER_STATUS]
                    )
                )
                {
                    $c[COL_SHIPCLOUD_CARRIER_STATUS] = 1;
                }
                $c['shipcloud_carrier_name'] = $c['name'];
                $c['shipcloud_carrier_data'] = json_encode($c);

                $this->_set($c, 'new');
            }
        }
        catch(Exception $e)
        {
            $r->success = false;
            $r->msg = $e->getMessage();
        }

        echo json_encode($r);
        die();
    }


    /**
     *
     * @return array die verfügbaren shipcloud versender (dhl,tnt,...)
     */
    public static function getShipcloudCarriers($enabled = -1)
    {
        global $db;

        $sc = new xt_shipcloud_carriers();
        $sc->setPosition('admin');
        $sc->url_data['get_data'] = true;
        $xt_data = $sc->_get(0, $enabled);
        $data = array();

        foreach ($xt_data->data as $k => $c)
        {
            $data[] =  array(
                'id' => $c['code'],
                'name' => $c[COL_SHIPCLOUD_CARRIER_NAME]
            );
        }
        if(count($data) == 0)
        {
            $data[] =  array(
                'id' => 0,
                'name' => __define('TEXT_SHIPCLOUD_NO_ACTIVE_CARRIER')
            );
        }

        return $data;
    }

}