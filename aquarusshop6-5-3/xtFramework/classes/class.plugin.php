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

include_once _SRV_WEBROOT . 'xtFramework/library/phpxml/xml.php';

class plugin extends xt_backend_cls
{

    public $master_id = 'plugin_id';
    public $debug_output = '';

    const MODE_INSERT = 'insert';
    const MODE_UPDATE = 'update';

    const PLUGIN_UPDATE_SERVICE = 'https://webservices.xt-commerce.com/';
    const PLUGIN_DIRECTORY = _SRV_WEBROOT . 'plugins/';
    const PLUGIN_DOWNLOAD_PATH = _SRV_WEBROOT . 'plugin_cache/download/';
    const ALLOW_ONLINE_UPDATE = true;
    const FILE_PERMISSIONS = 0777;
    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;
    /**
     * @var int
     */
    public $plugin_id;

    function __construct($plugin_id = '')
    {
        global $db;
        if ($plugin_id != '')
        {
            $this->plugin_id = (int)$plugin_id;
            $this->mode = 'update';

            $rs = $db->Execute(
                "SELECT * FROM " . TABLE_PLUGIN_PRODUCTS . " WHERE plugin_id=?",
                array((int)$this->plugin_id)
            );
            $this->data = $rs->fields;
        }
        else
        {
            $this->mode = 'insert';
            $this->data = array();
        }
    }

    public function _initiate() {

        $this->client = new GuzzleHttp\Client ( [
            'base_uri' => self::PLUGIN_UPDATE_SERVICE,
            /*
            'auth' => [
                $this->lic_key,
                $this->lic_email
            ],
            */
            'http_errors' => false,
            'connect_timeout' => 5,
            'timeout' => 5
        ] );

    }

    function _getParams()
    {
    }

    function savePlugin($data, $update_version = true)
    {
        global $db, $filter;

        $input_data = array();
        $input_data['name'] = $filter->_filter($data['plugin_name']);
        if ($update_version)
        {
            $input_data['version'] = $filter->_filter($data['plugin_version']);
        }
        $input_data['description'] = $filter->_filter($data['plugin_description']);
        $input_data['url'] = $filter->_filter($data['plugin_url']);
        $input_data['developer'] = $filter->_filter($data['developer']);
        $input_data['documentation_link'] = $filter->_filter($data['documentation_link']);
        $input_data['marketplace_link'] = $filter->_filter($data['marketplace_link']);

        $input_data['code'] = $filter->_filter($data['plugin_code']);
        $input_data['type'] = $filter->_filter($data['type']);
        if (isset($data['plugin_active']))
        {
            $input_data['plugin_status'] = $filter->_filter($data['plugin_active']);
        }

        $pluginId = $this->checkInstall($data['plugin_code']);
        if (!$pluginId)
        {
            $db->AutoExecute(TABLE_PLUGIN_PRODUCTS, $input_data);
            $pluginId = $db->Insert_ID();
            $plg_sql = array(
                'install' => $data['db_install'],
                'uninstall' => $data['db_uninstall'],
                'plugin_id' => $pluginId,
                'version' => $input_data['version']
            );
            $db->AutoExecute(TABLE_PLUGIN_SQL, $plg_sql);
        }
        else
        {
            $db->AutoExecute(TABLE_PLUGIN_PRODUCTS, $input_data, 'UPDATE', "plugin_id = '" . (int)$pluginId . "'");
            $plg_sql = array(
                'install' => $data['db_install'],
                'uninstall' => $data['db_uninstall'],
                'version' => $filter->_filter($data['plugin_version'])//$input_data['version']
            );
            $db->AutoExecute(TABLE_PLUGIN_SQL, $plg_sql, 'UPDATE', "plugin_id = '" . (int)$pluginId . "'");
        }

        return $pluginId;

    }

    /**
     * unserialize XML to Standard Array
     *
     * @param String $xml
     * @return unknown
     */
    function xmlToArray($xml)
    {

        $xml = file_get_contents($xml);
        return XML_unserialize($xml);
    }

    /**
     * Get Installed Plugins
     *
     * @return array
     */
    function getInstalledPlugins()
    {
        global $db;

        $rs = $db->Execute("SELECT * FROM " . TABLE_PLUGIN_PRODUCTS);
        $arr = array();
        while (!$rs->EOF)
        {

            $arr[] = $rs->fields;
            $rs->MoveNext();
        }
        return $arr;

    }

    function setPluginConfig($data)
    {
        global $db, $store_handler, $filter, $xtPlugin;

        if ($this->position != 'admin')
        {
            return false;
        }

        ($plugin_code = $xtPlugin->PluginCode('class.plugin.php:setPluginConfigTop')) ? eval($plugin_code) : false;
        $plugin_id = (int)$data['plugin_id'];
        if (!is_int($plugin_id))
        {
            return false;
        }

        $stores = $store_handler->getStores();

        foreach ($stores as $sdata)
        {
            $store_names[] = $sdata['text'];
            $query = "SELECT * FROM " . TABLE_PLUGIN_CONFIGURATION . " where plugin_id = ? and shop_id=?";
            $record = $db->Execute($query, array($plugin_id, $sdata['id']));

            while (!$record->EOF)
            {
                $conf_value = $data['conf_' . $record->fields['config_key'] . '_shop_' . $sdata['id']];
                $db->Execute(
                    "UPDATE " . TABLE_PLUGIN_CONFIGURATION . " SET config_value=? WHERE plugin_id=? and shop_id=? and config_key=?",
                    array($conf_value, $plugin_id, $sdata['id'], $record->fields['config_key'])
                );
                $record->MoveNext();
            }
        }

        ($plugin_code = $xtPlugin->PluginCode('class.plugin.php:setPluginConfig')) ? eval($plugin_code) : false;
        return true;
    }

    function getConfigHeaderData()
    {
        global $db, $store_handler, $xtPlugin;

        $stores = $store_handler->getStores();
        $header = array();
        $grouping = array();
        // query config_payment
        foreach ($stores as $sdata)
        {

            $store_names[] = 'SHOP_' . $sdata['id'];

            ($plugin_code = $xtPlugin->PluginCode(__CLASS__ . ':getConfigHeaderData_top')) ? eval($plugin_code) : false;

            $query = "SELECT p.*,pp.code FROM " . TABLE_PLUGIN_CONFIGURATION . " p INNER JOIN " . TABLE_PLUGIN_PRODUCTS . " pp ON pp.plugin_id = p.plugin_id
			         where p.plugin_id = ? and p.shop_id=?";
            $record = $db->Execute($query, array($this->_plugin_id, $sdata['id']));

            while (!$record->EOF)
            {
                $type = '';
                $required = true;
                if ($record->fields['config_value'] == 'true' || $record->fields['config_value'] == 'false')
                {
                    $type = 'truefalse';
                }

                if ($record->fields['type'])
                {
                    $type = $record->fields['type'];
                }

                if ($record->fields['type'] == 'dropdown')
                {
                    if (strstr($record->fields['url'], 'status:'))
                    {
                        $record->fields['url'] = str_replace('status:', '', $record->fields['url']);
                        $url = 'DropdownData.php?systemstatus=' . $record->fields['url'];
                    }
                    else
                    {
                        $url = 'DropdownData.php?get=' . $record->fields['url'] . '&plugin_code=' . $record->fields['code'];
                    }
                }
                else
                {
                    $url = $record->fields['url'];
                }

                $required = false;

                $groupingPosition = 'SHOP_' . $sdata['id'];
                $grouping['conf_' . $record->fields['config_key'] . '_shop_' . $sdata['id']] = array('position' => $groupingPosition);
                // set header data
                $header['conf_' . $record->fields['config_key'] . '_shop_' . $sdata['id']] = $tmp_data = array(
                    'name' => 'conf_' . $record->fields['config_key'] . '_shop_' . $sdata['id'],
                    'text' => __define($record->fields['config_key'] . '_TITLE'),
                    'masterkey' => false,
                    'lang' => false,
                    'value' => _filterText($record->fields['config_value'], $type == 'textarea' ? 'notfull' : 'full'),
                    'hidden' => false,
                    'min' => null,
                    'max' => null,
                    'readonly' => false,
                    'required' => $required,
                    'type' => $type,
                    'url' => $url,
                    'renderer' => null
                );

                $record->MoveNext();
            }
            $record->Close();
        }

        $panelSettings[] = array('position' => 'store_settings', 'text' => __text('TEXT_EXPORT_SETTINGS'), 'groupingPosition' => $store_names);
        return array('header' => $header, 'panelSettings' => $panelSettings, 'grouping' => $grouping);
    }


    function getPluginCodeList($data)
    {
        global $db;

        if (isset($data['edit_id']))
        {
            $limit = " LIMIT " . (int)$data['start'] . ", " . (int)$data['limit'];
            $rs = $db->Execute(
                "SELECT * FROM " . TABLE_PLUGIN_CODE . " WHERE plugin_id=?" . $limit,
                array((int)$data['edit_id'])
            );
            $arr = array();
            while (!$rs->EOF)
            {

                $arr[] = $rs->fields;
                $rs->MoveNext();
            }
            return $arr;
        }

    }

    function getPluginCode($id)
    {
        global $db;

        if ($id > 0)
        {
            $rs = $db->Execute("SELECT * FROM " . TABLE_PLUGIN_CODE . " WHERE id=?", array((int)$id));
            return $rs->fields;
        }
    }

    function getPluginName($id)
    {
        global $db;

        if ($id)
        {
            $rs = $db->Execute("SELECT code FROM " . TABLE_PLUGIN_PRODUCTS . " WHERE plugin_id=?", array((int)$id));
            return $rs->fields['code'];
        }
    }

    function getPluginVersion($pluginCode)
    {
        global $db;

        if ($pluginCode)
        {
            $rs = $db->Execute("SELECT version FROM " . TABLE_PLUGIN_PRODUCTS . " WHERE code=?", array($pluginCode));
            return $rs->fields['version'];
        }
        return false;
    }


    function getFilePlugins($plg_code = '')
    {
        global $db;

        // get installed plugins
        $installed = $this->getInstalledPlugins();
        $plugins_installed = array();
        if (count($installed) > 0)
        {
            foreach ($installed as $key => $val)
            {
                $plugins_installed[] = $val['code'];
            }

        }

        $plugins = array();
        if ($dir = opendir(_SRV_WEBROOT . 'plugins/'))
        {
            while (($f = readdir($dir)) !== false)
            {
                if ((is_dir($f) || !stristr($f, ".")) && $f != "." && $f != "..")
                {
                    if ($f != 'payment' && $f != '.svn')
                    {
                        $dirs[] = $f;
                    }
                }
            } // while
            closedir($dir);
        }
        $i = 0;

        if ($plg_code)
        {
            unset($dirs);
            $dirs[] = $plg_code;
        }

        foreach ($dirs as $key => $val)
        {
            $file = _SRV_WEBROOT . 'plugins/' . $val . '/installer/' . $val . '.xml';
            if (is_file($file))
            {
                //$xml = file_get_contents($file);
                $xml = $this->xmlToArray($file);

                if (!in_array($xml['xtcommerceplugin']['code'], $plugins_installed))
                {
                    $icon = '';
                    if ($xml['xtcommerceplugin']['icon'] != '')
                    {
                        $icon = '../plugins/' . $val . '/images/' . $xml['xtcommerceplugin']['icon'];
                    }
                    $plugins[$i] = array('payment_icon' => $icon, 'title' => $xml['xtcommerceplugin']['title'], 'url' => $xml['xtcommerceplugin']['url'], 'code' => $xml['xtcommerceplugin']['code'], 'description' => $xml['xtcommerceplugin']['description'], 'icon' => $xml['xtcommerceplugin']['icon'], 'type' => $xml['xtcommerceplugin']['type'], 'version' => $xml['xtcommerceplugin']['version']);
                    $plugins[$i]['file'] = $val;
                    $i++;
                }
            }
        }

        usort($plugins, array($this, "sortByType"));

        return $plugins;
    }

    function sortByType($a, $b)
    {
        if (strcmp($a['type'], $b['type']) == 0)
        {
            return 0;
        }
        return (strcmp($a['type'], $b['type']) < 0 ? -1 : 1);
    }

    function insertHook($data, $mode)
    {
        global $db, $filter;

        $insert_array = array();
        $hook_id = (int)$data['edit_id'];
        $insert_array['title'] = $filter->_filter($data['title']);
        $insert_array['hook'] = $filter->_filter($data['hook']);
        $insert_array['code'] = stripslashes($data['code']);
        $insert_array['sortorder'] = $filter->_filter($data['sortorder']);

        $insert_array['plugin_status'] = $db->GetOne("SELECT plugin_status FROM ".TABLE_PLUGIN_PRODUCTS." WHERE plugin_id=?", array($insert_array['plugin_id'])) ? 1 : 0;

        if ($mode == 'insert')
        {
            $insert_array['plugin_id'] = (int)$data['plugin_id'];
            $db->AutoExecute(TABLE_PLUGIN_CODE, $insert_array);
        }
        else
        {
            $db->AutoExecute(TABLE_PLUGIN_CODE, $insert_array, 'UPDATE', "id = '" . (int)$hook_id . "'");
        }
    }

    /**
     * delete plugin, also check for payment entries if check = true
     *
     * @param unknown_type $plugin_id
     * @param unknown_type $check
     */
    function DeletePlugin($plugin_id, $check = true)
    {
        global $db, $xtPlugin;
        GLOBAL $ADODB_THROW_EXCEPTIONS;
        $ADODB_THROW_EXCEPTIONS = true;

        ($plugin_code = $xtPlugin->PluginCode('class.plugin.php:DeletePlugin_top')) ? eval($plugin_code) : false;
        if(isset($plugin_return_value))
            return $plugin_return_value;

        $query = "SELECT * FROM " . TABLE_PLUGIN_PRODUCTS . " where plugin_id = ?";
        $res = $db->Execute($query, array($plugin_id));
        if ($res->RecordCount() > 0)
        {
            $urs = $db->Execute("SELECT * FROM " . TABLE_PLUGIN_SQL . " where plugin_id=? ORDER BY created ASC", array($plugin_id));

            if (array_key_exists('deleteSql', $this->url_data) && $this->url_data['deleteSql'] == 'true' && _SYSTEM_DEBUG_FINAL == 'true')
            {
                // perform uninstall
                while (!$urs->EOF)
                {
                    if ($urs->fields['uninstall'] != '')
                    {
                        $pluginCeckSQL = new pluginCheckSQL($urs->fields['uninstall']);
                        $uninstall = $pluginCeckSQL->check();
                        eval($uninstall);
                    }
                    $urs->MoveNext();
                }
            }
            else
            {
                while (!$urs->EOF)
                {
                    if ($urs->fields['uninstall'] != '')
                    {
                        $uninstall = $urs->fields['uninstall'];
                        eval($uninstall);
                    }
                    $urs->MoveNext();
                }
            }

            if ($res->fields['code'] != '')
            {
                $db->Execute(
                    "DELETE FROM " . TABLE_LANGUAGE_CONTENT . " WHERE plugin_key=?",
                    array($res->fields['code'])
                );

                // remove cronjobs
                $file = _SRV_WEBROOT.'plugins/'.$res->fields['code'].'/installer/'.$res->fields['code'].'.xml';
                if (is_file($file))
                {
                    $xml_data = $this->xmlToArray($file);
                    if (array_key_exists('cronjobs', $xml_data['xtcommerceplugin']) && is_array($xml_data['xtcommerceplugin']['cronjobs']) && is_array($xml_data['xtcommerceplugin']['cronjobs']['cronjob']))
                    {
                        if(array_key_exists('cron_action', $xml_data['xtcommerceplugin']['cronjobs']['cronjob']))
                        {
                            $jobs = $xml_data['xtcommerceplugin']['cronjobs'];
                        }
                        else {
                            $jobs = $xml_data['xtcommerceplugin']['cronjobs']['cronjob'];
                        }

                        foreach ($jobs as $cj)
                        {
                            $cj_file = explode(':', $cj['cron_action']);
                            if (count($cj_file) > 1)
                            {
                                $cj_file = $cj_file[1];

                                $ftarget = _SRV_WEBROOT . _SRV_WEB_CRONJOBS . $cj_file;
                                if (file_exists($ftarget))
                                {
                                    unlink($ftarget);
                                }
                                $db->Execute("DELETE FROM " . TABLE_CRON . " WHERE cron_action = ?", array($cj['cron_action']));
                            }
                        }
                    }
                }
            }

            $xtPlugin->_MultiDeleteHookFiles($plugin_id);

            $db->Execute("DELETE FROM " . TABLE_PLUGIN_CODE . " WHERE plugin_id=?", array($plugin_id));
            $db->Execute("DELETE FROM " . TABLE_PLUGIN_PRODUCTS . " WHERE plugin_id=?", array($plugin_id));
            $db->Execute("DELETE FROM " . TABLE_PLUGIN_CONFIGURATION . " WHERE plugin_id=?", array($plugin_id));
            $db->Execute("DELETE FROM " . TABLE_PLUGIN_SQL . " WHERE plugin_id=?", array($plugin_id));

            if ($check == true)
            {
                $payment_id = $db->GetOne("SELECT payment_id FROM " . TABLE_PAYMENT . " WHERE plugin_installed=?", array($plugin_id));
                if ($payment_id)
                {
                    $payment = new payment();
                    $payment->setPosition($this->position);
                    $payment->cleanupData($payment_id);
                }
            }

            xt_cache::deleteCache('language_content');
            $db->cacheFlush();
        }
    }

    /*Adapt plugin update version to have 2 digits
     * inbetween the dots and casr to int*/
    /*
    function adaptPluginUpdateVersion($version){
        $v = explode(".",$version);
        $final = '';
        for($i=0;$i<count($v);$i++){
            if (strlen((string)$v[$i])==1){
                $final .= '0'.$v[$i];  // adding leadig 0
            }else if (strlen((string)$v[$i])==2){
                $final .= $v[$i];
            }
        }
        return (int)$final;
    }
    */

    function InstallPlugin($xml, $output_res = 'true', $plugin_id = '')
    {
        global $db, $filter, $store_handler, $language, $logHandler, $xtPlugin;

        GLOBAL $ADODB_THROW_EXCEPTIONS;
        $ADODB_THROW_EXCEPTIONS = true;

        $product_id = false;
        try
        {

        $xml_data = $this->xmlToArray($xml);
        $plugin = $xml_data['xtcommerceplugin'];

        $stores = $store_handler->getStores();
        if ($plugin_id != '')
        {
            $_GET['plugin_id'] = $plugin_id;
        }
        $get_current_plg_version = 0;
        if(array_key_exists('plugin_id', $_GET)) $get_current_plg_version = $this->getPluginVersion($modul = $this->getPluginName($_GET['plugin_id']));

        $output = "";

        if (isset($_GET['pluginHistoryId']))
        {
            $this->mode = 'update';
        }

        $obj = new stdClass;
        $_plugin_code = $filter->_filter($plugin['code']);

        //check if plugin is compatible with the current shop version
        if (isset($plugin['minimumshopversion']))
        {
            if (version_compare($plugin['minimumshopversion'], _SYSTEM_VERSION)  > 0)
            {
                $output .= '<ul class="stack">';
                $output .= '<li class="error"><b>' . __text('TEXT_ERROR_MINIMUM_SHOP_VERSION') . ' ' . $plugin['minimumshopversion'] . '</b></li>';

                $output .= '</ul>';
                return $output;
            }
        }

        global $is_pro_version;
        $plgLicType = $this->getRequiredStoreLicenseTypeFromXml($xml);
        if ($is_pro_version && $plgLicType == 'FREE')
        {
            $error = sprintf(__text('TEXT_ERROR_PLUGIN_ONLY_IN_FREE_VERSION'));
            $output .= '<ul class="stack">';
            $output .= '<li class="error"><b>' . $error . '</b></li>';

            $output .= '</ul>';
            return $output;
        }
        else if (!$is_pro_version && $plgLicType == 'PRO')
        {
            $error = sprintf(__text('TEXT_ERROR_PLUGIN_ONLY_IN_PRO_VERSION'));
            $output .= '<ul class="stack">';
            $output .= '<li class="error"><b>' . $error . '</b></li>';

            $output .= '</ul>';
            return $output;
        }

        // check if plugin requires a license file
        if (isset($plugin['require_license_file']) && $plugin['require_license_file'] == 'true')
        {

            $_pass = true;
            $output .= '<ul class="stack">';
            $_files = '';

            $lic_file = $filter->_filter($plugin['license_file']);
            $purchase_link = $filter->_filter($plugin['marketplace_link']);
            if (!file_exists(_SRV_WEBROOT . 'lic/' . $lic_file))
            {
                $_pass = false;
                $_files .= '<li class="plugin_lic_missing">' . __text('ERROR_MISSING_PLG_LIC_FILE') . ' <a href="' . $purchase_link . '" target="_new">' . __text('TEXT_PLG_BUY_LIC') . '</a></li>';
            }


            if (!$_pass)
            {
                $output .= '<li class="error"><b>' . __text('TEXT_INSTALLATION_FAIL_LICENSE') . '</b></li>';
                $output .= $_files;
                $output .= '</ul>';
                return $output;
            }
            $output = '';

        }

        // check if plugin requires ioncube encoder, also check for encoder version if given
        if (isset($plugin['ioncube']) && $plugin['ioncube'] == 'required') {

            $ioncube = $this->getIonCubeVersion();

            if (!$ioncube) {
                $output .= '<li class="error"><b>';
                $output .= __text('TEXT_IONCUBE_NOT_FOUND');
                $output .= '</b></li>';
                $output .= '</ul>';
                return $output;
            } else {
                if (isset($plugin['ioncube_minversion'])) {
                    if (version_compare($plugin['ioncube_minversion'],$ioncube) >= 0) {
                        $output .= '<li class="error"><b>';
                        $output .= sprintf(__text('TEXT_IONCUBE_MIN_VERSION_TO_LOW'), $ioncube, $plugin['ioncube_minversion']);
                        $output .= '</b></li>';
                        $output .= '</ul>';
                        return $output;
                    }
                }
            }
        }


        if (($this->mode == 'update') && isset($plugin['minimumupdateversion']))
        {
            $min_update_version = (int)str_replace(".", "", $plugin['minimumupdateversion']);
            $current = (int)str_replace(".", "", $get_current_plg_version);
            if ($current < $min_update_version)
            {

                $output .= '<li class="error"><b>';
                $output .= sprintf(__text('TEXT_UPDATE_VERSION_MIN_INFO'), $get_current_plg_version, $plugin['minimumupdateversion']);
                $output .= '</b></li>';
                $output .= '</ul>';
                return $output;
            }
        }

        // check if we need some writeable dirs, abort installation if not writeable
        if (array_key_exists('permission_check', $plugin) && is_array($plugin['permission_check']) or isset($plugin['permission_check']))
        {

            $_pass = true;
            $output .= '<ul class="stack">';
            $_files = '';

            if (isset($plugin['permission_check']['resource']) && !is_array($plugin['permission_check']['resource']))
            {
                $_tmp = $plugin['permission_check']['resource'];
                $plugin['permission_check'] = array();
                $plugin['permission_check'][] = $_tmp;
            }
            else
            {
                $plugin['permission_check'] = $plugin['permission_check']['resource'];
            }

            foreach ($plugin['permission_check'] as $key => $resource)
            {

                if (!is_writeable(_SRV_WEBROOT . $resource))
                {
                    $_pass = false;
                    $_files .= '<li class="plugin_config_add">!ERROR, NOT WRITEABLE! ' . $filter->_filter($resource) . '</li>';
                }
            }

            if (!$_pass)
            {
                $output .= '<li class="error"><b>' . __text('TEXT_INSTALLATION_FAIL') . '</b></li>';
                $output .= $_files;
                $output .= '</ul>';
                return $output;
            }
            $output = '';
        }

        // check if plugin requires other plugins to be installed first
        if (array_key_exists('required_plugins', $plugin) && is_array($plugin['required_plugins']) or isset($plugin['required_plugins']))
        {

            $_pass = true;
            $output .= '<ul class="stack">';
            $_files = '';

            if (isset($plugin['required_plugins']['plugin']) && !is_array($plugin['required_plugins']['plugin']))
            {
                $_tmp = $plugin['required_plugins']['plugin'];
                $plugin['required_plugins'] = array();
                $plugin['required_plugins'][] = $_tmp;
            }
            else
            {
                if ((is_array($plugin['required_plugins']['plugin']) &&
                        count($plugin['required_plugins']['plugin']) == 1) ||
                    isset($plugin['required_plugins']['plugin']['version'])
                )
                {
                    $plugin['required_plugins'] = $plugin['required_plugins'];
                }
                else
                {
                    $plugin['required_plugins'] = $plugin['required_plugins']['plugin'];
                }
            }

            foreach ($plugin['required_plugins'] as $key => $resource)
            {
                if (!is_array($resource))
                {
                    $check_plugin = $filter->_filter($resource);

                    if (!$this->checkInstall($check_plugin))
                    {
                        $_pass = false;
                        $_files .= '<li class="plugin_config_add">' . $filter->_filter($check_plugin) . '</li>';
                    }
                }
                else
                {
                    $check_plugin = $filter->_filter($resource['code']);

                    $required_version = array_key_exists('version', $resource) ? $resource['version'] : false;
                    if (!$this->checkInstall($check_plugin, $required_version))
                    {
                        $_pass = false;
                        $_files .= '<li class="plugin_config_add">' . $filter->_filter($check_plugin) . ' version >= ' . $resource['version'] . '</li>';
                    }
                }
            }

            if (!$_pass)
            {
                $output .= '<li class="error"><b>' . __text('TEXT_INSTALLATION_FAIL_REQUIRED_PLUGINS') . '</b></li>';
                $output .= $_files;
                $output .= '</ul>';
                return $output;
            }
            $output = '';
        }

        // TODO check if plugin requires special php version or loader version
        $data = array();
        $data['plugin_name']        = $filter->_filter($plugin['title']);
        $data['plugin_version']     = $filter->_filter($plugin['version']);
        $data['plugin_description'] = array_key_exists('description', $plugin)        ? $filter->_filter($plugin['description']) : '';
        $data['type']               = array_key_exists('type', $plugin)               ? $filter->_filter($plugin['type']) : '';
        $data['plugin_url']         = array_key_exists('url', $plugin)                ? $plugin['url'] : '';
        $data['db_install']         = array_key_exists('db_install', $plugin)         ? $plugin['db_install'] : '';
        $data['db_uninstall']       = array_key_exists('db_uninstall', $plugin)       ? $plugin['db_uninstall'] : '';
        $data['developer']          = array_key_exists('developer', $plugin)          ? $plugin['developer'] : '';
        $data['documentation_link'] = array_key_exists('documentation_link', $plugin) ? $plugin['documentation_link'] : '';
        $data['marketplace_link']   = array_key_exists('marketplace_link', $plugin)   ? $plugin['marketplace_link'] : '';
        $data['plugin_code'] = $_plugin_code;
        $data['plugin_active'] = '0';

        ($plugin_code = $xtPlugin->PluginCode('class.plugin.php:InstallPlugin_data')) ? eval($plugin_code) : false;

        $pluginCeckSQL = new pluginCheckSQL($data['db_install']);
        if ($this->checkInstall($data['plugin_code']))
        {
            $pluginCeckSQL->mode = 'update';
        }

        $data['run_db_stmt'] = $pluginCeckSQL->check();
        $data_update = $data;
        if ($this->mode == 'update')
        {
            $save_plugin_version = false;
        }
        else
        {
            $save_plugin_version = true;
        }
        $product_id = $this->savePlugin($data, $save_plugin_version);
            $pli = new plugin_installed();
            $pli->_setStatus($product_id, $data['plugin_active'], true);


        // payment Module ?
        if ($plugin['type'] == 'payment' && array_key_exists('payment', $plugin))
        {
            $create_order_status_new = true;
            if(array_key_exists('create_order_status_new', $plugin['payment']) && $plugin['payment']['create_order_status_new'] == 'false') $create_order_status_new = false;
            $payment_id = $this->installpayment($plugin, $product_id, $_plugin_code);
        }

        if (isset($_GET['pluginHistoryId']) || ($this->mode == 'update')) // if pluginHistoryId is set or mode is update => install db_update script and skipp db_install
        {
            //$get_current_plg_version = (int)str_replace(".", "", $get_current_plg_version);
            //$get_current_plg_version = $this->adaptPluginUpdateVersion($get_current_plg_version);
            $save_plugin_version = true;
            if (isset($plugin['db_update']) && isset($plugin['db_update']['update']))
            {
                $update_script = $plugin['db_update']['update'];

                if (is_array($update_script[0]))  // more than one script to execute
                {

                    foreach ($update_script as $ar)
                    {
                        $up_v = $ar["version"];//$this->adaptPluginUpdateVersion($ar["version"]);
                        $db_update_script[$up_v] = array("version" => $ar["version"], "to_version" => $ar["to_version"], "code" => $ar["code"], "messages" => array_key_exists('messages', $ar) ? $ar["messages"] : []);
                    }
                }
                else if(!empty($update_script["version"]))
                {
                    $up_v = $update_script["version"];//$this->adaptPluginUpdateVersion($update_script["version"]);
                    $db_update_script[$up_v] = array("version" => $update_script["version"], "to_version" => $update_script["to_version"], "code" => $update_script["code"], "messages" =>  array_key_exists('messages', $update_script) ? $update_script["messages"] : [] );
                }

                if(count($db_update_script))
                {
                    uksort($db_update_script, array($this, 'sortVersion'));
                    $min_update_script = "true";

                    foreach ($db_update_script as $k => $v)
                    {
                        $save_plugin_version = false;
                        /*
                        //if (($get_current_plg_version<$k) && $min_update_script=="true"){
                        if ((version_compare($get_current_plg_version, $k)) == -1 && $min_update_script == "true")
                        {
                            $data_update['plugin_version'] = $get_current_plg_version;
                            if ($output_res == 'true')
                            {
                                echo "<br /><b style='color:red;'>";
                                printf(__text('TEXT_UPDATE_PLUGIN_MIN_UPDATE_SCRIPT'), $v["version"]);
                                echo "</b>";
                            }
                            $min_update_script = "false";
                        }
                        */

                        if (version_compare($get_current_plg_version, $k) < 1)
                        {
                            /*  imo is das obsolet bzw geht so nicht mehr und erzeugt falsche fehler
                            wenn es einen fehler gibt, wird der angezeigt
                            $log_data = array();
                            $log_data['message'] = 'error executing update database script from v. ' . $v["version"] . ' to ' . $v["to_version"];
                            $log_data['time'] = time();
                            $logHandler->_addLog('error', __CLASS__ . '_updater_' . $_plugin_code, $_GET["plugin_id"], $log_data);
                            */

                            $code = trim($v["code"]);
                            if ($code != '')
                            {
                                eval($code);
                                $msg = __text('TEXT_UPDATE_PLUGIN_UPDATED_SCRIPT');
                            }
                            else
                            {
                                $msg = __text('TEXT_UPDATE_PLUGIN_UPDATED_SCRIPT_SKIPPED');
                            }

                            if ($output_res == 'true')
                            {
                                echo "<br /><b>";
                                printf($msg, $v["version"], $v["to_version"]);
                                echo "</b>";
                            }

                            //if ((int)str_replace(".", "", $v["to_version"]) <=(int)str_replace(".", "", $data['plugin_version']))
                            if (version_compare($data['plugin_version'], $v["to_version"] <= 0))
                            {
                                $get_current_plg_version = $v["to_version"];//$this->adaptPluginUpdateVersion($v["to_version"]);
                            }

                            $this->clearErrorLog($_GET["plugin_id"]); // delete the pre-logged error in logs
                        }
                        $save_plugin_version = true; // save_plugin_version is to ensure all db_update scripts successed
                    }
                }

                $install_messages = $db_update_script[$k]['messages'];
                if (
                    isset($install_messages) &&
                    isset($install_messages['post']) && is_array($install_messages['post'])
                )
                {
                    if (isset($install_messages['post'][$language->content_language]))
                    {
                        $msg = $install_messages['post'][$language->content_language];
                    }
                    else
                    {
                        reset($install_messages['post']);
                        $msg = $install_messages['post'][key($install_messages['post'])];
                    }

                    $output .= " <div style='padding:10px; border:1px solid #005da2; background: #AFD0E1'>" . $msg . "</div>";
                }
            }

        }

            $output .= '<ul class="stack">';
            // php code
            if (is_array($plugin) && array_key_exists('plugin_code', $plugin)
                && is_array($plugin['plugin_code']) && array_key_exists('code', $plugin['plugin_code'])
                && is_array($plugin['plugin_code']['code']))
            {
                $this->debug_output .= $this->CheckForRemovedHooks($plugin['plugin_code']['code'], $product_id, $_plugin_code);
                // more than 1 code ?
                if (array_key_exists(0, $plugin['plugin_code']['code']) && is_array($plugin['plugin_code']['code'][0]))
                {
                    foreach ($plugin['plugin_code']['code'] as $key => $arr)
                    {
                        $this->debug_output .= $this->_addCode($product_id, $arr, $_plugin_code);
                    }
                }
                else
                {
                    $arr = $plugin['plugin_code']['code'];
                    $this->debug_output .= $this->_addCode($product_id, $arr, $_plugin_code);
                }
            }

            // check for _lng.xml file
            $lng_xml = str_replace('.xml', '_lng.xml', $xml);
            require_once _SRV_WEBROOT . 'xtFramework/classes/class.language_sync.php';

            if (strtolower($plugin['developer']) == 'xt:commerce')
            {
                $lang_sync = new language_sync();
                $lang_sync->downloadPluginTranslations($_plugin_code);
            }

            if (is_file($lng_xml))
            {
                $lng_xml_data = $this->xmlToArray($lng_xml);

                if (isset($lng_xml_data['xtcommerceplugin']['language_content']['phrase']))
                {
                    $plugin['language_content'] = $lng_xml_data['xtcommerceplugin']['language_content'];
                }
            }

            // language vars ?
            if (isset($plugin['language_content']['phrase']) && is_array($plugin['language_content']['phrase']))
            {
                if (array_key_exists(0, $plugin['language_content']['phrase']) && is_array($plugin['language_content']['phrase'][0]))
                {
                    foreach ($plugin['language_content']['phrase'] as $key => $val)
                    {
                        $this->debug_output .= $this->_addLangContent($_plugin_code, $val);
                    }
                }
                else
                {
                    $val = $plugin['language_content']['phrase'];
                    $this->debug_output .= $this->_addLangContent($_plugin_code, $val);
                }
            }

            // config code ?
            if (array_key_exists('configuration', $plugin) && is_array($plugin['configuration']) &&
                array_key_exists('config', $plugin['configuration']) && is_array($plugin['configuration']['config']))
            {

                $this->debug_output .= $this->CheckForRemovedConfig($plugin['configuration']['config'], $product_id, $_plugin_code);

                // more than 1 ?
                if (array_key_exists(0, $plugin['configuration']['config']) && is_array($plugin['configuration']['config'][0]))
                {
                    $sort_order = 1000;
                    foreach ($plugin['configuration']['config'] as $key => $val)
                    {
                        if(!isset($val['sort_order']) || trim($val['sort_order']) == '') {
                            $val['sort_order'] = $sort_order;
                            $sort_order += 10;
                        }

                        foreach ($stores as $sdata)
                        {
                            $this->debug_output .= $this->_addStoreConfig($val, $product_id, $sdata['id'], 'plugin');
                        }

                        $this->debug_output .= '<li class="plugin_config_add"> ' . __text('TEXT_CONFIGURATION_KEY'). ' ' . $filter->_filter($val['key']) . ' '.__text('TEXT_ADDED') . '</li>';

                        // install language content
                        $this->debug_output .= $this->_addLangContentModule($_plugin_code, $val);
                    }
                }
                else
                {
                    $input_data = array();
                    $val = $plugin['configuration']['config'];

                    $sort_order = 1;
                    foreach ($stores as $sdata)
                    {
                        $val['sort_order'] = $sort_order;
                        $this->debug_output .= $this->_addStoreConfig($val, $product_id, $sdata['id'], 'plugin');
                    }

                    // install language content
                    $this->debug_output .= $this->_addLangContentModule($_plugin_code, $val);

                }
            }

            // install cronjobs
            if (array_key_exists('cronjobs', $plugin) && is_array($plugin['cronjobs']) && is_array($plugin['cronjobs']['cronjob']))
            {
                $cron = new xt_cron();
                $cron->setPosition('admin');

                if(array_key_exists('cron_action', $plugin['cronjobs']['cronjob']))
                {
                    $jobs = $plugin['cronjobs'];
                }
                else {
                    $jobs = $plugin['cronjobs']['cronjob'];
                }

                foreach($jobs as $cj)
                {
                    $cj_file = explode(':', $cj['cron_action']);
                    if(count($cj_file)>1)
                    {
                        $cj_file = $cj_file[1];
                        $fsource = _SRV_WEBROOT . _SRV_WEB_PLUGINS . $_plugin_code . '/cronjobs/'.$cj_file;
                        $hintPathSource = _SRV_WEB_PLUGINS . $_plugin_code . '/cronjobs/'.$cj_file;

                        if(file_exists($fsource))
                        {
                            $hintPathTarget = _SRV_WEB_CRONJOBS . $cj_file;

                            $ftarget = _SRV_WEBROOT . _SRV_WEB_CRONJOBS . $cj_file;
                            if(file_exists($ftarget))
                            {
                                unlink($ftarget);
                            }
                            copy($fsource, $ftarget);

                            if (!file_exists($ftarget))
                            {
                                $hint = "Installer tried to copy cron file but failed.<br />You have to copy manually {$hintPathSource} to <br/> {$hintPathTarget}";
                                $output .= "<br /><div style='border:solid 1px #fecf43;background: #ffe086; padding:10px;'>";
                                $output .= $hint;
                                $output .= "</div>";
                            }
                            else
                            {
                                $exists = $db->GetOne('SELECT true FROM '.TABLE_CRON.' WHERE cron_action = ?', array($cj['cron_action']));
                                if(!$exists)
                                {
                                    $cron_data = array(
                                        "cron_id" => "",
                                        "cron_note" => $cj['cron_note'],
                                        "cron_value" => $cj['cron_value'],
                                        "cron_type" => $cj['cron_type'],
                                        "hour" => $cj['hour'],
                                        "minute" => $cj['minute'],
                                        "cron_action" => $cj['cron_action'],
                                        "cron_parameter" => $cj['cron_parameter'],
                                    );
                                    $cron->_set($cron_data, 'add');
                                }
                            }
                        }
                        else {
                            $hint = "Installer tried to copy cron file but failed.<br />Cron file {$hintPathSource} does not exist";
                            $output .= "<br /><div style='border:solid 1px #fecf43;background: #ffe086; padding:10px;'>";
                            $output .= $hint;
                            $output .= "</div>";
                        }
                    }
                }
            }

            if (isset($_GET['pluginHistoryId']) || ($this->mode == 'update')) // if pluginHistoryId is set or mode is update => install db_update script was run above
            {}
            elseif ($data['db_install'] != '') // run installation part
            {
                eval($data['db_install']);
            }

            $this->installHelpLinks($xml_data);

            //	if ($data['run_db_stmt']!='') eval($data['run_db_stmt']);
            if ($save_plugin_version)
            {
                $product_id = $this->savePlugin($data_update, true);
            }

            $versionAvailable = $db->GetOne("SELECT version_available FROM ".TABLE_PLUGIN_PRODUCTS." WHERE code= ? ", [$_plugin_code]);
            if(!empty($versionAvailable) && version_compare($data['plugin_version'], $versionAvailable, '>='))
            {
                $sql = "UPDATE " . TABLE_PLUGIN_PRODUCTS . " SET version_available = '' WHERE code= ? ";
                $db->Execute($sql, array($_plugin_code));
            }


        $output .= '<li class="success"><b>' . __text('TEXT_INSTALLATION_SUCCESSFULL') . '</b></li>';
        $output .= '</ul>';

            $install_messages = array_key_exists('messages', $plugin) ? $plugin['messages'] : array();
            if ($this->mode == self::MODE_INSERT &&
                isset($install_messages) &&
                isset($install_messages['post']) && is_array($install_messages['post'])
            )
            {
                if (isset($install_messages['post'][$language->content_language]))
                {
                    $msg = $install_messages['post'][$language->content_language];
                }
                else
                {
                    reset($install_messages['post']);
                    $msg = $install_messages['post'][key($install_messages['post'])];
                }

                $output .= "
				<div style='padding:10px; border:1px solid #005da2; background: #AFD0E1'>" . $msg . "</div>
			";
            }

            self::clearLanguageCache();

            if (!defined('_DONT_CLEAR_CACHE_ADODB_AUTOMATICALLY') || constant('_DONT_CLEAR_CACHE_ADODB_AUTOMATICALLY') != true) $db->cacheFlush();
            return $output;
        }
        catch (Exception $e)
        {
            if(!empty($product_id) && $this->mode == self::MODE_INSERT)
            {
                try{
                    $this->DeletePlugin($product_id);
                }
                catch(Exception $innerEx)
                {

                }
            }
            $db->cacheFlush();
            if(defined('XT_WIZARD_STARTED') && XT_WIZARD_STARTED==true)
            {
                throw $e;
            }
            $this->debug_output = '<li class="error">' . 'Install: ' . $e->getMessage() . '<br />' .str_replace('#','<br/>#', $e->getTraceAsString()).'</li>';
            if ($this->mode == self::MODE_INSERT)
            {
                $output = "
				<div style='padding:10px; border:1px solid #a21d0c; background: #e1a7a1'><p>Es ist ein Fehler aufgetreten. Plugin wurde nicht installiert.</p></div>
			";
            }
            else
            {
                $output = "
				<div style='padding:10px; border:1px solid #a21d0c; background: #e1a7a1'><p>Es ist ein Fehler aufgetreten. Plugin wurde nicht aktualisiert.</p></div>
			";
            }

            if ($this->mode == self::MODE_INSERT)
            {
                try
                {
                    if ($product_id)
                    {
                        $this->DeletePlugin($product_id);
                    }
                } catch (Exception $e)
                {
                    $this->debug_output .= '<li class="error">' . 'Uninstall: ' . $e->getMessage() . '</li>';
                }
            }

            return $output;
        }
    }

    /**
     * @param $plugin array plugin data
     * @param $product_id int plugin id
     * @param $_plugin_code string plugin code
     *
     * @return int the payment id
     */
    public function installPayment($plugin, $product_id, $_plugin_code, $add_status_new = true)
    {
        global $db, $filter, $store_handler;

        $stores = $store_handler->getStores();

        $payment_id = false;

        // install payment
        if (is_array($plugin['payment']))
        {
            $val = $plugin['payment'];
            $payment = new payment();
            $payment_id = $payment->install($val, $product_id);
            $this->debug_output .= '<li class="plugin_payment_add">' . __text('TEXT_ADDED_PAYMENT_MODULE') . $filter->_filter($val['payment_code']) . '</li>';


            if (array_key_exists('configuration_payment', $plugin) && is_array($plugin['configuration_payment']) && array_key_exists('config', $plugin['configuration_payment']) && is_array($plugin['configuration_payment']['config']))
            {

                if (is_array($plugin['configuration_payment']['config'][0]))
                {
                    $auto_increment_config_sort = 5;
                    if(array_key_exists('sort_order', $val))
                    {
                        $auto_increment_config_sort = false;
                    }
                    // add / update keys
                    // collect all keys from xml
                    $keys_xml = array();
                    foreach ($plugin['configuration_payment']['config'] as $key => $val)
                    {
                        if($auto_increment_config_sort)
                        {
                            $val['sort_order'] = $auto_increment_config_sort;
                            $auto_increment_config_sort += 5;
                        }
                        $val['key'] = strtoupper($_plugin_code) . '_' . $val['key'];
                        $keys_xml[] = $val['key'];
                        // install language content
                        $this->debug_output .= $this->_addLangContentModule($_plugin_code, $val);
                        foreach ($stores as $sdata)
                        {
                            $this->debug_output .= $this->_addStoreConfig($val, $payment_id, $sdata['id'], 'payment');
                        }
                    }
                    // add key order_status_new, its coming from framework not xml
                    if ($add_status_new) $keys_xml[] = strtoupper($_plugin_code) . '_ORDER_STATUS_NEW';
                    // remove obsolete payment configuration keys
                    $keys_db = $db->GetArray("SELECT config_key FROM ".TABLE_CONFIGURATION_PAYMENT." WHERE payment_id=?", array($payment_id));
                    foreach($keys_db as $k => &$v)
                    {
                        $v = $v['config_key'];
                    }
                    $keys_remove = array_diff($keys_db, $keys_xml);
                    foreach($keys_remove as $k)
                    {
                        $this->debug_output .= $this->_removeStoreConfigPayment($k);
                    }

                }
                else
                {
                    $val = $plugin['configuration_payment']['config'];
                    $val['key'] = strtoupper($_plugin_code) . '_' . $val['key'];
                    $val['sort_order'] = 5;

                    // install language content
                    $this->debug_output .= $this->_addLangContentModule($_plugin_code, $val);

                    foreach ($stores as $sdata)
                    {
                        $this->debug_output .= $this->_addStoreConfig($val, $payment_id, $sdata['id'], 'payment');
                    }
                }
            }
            //set order status for new orders
            if($add_status_new)
            {
                foreach ($stores as $sdata)
                {

                    $rs = $db->Execute("SELECT config_value FROM " . TABLE_CONFIGURATION_MULTI . (int)$sdata['id'] . " where config_key = '_STORE_DEFAULT_ORDER_STATUS'");
                    if ($rs->RecordCount() > 0)
                    {
                        $o_status = $rs->fields['config_value'];
                    }
                    else
                    {
                        $o_status = 16;
                    }

                    $val = array();
                    $val['key'] = strtoupper($_plugin_code) . '_ORDER_STATUS_NEW';
                    $val['value'] = $o_status;

                    $val['group_id'] = 0;
                    $val['sort_order'] = (int)0;

                    $val['type'] = 'dropdown';
                    $val['url'] = 'status:order_status';
                    $val['shop_id'] = $sdata['id'];
                    $this->debug_output .= $this->_addStoreConfig($val, $payment_id, $sdata['id'], 'payment');
                    $val['en']['title'] = 'New Order Status';
                    $this->debug_output .= $this->_addLangContentModule($_plugin_code, $val);
                }
            }
        }

        return $payment_id;

    }

    function installHelpLinks($xml_data)
    {
        global $db;

        if (array_key_exists('doc_links', $xml_data['xtcommerceplugin']) && is_array($xml_data['xtcommerceplugin']['doc_links']) && is_array($xml_data['xtcommerceplugin']['doc_links']['doc_link']))
        {
            $doc_links = array_key_exists('url', $xml_data['xtcommerceplugin']['doc_links']['doc_link']) ?
                array($xml_data['xtcommerceplugin']['doc_links']['doc_link']) : $xml_data['xtcommerceplugin']['doc_links']['doc_link'];
            foreach ($doc_links as $doc_link)
            {
                switch($doc_link['type'])
                {
                    case 'config_group':
                        $db->Execute("UPDATE ".TABLE_CONFIGURATION_GROUP." SET url_h=? WHERE group_title=?", array($doc_link['url'],$doc_link['key']));
                        break;
                    case 'acl_nav':
                    default:
                        $db->Execute("UPDATE ".TABLE_ADMIN_NAVIGATION." SET url_h=? WHERE text=?", array($doc_link['url'],$doc_link['key']));
                }
            }
        }
    }

    static function clearLanguageCache()
    {
        // _cache_xt.language_content
        try
        {
            $files = glob(_SRV_WEBROOT . 'cache/_cache_xt.language_content*');
            array_map('unlink', $files);
        }
        catch(Exception $e){ error_log('plugin::clearLanguageCache: '.$e->getMessage()); }
    }

    function clearErrorLog($id)
    {
        global $logHandler;
        $logHandler->clearLogMessages(__CLASS__, $id);
    }

    function _addStoreConfig($val, $id, $store_id, $type = 'plugin')
    {
        global $db, $filter;

        $input_data = array();
        if ($type == 'plugin')
        {
            $check = 'plugin_config';
            $input_data['plugin_id'] = $id;
            $table = TABLE_PLUGIN_CONFIGURATION;
        }
        else
        {
            $check = 'payment_config';
            $input_data['payment_id'] = $id;
            $table = TABLE_CONFIGURATION_PAYMENT;
        }
        $input_data['config_key'] = $filter->_filter($val['key']);
        $input_data['config_value'] = array_key_exists('value', $val) ? $filter->_filter($val['value']) : '';
        if (!isset($val['group_id']))
        {
            $val['group_id'] = 0;
        }
        $input_data['group_id'] = $filter->_filter($val['group_id']);
        if (!isset($val['sort_order']))
        {
            $val['sort_order'] = 0;
        }
        $input_data['sort_order'] = (int)$val['sort_order'];
        if (!isset($val['type']))
        {
            $val['type'] = '';
        }
        $input_data['type'] = $filter->_filter($val['type']);
        if (!isset($val['url']))
        {
            $val['url'] = '';
        }
        $input_data['url'] = $filter->_filter($val['url']);
        $input_data['shop_id'] = $store_id;

        if (!$this->_checkIfExists($input_data['config_key'], $check, " and shop_id='" . $store_id . "'"))
        {
            $error = false;
            $db->AutoExecute($table, $input_data);
        }
        else
        {
            if (($type == 'plugin') && !$this->_checkIfExists($input_data['config_key'], $check, " and shop_id='" . $store_id . "' and type = '" . $input_data['type'] . "' and url ='" . $input_data['url'] . "'"))
            {
                $db->AutoExecute(TABLE_PLUGIN_CONFIGURATION, $input_data, 'UPDATE', " config_key = '" . $input_data['config_key'] . "' and shop_id='" . $store_id . "'");
                $error = false;
            }
            else
            {
                $error = true;
            }
        }
        if ($type == 'plugin')
        {
            if (!$error)
            {
                return '<li class="plugin_config_add">' . __text('TEXT_ADDED_PLUGIN_CONFIG_FOR_STORE') . $store_id . ': ' . $filter->_filter($val['key']) . '</li>';
            }
            else
            {
                return '<li class="plugin_config_add">!ERROR, ALREADY EXISTS! ' . __text('TEXT_ADDED_PLUGIN_CONFIG_FOR_STORE') . $store_id . ': ' . $filter->_filter($val['key']) . '</li>';
            }
        }
        else
        {
            if (!$error)
            {
                return '<li class="plugin_payment_config_add">' . __text('TEXT_ADDED_PAYMENT_CONFIG_FOR_STORE') . $store_id . ': ' . $filter->_filter($val['key']) . '</li>';
            }
            else
            {
                return '<li class="plugin_payment_config_add">!ERROR, ALREADY EXISTS! ' . __text('TEXT_ADDED_PAYMENT_CONFIG_FOR_STORE') . $store_id . ': ' . $filter->_filter($val['key']) . '</li>';
            }
        }

    }

    function _removeStoreConfigPayment($key)
    {
        global $db;

        $db->Execute("DELETE FROM ".TABLE_CONFIGURATION_PAYMENT. " WHERE config_key=?", array($key));

        return '<li class="plugin_payment_config_remove">' . __text('TEXT_REMOVED_PAYMENT_CONFIG_FOR_STORE') .  ': '. $key . '</li>';

    }

    /**
     * add code for hookpoint
     *
     * @param int $product_id
     * @param array $arr
     * @return string
     */
    function _addCode($product_id, $arr, $_plugin_code)
    {
        global $db, $filter;
        $output = '';
        $input_data = array();
        $input_data['plugin_id'] = $product_id;
        $input_data['hook'] = $filter->_filter($arr['hook']);
        $input_data['code'] = $arr['phpcode'];
        $input_data['code_status'] = $filter->_filter($arr['active']);
        $input_data['plugin_code'] = $_plugin_code;
        $input_data['sortorder'] = $filter->_filter($arr['order']);

        $input_data['plugin_status'] = $db->GetOne("SELECT plugin_status FROM ".TABLE_PLUGIN_PRODUCTS." WHERE plugin_id=?", array($input_data['plugin_id'])) ? 1 : 0;

        if ($this->mode == 'insert')
        {

            // check for double
            $rs = $db->Execute(
                "SELECT plugin_id FROM " . TABLE_PLUGIN_CODE . " WHERE plugin_id = ? and hook = ?",
                array((int)$input_data['plugin_id'], $input_data['hook'])
            );
            if ($rs->RecordCount() == 0)
            {
                $db->AutoExecute(TABLE_PLUGIN_CODE, $input_data);
            }
        }
        else
        {
            // Check if Update or Insert
            if ((int)$this->plugin_id == 0)
            {
                $this->plugin_id = $product_id;
            }
            $rs = $db->Execute(
                "SELECT plugin_id FROM " . TABLE_PLUGIN_CODE . " WHERE plugin_id = ? and hook = ?",
                array((int)$input_data['plugin_id'], $input_data['hook'])
            );
            if ($rs->RecordCount() == 1)
            {
                $db->AutoExecute(TABLE_PLUGIN_CODE, $input_data, 'UPDATE', "plugin_id = '" . (int)$input_data['plugin_id'] . "' and hook = " . $db->Quote($input_data['hook']) . "");
            }
            else
            {
                $db->AutoExecute(TABLE_PLUGIN_CODE, $input_data);
            }
        }

        $output .= '<li class="plugin_script_add">' . __text('TEXT_CODE_ADDED_FOR_HOOKPOINT') . $filter->_filter($arr['hook']) . '</li>';
        return $output;
    }

    private function getAllConfigsFromXml($arr)
    {
        global $filter;
        $all_config = array();
        if (is_array($arr) && array_key_exists(0, $arr) && is_array($arr[0]))
        {
            foreach ($arr as $key => $arr2)
            {
                array_push($all_config, $filter->_filter($arr2['key']));
            }
        }
        else
        {
            array_push($all_config, $filter->_filter($arr['key']));
        }

        return $all_config;
    }

    private function getAllHooksFromXml($arr)
    {
        global $filter;
        $all_hooks = array();
        if (is_array($arr) && array_key_exists(0, $arr) && is_array($arr[0]))
        {
            foreach ($arr as $key => $arr2)
            {
                array_push($all_hooks, $filter->_filter($arr2['hook']));

            }
        }
        else
        {
            array_push($all_hooks, $filter->_filter($arr['hook']));
        }

        return $all_hooks;
    }

    function CheckForRemovedConfig($arr, $product_id, $_plugin_code)
    {
        global $db;
        $output = '';
        $all_config = $this->getAllConfigsFromXml($arr);

        $rs = $db->Execute(
            "SELECT id,config_key FROM " . TABLE_PLUGIN_CONFIGURATION . " WHERE plugin_id = ? ",
            array((int)$this->plugin_id)
        );
        while (!$rs->EOF)
        {
            if (!in_array($rs->fields['config_key'], $all_config))
            {
                $db->Execute(
                    "DELETE FROM " . TABLE_PLUGIN_CONFIGURATION . " WHERE id = ? ",
                    array((int)$rs->fields['id'])
                );
                $output .= '<li class="plugin_script_add">' . __text('TEXT_CODE_REMOVED_FOR_CONFIG') . ' ' . $rs->fields['config_key'] . '</li>';
            }

            $rs->MoveNext();
        }
        return $output;
    }

    function CheckForRemovedHooks($arr, $product_id, $_plugin_code)
    {
        global $db;
        $output = '';
        $all_hooks = $this->getAllHooksFromXml($arr);

        $rs = $db->Execute("SELECT hook, id FROM " . TABLE_PLUGIN_CODE . " WHERE plugin_id = ? ", array((int)$this->plugin_id));
        while (!$rs->EOF)
        {
            if (!in_array($rs->fields['hook'], $all_hooks))
            {
                $db->Execute("DELETE FROM " . TABLE_PLUGIN_CODE . " WHERE id = ? ", array((int)$rs->fields['id']));
                $output .= '<li class="plugin_script_add">' . __text('TEXT_CODE_REMOVED_FOR_HOOKPOINT') . ' ' . $rs->fields['hook'] . '</li>';
            }

            $rs->MoveNext();
        }
        return $output;
    }


    function _addLangContent($plugin_code, $val)
    {
        global $db, $language, $filter;
        $output = '';

        $language_list = $language->_getLanguageList('all');

        foreach ($language_list as $lkey => $lval)
        {

            $lang_data = array();
            $lang_data['plugin_key'] = $plugin_code;
            $lang_data['language_code'] = $lval['code'];
            $lang_data['class'] = $filter->_filter($val['class']);

            if (array_key_exists($lval['code'], $val) && is_array($val[$lval['code']]))
            {
                $lang_data['language_key'] = $filter->_filter($val['key']);
                $lang_data['language_value'] = $filter->_filter($val[$lval['code']]['value']);

                if (!$this->_checkIfExists($lang_data['language_key'], 'language_content', " and language_code='" . $lval['code'] . "' and class='" . $lang_data['class'] . "'"))
                {
                    $db->AutoExecute(TABLE_LANGUAGE_CONTENT, $lang_data);
                    $output .= '<li class="plugin_lang_add">' . __text('TEXT_PHRASE_ADDED_FOR') . $lval['code'] . ': ' . $filter->_filter($val['key']) . '</li>';
                }
                else
                {
                    if (($translation = $this->_checkIfExists($lang_data['language_key'], 'language_content', " and language_code='" . $lval['code'] . "' and class='" . $lang_data['class'] . "' and readonly='0'")))
                    {
                        $db->AutoExecute(TABLE_LANGUAGE_CONTENT, array('language_value' => $lang_data['language_value']), 'UPDATE', "language_content_id='{$translation['language_content_id']}'");
                        $output .= '<li class="plugin_lang_add">' . __text('TEXT_PHRASE_ADDED_FOR') . $lval['code'] . ': ' . $filter->_filter($val['key']) . '</li>';
                    }
                    else
                    {
                        $output .= '<li class="plugin_lang_add">!ERROR, ALREADY EXISTS! ' . __text('TEXT_PHRASE_ADDED_FOR') . $lval['code'] . ': ' . $filter->_filter($val['key']) . '</li>';
                    }
                }
            }
            else
            {
                $lang_data['language_key'] = $filter->_filter($val['key']);
                $lang_data['translated'] = '1';
                $lang_data['language_value'] = $filter->_filter($val['en']['value']);

                if (!($translation = $this->_checkIfExists($lang_data['language_key'], 'language_content', " and language_code='" . $lval['code'] . "' and class='" . $lang_data['class'] . "'")))
                {
                    $db->AutoExecute(TABLE_LANGUAGE_CONTENT, $lang_data);
                    $output .= '<li class="plugin_lang_add">' . __text('TEXT_PHRASE_ADDED_FOR') . $lval['code'] . ': ' . $filter->_filter($val['key']) . '</li>';
                }
                else
                {
                    if (($translation = $this->_checkIfExists($lang_data['language_key'], 'language_content', " and language_code='" . $lval['code'] . "' and class='" . $lang_data['class'] . "' and readonly='0'")))
                    {
                        $db->AutoExecute(TABLE_LANGUAGE_CONTENT, array('language_value' => $lang_data['language_value']), 'UPDATE', "language_content_id='{$translation['language_content_id']}'");
                        $output .= '<li class="plugin_lang_add">' . __text('TEXT_PHRASE_ADDED_FOR') . $lval['code'] . ': ' . $filter->_filter($val['key']) . '</li>';
                    }
                    else
                    {
                        $output .= '<li class="plugin_lang_add">!ERROR, ALREADY EXISTS! ' . __text('TEXT_PHRASE_ADDED_FOR') . $lval['code'] . ': ' . $filter->_filter($val['key']) . '</li>';
                    }
                }
            }

        }

        return $output;
    }

    /**
     * check if language or config element alrady exists
     *
     * @param string $key
     * @param string $source
     * @param string $where
     * @return boolean
     */
    function _checkIfExists($key, $source = 'language_content', $where = '')
    {
        global $db;

        switch ($source)
        {
            case 'language_content':
                $rs = $db->Execute("SELECT * FROM " . TABLE_LANGUAGE_CONTENT . " WHERE language_key=? " . $where, array($key));
                if ($rs->RecordCount() == 0)
                {
                    return false;
                }
                return $rs->fields;
                break;

            case 'payment_config':
                $rs = $db->Execute("SELECT * FROM " . TABLE_CONFIGURATION_PAYMENT . " WHERE config_key=? " . $where, array($key));
                if ($rs->RecordCount() == 0)
                {
                    return false;
                }
                return true;
                break;

            case 'plugin_config':
                $rs = $db->Execute("SELECT * FROM " . TABLE_PLUGIN_CONFIGURATION . " WHERE config_key=? " . $where, array($key));
                if ($rs->RecordCount() == 0)
                {
                    return false;
                }
                return true;
                break;
        }

    }

    /**
     * check if field exists within a table (precheck for plugins)
     *
     * @param string $field
     * @param string $table
     * @return boolean
     */
    function _FieldExists($field, $table)
    {
        global $db, $filter;

        $table = $filter->_charNum($table);
        $field = $filter->_charNum($field);

        $colExists = $db->GetOne("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='" . _SYSTEM_DATABASE_DATABASE . "' AND COLUMN_NAME=? AND TABLE_NAME=?", [$field, $table]);

        return $colExists;
    }

    /**
     * check if table exists within
     *
     * @param string $field
     * @param string $table
     * @return boolean
     */
    function _TableExists($table)
    {
        global $db, $filter;

        $table = $filter->_charNum($table);

        $tableExists = $db->GetOne("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA='" . _SYSTEM_DATABASE_DATABASE . "' AND TABLE_NAME=? ", [$table]);

        return $tableExists;
    }

    function _checkIsInstalled($plugin_class)
    {

        return true;
    }


    function _addLangContentModule($plugin_code, $val)
    {
        global $db, $language, $filter;
        // install language content
        $output = '';
        foreach ($language->_getLanguageList('all') as $lkey => $lval)
        {
//			debugbreak();
            $lang_data = array();
            $lang_data['plugin_key'] = $plugin_code;
            $lang_data['language_code'] = $lval['code'];
            $lang_data['class'] = 'admin';

            if (array_key_exists($lval['code'], $val) && is_array($val[$lval['code']]))
            {
                $lang_data['language_key'] = $filter->_filter($val['key']) . '_TITLE';
                $lang_data['language_value'] = $filter->_filter($val[$lval['code']]['title']);

                if (!$this->_checkIfExists($lang_data['language_key'], 'language_content', " and language_code=" . $db->Quote($lval['code']) . " and class=" . $db->Quote($lang_data['class']) . ""))
                {
                    $db->AutoExecute(TABLE_LANGUAGE_CONTENT, $lang_data);
                    $output .= '<li class="plugin_lang_add">' . __text('TEXT_PHRASE_ADDED_FOR') . ' ' . $lval['code'] . ': ' . $filter->_filter($val['key']) . '</li>';
                }
                else
                {
                    $db->AutoExecute(TABLE_LANGUAGE_CONTENT, $lang_data, 'UPDATE', "language_key = " . $db->Quote($lang_data['language_key']) . " and language_code=" . $db->Quote($lval['code']) . " and class=" . $db->Quote($lang_data['class']) . "");
                    $output .= '<li class="plugin_lang_add">' . __text('TEXT_PHRASE_UPDATED_FOR') . ' ' . $lval['code'] . ': ' . $filter->_filter($val['key']) . '</li>';
                }
            }
            else
            {
                $lang_data['language_key'] = $filter->_filter($val['key']) . '_TITLE';
                $lang_data['translated'] = '0';
                $lang_data['language_value'] = $filter->_filter($val['en']['title']);

                if (!$this->_checkIfExists($lang_data['language_key'], 'language_content', " and language_code=" . $db->Quote($lval['code']) . " and class=" . $db->Quote($lang_data['class']) . ""))
                {
                    $db->AutoExecute(TABLE_LANGUAGE_CONTENT, $lang_data);
                    $output .= '<li class="plugin_lang_add">' . __text('TEXT_PHRASE_ADDED_FOR') . ' ' . $lval['code'] . ': ' . $filter->_filter($val['key']) . '</li>';
                }
                else
                {
                    $db->AutoExecute(TABLE_LANGUAGE_CONTENT, $lang_data, 'UPDATE', "language_key = " . $db->Quote($lang_data['language_key']) . " and language_code=" . $db->Quote($lval['code']) . " and class=" . $db->Quote($lang_data['class']) . "");
                    $output .= '<li class="plugin_lang_add">' . __text('TEXT_PHRASE_UPDATED_FOR') . ' ' . $lval['code'] . ': ' . $filter->_filter($val['key']) . '</li>';
                }
            }

        }
        return $output;
    }

    function checkInstall($pluginCode, $required_version = false)
    {
        global $db;

        // check if allready installed ?
        $query = "SELECT plugin_id, version FROM " . TABLE_PLUGIN_PRODUCTS . " WHERE code = ?";

        $rs = $db->Execute($query, array($pluginCode));
        if ($rs->RecordCount() == 1)
        {
            if ($required_version == false)
            {
                return $rs->fields['plugin_id'];
            }
            $vc = version_compare($rs->fields['version'], $required_version) >= 0 ? true : false;
            return $vc ? $rs->fields['plugin_id'] : false;
        }
        return false;
    }

    function _UpdateCode($product_id, $arr, $_plugin_code)
    {
        global $db, $filter;

        $input_data = array();
        $input_data['plugin_id'] = $product_id;
        $input_data['hook'] = $filter->_filter($arr['hook']);
        $input_data['code'] = $arr['phpcode'];
        $input_data['code_status'] = $filter->_filter($arr['active']);
        $input_data['plugin_code'] = $_plugin_code;
        $input_data['sortorder'] = $filter->_filter($arr['order']);

        $input_data['plugin_status'] = $db->GetOne("SELECT plugin_status FROM ".TABLE_PLUGIN_PRODUCTS." WHERE plugin_id=?", array($input_data['plugin_id'])) ? 1 : 0;

        $rs = $db->Execute(
            "SELECT plugin_id FROM " . TABLE_PLUGIN_CODE . " WHERE plugin_id = ? and hook = ?",
            array((int)$this->plugin_id, $input_data['hook'])
        );
        if ($rs->RecordCount() == 1)
        {
            $db->AutoExecute(TABLE_PLUGIN_CODE, $input_data, 'UPDATE', "plugin_id = '" . (int)$this->plugin_id . "' and hook = " . $db->Quote($input_data['hook']) . "");
        }
        else
        {
            $db->AutoExecute(TABLE_PLUGIN_CODE, $input_data);
        }
    }

    function _UpdateLangContent($plugin_code, $val)
    {
        global $db, $language, $filter;

        foreach ($language->_getLanguageList() as $lkey => $lval)
        {

            $lang_data = array();
            $lang_data['plugin_key'] = $plugin_code;
            $lang_data['language_code'] = $lval['code'];
            $lang_data['class'] = $filter->_filter($val['class']);

            if (is_array($val[$lval['code']]))
            {
                $lang_data['language_key'] = $filter->_filter($val['key']);
                $lang_data['language_value'] = $filter->_filter($val[$lval['code']]['value']);

                if (!$this->_checkIfExists($lang_data['language_key'], 'language_content', " and language_code=" . $db->Quote($lval['code']) . " and class=" . $db->Quote($lang_data['class']) . ""))
                {
                    $db->AutoExecute(TABLE_LANGUAGE_CONTENT, $lang_data);
                }
                else
                {
                    $db->AutoExecute(TABLE_LANGUAGE_CONTENT, $lang_data, 'UPDATE', "language_key = " . $db->Quote($lang_data['language_key']) . " and language_code=" . $db->Quote($lval['code']) . " and class=" . $db->Quote($lang_data['class']) . "");
                }

            }
            else
            {
                $lang_data['language_key'] = $filter->_filter($val['key']);
                $lang_data['language_value'] = $filter->_filter($val['en']['value']);

                if (!$this->_checkIfExists($lang_data['language_key'], 'language_content', " and language_code=" . $db->Quote($lval['code']) . " and class=" . $db->Quote($lang_data['class']) . ""))
                {
                    $db->AutoExecute(TABLE_LANGUAGE_CONTENT, $lang_data);
                }
                else
                {
                    $db->AutoExecute(TABLE_LANGUAGE_CONTENT, $lang_data, 'UPDATE', "language_key = " . $db->Quote($lang_data['language_key']) . " and language_code=" . $db->Quote($lval['code']) . " and class=" . $db->Quote($lang_data['class']) . "");
                }
            }
        }
    }

    function _UpdateLangContentModule($plugin_code, $val)
    {
        global $db, $language, $filter;

        foreach ($language->_getLanguageList() as $lkey => $lval)
        {
            $lang_data = array();
            $lang_data['plugin_key'] = $plugin_code;
            $lang_data['language_code'] = $lval['code'];
            $lang_data['class'] = 'admin';

            if (is_array($val[$lval['code']]))
            {
                $lang_data['language_key'] = $filter->_filter($val['key']) . '_TITLE';
                $lang_data['language_value'] = $filter->_filter($val[$lval['code']]['title']);

                if (!$this->_checkIfExists($lang_data['language_key'], 'language_content', " and language_code=" . $db->Quote($lval['code']) . " and class=" . $db->Quote($lang_data['class']) . ""))
                {
                    $db->AutoExecute(TABLE_LANGUAGE_CONTENT, $lang_data);
                }
                else
                {
                    $db->AutoExecute(TABLE_LANGUAGE_CONTENT, $lang_data, 'UPDATE', "language_key = " . $db->Quote($lang_data['language_key']) . " and language_code=" . $db->Quote($lval['code']) . " and class=" . $db->Quote($lang_data['class']) . "");
                }

            }
            else
            {

                $lang_data['language_key'] = $filter->_filter($val['key']) . '_TITLE';
                $lang_data['language_value'] = $filter->_filter($val['en']['title']);

                if (!$this->_checkIfExists($lang_data['language_key'], 'language_content', " and language_code=" . $db->Quote($lval['code']) . " and class=" . $db->Quote($lang_data['class']) . ""))
                {
                    $db->AutoExecute(TABLE_LANGUAGE_CONTENT, $lang_data);
                }
                else
                {
                    $db->AutoExecute(TABLE_LANGUAGE_CONTENT, $lang_data, 'UPDATE', "language_key = " . $db->Quote($lang_data['language_key']) . " and language_code=" . $db->Quote($lval['code']) . " and class=" . $db->Quote($lang_data['class']) . "");
                }
            }

        }
        return null;
    }

    function _UpdateStoreConfig($val, $id, $store_id, $type = 'plugin')
    {
        global $db, $filter;

        $input_data = array();
        if ($type == 'plugin')
        {
            $check = 'plugin_config';
            $input_data['plugin_id'] = $id;
            $table = TABLE_PLUGIN_CONFIGURATION;
        }
        else
        {
            $check = 'payment_config';
            $input_data['payment_id'] = $id;
            $table = TABLE_CONFIGURATION_PAYMENT;
        }
        $input_data['config_key'] = $filter->_filter($val['key']);
        $input_data['config_value'] = $filter->_filter($val['value']);
        if (!isset($val['group_id']))
        {
            $val['group_id'] = 0;
        }
        $input_data['group_id'] = $filter->_filter($val['group_id']);
        if (!isset($val['sort_order']))
        {
            $val['sort_order'] = 0;
        }
        $input_data['sort_order'] = (int)$val['sort_order'];
        if (!isset($val['type']))
        {
            $val['type'] = '';
        }
        $input_data['type'] = $filter->_filter($val['type']);
        if (!isset($val['url']))
        {
            $val['url'] = '';
        }
        $input_data['url'] = $filter->_filter($val['url']);
        $input_data['shop_id'] = $store_id;

        if (!$this->_checkIfExists($input_data['config_key'], $check, " and shop_id='" . $store_id . "'"))
        {
            $db->AutoExecute($table, $input_data);
        }
        else
        {
            $db->AutoExecute($table, $input_data, 'UPDATE', "config_key = " . $db->Quote($input_data['config_key']) . " and shop_id=" . $db->Quote($store_id) . "");
        }
    }

    /**
     * check if ioncube is installed and get version numer in case
     * @return bool
     */
    public function getIonCubeVersion() {

        $extensions = get_loaded_extensions();

        if (in_array('ionCube Loader', $extensions)) {

            if (function_exists('ioncube_loader_version')) {
                $ionCubeVersion = ioncube_loader_version();
                return $ionCubeVersion;
            }

            return true;
        } else {
            return false;
        }



    }

    function getPluginNameFromXML($xml)
    {
        $xml_data = $this->xmlToArray($xml);
        return $xml_data['xtcommerceplugin']['title'];
    }

    public function getMinimShopVersionFromXml($xml)
    {
        $xml_data = $this->xmlToArray($xml);
        if (isset($xml_data['xtcommerceplugin']['minimum_store_version']))
        {
            return $xml_data['xtcommerceplugin']['minimum_store_version'];
        }

        return 0;
    }

    public function getRequiredStoreLicenseTypeFromXml($xml, $default = 'ALL')
    {
        $xml_data = $this->xmlToArray($xml);
        if (isset($xml_data['xtcommerceplugin']['store_license_type']))
        {
            return $xml_data['xtcommerceplugin']['store_license_type'];
        }

        return $default;
    }

    public function getRequiredStoreLicenseTypeByPluginCode($plg_code, $default = 'ALL')
    {
        $file = _SRV_WEBROOT.'plugins/'.$plg_code.'/installer/'.$plg_code.'.xml';
        return $this->getRequiredStoreLicenseTypeFromXml($file, $default);
    }

    public function getRequiredStoreLicenseTypeByPluginId($plg_id, $default = 'ALL')
    {
        global $db;
        $plg_code = $db->GetOne("SELECT `code` FROM ". TABLE_PLUGIN_PRODUCTS. " WHERE plugin_id=?", array($plg_id));
        return $this->getRequiredStoreLicenseTypeByPluginCode($plg_code, $default);
    }

    public function getPluginChangeLog() {
        global $db, $xtLink, $is_pro_version,$logHandler,$xtc_acl;

        $id = $_GET['plugin_id'];
        if (preg_match('/[a-zA-Z]/', $id))
        {
            $modul = $_GET['plugin_id'];
        }
        else
        {
            $this->plugin_id = (int)$_GET['plugin_id'];
            $modul = $this->getPluginName($this->plugin_id);
        }


        if (self::ALLOW_ONLINE_UPDATE && (!defined('DEVELOPER_MODE') || !DEVELOPER_MODE)) {
            $this->_initiate();
            $error = '';
            $onlinePlugin = $this->getPluginChangeLogInformation($modul);

            if (isset($onlinePlugin->error) || !empty($error)) {

                $output = '<h1> ' . __text('TEXT_PLUGIN') . ' <b>' . $modul . '</b></h1>';
                $output .= '<br /><b>Plugin auf xt:Commerce Server nicht verfügbar</b><br />';

            } else {

                $output = '<h1> ' . __text('TEXT_PLUGIN') . ' <b>' . $modul . '</b></h1>';
                if (strlen($onlinePlugin->plugin_changelog) > 10 ) {
                    $output .= '<br /><p>'.nl2br($onlinePlugin->plugin_changelog).'</p><br />';
                } else {
                    $output .= '<br /><b>Kein Changelog verfügbar</b><br />';
                }
            }

        }
        echo '<div class="highlightbox">' . $output . '</div>';
    }

    /**
     * Update Plugin Popup
     * @throws Exception
     */
    function getPage()
    {
        global $db, $xtLink, $is_pro_version,$logHandler,$xtc_acl;

        $id = $_GET['plugin_id'];
        if (preg_match('/[a-zA-Z]/', $id))
        {
            $modul = $_GET['plugin_id'];
        }
        else
        {
            $this->plugin_id = (int)$_GET['plugin_id'];
            $modul = $this->getPluginName($this->plugin_id);
        }
        $param = '/[^a-zA-Z0-9_-]/';
        $modul = preg_replace($param, '', $modul);
        $pluginPath = _SRV_WEBROOT . 'plugins/' . $modul;
        $xml = $pluginPath . '/installer/' . $modul . '.xml';


        include_once(_SRV_WEBROOT_ADMIN . 'page_includes.php');

        $minimStoreVersion = $this->getMinimShopVersionFromXml($xml);
        if (version_compare($minimStoreVersion,_SYSTEM_VERSION) > 0)
        {
            $error = sprintf(__text('TEXT_ERROR_MINIMUM_STORE_VERSION'), $modul, $minimStoreVersion, _SYSTEM_VERSION);
            echo '<p class="updateinfo">' . $error . '</p>';
            die;
        }

        $plgLicType = $this->getRequiredStoreLicenseTypeFromXml($xml);
        $plgLicTypeError = false;
        if ($is_pro_version && $plgLicType == 'FREE')
        {
            $plgLicTypeError = sprintf(__text('TEXT_ERROR_PLUGIN_ONLY_IN_FREE_VERSION'));
        }
        else if (!$is_pro_version && $plgLicType == 'PRO')
        {
            $plgLicTypeError = sprintf(__text('TEXT_ERROR_PLUGIN_ONLY_IN_PRO_VERSION'));
        }
        if($plgLicTypeError)
        {
            if($this->plugin_id)
            {
                $pi = new plugin_installed();
                $pi->_setStatus($this->plugin_id, false, true);
            }
            echo '<p class="updateinfo">' . $plgLicTypeError . '</p>';
            die;
        }

        $checkInstall = $this->checkInstall($modul);
        if (!$checkInstall)
        {
            $output = '<h1> ' . __text('TEXT_PLUGIN') . ' <b>' . $this->getPluginNameFromXML($xml) . '</b> ' . __text('TEXT_WILLBE_INSTALL') . '</h1>';
        }
        else if ($checkInstall && $_GET['save'] == 'true')
        {
            $output = '<h1> ' . __text('TEXT_PLUGIN') . ' <b>' . $this->getPluginNameFromXML($xml) . '</b> ' . __text('TEXT_WILLBE_BACKUP') . '</h1>';
        }
        else
        {
            $output = '<h1> ' . __text('TEXT_PLUGIN') . ' <b>' . $this->getPluginNameFromXML($xml) . '</b> ' . __text('TEXT_WILLBE_UPDATE') . '</h1>';
        }

        if ($_GET['save'] == 'true')
        {
            $autoUpdateErrorMsg = 'Auto-Update-Error: ';
            $autoUpdatePossible = true;
            $update_type = 'manual';
            // check if plugin is available on update service
            if (self::ALLOW_ONLINE_UPDATE && (!defined('DEVELOPER_MODE') || !DEVELOPER_MODE)) {
                $this->_initiate();
                $error = '';
                $onlinePlugin = $this->getPluginUpdatePackage($modul, $this->getPluginVersion($modul), $error);
                $fileOutput = '';

                if (isset($onlinePlugin->error) || !empty($error)) {
                    $autoUpdatePossible = false;
                    $autoUpdateErrorMsg.= isset($onlinePlugin->error) ? $onlinePlugin->error : '';
                    $autoUpdateErrorMsg.= !empty($error) ? $error : '';
                    $fileOutput .= '<br /><b>xt:Commerce Plugin-Server: ' . $autoUpdateErrorMsg . '</b><br />';
                    // log detailed response into log
                    $log_data = array();
                    $log_data['message'] = isset($onlinePlugin) ? json_encode($onlinePlugin) : $error;
                    $log_data['time'] = time();
                    $logHandler->_addLog('error', __CLASS__ . '_updater_' . $modul, '', $log_data);
                } else {

                    if ($onlinePlugin->code == 'UPDATE') {
                        $dPlugin = $this->downloadPluginUpdatePackage($onlinePlugin->plugin_file_url, $modul, $onlinePlugin->plugin_version);
                        if ($dPlugin['response_code'] == '404') {
                            $fileOutput .= '<br /><b>xt:Commerce Plugin-Server: File not Found</b><br />';
                            $log_data = array();
                            $log_data['message'] = json_encode($dPlugin);
                            $log_data['time'] = time();
                            $logHandler->_addLog('error', __CLASS__ . '_updater_' . $modul, '', $log_data);
                        } elseif ($dPlugin['response_code'] == '200') {
                            $fileOutput .= '<ul class="stack"><li class="success">xt:Commerce Plugin-Server: Plugin Version (' . $onlinePlugin->plugin_version . ') vom Server geladen</li></ul>';
                            $update_type = 'auto';
                        }
                    }
                }
            }

                // save plugin files and db before update

                // generate plugin xml from db settings
                $savePlugin = new pluginGenerateXML($modul);
                $saveXml = $savePlugin->generateXml();

                // insert plugin history
                $insertHistory = array(
                    'plugin_code' => $modul,
                    'old_version' => $this->getPluginVersion($modul),
                    'xml' => $saveXml
                );

                $db->AutoExecute(TABLE_PLUGIN_HISTORY, $insertHistory);
                $pluginHistoryId = $db->Insert_ID();

                // deactivate plugin
                $pi = new plugin_installed();
                $pi->_setStatus($this->plugin_id, false, true);

                // backup plugin folder and files
                $fileOutput .= $this->backupPlugin($modul, $pluginHistoryId,true,$this->getPluginVersion($modul));


                // generate unique panel id
                $indexID = 'install' . time();
                $add_to_url = (isset($_SESSION['admin_user']['admin_key'])) ? ", sec: '" . $_SESSION['admin_user']['admin_key'] . "'" : '';
                // panel with ajax button
                $tp = new PhpExt_Panel();
                $tp->setTitle(' ')->setAutoScroll(true)->setAutoWidth(true);
                $tb = $tp->getBottomToolbar();
                $tb->addButton(1, __text('TEXT_RUN_INSTALL'), 'images/icons/plugin_go.png', new PhpExt_Handler(PhpExt_Javascript::stm("
                 this.disable();
                 var conn = new Ext.data.Connection();
                 conn.request({
                 url: 'plugin_install.php',
                 method:'GET',
                 params: {update_type: '" . $update_type . "', plugin_id: " . $_GET['plugin_id'] . ", pluginHistoryId: " . $pluginHistoryId . $add_to_url . " },
                 error: function(responseObject) {
                            Ext.Msg.alert('" . __text('TEXT_ALERT') . "', '" . __text('TEXT_NO_SUCCESS') . "');
                          },
                 waitMsg: '" . __text("TEXT_LOADING") . "',
                 success: function(responseObject) {
                        jQuery('#" . $indexID . "').html(responseObject.responseText)
                          }
                 });")));

                $tb->setRenderTo(PhpExt_Javascript::variable("Ext.get('" . $indexID . "')"));

                $js = PhpExt_Ext::OnReady(
                    PhpExt_Javascript::stm(PhpExt_QuickTips::init()),
                    $tb->getJavascript(false, "panel")
                );

                // prepare output data
                $output .= $fileOutput;
                $output .= '<script type="text/javascript">' . $js . '</script><div id="' . $indexID . '">';
                if ($onlinePlugin->code != 'UPDATE')
                {
                    $output .= '<p style="padding: 20px 0;">' . sprintf(__text('TEXT_UPLOAD_PLUGIN'), $pluginPath) . ' </p>';
                }
                $output .= '</div>';

        }
        else
        {
            // run install
            if (is_file($xml))
            {


                if ($_GET['update_type']=='auto') {

                    $return = $this->checkZipFile(self::PLUGIN_DOWNLOAD_PATH.$modul.'.zip');

                    if ($return===true) {
                        $this->removeDirectoryRecursive(self::PLUGIN_DIRECTORY.$modul);
                        rmdir(self::PLUGIN_DIRECTORY.$modul);

                        // extract plugin
                        $output_zip= $this->unzip_file(self::PLUGIN_DOWNLOAD_PATH.$modul.'.zip',self::PLUGIN_DIRECTORY,$modul);



                        $output .= $this->InstallPlugin($xml);
                        $output .= '<br /><hr noshade>';

                        $output .= '<ul class="stack">';
                        if (isset($output_zip)) $output.=$output_zip;
                        $output .= $this->debug_output;
                        $output .= '</ul>';

                        $new_version = $this->getPluginVersion($modul);

                        if ($_GET['pluginHistoryId'])
                        {
                            $pluginHistoryId = $_GET['pluginHistoryId'];
                            $updateHistory = array('current_version' => $new_version);
                            $db->AutoExecute(TABLE_PLUGIN_HISTORY, $updateHistory, "UPDATE", "id='" . $pluginHistoryId . "'");
                        }

                        $log_data = array();
                        $log_data['message'] = 'Updated Plugin '.$modul.' to version '.$new_version;
                        $log_data['time'] = time();
                        $logHandler->_addLog('success', __CLASS__ . '_updater_' . $modul, '', $log_data);



                    } else {
                        $output_zip ='<ul class="stack"><li class="error">'.$return.'</li></ul>';

                        $output .= '<br /><hr noshade>';

                        $output .= '<ul class="stack">';
                        if (isset($output_zip)) $output.=$output_zip;
                        $output .= $this->debug_output;
                        $output .= '</ul>';

                    }
                    unlink(self::PLUGIN_DOWNLOAD_PATH.$modul.'.zip');
                } else {
                    $output .= $this->InstallPlugin($xml);
                    $output .= '<br /><hr noshade>';

                    $output .= '<ul class="stack">';
                    if (isset($output_zip)) $output.=$output_zip;
                    $output .= $this->debug_output;
                    $output .= '</ul>';
                }




            }
            else
            {
                $output = '<h1>- File Error -</h1>';
            }

        }

        echo '<div class="highlightbox">' . $output . '</div>';
    }

    /**
     * check if we can open zip file
     * @param $file
     * @return bool|string
     */
    private function checkZipFile($file) {

        if (class_exists( 'ZipArchive', false )) {
            $zip = new ZipArchive();
            $zip_open = $zip->open( $file, ZIPARCHIVE::CHECKCONS );

            if ( true !== $zip_open ) {
                return 'Could not open zip Archive '.$file. ' (code: '.$zip_open.')';
            } else {
                return true;
            }

        } else {
            return 'zip class missing, contact Support';
        }

    }

    /**
     * unzip file
     * @param $file
     * @param $to
     * @param $plugin
     */
    private function unzip_file($file,$target_path,$plugin) {

        $output = '<li class="success"><b>' . __text('TEXT_PLUGIN_COPY_FILES') . '</b></li>';

        $target_file_perm = (fileperms(_SRV_WEBROOT.'index.php') & self::FILE_PERMISSIONS | 0644);

        if (class_exists( 'ZipArchive', false )) {

            $zip = new ZipArchive();
            $zip_open = $zip->open( $file, ZIPARCHIVE::CHECKCONS );
            if ( true !== $zip_open ) {
                die ('Could not open zip Archive '.$file. ' (code: '.$zip_open.')');
            }

            // go over files and create them with proper chmod
            for ( $i = 0; $i < $zip->numFiles; $i++ ) {
                $file = $zip->statIndex($i);

                if ( substr( $file['name'], -1 ) == '/' ) {
                    if (!is_dir(_SRV_WEBROOT.'plugins/'.$file['name'])) {
                        mkdir(_SRV_WEBROOT.'plugins/'.$file['name'],0777);
                    }
                    continue;
                }

                $file_content=$zip->getFromIndex($i);

                file_put_contents($target_path.$file['name'],$file_content);
                chmod($target_path.$file['name'],$target_file_perm);

                $output .= '<li class="plugin_config_add">'.__text('TEXT_PLUGIN_FILE_ADDED').': ' . $target_path.$file['name'] . '</li>';

            }


            $zip->close();

        } else {
            die ('zip class missing'); // TODO
        }

        return $output;

    }

    /**
     * delete plugin directory
     * @param $path
     */
    private function removeDirectoryRecursive($path) {

        $DI = new DirectoryIterator($path);
        foreach ($DI as $file) {
            if ($file->isFile()) {
                unlink($file->getRealPath());
            } elseif (!$file->isDot() && $file->isDir()) {
                $this->removeDirectoryRecursive($file->getRealPath());
                rmdir($file->getRealPath());
            }
        }

    }

    /**
     * backup existing plugin to plugin_cache folder
     * @param $pluginCode
     * @param $historyId
     * @param bool $verbose
     * @return bool|string
     */
    function backupPlugin($pluginCode, $historyId, $verbose ,$old_version)
    {
        if (!$pluginCode)
        {
            return false;
        }

        $backupFolder = _SRV_WEBROOT . 'plugin_cache/backup/';
        if (!is_dir($backupFolder))
        {
            mkdir($backupFolder);
            chmod($backupFolder, 0777);
        }

        $srcdir = _SRV_WEBROOT . 'plugins/' . $pluginCode;
        $dstdir = $backupFolder . $pluginCode . '_'.$old_version.'_' . $historyId;

        $output = '<br /><b>Backup to: ' . $dstdir . '/</b><br />';
        $return = $this->dircopy($srcdir, $dstdir, $verbose);
        $output .= '<div style="padding: 5px; border: 1px solid #777777; height: 100px; overflow:auto;">' . $return['output'] . '</div>';
       // $output .= '<b>' . $return['count'] . ' files copied</b><br />';
        $output .= '<ul class="stack"><li class="success">'.$return['count'] . ' files copied</li></ul>';
        return $output;

    }

    private function getPluginChangeLogInformation($pluginCode) {
        global $store_handler;

        if (!$pluginCode)
        {
            return false;
        }
        $values = array('license_key'=>$store_handler->_lic_license_key,
            'plugin_code'=>$pluginCode);

        try {

            $response = $this->client->request ( 'POST', 'plugins/getPluginChangeLog', [
                'json' => $values
            ] );

            return json_decode($response->getBody());

        } catch ( GuzzleHttp\Exception\ClientException $e ) {

            if ($e->hasResponse ()) {

                $response = json_decode ( $e->getResponse ()->getBody () );
                $error = json_encode($response);
            }
        } catch ( GuzzleHttp\Exception\ConnectException $e ) {

            $error = $e->getMessage();
        }
        catch ( Exception $e ) {

            $error = $e->getMessage();
        }
    }

    /**
     * get latest plugin url, if available
     * @param $pluginCode
     * @param $pluginVersion
     * $param $error passed by reference
     * @param bool|mixed $error
     * @return bool|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getPluginUpdatePackage($pluginCode, $pluginVersion, &$error = '') {
        global $store_handler;

        if (!$pluginCode)
        {
            return false;
        }

        $scope = '';
        if (defined('_PLUGIN_UPDATE_SCOPE') && _PLUGIN_UPDATE_SCOPE=='DEV') {
            $scope = 'DEV';
        }

        $values = array('license_key'=>$store_handler->_lic_license_key,
            'shop_version'=>_SYSTEM_VERSION,
            'plugin_version'=>$pluginVersion,
            'plugin_code'=>$pluginCode,
            'scope'=>$scope);


        try {

            $response = $this->client->request ( 'POST', 'plugins/getPluginUpdatePackage', [
                'json' => $values
            ] );

            return json_decode($response->getBody());

        } catch ( GuzzleHttp\Exception\ClientException $e ) {

            if ($e->hasResponse ()) {

                $response = json_decode ( $e->getResponse ()->getBody () );
                $error = json_encode($response);
            }
        } catch ( GuzzleHttp\Exception\ConnectException $e ) {

            $error = $e->getMessage();
        }
        catch ( Exception $e ) {

            $error = $e->getMessage();
        }
    }

    /**
     * download plugin from given url
     * @param $url
     * @param $pluginCode
     * @param $pluginVersion
     * @return array|void
     */
    function downloadPluginUpdatePackage($url,$pluginCode,$pluginVersion) {

        if(!is_dir(self::PLUGIN_DOWNLOAD_PATH))
        {
            mkdir(self::PLUGIN_DOWNLOAD_PATH,0777);
        }

        $path = self::PLUGIN_DOWNLOAD_PATH.$pluginCode.'.zip';

        try {

            $response = $this->client->get($url,['sink'=>$path]);


        } catch ( GuzzleHttp\Exception\ClientException $e ) {

            if ($e->hasResponse ()) {

                $response = json_decode ( $e->getResponse ()->getBody () );
                echo json_encode($response);
                return;
            }
        }

        return ['response_code'=>$response->getStatusCode(),'name'=>$path];


    }

    function dircopy($srcdir, $dstdir, $verbose = false)
    {
        $num = 0;
        if (!is_dir($dstdir))
        {
            mkdir($dstdir);
            chmod($dstdir, 0777);
        }

        $output = '';
        if ($curdir = opendir($srcdir))
        {
            while ($file = readdir($curdir))
            {
                if ($file != '.' && $file != '..' && $file != '.svn' && $file != '.DS_Store')
                {
                    $srcfile = $srcdir . '/' . $file;
                    $dstfile = $dstdir . '/' . $file;

                    if (is_file($srcfile))
                    {
                        if (is_file($dstfile))
                        {
                            $ow = filemtime($srcfile) - filemtime($dstfile);
                        }
                        else
                        {
                            $ow = 1;
                        }
                        if ($ow > 0)
                        {
                            if ($verbose)
                            {
                                $output .= "  Copying " . str_replace(_SRV_WEBROOT, '', $srcfile) . "...";
                            }
                            if (copy($srcfile, $dstfile))
                            {
                                touch($dstfile, filemtime($srcfile));
                                $num++;
                                if ($verbose)
                                {
                                    $output .= "OK<br />";
                                }
                            }
                            else
                            {
                                $output .= "Error: File " . str_replace(_SRV_WEBROOT, '', $srcfile) . " could not be copied!<br />";
                            }
                        }
                    }
                    elseif (is_dir($srcfile))
                    {
                        $data = $this->dircopy($srcfile, $dstfile, $verbose);
                        $num += $data['count'];
                        $output .= $data['output'];
                    }
                }
            }
            closedir($curdir);
        }

        return array('count' => $num, 'output' => $output);
    }

    /**
     * Return is plugin activated or not (true/false)
     * If plugin not installed return false.
     * @global type $db
     * @param string $code
     * @return boolean
     */
    public function getPluginStatus($code)
    {
        global $db;

        $sql = "
				SELECT `plugin_status`
                FROM " . TABLE_PLUGIN_PRODUCTS . "
                WHERE  `code` = ?
            ";
        $rs = $db->Execute($sql, array($code));
        if ($rs->RecordCount() == 1)
        {
            if ($rs->fields['plugin_status'] == 1)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        return false;
    }

    function sortVersion($v1, $v2)
    {
        return version_compare($v1, $v2);
    }
}