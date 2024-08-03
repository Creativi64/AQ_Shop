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

global $language;
if(!defined('TEXT_NEW_SHOP_BASE_ID'))
{
    if($language && $language->code == 'en') $txt = 'based on shop with ID';
    else $txt = 'basierend auf Shop mit ID';
    define('TEXT_NEW_SHOP_BASE_ID', $txt);
}
if(!defined('TEXT_NEW_SHOP_BASE_ID_INFO'))
{
    if($language && $language->code == 'en') $txt = '';
    else $txt = '';
    define('TEXT_NEW_SHOP_BASE_ID_INFO', $txt);
}
if(!defined('TEXT_NEW_SHOP_BASE_ID_INFO_TEXT'))
{
    if($language && $language->code == 'en') $txt = 'A copy is created based on the specified shop. Articles, categories etc. are created as in this shop.';
    else $txt = 'basierend auf angegebenem Shop wird eine Kopie erstellt. Artikel, Kategorien etc werden angelegt wie in diesem Shop.';
    define('TEXT_NEW_SHOP_BASE_ID_INFO_TEXT', $txt);
}

class multistore_base extends xt_backend_cls
{
    public $shop_id = 1;

    protected $_table = TABLE_MANDANT_CONFIG;
    protected $_table_lang = null;
    protected $_table_seo = null;
    protected $_master_key = 'shop_id';
    public $_lic_license_key,$_lic_license_created,$_lic_license_mailaddress,$_lic_license_project,$_lic_license_updatesuntil,$_lic_license_versiontype,$_lic_license_isdemo;

    public $defaultStore;
    public $active;
    public $store_count;
    public $_lic_trial_end_time;
    public $shop_active;
    public $domain;

    function __construct($admin = false)
    {
        $this->defaultStore = '1';

        $this->active = true;

        $this->readLicenseFile();
        if ($this->_lic_license_versiontype=='FREE') {
            $this->store_count = 1;
        } else {
            $this->store_count = $this->getStoresCount();

        }
    }

    /** load license file params */
    private function readLicenseFile() {

        $params = $this->getLicenseFileInfo(array('created','key','license','mailaddress','project','updatesuntil','versiontype','isdemo','trialdays'));

        $this->_lic_license_key = $params['key'];
        $this->_lic_license_created = $params['created'];
        $this->_lic_license_mailaddress = $params['mailaddress'];
        $this->_lic_license_project = $params['project'];
        $this->_lic_license_updatesuntil = $params['updatesuntil'];
        $this->_lic_license_versiontype = $params['versiontype'];
        $this->_lic_license_isdemo = array_key_exists('isdemo',$params) ? $params['isdemo'] : 0;
        if ($this->_lic_license_isdemo==1) {
            $trial_end_time = date('Y-m-d', strtotime($this->_lic_license_created . " + ".$params['trialdays']." day"));
            $this->_lic_trial_end_time = $trial_end_time;
        }

    }

    /**
     * read license file and return requested parameters
     * @param array $params
     * @param string $error_code
     * @return array|void
     */
    public function getLicenseFileInfo($params=array(), $error_code = 'lic62') {
    	
    	if (count($params)==0) return;
    	$return=array();
    	$_lic = _SRV_WEBROOT . 'lic/license.txt';
    	if (!file_exists($_lic))
    		die('- xt:Commerce License File missing - '.$error_code);


    	$_file_content = file($_lic);
    	foreach ($_file_content as $bline_num => $bline) {
    		if (strpos($bline, ':') !== FALSE) {

    			$val_line = substr($bline,0,strpos($bline, ':'));

    			if (in_array($val_line,$params)) {
    				$return[$val_line]=trim(substr($bline,strpos($bline, ':')+1));
    			}
    		}

    	}
    	return $return;
    }

    function checkAdminSSL()
    {
        global $db;

        if(!defined('_SYSTEM_ADMIN_SSL'))
        {
            $admin_ssl = false;
            $this->determineStoreId();
            $query = "SELECT shop_ssl FROM " . TABLE_MANDANT_CONFIG . " where shop_id=?";
            $record = $db->Execute($query, array($this->shop_id));
            if ($record->RecordCount() == 1 && $record->fields['shop_ssl'] == 1)
            {
                $admin_ssl = true;
            }

            define('_SYSTEM_ADMIN_SSL', $admin_ssl);
        }
    }

    function redirectAdminSSL()
    {
        global $filter;

        $port='';
        if(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT']!='443' && $_SERVER['SERVER_PORT']!='80') $port =':'.$_SERVER['SERVER_PORT'];

        $domain = $filter->_filter($_SERVER['SERVER_NAME']).$port;

        // wenn in der db xt_stores.shop_ssl 1 steht
        // versuchen wir auf auf https umzuleiten falls mit http aufgerufen (checkHTTPS() == false)
        // oder auch wenn 1&1 sonderfall
        if (_SYSTEM_ADMIN_SSL) // since 6.1 it takes its value from xt_stores.shop_ssl
        {
            $reload = false;
            $replace = "https://";
            if ((str_replace($replace, "", _SYSTEM_BASE_HTTPS) != $domain))
            {
                // ok, jetzt noch ein versuch für 1&1
                // 1&1 liefert den SERVER_NAME ohne www aus, dh wenn erreichbar unter www.meinsupershop.de ist server_name = meinsupershop.de
                // ? anders bei shop.supershop.de, dann ist server_name = shop.supershop.de, dann landen wir hier aber auch nicht
                if ((str_replace("https://www.", "", _SYSTEM_BASE_HTTPS) != $domain))
                {
                    $reload = true;
                }
            }

            $checkHttps = checkHTTPS();

            if ($checkHttps == false || $reload)
            {
                $param = 'admsslreload';
                if($_REQUEST[$param] == 1)
                {
                    $details = '
ERROR                   => redirectAdminSSL-1                    
_SYSTEM_BASE_HTTPS      => '._SYSTEM_BASE_HTTPS.'
$_SERVER[\'SERVER_NAME\'] => '.$_SERVER['SERVER_NAME'].'
$_SERVER[\'REQUEST_URI\'] => '.$_SERVER['REQUEST_URI'].'
$_SERVER[\'REQUEST_PORT\'] => '.$_SERVER['REQUEST_PORT'].'
reload                  => '. ($reload ? 'true' : 'false').'
checkHttps              => '. ($checkHttps ? 'true' : 'false');

                    $emailBody = str_replace(PHP_EOL,'%0D%0A', $details);
                    $emailBody = str_replace(' ','', $emailBody);

                    $msg = '<b>Ooops</b>, irgend etwas läuft hier falsch in der ssl/https-Konfiguration des Hostings und/oder des Shops. 
                    Infos dazu finden Sie im <a href="https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917676" target="_blank">Handbuch</a>.
                    Oder Sie finden eine Lösung im <a href="https://forums.xt-commerce.com/search/?q=ssl" target="_blank">xt:Commerce Community Forum</a>.
                     
                    Diese Seite nicht neu laden, sondern nach Behebung der Probleme die Original-Seite aufrufen > <a href="./login.php">xtAdmin/login.php</a>
                    
                    Wenn Sie das Problem nicht selbständig lösen konnten, wenden Sie sich bitte per E-Mail an den <a href="mailto:helpdesk@xt-commerce.com?subject=redirectAdminSSL-1&body='.$emailBody.'">Helpdesk</a>. 
                    Geben Sie dabei die unten aufgeführten Werte (Error etc) mit an.' ;

                    $details = '<pre style="font-size: 0.8em;">'.$details.'</pre>';

                    die('<div style="background-color: #f4ef94; border: solid 1px #9a834e; max-width:600px; margin: 20px auto; padding:10px; line-height:1.5em; font-family: sans-serif">' .nl2br($msg).$details.'</div>');
                }

                $apx = strpos($_SERVER['REQUEST_URI'],'?') === false ? '?' : '&';
                $apx .= $param.'=1';
                header('location: https://' . $domain . $_SERVER['REQUEST_URI'].$apx);
            }
        }

        // wenn in der db xt_stores.shop_ssl 0 steht ...
        else {
            // wenn in der db xt_stores.shop_ssl 0 steht und wir aber mit https soweit gekommen sind (checkHTTPS)
            if(checkHTTPS() == true)
            {
                // hinweis ssl-aktivierung im shop
                return 'Es scheint, dass der Shop nicht für https/ssl konfiguriert ist, obwohl der Shop auch über https erreichbar ist. Bitte stellen Sie den Shop auf Verwendung von SSL um. <a href="https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917676" target="_blank">Handbuch</a>';
            }
            // oder SSL ist einfach nicht verfügbar
            else {
                // hinweis, dass SSL im hosting und im backend aktiviert werden soll
                return 'Es scheint, dass Ihr Hosting und/oder der Shop nicht für https/ssl konfiguriert ist. Bitte aktivieren Sie Hosting und Shop auf Verwendung von SSL. <a href="https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917676" target="_blank">Handbuch</a>';
            }
        }
        return 0;
    }

    function determineStoreId()
    {
        global $filter;

        $this->shop_id = $this->defaultStore;
        $this->setStatusAndDieIfNotActive($this->shop_id);

        $domain = $filter->_filter($_SERVER['SERVER_NAME']);
        $this->domain = $domain;

        return $this->shop_id;
    }

    function setStatusAndDieIfNotActive($store_id)
    {
        global $db, $debug_ip;

        $query = "SELECT shop_status FROM " . TABLE_MANDANT_CONFIG . " where shop_id = ? ";
        $shop_status = $db->GetOne($query, array($store_id));

        if($shop_status == 0)
        {
            $this->shop_active = false;

            if (empty($debug_ip) && USER_POSITION == 'store' && !defined('XT_WIZARD_STARTED'))
            {
                $error_file = _SRV_WEBROOT . 'xtCore/ShopNotActive.html';

                if (file_exists($error_file)) {
                    $fp = fopen($error_file, "rb");
                    $buffer = fread($fp, filesize($error_file));
                    fclose($fp);
                    die ($buffer);
                }
            }
        }
    }

    /**
     * load and define main store configuration from config_x
     * @param string $id
     */
    function loadStoreConfigMainData($id = '')
    {
        global $db, $xtPlugin, $language;
        ($plugin_code = $xtPlugin->PluginCode(__CLASS__ . ':loadConfig_top')) ? eval($plugin_code) : false;

        if ($id != '')
        {
            $this->shop_id = $id;
        }
        else
        {
            if (!isset ($this->shop_id))
            {
                $this->shop_id = $this->defaultStore;
            }
        }

        _buildDefine($db, TABLE_CONFIGURATION_MULTI . $this->shop_id);
    }

    /**
     * load main store configuration
     * @param string $id
     * @return array :NULL
     */
    function getStoreConfig($id='') {
    	global $db, $xtPlugin, $language;
        /** @var ADORecordSet $record */
    	
    	($plugin_code = $xtPlugin->PluginCode(__CLASS__ . ':getStoreConfig')) ? eval($plugin_code) : false;
    	
    	if ($id != '')
    	{
    		$this->shop_id = $id;
    	}
    	else
    	{
    		if (!isset ($this->shop_id))
    		{
    			$this->shop_id = $this->defaultStore;
    		}
    	}
    	
    	$record = $db->Execute("SELECT config_key, config_value FROM " .TABLE_CONFIGURATION_MULTI . $this->shop_id);
    	$config = array();
    	while (!$record->EOF) {   		
    		$config[strtoupper($record->fields['config_key'])] = $record->fields['config_value'];
    		$record->MoveNext();
    	}
    	$record->Close();
    	return $config;
    }

    /**
     * load and define full store configuration (config, payment, language)
     * @param string $id
     */
    function loadConfig($id = '')
    {
        global $db, $xtPlugin, $language;
        ($plugin_code = $xtPlugin->PluginCode(__CLASS__ . ':loadConfig_top')) ? eval($plugin_code) : false;

        if ($id != '') {
            $this->shop_id = $id;
        } else {
            if (!isset ($this->shop_id))
                $this->shop_id = $this->defaultStore;
        }

        _buildDefine($db, TABLE_CONFIGURATION_LANG_MULTI, 'config_key', 'language_value', 'store_id=' . $this->shop_id . ' AND language_code='.$db->Quote($language->content_language));

        _buildDefine($db, TABLE_CONFIGURATION_PAYMENT, 'config_key', 'config_value', 'shop_id=' . $this->shop_id);

        _buildDefine($db, TABLE_PLUGIN_CONFIGURATION, 'config_key', 'config_value', 'shop_id=' . $this->shop_id);

        $record = $db->Execute("SELECT * FROM " . TABLE_MANDANT_CONFIG . " where shop_id = ?", [(int)$this->shop_id]);

        // teilweise übernahme aus startpagescript 'mandant sichern'
        $portHttp='';
        $portHttps='';
        if(checkHTTPS() && isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT']!='443') {
            // do not add port 80 if SSL is dedected, in case of reverse SSL proxy
            if ($_SERVER['SERVER_PORT']!='80') $portHttps =':'.$_SERVER['SERVER_PORT'];
        } else if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT']!='80') {
            $portHttp  =':'.$_SERVER['SERVER_PORT'];
        }
        $domain = $record->fields['shop_ssl_domain'];
        define('_SHOP_SSL_DOMAIN', $domain);

        $shop_http  = 'http://'.$domain.$portHttp;
        $shop_https = 'https://'.$domain.$portHttps;

        define('_SYSTEM_BASE_HTTP', $shop_http);
        define('_SYSTEM_BASE_HTTPS', $shop_https);

        if ($record->fields['shop_ssl'] == 0) {
            define('_SYSTEM_SSL', false);
            define('_SYSTEM_FULL_SSL', false);
            $ssl = false;
        } else {
            define('_SYSTEM_SSL', true);
            define('_SYSTEM_FULL_SSL', true);
            $ssl = false;
            if (checkHTTPS()) {
                $ssl = true;
            }
        }

        if (!$ssl) {
            define('_SYSTEM_CONNECTION', 'NOSSL');
            define('_SYSTEM_BASE_URL', _SYSTEM_BASE_HTTP);
        } else {
            define('_SYSTEM_CONNECTION', 'SSL');
            define('_SYSTEM_BASE_URL', _SYSTEM_BASE_HTTPS);
        }
    }

    /**
     * get count of stores
     * @return mixed
     */
    private function getStoresCount() {
        global $db;

        $rs = $db->CacheExecute("SELECT count(*) as count FROM " . TABLE_MANDANT_CONFIG);
        return $rs->fields['count'];

    }

    function getStores()
    {
        global $db, $xtPlugin;
        /** @var ADORecordSet $rs */

        $where = '';
        ($plugin_code = $xtPlugin->PluginCode(__CLASS__ . ':getStores_top')) ? eval($plugin_code) : false;
        static $cache =  array();

        $hash = $where; //crc32($where);
        if(!array_key_exists($hash, $cache))
        {
            $rs = $db->CacheExecute("SELECT * FROM " . TABLE_MANDANT_CONFIG . " " . $where . " ORDER BY shop_id");
            $data = array();
            while (!$rs->EOF)
            {
                $data[] = array(
                    'id' => $rs->fields['shop_id'],
                    'text' => $rs->fields['shop_ssl_domain'],
                    'status' => $rs->fields['shop_status']
                );
                $rs->MoveNext();
            }
            $rs->Close();

            $cache[$hash] = $data;
        }
        $data = $cache[$hash]; // to be compatible for old code using the next hook

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__ . ':getStores_bottom')) ? eval($plugin_code) : false;

        return $data;
    }

    /**
     * define store permission names for TEXT_SHOP_n
     *
     */
    function _defineStorePermissionNames()
    {
        global $db;
        /** @var ADORecordSet $rs */

        $rs = $db->Execute("SELECT shop_ssl_domain,shop_id FROM " . TABLE_MANDANT_CONFIG);
        if ($rs->RecordCount() > 0) {
            while (!$rs->EOF) {
                define('TEXT_SHOP_' . $rs->fields['shop_id'], $rs->fields['shop_ssl_domain']);
                $rs->MoveNext();
            }
            $rs->Close();
        }
    }

    function getStoreName($id)
    {
        global $db;

        $shop_ssl_domain = $db->GetOne("SELECT shop_ssl_domain FROM " . TABLE_MANDANT_CONFIG . " where shop_id = ?", array((int)$id));
        return $shop_ssl_domain;
    }

    function saveStore($data)
    {
        global $db, $filter, $currency, $language;
        /** @var ADORecordSet $rs */

        $shop_id = (int)$data['edit_id'];

        $data_array = array();
        $data_array['shop_ssl_domain'] = $filter->_filter($data['shop_ssl_domain']);
        $data_array['shop_ssl'] = $filter->_filter($data['shop_https_status']);

        if ($data_array['shop_ssl'] != 1)
            $data_array['shop_ssl'] = 0;

        $db->AutoExecute(TABLE_MANDANT_CONFIG, $data_array, 'UPDATE', 'shop_id=' . (int)$shop_id);

        // language & Currency
        $db->Execute("UPDATE " . TABLE_LANGUAGES . " SET shop_" . $shop_id . "=0");
        foreach ($language->_getLanguageList('admin') as $key => $val) {
            if ($data['lang_' . $val['id']] == 'on')
                $db->Execute("UPDATE " . TABLE_LANGUAGES . " SET shop_" . $shop_id . "=1 WHERE code=?", array($val['id']));
        }

        $db->Execute("UPDATE " . TABLE_CURRENCIES . " SET shop_" . (int)$shop_id . "=0");
        foreach ($currency->_getCurrencyList('admin') as $key => $val) {
            if ($data['currency_' . $val['id']] == 'on')
                $db->Execute("UPDATE " . TABLE_CURRENCIES . " SET shop_" . (int)$shop_id . "=1 WHERE code=?", array($val['id']));
        }

        $db->Execute(
            "UPDATE " . TABLE_CONFIGURATION_MULTI . $shop_id . " SET config_value=? where config_key='_STORE_CURRENCY'",
            array($data['default_currency'])
        );
        $db->Execute(
            "UPDATE " . TABLE_CONFIGURATION_MULTI . $shop_id . " SET config_value=? where config_key='_STORE_LANGUAGE'",
            array($data['default_language'])
        );

        $rs = $db->Execute("select * from " . TABLE_CONFIGURATION_MULTI . $shop_id);
        while (!$rs->EOF) {
            if ($rs->fields['group_id'] != 6) {
                $db->Execute(
                    "UPDATE " . TABLE_CONFIGURATION_MULTI . $shop_id . " SET config_value=? where config_key=?",
                    array($data[$rs->fields['config_key']], $rs->fields['config_key'])
                );
            }
            $rs->MoveNext();
        }
        $rs->Close();
    }


    function inStallStore($data)
    {
        global $db, $filter;

        $insert_array = array();
        $insert_array['shop_ssl_domain'] = $filter->_quote($data['shop_ssl_domain']);
        $insert_array['shop_ssl'] = (int)$data['shop_ssl'];
        $insert_array['shop_status'] = '0';
        $db->AutoExecute(TABLE_MANDANT_CONFIG, $insert_array);
        $shop_id = $db->Insert_ID();
        // replicate shopsettings
        $db->Execute("DROP TABLE IF EXISTS " . TABLE_CONFIGURATION_MULTI . $shop_id);
        $db->Execute("CREATE TABLE " . TABLE_CONFIGURATION_MULTI . $shop_id . " LIKE " . TABLE_CONFIGURATION_MULTI . "1");
        $db->Execute("INSERT INTO " . TABLE_CONFIGURATION_MULTI . $shop_id . " select * FROM " . TABLE_CONFIGURATION_MULTI . "1");
        // extend languages
        $db->Execute("ALTER TABLE " . TABLE_LANGUAGES . " ADD shop_" . $shop_id . " INT( 11 ) NOT NULL DEFAULT '0'");
        $db->Execute("ALTER TABLE " . TABLE_CURRENCIES . " ADD shop_" . $shop_id . " INT( 11 ) NOT NULL DEFAULT '0'");
        $db->Execute("ALTER TABLE " . TABLE_CUSTOMERS_STATUS . " ADD shop_" . $shop_id . " INT( 11 ) NOT NULL DEFAULT '0'");
        $db->Execute("ALTER TABLE " . TABLE_MAIL_TEMPLATES_ATTACHMENT . " ADD shop_" . $shop_id . " INT( 11 ) NOT NULL DEFAULT '0'");
        $db->Execute("ALTER TABLE " . TABLE_CONTENT . " ADD shop_" . $shop_id . " INT( 11 ) NOT NULL DEFAULT '0'");
        $db->Execute("ALTER TABLE " . TABLE_MAIL_TEMPLATES . " ADD shop_" . $shop_id . " INT( 11 ) NOT NULL DEFAULT '0'");

        // language & Currency
        $db->Execute("UPDATE " . TABLE_CURRENCIES . " SET shop_" . $shop_id . "=0");
        if (is_array($data['currencies'])) {
            foreach ($data['currencies'] as $key => $var) {
                $db->Execute("UPDATE " . TABLE_CURRENCIES . " SET shop_" . $shop_id . "=1 WHERE code=?", array($var));
            }
        }
        $db->Execute("UPDATE " . TABLE_LANGUAGES . " SET shop_" . $shop_id . "=0");
        if (is_array($data['languages'])) {
            foreach ($data['languages'] as $key => $var) {
                $db->Execute("UPDATE " . TABLE_LANGUAGES . " SET shop_" . $shop_id . "=1 WHERE languages_id=?", array($var));
            }
        }
    }

    function deleteStore($id)
    {
        global $db;
        $shop_id = (int)$id;
        if ($id != '1') {
            // drop config DB
            $db->Execute("DROP TABLE IF EXISTS " . TABLE_CONFIGURATION_MULTI . $shop_id);
            $db->Execute("ALTER TABLE " . TABLE_LANGUAGES . " DROP shop_" . $shop_id);
            $db->Execute("ALTER TABLE " . TABLE_CURRENCIES . " DROP shop_" . $shop_id);
            $db->Execute("ALTER TABLE " . TABLE_CUSTOMERS_STATUS . " DROP shop_" . $shop_id);
            $db->Execute("ALTER TABLE " . TABLE_MAIL_TEMPLATES_ATTACHMENT . " DROP shop_" . $shop_id);
            $db->Execute("ALTER TABLE " . TABLE_CONTENT . " DROP shop_" . $shop_id);
            $db->Execute("ALTER TABLE " . TABLE_MAIL_TEMPLATES . " DROP shop_" . $shop_id);

            // remove entry
            $db->Execute("DELETE FROM " . TABLE_MANDANT_CONFIG . " WHERE shop_id=?", array($shop_id));
            // remove payment config
            $db->Execute("DELETE FROM " . TABLE_CONFIGURATION_PAYMENT . " WHERE shop_id=?", array($shop_id));
        }
    }

    function getStoresData($parent = 0)
    {

        if ($parent == 'stores') {
            $store_id = 0;
        }
        if (strstr($parent, 'stores_')) {
            $tmp = explode('_', $parent);
            $store_id = (int)$tmp[1];
        }


        $stores = $this->getStores();
        $erg = array();
        if (is_array($stores)) {
            foreach ($stores as $key => $store) {

                $leaf = '';
                $type = 'G';
                $icon = 'images/icons/server.png';
                if ($store['status'] == 0) $icon = 'images/icons/server_delete.png';

                $url_d = 'adminHandler.php?load_section=multistore&edit_id=' . $store['id'];
                $arrTMP = Array('text' => $store['text'].'  id:'.$store['id']
                , 'url_i' => ''
                , 'url_d' => $url_d
                , 'url_h' => 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917676/'
                , 'tabtext' => $store['text']
                , 'id' => 'store_' . $store['id']
                , 'type' => 'G'
                , 'leaf' => ''
                , 'icon' => $icon
                );
                $erg[] = $arrTMP;
            }

        }
        return $erg;
    }

    function copyPermissions($table, $target_store, $source_store = '1')
    {
        global $db;
        /** @var ADORecordSet $record */

        if(empty($source_store))
        {
            $result = $this->getOldestShop();
            if ($result != '')
            {
                $source_store = $result['shop_id'];
            }
        }

        $record = $db->Execute("SELECT * FROM " . $table . " where pgroup = 'shop_" . $source_store . "'");
        while (!$record->EOF) {

            $record->fields['pgroup'] = 'shop_' . $target_store;
            $db->AutoExecute($table, $record->fields);

            $record->MoveNext();
        }
        $record->Close();

    }

    /* copySeoValuesByShop
     *
     * copy content of a given table
     *
     * @param (string) ($table) - the resource table
     * @param (string) ($target_store) - data will be copied for this store id
     * @param (string) ($source_store) - store details to be copied
     * @param (string) ($store_field) - filed for store ID in the given table
     * @param (string) ($where) - sql added to query
     */
    function copyTableValuesByShop($table, $target_store, $source_store = '', $store_field = 'shop_id', $where = '', $primary_key = 'id')
    {
        global $db;
        /** @var ADORecordSet $record */

        if(empty($source_store))
        {
            $result = $this->getOldestShop();
            if ($result != '')
            {
                $source_store = $result['shop_id'];
            }
        }

        $record = $db->Execute("SELECT * FROM " . $table . " where " . $store_field . " = ? " . $where, array($source_store));
        while (!$record->EOF) {

            $record->fields[$store_field] = $target_store;
            unset ($record->fields[$primary_key]);
            $db->AutoExecute($table, $record->fields);

            $record->MoveNext();
        }
        $record->Close();

    }

    /* copySeoValuesByShop
     *
     * copy content of seo_urls table
     *
     * @param (string) ($table) - the resource table
     * @param (string) ($target_store) - data will be copied for this store id
     * @param (string) ($source_store) - store details to be copied
     */
    function copySeoValuesByShop($table, $target_store, $source_store = '')
    {
        global $db;
        /** @var ADORecordSet $record */

        if(empty($source_store))
        {
            $result = $this->getOldestShop();
            if ($result != '')
            {
                $source_store = $result['shop_id'];
            }
        }

        $record = $db->Execute("SELECT * FROM " . $table . " where store_id = ? ", array($source_store));
        while (!$record->EOF) {

            $record->fields['store_id'] = $target_store;
            unset ($record->fields['id']);
            $record->fields['url_md5'] = md5($record->fields['url_text']);
            $db->AutoExecute($table, $record->fields);

            $record->MoveNext();
        }
        $record->Close();

    }

    /**
     * @return array
     */
    function getOldestShop()
    {
        global $db;
        /** @var ADORecordSet $record */

        $result = ['shop_id' => 0];
        $record = $db->Execute("SELECT shop_id FROM " . TABLE_MANDANT_CONFIG . " WHERE shop_status=1 ORDER BY shop_id LIMIT 0,1");
        if ($record->RecordCount() > 0) {
            $result = $record->fields;
        }
        $record->Close();

        return $result;
    }

    function deletePermissions($table, $id)
    {
        global $db;

        $db->Execute("DELETE FROM " . $table . " WHERE pgroup = 'shop_" . (int)$id . "'");

    }

    function deleteCustomer($id)
    {
        global $db;
        /** @var ADORecordSet $record */

        $record = $db->Execute("SELECT customers_id FROM " . TABLE_CUSTOMERS . " where shop_id = ?", array($id));
        while (!$record->EOF) {
            $customer = new customer($record->fields['customers_id']);
            $customer->_unset($record->fields['customers_id']);
            $record->MoveNext();
        }
        $record->Close();

    }

    function deleteOrder($id)
    {
        global $db;
        /** @var ADORecordSet $record */

        $record = $db->Execute("SELECT customers_id,orders_id FROM " . TABLE_ORDERS . " where shop_id = ?", array($id));
        while (!$record->EOF) {

            $order = new order($record->fields['orders_id'], $record->fields['customers_id']);
            $order->_deleteOrder($record->fields['orders_id']);
            $record->MoveNext();
        }
        $record->Close();
    }

    function _get($ID = 0)
    {
        global $db;

        $obj = new stdClass;

        $new_shop = false;
        if ($ID === 'new') {
            $new_shop = true;
            $obj = $this->_set(array(), 'new');
            $ID = $obj->new_id;
        } else {
            $ID = str_replace('store_', '', $ID);
            $ID = (int)$ID;
        }

        $table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key);

        if ($this->url_data['get_data']) {
            $data = $table_data->getData();
        } elseif ($ID) {
            $data = $table_data->getData($ID);
        } else {
            $data = $table_data->getHeader();
            if($new_shop)
            {
                $data[0]['new_shop_base_id'] = str_replace('store_', '', $this->url_data["master_node"]);
                $data[0]['new_shop_base_id_info'] = __text('TEXT_NEW_SHOP_BASE_ID_INFO_TEXT');
            }
        }

        if ($table_data->_total_count != 0 || !$table_data->_total_count)
            $count_data = $table_data->_total_count;
        else
            $count_data = count($data);
        if ($new_shop) $data[0]['shop_ssl'] = 0;

        $obj->totalCount = $count_data;
        $obj->data = $data;

        return $obj;
    }

    function _set($data, $set_type = 'edit')
    {
        global $db, $xtPlugin, $language;
        /** @var ADORecordSet $record */
        /** @var ADORecordSet $rs */

        $obj = new stdClass;

        foreach ($data as $key => $val) {
            if ($val == 'on')
                $val = 1;
            $data[$key] = $val;
        }

        $objS = new stdClass();
        $objS->success = false;

        if ($set_type != 'new') {


            // only perform this code if
            $data['shop_id'] = (int)$data['shop_id'];

            if ($data['shop_id'] > 0) {


            	
                $o = new adminDB_DataSave($this->_table, $data, false, __CLASS__);
                $objS = $o->saveDataSet();


                foreach ($language->_getLanguageList() as $key => $val) {
                    $db->Execute(
                        "UPDATE " . TABLE_LANGUAGE_CONTENT . " SET language_value =?
                            WHERE language_code = ? and  language_key= ? ",
                        array($data['shop_ssl_domain'], $val['code'], 'TEXT_CAT_STORE' . $data['shop_id'])
                        );
                }

            
            } else {
                // we do have a new shop here
                // check if max amount allready in db

                $new_shop_base_id = $data['new_shop_base_id'];

                    if(empty($new_shop_base_id)) $new_shop_base_id = 1;
                $rs = $db->Execute("SELECT * FROM " . $this->_table);
                $rc_count = $rs->RecordCount();
                if (/*multi_store_check_max_stores($rc_count)*/ true) {

                    // save entry
                    $o = new adminDB_DataSave($this->_table, $data, false, __CLASS__);
                    $objS = $o->saveDataSet();

                    $shop_id = $objS->new_id;

                    // create tables
                    $db->Execute("DROP TABLE IF EXISTS " . TABLE_CONFIGURATION_MULTI . $objS->new_id);

                    $db->Execute("CREATE TABLE " . TABLE_CONFIGURATION_MULTI . $objS->new_id . " LIKE " . TABLE_CONFIGURATION_MULTI . $new_shop_base_id);
                    $db->Execute("INSERT INTO " . TABLE_CONFIGURATION_MULTI . $objS->new_id . " select * FROM " . TABLE_CONFIGURATION_MULTI . $new_shop_base_id);

                    $this->copyPermissions(TABLE_CONTENT_PERMISSION, $objS->new_id, $new_shop_base_id);
                    $this->copyPermissions(TABLE_PRODUCTS_PERMISSION, $objS->new_id, $new_shop_base_id);
                    $this->copyPermissions(TABLE_CATEGORIES_PERMISSION, $objS->new_id, $new_shop_base_id);

                    $this->copyTableValuesByShop(TABLE_CONFIGURATION_PAYMENT, $objS->new_id, $new_shop_base_id);

                    if (StoreIdExists(TABLE_PAYMENT_DESCRIPTION, 'payment_description_store_id'))
                        $this->copyTableValuesByShop(TABLE_PAYMENT_DESCRIPTION, $shop_id, $new_shop_base_id, 'payment_description_store_id');

                    $this->copyTableValuesByShop(TABLE_PLUGIN_CONFIGURATION, $objS->new_id, $new_shop_base_id);

                    if (StoreIdExists(TABLE_PRODUCTS_DESCRIPTION, 'products_store_id'))
                        $this->copyTableValuesByShop(TABLE_PRODUCTS_DESCRIPTION, $shop_id, $new_shop_base_id, 'products_store_id');

                    if (StoreIdExists(TABLE_CONTENT_ELEMENTS, 'content_store_id'))
                        $this->copyTableValuesByShop(TABLE_CONTENT_ELEMENTS, $shop_id, $new_shop_base_id, 'content_store_id');

                    if (StoreIdExists(TABLE_MANUFACTURERS_DESCRIPTION, 'manufacturers_store_id'))
                        $this->copyTableValuesByShop(TABLE_MANUFACTURERS_DESCRIPTION, $shop_id, $new_shop_base_id, 'manufacturers_store_id');

                    if (StoreIdExists(TABLE_CATEGORIES_DESCRIPTION, 'categories_store_id'))
                        $this->copyTableValuesByShop(TABLE_CATEGORIES_DESCRIPTION, $shop_id, $new_shop_base_id, 'categories_store_id');

                    if (StoreIdExists(TABLE_PRODUCTS_TO_CATEGORIES, 'store_id'))
                        $this->copyTableValuesByShop(TABLE_PRODUCTS_TO_CATEGORIES, $shop_id, $new_shop_base_id, 'store_id');

                    if (StoreIdExists(TABLE_SEO_URL, 'store_id'))
                        $this->copySeoValuesByShop(TABLE_SEO_URL, $objS->new_id, $new_shop_base_id);

                    $db->Execute("INSERT INTO " . TABLE_ADMIN_NAVIGATION . " (text,  sortorder, parent, type, navtype)
								VALUES ('cat_store" . $objS->new_id . "','1000','shop','G','W') ");
                    foreach ($language->_getLanguageList() as $key => $val) {

                        $db->Execute(
                            "INSERT IGNORE INTO " . TABLE_LANGUAGE_CONTENT . " (translated,  language_code, language_key, language_value, 	class)
                                VALUES ('1',?,'TEXT_CAT_STORE" . (int)$objS->new_id . "',?,'admin') ",
                            array($val['code'], $data['shop_ssl_domain'])
                        );

                        $title = $this->getBaseLangContent($val['code'], 'TEXT_PRODUCTS_NAME');
                        if ($title) {
                            $db->Execute(
                                "INSERT IGNORE INTO " . TABLE_LANGUAGE_CONTENT . " (translated,  language_code, language_key, language_value, 	class)
                                    VALUES ('1',?,'TEXT_PRODUCTS_NAME_STORE" . (int)$objS->new_id . "',?,'admin') ",
                                array($val['code'], $title)
                            );
                        }

                        $title = $this->getBaseLangContent($val['code'], 'TEXT_CONTENT_TITLE');
                        if ($title) {
                            $db->Execute(
                                "INSERT IGNORE INTO " . TABLE_LANGUAGE_CONTENT . " (translated,  language_code, language_key, language_value, 	class)
                                    VALUES ('1',?,'TEXT_CONTENT_TITLE_STORE" . (int)$objS->new_id . "',?,'admin') ",
                                array($val['code'], $title)
                            );
                        }
                    }

                    $this->copyTableValuesByShop(TABLE_CONFIGURATION_LANG_MULTI, $objS->new_id, $new_shop_base_id, 'store_id', '', 'language_content_id');

                    ($plugin_code = $xtPlugin->PluginCode('class.multistore.php:_set')) ? eval ($plugin_code) : false;
                }
            }
        }

        if ($objS->success) {
            $obj->success = true;
        } else {
            $obj->success = false;
        }

        if ($this->url_data['new'] == 'true' && $this->url_data['save'] == 'true') {
            $obj->message = __text('TEXT_STORE_CREATED');
        }

        return $obj;

    }

    /** getBaseLangContent
     *
     * Returning the content text based on language and key
     *
     * @param (string) ($lang) - language code
     * @param (string) ($key) - language key to search for
     * @return string (if content variable is found) or false ( if missing)
     */
    function getBaseLangContent($lang, $key)
    {
        global $db;
        /** @var ADORecordSet $t */

        $t = $db->Execute(
            "SELECT language_value FROM " . TABLE_LANGUAGE_CONTENT . " WHERE language_code=? and language_key=? and class='admin' LIMIT 0,1",
            array($lang, $key)
        );
        if ($t->RecordCount() > 0) {
            return $t->fields['language_value'];
        } else return false;
    }

    function _unset($id = 0)
    {
        global $db, $xtPlugin;

        if (strstr($id, 'store_')) {
            $id = str_replace('store_', '', $id);
        }

        if ($id == 0 || $id == 1) return false;
        if ($this->position != 'admin') return false;
        $id = (int)$id;
        if (!is_int($id)) return false;

        /**  verbindungen zur verschiedenen store id's, stand nov 2019
         *   customer und order haben noch eigene verbindungen
         *   diese werden in deleteCustomer und deleteOrder gelöst

        ---  store_id
        x   _categories_custom_link_url
        x	_config_lang
        x   _products_to_categories
        x	_seo_url
        x   _seo_url_redirect
         *
         * ---  {X}_store_id
        x	_categories_description		categories_store_id
        x	_content_elements			content_store_id
        x   _feed						feed_store_id
        x	_manufacturers_info			manufacturers_store_id
        x   _payment_description		payment_description_store_id
        x	_products_description		products_store_id

        ---  shop_id
        x	_config_payment
        x	_config_plugin
        x	_customers
        x	_orders
        x	_stores
        x   _search
        x   _sales_stats

        --- {X}_store{I}
        x	_acl_nav				cat_store{I}

         */

        $this->deletePermissions(TABLE_CONTENT_PERMISSION, $id);
        $this->deletePermissions(TABLE_PRODUCTS_PERMISSION, $id);
        $this->deletePermissions(TABLE_CATEGORIES_PERMISSION, $id);
        $this->deletePermissions(TABLE_COUNTRIES_PERMISSION, $id);
        $this->deletePermissions(TABLE_MANUFACTURERS_PERMISSION, $id);

        $this->deleteCustomer($id);
        $this->deleteOrder($id);

        $db->Execute("DROP TABLE IF EXISTS " . TABLE_CONFIGURATION_MULTI . $id);
        $db->Execute("DELETE FROM " . $this->_table . " WHERE " . $this->_master_key . " = ?", array($id));

        // delete from payment and plugin config
        $db->Execute("DELETE FROM " . TABLE_CONFIGURATION_PAYMENT . " WHERE shop_id=?", array($id));
        $db->Execute("DELETE FROM " . TABLE_PLUGIN_CONFIGURATION . " WHERE shop_id=?", array($id));
        $db->Execute("DELETE FROM " . TABLE_ADMIN_NAVIGATION . " WHERE text='cat_store" . $id . "'");
        $db->Execute("DELETE FROM " . TABLE_SEO_URL . " WHERE store_id=?", array($id));
        $db->Execute("DELETE FROM " . TABLE_SEO_URL_REDIRECT . " WHERE store_id=?", array($id));
        $db->Execute("DELETE FROM " . TABLE_PRODUCTS_DESCRIPTION . " WHERE products_store_id=?", array($id));
        $db->Execute("DELETE FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " WHERE store_id=?", array($id));
        $db->Execute("DELETE FROM " . TABLE_CONTENT_ELEMENTS . " WHERE content_store_id=?", array($id));
        $db->Execute("DELETE FROM " . TABLE_MANUFACTURERS_DESCRIPTION . " WHERE manufacturers_store_id=?", array($id));
        $db->Execute("DELETE FROM " . TABLE_CATEGORIES_DESCRIPTION . " WHERE categories_store_id=?", array($id));
        $db->Execute("DELETE FROM " . TABLE_CATEGORIES_CUSTOM_LINK_URL . " WHERE store_id=?", array($id));
        $db->Execute("DELETE FROM " . TABLE_CONFIGURATION_LANG_MULTI . " WHERE store_id=?", array($id));
        $db->Execute("DELETE FROM " . TABLE_FEED . " WHERE feed_store_id=?", array($id));
        $db->Execute("DELETE FROM " . TABLE_PAYMENT_DESCRIPTION . " WHERE payment_description_store_id=?", array($id));
        $db->Execute("DELETE FROM " . TABLE_SEARCH . " WHERE shop_id=?", array($id));
        $db->Execute("DELETE FROM " . TABLE_SALES_STATS . " WHERE shop_id=?", array($id));

        $ret = true;
        ($plugin_code = $xtPlugin->PluginCode('class.multistore.php:_unset')) ? eval ($plugin_code) : false;

        return $ret;
    }

    function _getParams()
    {
        $params = array();

        $header['shop_ssl_domain'] = array('type' => 'textfield');
        $header['shop_id'] = array('type' => 'hidden');
        $header['new_shop_base_id'] = array('type' => 'textfield', 'readonly' => true);
        $header['new_shop_base_id_info'] = array('type' => 'admininfo', 'readonly' => true);


        $header['shop_ssl'] = array('type' => 'status');
        $groupingPosition = 'SHOP';
        $grouping['shop_domain'] = array('position' => $groupingPosition);
        $grouping['shop_ssl_domain'] = array('position' => $groupingPosition);
        $grouping['shop_http'] = array('position' => $groupingPosition);
        $grouping['shop_https'] = array('position' => $groupingPosition);
        $grouping['shop_id'] = array('position' => $groupingPosition);
        $grouping['new_shop_base_id'] = array('position' => $groupingPosition);
        $grouping['new_shop_base_id_info'] = array('position' => $groupingPosition);
        $grouping['shop_ssl'] = array('position' => $groupingPosition);
        $grouping['shop_status'] = array('position' => $groupingPosition);
        $grouping['virtual_folder'] = array('position' => $groupingPosition);

        $params['header'] = $header;
        $params['grouping'] = $grouping;
        $params['master_key'] = 'shop_id';
        $params['default_sort'] = 'shop_id';

        return $params;
    }
}
