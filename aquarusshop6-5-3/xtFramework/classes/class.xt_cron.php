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


class xt_cron extends xt_backend_cls
{

    protected $_table = TABLE_CRON;
    protected $_table_lang = null;
    protected $_table_seo = null;
    protected $_master_key = 'cron_id';

    function _getParams()
    {


        $params = array();

        $header[$this->_master_key] = array('type' => 'hidden');
        $header['cron_type'] = array('type' => 'dropdown', 'url' => 'DropdownData.php?get=cron_type');
        $header['cron_action'] = array('type' => 'dropdown', 'url' => 'DropdownData.php?get=cron_action');
        $header['action_config'] = array('type' => 'hidden');
        $header['action_parameter'] = array('type' => 'textarea', 'cols' => 3);
        $header['last_run_date'] = array('readonly' => 1, 'type' => 'textfield');
        $header['next_run_date'] = array('readonly' => 1, 'type' => 'textfield');
        $header['hour'] = array('type' => 'dropdown', 'url' => 'DropdownData.php?get=hours');
        $header['minute'] = array('type' => 'dropdown', 'url' => 'DropdownData.php?get=minutes');

        $params['display_deleteBtn'] = true;
        $params['display_searchPanel'] = false;
        $params['display_statusTrueBtn']  = true;
        $params['display_statusFalseBtn']  = true;


        $params['header'] = $header;
        $params['master_key'] = $this->_master_key;

        $rowActions = array();

        if (!$this->url_data['edit_id'] && $this->url_data['new'] != true)
        {

            $params['include'] = array('cron_id', 'active_status', 'running_status','cron_note', 'last_run_date', 'next_run_date');

            $rowActions[] = array('iconCls' => 'cron_log', 'qtipIndex' => 'qtip1', 'tooltip' => __text('TEXT_CRON_LOG'));
                $js = "var edit_id = record.id;";

            $extF = new ExtFunctions();
            $js .= $extF->_RemoteWindow("TEXT_CRON_LOG", "TEXT_CRON_LOG", "adminHandler.php?load_section=xt_cron_log&pg=overview&cron_id='+edit_id+'", '', array(), 800, 600) . ' new_window.show();';

            $rowActionsFunctions['cron_log'] = $js;

            $rowActions[] = array('iconCls' => 'cron_start', 'qtipIndex' => 'qtip1', 'tooltip' => __text('TEXT_CRON_START'));

            $js = "
            Ext.Msg.confirm('" . __text('TEXT_START') . "','" . __text('TEXT_START_ASK') . "',function(btn){ if(btn == 'yes') runCron(record.id); })

            function runCron(edit_id)
            {
                Ext.Ajax.request({
                    url: 'adminHandler.php',
                    method: 'GET',
                    params: { load_section:'xt_cron', pg:'startManually' , 'cron_id':edit_id, 'seckey':'"._SYSTEM_SECURITY_KEY."'},
                    success: function(xhr)
                    {
                        var json = Ext.util.JSON.decode(xhr.responseText);
                        if (typeof(json)=='object' && json.success==true)
                        {
                            contentTabs.findById('node_xt_cron').getUpdater().refresh();
                            Ext.MessageBox.alert('Message', '".__text('TEXT_CRON_STARTED')."');
                        }
                        else {
                            Ext.MessageBox.alert('Message', '".__text('TEXT_ERROR_MESSAGE')."');
                        }
                    },
                    failure: function(){
                        Ext.MessageBox.alert('Message', '".__text('TEXT_ERROR_MESSAGE')."');
                    }
                });
            };
		";


            $rowActionsFunctions['cron_start'] = $js;


            $params['rowActions'] = $rowActions;
            $params['rowActionsFunctions'] = $rowActionsFunctions;
        }
        else
        {
            $params['exclude'] = array('running_status');
        }

        $params['display_checkCol']  = true;

        return $params;
    }

    function _get($ID = 0)
    {
        global $xtPlugin, $db, $language;
        $obj = new stdClass;
        if ($this->position != 'admin') return false;

        if ($ID === 'new') {
            $obj = $this->_set(array(), 'new');
            $ID = $obj->new_id;
        }

        $table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key);

        if ($this->url_data['get_data']) {
            $data = $table_data->getData();

        } elseif ($ID) {
            $data = $table_data->getData($ID);
        } else {
            $data = $table_data->getHeader();
        }

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
        global $db, $language, $filter;

        // calc next run time in saving
        if(array_key_exists('active_status', $data) && $data['active_status'] == true)
        	$data = $this->calc_next_run($data, true);
        else $data['next_run_date'] = '';

        $o = new adminDB_DataSave($this->_table, $data, false, __CLASS__);
        $obj = $o->saveDataSet();

        $obj->success = true;

        return $obj;
    }

    function _setStatus($id, $status)
    {
        global $db;
        if($status == true)
        {
            $current = $this->_get($id);
            $current = $current->data[0];
            $updated = $this->calc_next_run($current);
            $params = [1, $updated["next_run_date"]];
        }
        else {
            $params = [0, NULL];
        }
        $params[] = $id;

        $db->Execute("UPDATE ".$this->_table." SET active_status=?, next_run_date=? WHERE cron_id=?", $params);
    }

    function _unset($id = 0)
    {
        global $db,$xtPlugin;
        if ($id == 0) return false;
        if ($this->position != 'admin') return false;
        $id = (int)$id;
        if (!is_int($id)) return false;

        ($plugin_code = $xtPlugin->PluginCode('class.xt_cron.php:_unset_top')) ? eval($plugin_code) : false;
        if(isset($plugin_return_value))
            return $plugin_return_value;

        $db->Execute("DELETE FROM " . $this->_table . " WHERE " . $this->_master_key . " = ?", array($id));
    }

    function getCronTypes()
    {
        $erg = array();
        $erg[] = array('id' => 'i',
            'name' => 'Minuten',
            'desc' => ''
        );
        $erg[] = array('id' => 'h',
            'name' => 'Stunden',
            'desc' => ''
        );
        $erg[] = array('id' => 'd',
            'name' => 'Tage',
            'desc' => ''
        );
        $erg[] = array('id' => 'm',
            'name' => 'am x. des Monat',
            'desc' => ''
        );
        $erg[] = array('id' => 'w',
            'name' => 'jeden x. Tag der Woche (Montag = 1)',
            'desc' => ''
        );
        return $erg;
    }

    function getCronActions()
    {
        global $db;
        $erg = array();
        $sql = 'SELECT * FROM ' . TABLE_PLUGIN_CODE . ' WHERE hook LIKE  \'cron_action:%\' order by sortorder;';
        $arr_all = $db->getAll($sql);
        if ((is_array($arr_all)) and (count($arr_all) > 0)) {
            foreach ($arr_all as $item) {
                list(, $cron_action) = explode(':', $item['hook'], 2);
                $erg[] = array('id' => $cron_action,
                    'name' => $cron_action,
                    'desc' => ''
                );
            }
        }

        // load cron files
        $dir = _SRV_WEBROOT . _SRV_WEB_CRONJOBS;

        $d = dir($dir);
        while ($name = $d->read()) {
            if (!preg_match('/\.(php)$/', $name)) continue;

            $erg[] = array('id' => 'file:' . $name,
                'name' => $name);
        }

        return $erg;
    }


    function calc_next_run($data, $force = false)
    {
        global $db;
        $db_time = $db->GetOne('SELECT now()');
        if(!array_key_exists('next_run_date', $data)) $data['next_run_date'] = '1900-01-01 00:00:00';
        if (strtotime($data['next_run_date']) <= strtotime($db_time) || $force === true) {
            if(!array_key_exists('last_run_date', $data)) $data['last_run_date'] = '1900-01-01 00:00:00';
            $last_run = strtotime($data['last_run_date']);
            if ($last_run < time()) {
                $last_run = time();
            }
            $v = (int)$data['cron_value'];
            $h = (int)$data['hour'];
            $m = (int)$data['minute'];
            switch ($data['cron_type']) {
                case 'i' :
                    $next_run = $last_run + ($v * 60);
                    break;
                case 'h' :
                    $next_run = $last_run + ($v * 60 * 60);
                    break;
                case 'd' :
                    $next_run = strtotime('+ ' . $v . ' day', $last_run);
                    if (($h + $m) > 0) {
                        $next_run_str = date('Y-m-d', $next_run);
                        $next_run_str .= ' ' . $h . ':' . $m;
                        $next_run = strtotime($next_run_str);
                    }
                    break;
                case 'm' :
                    $next_run = strtotime('+ 1 month', $last_run);
                    $next_run_str = date('Y-m', $next_run);
                    $next_run_str .= '-' . $v;
                    if (($h + $m) > 0) {
                        $next_run_str .= ' ' . $h . ':' . $m;
                    }
                    $next_run = strtotime($next_run_str);
                    break;
                case 'w' :
                    switch ($data['cron_value']) {
                        case 0  :
                        case 7  :
                            $next_run = strtotime('next Sunday', $last_run);
                            break;
                        case 1  :
                            $next_run = strtotime('next Monday', $last_run);;
                            break;
                        case 2  :
                            $next_run = strtotime('next Tuesday', $last_run);
                            break;
                        case 3  :
                            $next_run = strtotime('next Wednesday', $last_run);
                            break;
                        case 4  :
                            $next_run = strtotime('next Thursday', $last_run);
                            break;
                        case 5  :
                            $next_run = strtotime('next Friday', $last_run);
                            break;
                        case 6  :
                            $next_run = strtotime('next Saturday', $last_run);
                            break;
                        default :
                            $next_run = $last_run + 1;
                    }

                    if (($h + $m) > 0) {
                        $next_run_str = date('Y-m-d', $next_run);
                        $next_run_str .= ' ' . $h . ':' . $m;
                        $next_run = strtotime($next_run_str);
                    }
                    break;
            }
            $data['next_run_date'] = date('Y-m-d H:i', $next_run);
        }
        return $data;
    }

    function getReqValue($name, $default = null)
    {
        if (isset($_POST[$name])) {
            return $_POST[$name];
        } else if (isset($_GET[$name])) {
            return $_GET[$name];
        } else {
            return $default;
        }
    }

    function startManually($data)
    {
        if($data['seckey']!=_SYSTEM_SECURITY_KEY) die();

        $r = new stdClass();
        $r->success = true;
        $r->msg = __text('TEXT_SUCCESS');


        try {
            $this->cron_start_by_id($data['cron_id'], true);
        }
        catch(Exception $e)
        {
            $r->success = false;
            $r->msg = $e->getMessage();
        }

        echo json_encode($r);
        die();
    }

    function cron_start_by_id($id, $calc_next_run = false)
    {
        global $db, $xtPlugin, $logHandler;

        $timer = new timer();
        $timer->_start();

        $error = false;

        $id = (int)$id;
        $sql = "SELECT * FROM " . TABLE_CRON . " WHERE cron_id ='" . $id . "'";
        $arr_cron = $db->getRow($sql);
        $arr_cron_parameter = $this->prepare_parameter($arr_cron['cron_parameter']);
        $cron_action = $arr_cron['cron_action'];

        if(empty($_REQUEST['cronstep'])) 
        {
        	$sql = 'update  '.TABLE_CRON.' set  last_run_date="'.date('Y-m-d H:i:s').'", next_run_date=NULL where cron_id =?';
        	$db->execute($sql, [$id]);
            $sql = 'update  '.TABLE_CRON.' set  running_status=1 where cron_id =?';
            $db->execute($sql, [$id]);
        }

        // check if we need to load cron file
        if (strpos($arr_cron['cron_action'], 'file:') === false) {
            if (trim($cron_action != '')) {
                ($plugin_code = $xtPlugin->PluginCode('cron_action:' . $cron_action)) ? eval($plugin_code) : false;
            }
        } else {
            $file = str_replace('file:', '', $arr_cron['cron_action']);
            if (file_exists(_SRV_WEBROOT . _SRV_WEB_CRONJOBS . $file)) {
                include _SRV_WEBROOT . _SRV_WEB_CRONJOBS . $file;
                $file_arr = explode('.', $file);
                if (class_exists('cron_' . $file_arr[1])) {
                    $classname = 'cron_' . $file_arr[1];
                    $class = new $classname;
                    $return = $class->_run($arr_cron_parameter, $id);
                    if ($return !== true) {
                        if(is_object($return))
                        {
                            if(isset($return->success) && $return->success !== true)
                            {
                                $error = true;
                                $log_data = array();
                                $log_data['error'] = isset($return->msg) ? $return->msg : '';
                                $logHandler->_addLog('error', 'cronjob', $id, $log_data);
                            }
                        }
                        else {
                        $error = true;
                        $log_data = array();
                            $log_data['error'] = '';
                        $logHandler->_addLog('error', 'cronjob', $id, $log_data);
                        }

                        $sql = 'update  '.TABLE_CRON.' set  running_status=0 where cron_id =?';
                        $db->execute($sql, [$id]);

                        $sql = 'update  ' . TABLE_CRON . ' set  last_run_date="' . date('Y-m-d H:i:s') . '" ';
                        $arr_cron = $this->calc_next_run($arr_cron);
                        $sql .= ', next_run_date=? ';
                        $sql .= ' where cron_id =? limit 1';
                        $db->execute($sql, [$arr_cron['next_run_date'], $id]);
                    }
                } else {
                    $error = true;
                    $log_data = array();
                    $log_data['error'] = 'class ' . 'cron_' . $file_arr[1] . ' not found';
                    $logHandler->_addLog('error', 'cronjob', $id, $log_data);
                }
            } else {
                $error = true;
                $log_data = array();
                $log_data['error'] = 'file not found:' .$file;
                $logHandler->_addLog('error', 'cronjob', $id, $log_data);
            }
        }

        $sql = 'update  ' . TABLE_CRON . ' set  last_run_date="' . date('Y-m-d H:i:s') . '" ';
        if ($calc_next_run == true) {
            $arr_cron = $this->calc_next_run($arr_cron);
            $sql .= ', next_run_date=? ';
        }
        $sql .= ' where cron_id =? limit 1';
        $db->execute($sql, [$arr_cron['next_run_date'], $id]);

        $runtime = $timer->_stop();
        // log succes
        if ($error === false /*&& !empty($_REQUEST['timer_total'])*/)
        {
            $runtime += $_REQUEST['timer_total'];
            $log_data = array();
            $log_data['runtime'] = $runtime;
            $logHandler->_addLog('success', 'cronjob', $id, $log_data);

            $sql = 'update  '.TABLE_CRON.' set  running_status=0 where cron_id =?';
            $db->execute($sql, [$id]);
        }
    }

    function prepare_parameter($parameter)
    {
        $tmp = $parameter;
        $tmp = str_replace("\r", ';', $tmp);
        $tmp = str_replace("\n", ';', $tmp);
        $tmp = explode(';', $tmp);
        $arr_cron_parameter = array();
        foreach ($tmp as $item) {
            list($key, $value) = explode('=', $item, 2);
            if (Trim($key) != '') {
                $arr_cron_parameter[$key] = $value;
            }
        }
        unset($tmp);
        return $arr_cron_parameter;
    }
}