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

set_time_limit ( 600 );
include_once _SRV_WEBROOT.'xtWizard/config.php';

include_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'classes/class.plugin.php';
include_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'classes/class.FileHandler.php';
include_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'classes/class.ImageTypes.php';


class Update500xTo51x extends ExecutableScript {

    /**
     * (non-PHPdoc)
     * @see IExecutableScript::getWizardType()
     */
    public function getWizardType() {
        return Wizard::WIZARD_TYPE_UPDATE;
    }
    
    /**
     * Needed only for update scripts. Needed to determine if the script can be applied
     * to the current shop version
    */
    
    public function getAppliableShopVersion() {
        return '5.0.0';
    }


    public function getTargetShopVersion() {
        return '5.1.4';
    }
    public function getSkippableShopVersion(){
        return '5.1.0';
    }
    
    /**
     * Get pages
     * @return string
     */
    protected function getPagesXml() {
        return file_get_contents(dirname(__FILE__) . DS . 'pages.xml');
    }
    
    /**
     * Index action - testing quesries
     * @param WizardPage $page
     */
    public function displayStartPage(WizardPage $page) {
        $this->InitialSQL($page);
    }


    /**
     * Complete action
     * @param WizardPage $page
     */
    public function displayFinishPage(WizardPage $page)
    {
		$url = Wizard::getInstance()->buildUrl(array(
			Wizard::PARAM_SCRIPT => 'StartPageScript',
            Wizard::PARAM_PAGE => 'update',
        ));
        // Redirect to update page
        Wizard::getInstance()->redirect($url);
    }
    
    public function displayFinishInstructionsPage(WizardPage $page)
    {
        $page
            ->setTemplate('templates/info_page.html')
            ->assignTemplateVar('message', TEXT_FINISH_INSTRUCTIONS_51.
                '<br /><p class="alert alert-info">'.TEXT_COPY_VERSION_INFO.'</p>'.
                '<p class="alert">'.TEXT_INFO_PLACE_LICENSE.'</p>')
            ->assignTemplateVar('title', 'INFO')
            ->assignTemplateVar('next', true);
    }
    

    protected static function _getFileContent($filename) {
        $handle = fopen($filename, 'rb');
        $content = fread($handle, filesize($filename));
        fclose($handle);
        return $content;
    }

    /**
    * @param WizardPage $page
    */
    public function doUpdate(WizardPage $page)
    {
        global $store_handler, $language;

        //$page->setSuccessMessage(TEXT_COPY_VERSION_INFO);

        $idb = Wizard::getInstance()->getDatabaseObject();


        $sql_version = $idb->GetOne("SELECT VERSION() AS Version");
        if(version_compare($sql_version, '5.5') == -1)
        {
            die('Systemanforderung  mysql 5.5 minimum nicht erfüllt. <a href="https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917573/Systemanforderungen" target="_blank">Handbuch</a>');
        }

        $stores = $store_handler->getStores();
        $shop_lngs = $language->_getLanguageList('all');

        include_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'classes/class.plugin.php';
        $plugin = new plugin();

        $scriptDir = dirname(__FILE__);


        $msg = TEXT_ALTER_DB.': '.TABLE_TAX_RATES.'/'.TABLE_TAX_CLASS;
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($idb, $stores, $shop_lngs, $msg, $plugin, $scriptDir) {
                try{
                    // fix timestamp coloumn for NO_ZERO_IN DATE (default in some distributions since myslq 5.7)
                    $idb->Execute("UPDATE " . TABLE_TAX_CLASS . " SET last_modified=NOW();");
                    $idb->Execute("UPDATE " . TABLE_TAX_CLASS . " SET date_added=NOW();");
                    $idb->Execute("UPDATE " . TABLE_TAX_RATES . " SET last_modified=NOW();");
                    $idb->Execute("UPDATE " . TABLE_TAX_RATES . " SET date_added=NOW();");

                    //  tax class
                    if (!$plugin->_FieldExists('is_digital_tax', TABLE_TAX_CLASS))
                    {
                        $idb->Execute("ALTER TABLE " . TABLE_TAX_CLASS . " ADD COLUMN is_digital_tax INT(1) UNSIGNED NULL DEFAULT 0 ;");
                    }
                    //  tax rates
                    if (!$plugin->_FieldExists('tax_zone_id', TABLE_TAX_RATES))
                    {
                        $idb->Execute("ALTER TABLE " . TABLE_TAX_RATES . " CHANGE COLUMN tax_zone_id tax_zone_id INT(11) NOT NULL DEFAULT 0 ;");
                    }
                    if (!$plugin->_FieldExists('tax_class_id', TABLE_TAX_RATES))
                    {
                        $idb->Execute("ALTER TABLE " . TABLE_TAX_RATES . " CHANGE COLUMN tax_class_id tax_class_id INT(11) NOT NULL DEFAULT 0 ;");
                    }
                    // fehlerhafte tax_class_countries korregieren
                    $idb->Execute('UPDATE '.TABLE_TAX_RATES." SET tax_rate_countries='' WHERE tax_rate_countries='New' ");

                    // fehlerhafte tax_zone_id korregieren
                    $idb->Execute('UPDATE '.TABLE_TAX_RATES." SET tax_zone_id=0 WHERE tax_zone_id=6 ");

                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-error'>".TEXT_FAILED." : " . $e->getMessage() . "</span><br/>");
                    WizardLogger::getInstance()->log($msg . PHP_EOL . $e->getMessage());
                    return false;
                }
                return true;
            })
            ->setDescription($msg)
            ->setAbortOnError(true)
        );


        $msg = TEXT_ALTER_DB.': '.TABLE_CUSTOMERS_STATUS;
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($idb, $stores, $shop_lngs, $msg, $plugin) {
                try{
                    // customers status
                    if (!$plugin->_FieldExists('customers_status_tax_rates_calculation_base', TABLE_CUSTOMERS_STATUS))
                    {
                        $idb->Execute("ALTER TABLE " . TABLE_CUSTOMERS_STATUS . " ADD COLUMN customers_status_tax_rates_calculation_base VARCHAR(64) NULL DEFAULT 'b2c_eu' ;");
                    }
                    if ($plugin->_FieldExists('shop_1', TABLE_CUSTOMERS_STATUS))
                    {
                        $idb->Execute("ALTER TABLE " . TABLE_CUSTOMERS_STATUS . " DROP COLUMN shop_1 ;");
                    }
                    if (!$plugin->_FieldExists('customers_status_tax_rates_calculation_base', TABLE_CUSTOMERS_STATUS))
                    {
                        $idb->Execute("ALTER TABLE " . TABLE_CUSTOMERS_STATUS . " ADD COLUMN customers_status_tax_rates_calculation_base VARCHAR(64) NULL DEFAULT 'b2c_eu' ;");
                    }

                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-error'>".TEXT_FAILED." : " . $e->getMessage() . "</span><br/>");
                    WizardLogger::getInstance()->log($msg . PHP_EOL . $e->getMessage());
                    return false;
                }
                return true;
            })
            ->setDescription($msg)
            ->setAbortOnError(true)
        );


        $msg = TEXT_ALTER_DB.': '.TABLE_ORDERS_PRODUCTS;
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($idb, $stores, $shop_lngs, $msg, $plugin) {
                try{
                    // order products
                    if (!$plugin->_FieldExists('products_unit', TABLE_ORDERS_PRODUCTS))
                    {
                        $idb->Execute("ALTER TABLE " . TABLE_ORDERS_PRODUCTS . " ADD COLUMN products_unit INT(11) NULL DEFAULT 0;");
                    }

                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-error'>".TEXT_FAILED." : " . $e->getMessage() . "</span><br/>");
                    WizardLogger::getInstance()->log($msg . PHP_EOL . $e->getMessage());
                    return false;
                }
                return true;
            })
            ->setDescription($msg)
            ->setAbortOnError(true)
        );

        $msg = TEXT_ALTER_DB.': '.TABLE_PRODUCTS;
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($idb, $stores, $shop_lngs, $msg, $plugin) {
                try{
                    // order products
                    if (!$plugin->_FieldExists('products_unit', TABLE_PRODUCTS))
                    {
                        $idb->Execute("ALTER TABLE " . TABLE_PRODUCTS . " ADD COLUMN products_unit INT(11) NULL DEFAULT 0;");
                    }

                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-error'>".TEXT_FAILED." : " . $e->getMessage() . "</span><br/>");
                    WizardLogger::getInstance()->log($msg . PHP_EOL . $e->getMessage());
                    return false;
                }
                return true;
            })
            ->setDescription($msg)
            ->setAbortOnError(true)
        );


        $msg = TEXT_ALTER_DB.': '.TABLE_SEO_URL_REDIRECT;
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($idb, $stores, $shop_lngs, $msg, $plugin) {
                try{
                    // seo redirect / redirect 404
                    if (!$plugin->_FieldExists('last_access', TABLE_SEO_URL_REDIRECT))
                    {
                        $idb->Execute("ALTER TABLE " . TABLE_SEO_URL_REDIRECT . " ADD COLUMN last_access TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP ;");
                        $idb->Execute("ALTER TABLE " . TABLE_SEO_URL_REDIRECT . " CHANGE COLUMN last_access last_access TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ;");
                    }
                    if (!$plugin->_FieldExists('count_day_last_access', TABLE_SEO_URL_REDIRECT))
                    {
                        $idb->Execute("ALTER TABLE " . TABLE_SEO_URL_REDIRECT . " ADD COLUMN count_day_last_access INT NULL DEFAULT 0 ;");
                    }
                    $idb->Execute("ALTER TABLE " . TABLE_SEO_URL_REDIRECT . " CHANGE COLUMN url_text_redirect url_text_redirect VARCHAR(2048) NULL;");
                    $idb->Execute("ALTER TABLE " . TABLE_SEO_URL_REDIRECT . " CHANGE COLUMN url_md5_redirect url_md5_redirect VARCHAR(32) NULL;");
                    $idb->Execute("ALTER TABLE " . TABLE_SEO_URL_REDIRECT . " CHANGE COLUMN is_deleted is_deleted TINYINT(4) NOT NULL DEFAULT 0;");
                    $idb->Execute("ALTER TABLE " . TABLE_SEO_URL_REDIRECT . " CHANGE COLUMN link_type link_type int(11) NOT NULL DEFAULT 0;");

                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-error'>".TEXT_FAILED." : " . $e->getMessage() . "</span><br/>");
                    WizardLogger::getInstance()->log($msg . PHP_EOL . $e->getMessage());
                    return false;
                }
                return true;
            })
            ->setDescription($msg)
            ->setAbortOnError(true)
        );


        $msg = TEXT_ALTER_DB.': '.TABLE_CATEGORIES.'/'.TABLE_CATEGORIES_CUSTOM_LINK_URL;
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($idb, $stores, $shop_lngs, $msg, $plugin) {
                try{
                    // categories custom link
                    if ($plugin->_FieldExists('link_url', TABLE_CATEGORIES_CUSTOM_LINK_URL))
                    {
                        $idb->Execute("ALTER TABLE " . TABLE_CATEGORIES_CUSTOM_LINK_URL . " CHANGE COLUMN link_url link_url VARCHAR(256) NULL;");
                    }

                    // category start page
                    if (!$plugin->_FieldExists('start_page_category', TABLE_CATEGORIES))
                    {
                        $idb->Execute("ALTER TABLE " . TABLE_CATEGORIES . " ADD COLUMN start_page_category INT(1) DEFAULT 0 ;");
                    }

                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-error'>".TEXT_FAILED." : " . $e->getMessage() . "</span><br/>");
                    WizardLogger::getInstance()->log($msg . PHP_EOL . $e->getMessage());
                    return false;
                }
                return true;
            })
            ->setDescription($msg)
            ->setAbortOnError(true)
        );


        $msg = TEXT_CLEAN_ORDER_STATUS_CODES;
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($idb, $stores, $shop_lngs, $msg, $plugin) {
                try{
                    // delete order status codes introduced in 5.0.6 which have no entry in system_status_description
                    $result = $idb->GetArray(
                        "SELECT ss.status_id
                            FROM ".TABLE_SYSTEM_STATUS." ss LEFT JOIN  ".TABLE_SYSTEM_STATUS_DESCRIPTION." ssd
                            ON  ss.status_id = ssd.status_id
                            WHERE (ssd.status_id IS NULL)"
                    );
                    $status_ids_to_delete = array();
                    foreach($result as $v)
                    {
                        $status_ids_to_delete[] = (int)$v['status_id'];
                    }
                    if(count($status_ids_to_delete))
                    {
                        $idb->Execute("DELETE FROM ".TABLE_SYSTEM_STATUS." WHERE status_id IN (".implode(',',$status_ids_to_delete).")");

                        // insert order status codes
                        global $language;
                        $status_code_lang_data = array(
                            'de' => array(
                                'Zurückgezahlt',
                                'Teilweise Zurückgezahlt',
                                'Zahlung in Prüfung',
                                'Zahlung abgelehnt',
                                'Zahlung einbehalten',
                                'Zahlung fehlgeschlagen',
                                'Zahlung abgelaufen'
                            ),
                            'en' => array(
                                'Refunded',
                                'Partially refunded',
                                'Payment Pending',
                                'Payment Denied',
                                'Payment Reversed',
                                'Payment Failed',
                                'Payment Expired'
                            )
                        );
                        $status_code_data = array();
                        for($i = 0; $i<count($status_code_lang_data['de']); $i++)
                        {
                            $status_code_data[] = array(
                                'de' => $status_code_lang_data['de'][$i],
                                'en' => $status_code_lang_data['en'][$i]
                            );
                        }

                        foreach($status_code_data as $lng_values)
                        {
                            $idb->Execute("INSERT INTO `".DB_PREFIX."_system_status` (status_class, status_values) VALUES (?,?);",
                                array('order_status', 'a:1:{s:4:"data";a:5:{s:15:"enable_download";i:0;s:7:"visible";s:1:"1";s:13:"visible_admin";i:1;s:19:"calculate_statistic";i:0;s:12:"reduce_stock";i:1;}}'));
                            $status_id = $idb->insert_ID();

                            foreach($shop_lngs as $lng)
                            {
                                if(strtolower($lng['name'])=='new') continue;

                                if(array_key_exists($lng['code'], $status_code_lang_data))
                                {
                                    $lng_str = $lng_values[$lng['code']];
                                }
                                else {
                                    $lng_str = $lng_values['en'];
                                }
                                $idb->Execute("INSERT INTO `".DB_PREFIX."_system_status_description` VALUES (?, ?, ?, NULL);",
                                    array($status_id, $lng['code'], $lng_str));
                            }
                        }
                        // end insert order status codes
                    }

                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-error'>".TEXT_FAILED." : " . $e->getMessage() . "</span><br/>");
                    WizardLogger::getInstance()->log($msg . PHP_EOL . $e->getMessage());
                }
                return true;
            })
            ->setDescription($msg)
            ->setAbortOnError(true)
        );


        $msg = TEXT_NEW_IMG_TYPE. ' category/startpage';
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($idb, $stores, $shop_lngs, $msg, $plugin) {
                try{
                    require_once(_SRV_WEBROOT._SRV_WEB_FRAMEWORK.'admin/classes/class.adminDB_DataSave.php');
                    include_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'classes/class.ImageTypes.php';

                    // neuer bildtyp für start page categories
                    $exists = $idb->GetOne("SELECT 1 FROM ".TABLE_IMAGE_TYPE." WHERE folder='category/startpage' AND class='category'");
                    if (!$exists)
                    {
                        $it = new ImageTypes();
                        $r = $it->_set(array(
                            'folder' => 'category/startpage',
                            'width' => 360,
                            'height'=> 120,
                            'watermark'=>'false',
                            'process' => 'true',
                            'class' => 'category',
                        ), '');
                    }

                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-error'>".TEXT_FAILED." : " . $e->getMessage() . "</span><br/>");
                    WizardLogger::getInstance()->log($msg . PHP_EOL . $e->getMessage());
                    return false;
                }
                return true;
            })
            ->setDescription($msg)
            ->setAbortOnError(true)
        );


        $msg = 'Fix Sprache/language';
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($idb, $stores, $shop_lngs, $msg, $plugin, $scriptDir) {
                try{

                    require_once(_SRV_WEBROOT._SRV_WEB_FRAMEWORK.'admin/classes/class.adminDB_DataSave.php');

                    // language content
                    $lng_content = new language_content();
                    foreach (array('de', 'en') as $lng)
                    {
                        $lines = file("{$scriptDir}/languages/{$lng}/{$lng}.yml");

                        foreach ($lines as $line_num => $line)
                        {
                            $delimiterPos = strpos($line, '=', 0);

                            if ($delimiterPos === false)
                            {
                                continue;
                            }
                            $systemPart = substr($line, 0, $delimiterPos);
                            $value = substr($line, $delimiterPos + 1);
                            list($plugin, $class, $key) = explode('.', $systemPart);

                            $insert_data = array();
                            $insert_data['language_key'] = $key;
                            $insert_data['language_code'] = $lng;
                            $insert_data['language_value'] = trim(str_replace("\n", '', $value));
                            $insert_data['class'] = $class;
                            $insert_data['plugin_key'] = $plugin;
                            $insert_data['translated'] = '1';
                            $insert_data['readonly'] = '0';

                            $idb->Execute("DELETE FROM " . TABLE_LANGUAGE_CONTENT . " WHERE language_key=? and language_code=?", array($key, $lng));
                            $lng_content->_set($insert_data);
                        }
                    }

                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-error'>".TEXT_FAILED." : " . $e->getMessage() . "</span><br/>");
                    WizardLogger::getInstance()->log($msg . PHP_EOL . $e->getMessage());
                    return false;
                }
                return true;
            })
            ->setDescription($msg)
            ->setAbortOnError(true)
        );


        $msg = $msg = TEXT_ALTER_DB.': '.TABLE_MEDIA_DOWNLOAD_IP;
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($idb, $stores, $shop_lngs, $msg, $plugin) {
                try{
                    // xt_media_download_ip.user_ip : is not ip but md5(ip) thus varchar(32) not 15
                    $idb->Execute("ALTER TABLE " . TABLE_MEDIA_DOWNLOAD_IP . " CHANGE COLUMN user_ip user_ip  VARCHAR(32) DEFAULT '';");

                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-error'>".TEXT_FAILED." : " . $e->getMessage() . "</span><br/>");
                    WizardLogger::getInstance()->log($msg . PHP_EOL . $e->getMessage());
                    return false;
                }
                return true;
            })
            ->setDescription($msg)
            ->setAbortOnError(true)
        );


        $msg = $msg = TEXT_ALTER_DB.': '.TABLE_CONFIGURATION.'/'.TABLE_CONFIGURATION_MULTI.'x';
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($idb, $stores, $shop_lngs, $msg, $plugin) {
                try{

                    // fix timestamp coloumn for NO_ZERO_IN DATE (default in some distributions since myslq 5.7)
                    $idb->Execute("UPDATE " . TABLE_CONFIGURATION . " SET last_modified=NOW();");
                    $idb->Execute("UPDATE " . TABLE_CONFIGURATION . " SET date_added=NOW();");
                    $idb->Execute("ALTER TABLE " . TABLE_CONFIGURATION . " CHANGE COLUMN date_added date_added  DATETIME NULL DEFAULT NULL;");

                    $idb->Execute("ALTER TABLE " . TABLE_CUSTOMERS_ADDRESSES . " 
                    CHANGE COLUMN date_added date_added  DATETIME NULL DEFAULT NULL,
                    CHANGE COLUMN last_modified last_modified TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                    CHANGE COLUMN customers_dob customers_dob  DATETIME NULL DEFAULT NULL;");

                    foreach ($stores as $store)
                    {
                        // fix timestamp coloumn for NO_ZERO_IN DATE (default in some distributions since myslq 5.7)
                        $idb->Execute("UPDATE " . DB_PREFIX."_config_".$store['id'] . " SET last_modified=NOW();");
                        $idb->Execute("UPDATE " . DB_PREFIX."_config_".$store['id'] . " SET date_added=NOW();");
                        $idb->Execute("ALTER TABLE " . DB_PREFIX."_config_".$store['id'] . "
                         CHANGE COLUMN date_added date_added  DATETIME NULL DEFAULT NULL,
                         CHANGE COLUMN last_modified last_modified  TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP;");
                    }

                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-error'>".TEXT_FAILED." : " . $e->getMessage() . "</span><br/>");
                    WizardLogger::getInstance()->log($msg . PHP_EOL . $e->getMessage());
                    return false;
                }
                return true;
            })
            ->setDescription($msg)
            ->setAbortOnError(true)
        );

        $msg = $msg = TEXT_ALTER_DB.': '.TABLE_CUSTOMERS_ADDRESSES;
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($idb, $stores, $shop_lngs, $msg, $plugin) {
                try{
                    $idb->Execute("ALTER TABLE " . TABLE_CUSTOMERS_ADDRESSES . " 
                    CHANGE COLUMN date_added date_added  DATETIME NULL DEFAULT NULL,
                    CHANGE COLUMN last_modified last_modified TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                    CHANGE COLUMN customers_dob customers_dob  DATETIME NULL DEFAULT NULL;");

                    $idb->Execute("UPDATE " . TABLE_CUSTOMERS_ADDRESSES . " SET last_modified=NOW();");
                    $idb->Execute("UPDATE " . TABLE_CUSTOMERS_ADDRESSES . " SET date_added=NOW()  WHERE CAST(date_added AS CHAR(20)) = '0000-00-00 00:00:00';");

                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-error'>".TEXT_FAILED." : " . $e->getMessage() . "</span><br/>");
                    WizardLogger::getInstance()->log($msg . PHP_EOL . $e->getMessage());
                    return false;
                }
                return true;
            })
            ->setDescription($msg)
            ->setAbortOnError(true)
        );

        $msg = TEXT_NEW_FIELD . ' customer.title';
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($idb, $stores, $shop_lngs, $msg, $plugin) {
                try{
                    // customers title
                    foreach ($stores as $store)
                    {
                        $exists = $idb->GetOne("SELECT 1 FROM ".DB_PREFIX."_config_".$store['id']." WHERE config_key='_STORE_ACCOUNT_USE_TITLE'" );
                        if (!$exists)
                            $idb->Execute("INSERT INTO ".DB_PREFIX."_config_".$store['id']." (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_ACCOUNT_USE_TITLE', 1, 5, 2, 'status', NULL);");
                        $exists = $idb->GetOne("SELECT 1 FROM ".DB_PREFIX."_config_".$store['id']." WHERE config_key='_STORE_ACCOUNT_TITLE_REQUIRED'" );
                        if (!$exists)
                            $idb->Execute("INSERT INTO ".DB_PREFIX."_config_".$store['id']." (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_ACCOUNT_TITLE_REQUIRED', 0, 5, 2, 'status', NULL);");
                        $exists = $idb->GetOne("SELECT 1 FROM ".DB_PREFIX."_config_".$store['id']." WHERE config_key='_STORE_ACCOUNT_TITLE_PRESET'" );
                        if (!$exists)
                            $idb->Execute("INSERT INTO ".DB_PREFIX."_config_".$store['id']." (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_ACCOUNT_TITLE_PRESET', '', 5, 2, 'textarea', NULL);");
                    }

                    if (!$plugin->_FieldExists('customers_title', TABLE_CUSTOMERS_ADDRESSES))
                        $idb->Execute("ALTER TABLE ".TABLE_CUSTOMERS_ADDRESSES." ADD COLUMN `customers_title` VARCHAR(255) NULL DEFAULT '' AFTER `customers_gender`");

                    if (!$plugin->_FieldExists('delivery_title', TABLE_ORDERS))
                        $idb->Execute("ALTER TABLE ".TABLE_ORDERS." ADD COLUMN `delivery_title` VARCHAR(255) NULL DEFAULT '' AFTER `delivery_gender`");
                    if (!$plugin->_FieldExists('billing_title', TABLE_ORDERS))
                        $idb->Execute("ALTER TABLE ".TABLE_ORDERS." ADD COLUMN `billing_title` VARCHAR(255) NULL DEFAULT '' AFTER `billing_gender`");

                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-error'>".TEXT_FAILED." : " . $e->getMessage() . "</span><br/>");
                    WizardLogger::getInstance()->log($msg . PHP_EOL . $e->getMessage());
                    return false;
                }
                return true;
            })
            ->setDescription($msg)
            ->setAbortOnError(true)
        );

        $msg = TEXT_NEW_FIELD . ' shop-settings > customer details > show_gdpr_download';
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($idb, $stores, $shop_lngs, $msg, $plugin) {
                try{
                    // customers title
                    foreach ($stores as $store)
                    {
                        $exists = $idb->GetOne("SELECT 1 FROM ".DB_PREFIX."_config_".$store['id']." WHERE config_key='_STORE_ACCOUNT_SHOW_GDPR_DOWNLOAD'" );
                        if (!$exists)
                            $idb->Execute("INSERT INTO ".DB_PREFIX."_config_".$store['id']." (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_ACCOUNT_SHOW_GDPR_DOWNLOAD', 0, 5, 20, 'status', NULL);");
                    }

                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-error'>".TEXT_FAILED." : " . $e->getMessage() . "</span><br/>");
                    WizardLogger::getInstance()->log($msg . PHP_EOL . $e->getMessage());
                    return false;
                }
                return true;
            })
            ->setDescription($msg)
            ->setAbortOnError(true)
        );


        $msg = TEXT_ALTER_DB.': '.TABLE_ORDERS;
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($idb, $stores, $shop_lngs, $msg, $plugin) {
                try{
                    $idb->Execute("ALTER TABLE ".TABLE_ORDERS." CHANGE COLUMN `delivery_fax` `delivery_fax` VARCHAR(32) NULL DEFAULT NULL");
                    $idb->Execute("ALTER TABLE ".TABLE_ORDERS." CHANGE COLUMN `billing_fax`  `billing_fax`  VARCHAR(32) NULL DEFAULT NULL");
                    $idb->Execute("ALTER TABLE ".TABLE_ORDERS." CHANGE COLUMN `comments`  `comments` TEXT CHARACTER SET utf8mb4  NULL DEFAULT NULL");

                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-error'>".TEXT_FAILED." : " . $e->getMessage() . "</span><br/>");
                    WizardLogger::getInstance()->log($msg . PHP_EOL . $e->getMessage());
                    return false;
                }
                return true;
            })
            ->setDescription($msg)
            ->setAbortOnError(true)
        );


        $msg = TEXT_ALTER_DB.': '.TABLE_MEDIA_TO_MEDIA_GALLERY;
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($idb, $stores, $shop_lngs, $msg, $plugin) {
                try{
                    $idb->Execute("ALTER TABLE ".TABLE_MEDIA_TO_MEDIA_GALLERY." CHANGE COLUMN `mg_id`  `mg_id` INT(11) NULL DEFAULT 0");

                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-error'>".TEXT_FAILED." : " . $e->getMessage() . "</span><br/>");
                    WizardLogger::getInstance()->log($msg . PHP_EOL . $e->getMessage());
                    return false;
                }
                return true;
            })
            ->setDescription($msg)
            ->setAbortOnError(true)
        );

        if (!defined('TABLE_SLIDES')) define('TABLE_SLIDES', DB_PREFIX.'_slides');
        $msg = TEXT_ALTER_DB.': '.TABLE_SLIDES;
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($idb, $stores, $shop_lngs, $msg, $plugin) {
                try{
                    $idb->Execute("ALTER TABLE ".TABLE_SLIDES." CHANGE COLUMN `slide_date_to`  `slide_date_to` timestamp NULL DEFAULT NULL");

                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-error'>".TEXT_FAILED." : " . $e->getMessage() . "</span><br/>");
                    WizardLogger::getInstance()->log($msg . PHP_EOL . $e->getMessage());
                    return false;
                }
                return true;
            })
            ->setDescription($msg)
            ->setAbortOnError(true)
        );


        $msg = TEXT_WEST_NAVI_REMOVE . ' adodbperformance, adodbquery, adodblive';
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($idb, $stores, $shop_lngs, $msg, $plugin) {
                try{
                    // datenbankmonitor aus west navi entfernen / adodbperformance
                    $idb->Execute("DELETE FROM ".TABLE_ADMIN_NAVIGATION." where text in ('adodbperformance','adodbquery','adodblive')");

                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-error'>".TEXT_FAILED." : " . $e->getMessage() . "</span><br/>");
                    WizardLogger::getInstance()->log($msg . PHP_EOL . $e->getMessage());
                    return false;
                }
                return true;
            })
            ->setDescription($msg)
            ->setAbortOnError(true)
        );


        $msg = TEXT_WEST_NAVI_ADD . ': 1) Handbuch-Links / links to manual';;
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($idb, $stores, $shop_lngs, $msg, $plugin) {
                try{
                    // westnavi: url_h für links zum handbuch (hilfe-Links)
                    if (!$plugin->_FieldExists('url_h', TABLE_ADMIN_NAVIGATION))
                        $idb->Execute("ALTER TABLE ".TABLE_ADMIN_NAVIGATION." ADD COLUMN `url_h` VARCHAR(124) NULL DEFAULT '' ");
                    if ($plugin->_FieldExists('url_h', TABLE_ADMIN_NAVIGATION))
                    {
                        $file = __DIR__.'/url_h_acl_nav.txt';
                        if (($handle = fopen($file, "r")) !== FALSE) {
                            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE)
                            {
                                if(!empty($data[0]))
                                {
                                    $idb->Execute("UPDATE ".TABLE_ADMIN_NAVIGATION." SET url_h=? WHERE text=?", array($data[1],$data[0]));
                                }
                            }
                            fclose($handle);
                        }
                    }

                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-error'>".TEXT_FAILED." : " . $e->getMessage() . "</span><br/>");
                    WizardLogger::getInstance()->log($msg . PHP_EOL . $e->getMessage());
                    return false;
                }
                return true;
            })
            ->setDescription($msg)
            ->setAbortOnError(true)
        );


        $msg = TEXT_WEST_NAVI_ADD . ': 2) Handbuch-Links / links to manual';;
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($idb, $stores, $shop_lngs, $msg, $plugin) {
                try{
                    // ähnlich westnavi url_h in acl_nav, aber diesmal für tabelle xt_config group (zb einstellungen > konfiguration > email)
                    if (!$plugin->_FieldExists('url_h', TABLE_CONFIGURATION_GROUP))
                        $idb->Execute("ALTER TABLE ".TABLE_CONFIGURATION_GROUP." ADD COLUMN `url_h` VARCHAR(124) NULL DEFAULT '' ");
                    if ($plugin->_FieldExists('url_h', TABLE_CONFIGURATION_GROUP))
                    {
                        $file = __DIR__.'/url_h_config_group.txt';
                        if (($handle = fopen($file, "r")) !== FALSE) {
                            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE)
                            {
                                if(!empty($data[0]))
                                {
                                    $idb->Execute("UPDATE ".TABLE_CONFIGURATION_GROUP." SET url_h=? WHERE group_title=?", array($data[1],$data[0]));
                                }
                            }
                            fclose($handle);
                        }
                    }

                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-error'>".TEXT_FAILED." : " . $e->getMessage() . "</span><br/>");
                    WizardLogger::getInstance()->log($msg . PHP_EOL . $e->getMessage());
                    return false;
                }
                return true;
            })
            ->setDescription($msg)
            ->setAbortOnError(true)
        );


        $msg = TEXT_WEST_NAVI_HIDE_DEACTIVATED_PLGS;
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($idb, $stores, $shop_lngs, $msg, $plugin) {
                try{
                    // west navi: deaktivierte plugins komplett verbergen. funtioniert bis 5.1 nur wenn in acl_nav type==I
                    if (!$plugin->_FieldExists('plugin_code', TABLE_ADMIN_NAVIGATION))
                        $idb->Execute("ALTER TABLE ".TABLE_ADMIN_NAVIGATION." ADD COLUMN `plugin_code` VARCHAR(255) NULL DEFAULT '' AFTER `text`");
                    $texts = array(
                        'xt_coupons' => array('coupons', 'xt_coupons', 'xt_coupons_redeem', 'xt_coupons_token')
                    );
                    if ($plugin->_FieldExists('plugin_code', TABLE_ADMIN_NAVIGATION))
                    {
                        foreach ($texts as $plgCode => $text)
                        {
                            $text_keys = "'".implode("','", $text)."'";
                            $idb->Execute("UPDATE " . TABLE_ADMIN_NAVIGATION. " SET plugin_code = ? WHERE text IN ($text_keys)", array($plgCode));
                        }
                    }

                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-error'>".TEXT_FAILED." : " . $e->getMessage() . "</span><br/>");
                    WizardLogger::getInstance()->log($msg . PHP_EOL . $e->getMessage());
                    return false;
                }
                return true;
            })
            ->setDescription($msg)
            ->setAbortOnError(true)
        );


        $msg = TEXT_MULTI_STORE_CONFIG. ' 1) säubern / cleanup';
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($idb, $stores, $shop_lngs, $msg, $plugin) {
                try{
                    // multi store config - remove _STORE_TEMPLATE_PRODUCT_LIST
                    foreach ($stores as $store)
                    {
                        $tableExists = $idb->GetOne("SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='" . _SYSTEM_DATABASE_DATABASE . "' AND TABLE_NAME='" . TABLE_CONFIGURATION_MULTI.$store['id'] . "'");
                        if($tableExists)
                        {
                            $idb->Execute("DELETE FROM " . TABLE_CONFIGURATION_MULTI . $store['id'] . " WHERE config_key=?", array('_STORE_TEMPLATE_PRODUCT_LIST'));
                        }
                    }

                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-error'>".TEXT_FAILED." : " . $e->getMessage() . "</span><br/>");
                    WizardLogger::getInstance()->log($msg . PHP_EOL . $e->getMessage());
                    return false;
                }
                return true;
            })
            ->setDescription($msg)
            ->setAbortOnError(true)
        );


        $msg = TEXT_MULTI_STORE_CONFIG. ' 2) säubern / cleanup meta-Tags';
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($idb, $stores, $shop_lngs, $msg, $plugin) {
                try{
                    // unnötige meta keys aus store config löschen
                    $delete_keys = array('_STORE_META_AUTHOR', '_STORE_META_TOPIC', '_STORE_META_REPLY_TO', '_STORE_META_REVISIT_AFTER');
                    foreach ($stores as $store)
                    {
                        foreach($delete_keys as $d_key)
                        {
                            $idb->Execute("DELETE FROM ".TABLE_CONFIGURATION_MULTI.$store['id']." WHERE config_key=?", array($d_key));
                        }
                    }

                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-error'>".TEXT_FAILED." : " . $e->getMessage() . "</span><br/>");
                    WizardLogger::getInstance()->log($msg . PHP_EOL . $e->getMessage());
                    return false;
                }
                return true;
            })
            ->setDescription($msg)
            ->setAbortOnError(true)
        );


        $msg = TEXT_MULTI_STORE_CONFIG. ' Mehrsprachigkeit / multi language';
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($idb, $stores, $shop_lngs, $msg, $plugin) {
                try{
                    global $language;
                    // store config um lang tabelle erweitern; werte kopieren; alte werte löschen
                    define('TABLE_CONFIGURATION_LANG_MULTI', DB_PREFIX.'_config_lang');
                    $tableExists = $idb->GetOne("SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='" . _SYSTEM_DATABASE_DATABASE . "' AND TABLE_NAME='" . TABLE_CONFIGURATION_LANG_MULTI."'");
                    if(!$tableExists)
                    {
                        $idb->Execute("CREATE TABLE `".TABLE_CONFIGURATION_LANG_MULTI."` (
                              `language_content_id` int(11) NOT NULL auto_increment,
                              `language_code` char(2) default NULL,
                              `config_key` varchar(255) default NULL,
                              `language_value` text CHARACTER SET utf8mb4,
                              `store_id` int(11) NOT NULL DEFAULT 0,
                              `group_id` int(11) NOT NULL DEFAULT 0,
                              `sort_order` int(11) NOT NULL DEFAULT 0,
                              PRIMARY KEY  (`language_content_id`),
                              UNIQUE KEY `key_lang_store` (`language_code`,`config_key`,`store_id`)
                            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
                            ");

                        $copy_keys = array(
                            '_STORE_NAME' => array('config_value' => 'Mein Shop','group_id' => 1,'sort_order' => 5),
                            '_STORE_STORE_CLAIM' => array('config_value' => '','group_id' => 1,'sort_order' => 10),
                            '_STORE_META_PUBLISHER' => array('config_value' => '','group_id' => 16,'sort_order' => 5),
                            '_STORE_META_COMPANY' => array('config_value' => '','group_id' => 16,'sort_order' => 10),
                            '_STORE_META_DESCRIPTION' => array('config_value' => '','group_id' => 16,'sort_order' => 15),
                            '_STORE_META_KEYWORDS' => array('config_value' => '','group_id' => 16,'sort_order' => 20),
                            '_STORE_META_FREE_META' => array('config_value' => '','group_id' => 16,'sort_order' => 25)
                        );
                        foreach ($stores as $store)
                        {
                            foreach($copy_keys as $key => $default_values)
                            {
                                $default_values_db = $idb->GetArray("SELECT * FROM ".TABLE_CONFIGURATION_MULTI.$store['id']." WHERE config_key=?", array($key));
                                $default_values_db = $default_values_db[0];
                                if(!empty($default_values_db)){
                                    $default_values = array_merge($default_values, $default_values_db);
                                };

                                foreach ($language->_getLanguageList() as $lng)
                                {
                                    $idb->Execute("INSERT INTO ".TABLE_CONFIGURATION_LANG_MULTI."
							 (`language_code`,
							 `config_key`,
							 `language_value`,
							 `store_id`,
							 `group_id`,
							 `sort_order`
							 )
							 VALUES
							 (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `language_value` = `language_value`;
							 ;", array($lng['code'], $key, $default_values['config_value'], $store['id'], $default_values['group_id'] , $default_values['sort_order']));
                                }
                                $idb->Execute("DELETE FROM ".TABLE_CONFIGURATION_MULTI.$store['id']." WHERE config_key=?", array($key));
                            }

                            // unnötige meta keys aus store config löschen
                            $delete_keys = array('_STORE_META_AUTHOR', '_STORE_META_TOPIC', '_STORE_META_REPLY_TO', '_STORE_META_REVISIT_AFTER');
                            foreach($delete_keys as $d_key)
                            {
                                $idb->Execute("DELETE FROM ".TABLE_CONFIGURATION_MULTI.$store['id']." WHERE config_key=?", array($d_key));
                            }
                        }
                    }


                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-error'>".TEXT_FAILED." : " . $e->getMessage() . "</span><br/>");
                    WizardLogger::getInstance()->log($msg . PHP_EOL . $e->getMessage());
                    return false;
                }
                return true;
            })
            ->setDescription($msg)
            ->setAbortOnError(true)
        );


        $msg = TEXT_MULTI_STORE_CONFIG. ' Eigenschaften verschieben / move properties';
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($idb, $stores, $shop_lngs, $msg, $plugin) {
                try{
                    // store config werte verschieben
                    $update_keys = array(
                        '_STORE_META_DOCTYPE_HTML' => array('group_id' => '18', 'sort_order' => 30),
                        '_STORE_PRODUCT_DOWNLOAD_PUBLIC_ALLOWED' => array('group_id' => '1', 'sort_order' => 130)
                    );
                    foreach ($stores as $store)
                    {
                        foreach($update_keys as $key => $values)
                        {
                            $idb->Execute("UPDATE ".TABLE_CONFIGURATION_MULTI.$store['id']." SET group_id=?, sort_order=? WHERE config_key=?", array($values['group_id'], $values['sort_order'], $key));
                        }
                    }

                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-error'>".TEXT_FAILED." : " . $e->getMessage() . "</span><br/>");
                    WizardLogger::getInstance()->log($msg . PHP_EOL . $e->getMessage());
                    return false;
                }
                return true;
            })
            ->setDescription($msg)
            ->setAbortOnError(true)
        );


        $msg = TEXT_MULTI_STORE_CONFIG. ' erweitern / extend';
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($idb, $stores, $shop_lngs, $msg, $plugin) {
                try{
                    // store config erweitern
                    $insert_keys = array(
                        '_STORE_HREFLANG_DEFAULT' => array('group_id' => '16', 'sort_order' => 20, 'config_value' => '', 'type' => 'dropdown', 'url' => 'language_codes')
                    );
                    foreach ($stores as $store)
                    {
                        foreach($insert_keys as $key => $values)
                        {
                            $exists = $idb->GetOne("SELECT 1 FROM ".TABLE_CONFIGURATION_MULTI.$store['id']." WHERE config_key=?", array($key));
                            if($exists) continue;
                            $idb->Execute("INSERT INTO ".TABLE_CONFIGURATION_MULTI.$store['id']." (config_key, config_value, group_id, sort_order, type, url) values (?,?,?,?,?,?)",
                                array($key, $values['config_value'], $values['group_id'], $values['sort_order'], $values['type'], $values['url']));
                        }
                    }

                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-error'>".TEXT_FAILED." : " . $e->getMessage() . "</span><br/>");
                    WizardLogger::getInstance()->log($msg . PHP_EOL . $e->getMessage());
                    return false;
                }
                return true;
            })
            ->setDescription($msg)
            ->setAbortOnError(true)
        );


        $msg = HEADING_CONFIGURATION. ' erweitern / extend';
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($idb, $stores, $shop_lngs, $msg, $plugin) {
                try{
                    //  config erweitern
                    $insert_keys = array(
                        '_SYSTEM_HTML_MINIFY_OPTION' => array('group_id' => '21', 'sort_order' => 25, 'config_value' => '0', 'type' => 'status', 'url' => '')
                    );
                    foreach($insert_keys as $key => $values)
                    {
                        $exists = $idb->GetOne("SELECT 1 FROM ".TABLE_CONFIGURATION." WHERE config_key=?", array($key));
                        if($exists) continue;
                        $idb->Execute("INSERT INTO ".TABLE_CONFIGURATION." (config_key, config_value, group_id, sort_order, type, url) values (?,?,?,?,?,?)",
                            array($key, $values['config_value'], $values['group_id'], $values['sort_order'], $values['type'], $values['url']));
                    }

                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-error'>".TEXT_FAILED." : " . $e->getMessage() . "</span><br/>");
                    WizardLogger::getInstance()->log($msg . PHP_EOL . $e->getMessage());
                    return false;
                }
                return true;
            })
            ->setDescription($msg)
            ->setAbortOnError(true)
        );


        $msg = TEXT_ALTER_DB. '  FULLTEXT key categories_description';
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($idb, $stores, $shop_lngs, $msg, $plugin) {
                try{
                    // FULLTEXT key für cat desc
                    $key_exists = $idb->GetOne("SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS
                        WHERE table_schema = '"._SYSTEM_DATABASE_DATABASE."'
                        AND table_name = '".TABLE_CATEGORIES_DESCRIPTION."'
                        AND index_name = 'idx_categories_name' ");
                    if($key_exists)
                    {
                        $idb->Execute("ALTER TABLE ".TABLE_CATEGORIES_DESCRIPTION."
                            DROP INDEX `idx_categories_name` ,
                            ADD FULLTEXT INDEX `idx_categories_name`(`categories_name`(250) ASC)");
                    }
                    else {
                        $idb->Execute("ALTER TABLE ".TABLE_CATEGORIES_DESCRIPTION."
                            ADD FULLTEXT INDEX `idx_categories_name` (`categories_name`(250) ASC)");
                    }

                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-error'>".TEXT_FAILED." : " . $e->getMessage() . "</span><br/>");
                    WizardLogger::getInstance()->log($msg . PHP_EOL . $e->getMessage());
                    return false;
                }
                return true;
            })
            ->setDescription($msg)
            ->setAbortOnError(true)
        );


        $msg = TEXT_PAYMENT.' '.TEXT_DESCRIPTION.' Mehrsprachigkeit / multi language';
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($idb, $stores, $shop_lngs, $msg, $plugin) {
                try{
                    global $language;

                    // payment bekommt sprache pro shop, es wird der wert von shop 1 in allen anderen kopiert
                    if (!$plugin->_FieldExists('payment_description_store_id', TABLE_PAYMENT_DESCRIPTION))
                        $idb->Execute("ALTER TABLE ".TABLE_PAYMENT_DESCRIPTION." ADD COLUMN `payment_description_store_id` INT(11) NOT NULL DEFAULT 1");
                    else {

                    }

                    $idb->Execute("ALTER TABLE ".TABLE_PAYMENT_DESCRIPTION." 
                    DROP PRIMARY KEY,
                    ADD PRIMARY KEY (`language_code`, `payment_id`, `payment_description_store_id`)");

                    $payments = $idb->GetArray("SELECT payment_id FROM ".TABLE_PAYMENT);
                    $idb->Execute("DELETE FROM " . TABLE_PAYMENT_DESCRIPTION . " WHERE payment_description_store_id>1");
                    // eigentlich könnten wir einfach den gesamten block pro store kopieren
                    foreach($stores as $store)
                    {
                        if($store['id'] == 1) continue;

                        foreach($payments as $pmnt)
                        {
                            foreach ($language->_getLanguageList() as $lng)
                            {
                                $source_data = $idb->GetArray("SELECT * FROM " . TABLE_PAYMENT_DESCRIPTION . " WHERE language_code=? AND payment_id=? AND payment_description_store_id=?",
                                    array($lng['code'], $pmnt['payment_id'], 1));
                                $source_data = $source_data[0];
                                if(empty($source_data)) continue;

                                $source_data['payment_description_store_id'] = $store['id'];

                                $idb->Execute("DELETE FROM " . TABLE_PAYMENT_DESCRIPTION . " WHERE language_code=? AND payment_id=? AND payment_description_store_id=?",
                                    array($lng['code'], $pmnt['payment_id'], $store['id']));

                                $idb->Execute("INSERT INTO " . TABLE_PAYMENT_DESCRIPTION . "
                            (`payment_id`,
                            `language_code`,
                            `payment_name`,
                            `payment_desc`,
                            `payment_email_desc_txt`,
                            `payment_email_desc_html`,
                            `payment_description_store_id`)
                            VALUES
                            (?, ? , ?, ?, ? ,?, ?)", $source_data);
                            }
                        }
                    }

                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-error'>".TEXT_FAILED." : " . $e->getMessage() . "</span><br/>");
                    WizardLogger::getInstance()->log($msg . PHP_EOL . $e->getMessage());
                    return false;
                }
                return true;
            })
            ->setDescription($msg)
            ->setAbortOnError(true)
        );


        $msg = TEXT_NEW_FIELD. ' adresse.adressZusatz';
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($idb, $stores, $shop_lngs, $msg, $plugin) {
                try{
                    // neue spalte in adressen/order
                    if (!$plugin->_FieldExists('customers_address_addition', TABLE_CUSTOMERS_ADDRESSES))
                    {
                        $idb->Execute('ALTER TABLE '.TABLE_CUSTOMERS_ADDRESSES." ADD COLUMN `customers_address_addition` VARCHAR(64) NULL DEFAULT NULL AFTER `customers_street_address` ");
                    }
                    if (!$plugin->_FieldExists('delivery_address_addition', TABLE_ORDERS))
                    {
                        $idb->Execute('ALTER TABLE '.TABLE_ORDERS." ADD COLUMN `delivery_address_addition` VARCHAR(64) NULL DEFAULT NULL AFTER `delivery_street_address` ");
                    }
                    if (!$plugin->_FieldExists('billing_address_addition', TABLE_ORDERS))
                    {
                        $idb->Execute('ALTER TABLE '.TABLE_ORDERS." ADD COLUMN `billing_address_addition` VARCHAR(64) NULL DEFAULT NULL AFTER `billing_street_address` ");
                    }

                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-error'>".TEXT_FAILED." : " . $e->getMessage() . "</span><br/>");
                    WizardLogger::getInstance()->log($msg . PHP_EOL . $e->getMessage());
                    return false;
                }
                return true;
            })
            ->setDescription($msg)
            ->setAbortOnError(true)
        );


        $msg = TEXT_FIX_PRODUCTS_UNIT;
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($idb, $stores, $shop_lngs, $msg, $plugin) {
                try{

                    $arr = $idb->GetArray('SELECT status_id FROM '.TABLE_SYSTEM_STATUS." WHERE status_class='base_price' ");
                    $ids = array();
                    foreach($arr as $a)
                    {
                        $ids[] = $a['status_id'];
                    }
                    $idb->Execute('UPDATE '.TABLE_PRODUCTS.' SET products_unit=0 WHERE products_unit NOT IN ('.implode(',',$ids).')');

                    $idb->Execute("ALTER TABLE " . TABLE_PRODUCTS . " CHANGE COLUMN products_unit products_unit INT(11) DEFAULT 0;");

                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-error'>".TEXT_FAILED." : " . $e->getMessage() . "</span><br/>");
                    WizardLogger::getInstance()->log($msg . PHP_EOL . $e->getMessage());
                    return false;
                }
                return true;
            })
            ->setDescription($msg)
            ->setAbortOnError(true)
        );


        $msg = "Fix E-Mail-Templates";
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($idb, $stores, $shop_lngs, $msg, $plugin) {
                try{

                    $email_tpls = $idb->GetArray('SELECT * FROM '.TABLE_MAIL_TEMPLATES_CONTENT);
                    foreach ($email_tpls as $tpl)
                    {
                        $html = preg_replace("/\{txt[\s]+key=([\w]+)/", "{txt key=\"$1\"", $tpl['mail_body_html']);
                        $txt  = preg_replace("/\{txt[\s]+key=([\w]+)/", "{txt key=\"$1\"", $tpl['mail_body_txt']);
                        $subj = preg_replace("/\{txt[\s]+key=([\w]+)/", "{txt key=\"$1\"", $tpl['mail_subject']);
                        $idb->Execute("UPDATE ".TABLE_MAIL_TEMPLATES_CONTENT. " SET mail_body_html=?, mail_body_txt=?, mail_subject=? WHERE tpl_id=? AND language_code=?", array($html, $txt, $subj, $tpl['tpl_id'], $tpl['language_code']));
                    }

                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-error'>".TEXT_FAILED." : " . $e->getMessage() . "</span><br/>");
                    WizardLogger::getInstance()->log($msg . PHP_EOL . $e->getMessage());
                    return false;
                }
                return true;
            })
            ->setDescription($msg)
            ->setAbortOnError(true)
        );


        $msg = TEXT_INSTALL_MAIL_TEMPLATES;
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($idb, $stores, $shop_lngs, $msg, $plugin) {
                try{

                    // neue mail templates
                    $max_tpl_number = 15;
                    $mail_tpls = array('dsgvo' => 11);
                    foreach($mail_tpls as $tpl_code => $tpl_file_number)
                    {
                        $send_order_data = $idb->GetArray('SELECT * FROM '.TABLE_MAIL_TEMPLATES." WHERE tpl_type='send_order'");
                        if(is_array($send_order_data))
                        {
                            $send_order_data = $send_order_data[0];
                        }
                        else {
                            $send_order_data = array();
                        }

                        $insert_array = array();
                        $insert_array['tpl_type']       = $tpl_code;
                        $insert_array['tpl_special']    = 0;

                        $insert_array = array_merge($send_order_data, $insert_array);
                        unset($insert_array['tpl_id']);

                        $idb->AutoExecute(TABLE_MAIL_TEMPLATES,$insert_array);
                        $tpl_id = $idb->Insert_ID();

                        foreach ($shop_lngs as $lng)
                        {
                            switch ($lng['code'])
                            {
                                case 'de':
                                    self::_installMailTemplates($idb,'de', $max_tpl_number, $tpl_id);
                                    break;
                                default:
                                    self::_installMailTemplates($idb, 'en', $max_tpl_number, $tpl_id);
                            }
                        }
                    }

                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-error'>".TEXT_FAILED." : " . $e->getMessage() . "</span><br/>");
                    WizardLogger::getInstance()->log($msg . PHP_EOL . $e->getMessage());
                    return false;
                }
                return true;
            })
            ->setDescription($msg)
            ->setAbortOnError(true)
        );

        $sel_engine = 'innodb';
        $sql_version = $idb->GetOne("SELECT VERSION() AS Version");
        if(version_compare($sql_version, '5.6') == -1)
        {
            // before mysql 5.6 use myisam
            $sel_engine = 'myisam';
        }

        $msg = TEXT_UPDATE_CONFIG_PHP;
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($idb, $stores, $shop_lngs, $msg, $plugin, $sel_engine) {
                try{
                    $config_file = _SRV_WEBROOT . 'conf/config.php';

                    if(!file_exists($config_file)) throw new Exception($config_file.' nicht gefunden / not found');

                    $config_str = implode("",file($config_file));
                    $fp = fopen($config_file,'w+');
                    $config_str = str_replace('?>','',$config_str);
                    $config_str = preg_replace("/define\('DB_STORAGE_ENGINE','(innodb|myisam)'\);/", '',$config_str);
                    $config_str = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $config_str);
                    $config_str .= PHP_EOL . "define('DB_STORAGE_ENGINE','".$sel_engine."');" . PHP_EOL;
                    fwrite($fp, $config_str, strlen($config_str));
                    fclose($fp);

                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-error'>".TEXT_FAILED." : " . $e->getMessage() . "</span><br/>");
                    WizardLogger::getInstance()->log($msg . PHP_EOL . $e->getMessage());
                    return false;
                }
                return true;
            })
            ->setDescription($msg)
            ->setAbortOnError(true)
        );

/*
        $msg = TEXT_COPY_VERSION_INFO;
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($idb, $stores, $shop_lngs, $msg, $plugin) {
                try{

                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-error'>".TEXT_FAILED." : " . $e->getMessage() . "</span><br/>");
                    WizardLogger::getInstance()->log($msg . PHP_EOL . $e->getMessage());
                    return false;
                }
                return true;
            })
            ->setDescription($msg)
            ->setAbortOnError(true)
        );
*/

        $page->assignTemplateVar('processing',TEXT_UPDATING,true);
        $this->execAsyncAction($page);
    }


    /**
     * recalc digital tax
     * @param WizardPage $page
     */
    public function recalculateDigitalTax(WizardPage $page) {
        $idb = Wizard::getInstance()->getDatabaseObject();

        $tax_classes = $idb->GetArray('SELECT tax_class_id as `id`, tax_class_title as `text` FROM '.TABLE_TAX_CLASS);
        array_unshift($tax_classes, array('value' => 0, 'text' => 'Keine Digitalsteuer vorhanden / No digital tax'));
        $page->assignTemplateVar('tax_classes', $tax_classes);

        if (Wizard::getInstance()->isPost()) {

            $_error = array();

            $page->assignTemplateVar('default_tax_class', $_POST['tax_class_id']);

            $tax_class_id_for_update = (int)$_POST['tax_class_id'];

            $csv_file = _SRV_WEBROOT._SRV_WEB_EXPORT.'5.1.0_digital_price_backup.csv';
            if(file_exists($csv_file))
            {
                $_error[] = array('text' => 'Sie haben bereits eine Neuberechnung durchgeführt! Wenn Sie dies wirklich nocheinmal durchführen möchten, löschen Sie die Datei export/5.1.0_digital_price_backup.csv<br /><br/>You have already did a recalculation! If you really want to do this again, delete the file export/5.1.0_digital_price_backup.csv');
            }

            if (empty($_error) && $tax_class_id_for_update>0)
            {

                $csv_file = _SRV_WEBROOT._SRV_WEB_EXPORT.'5.1.0_digital_price_backup.csv';

                self::update_digital_price_507($idb, $tax_class_id_for_update, $csv_file);

                $idb->Execute('COMMIT;');

                $idb->Execute('COMMIT;');

            }
            else if ($tax_class_id_for_update>0) $page->assignTemplateVar('error', $_error);

            $url = Wizard::getInstance()->buildUrl(array(Wizard::PARAM_NEXT => 1, Wizard::PARAM_PAGE => Wizard::getInstance()->getCurrentPage()));
            Wizard::getInstance()->redirect($url);

        }
        else {

            $page->assignTemplateVar('default_tax_class', 0);
        }
        $page->setTemplate('scripts/update-5.0.x-5.1.x/templates/recalculateDigitalTax.html');
    }

    static function update_digital_price_507($idb, $tax_class, $csv_file)
    {
        global $tax;

        /**
         *   vorläufig auskommentiert, weil
         *   nur relevant für kunden mit digitalen produkten ohne angabe der steuerklasse
         *
         *   skript würde die bisherigen "bruttopreise" (fehlekoonfiguration) in nettopreise ändern
         *
         *   wir wollen aber keine preise ändern
         */
        /*
        $digital_products = $idb->GetArray("SELECT products_id, products_model, products_price FROM '.TABLE_PRODUCTS.' WHERE products_digital=1 AND (products_tax_class_id IS NULL OR products_tax_class_id=0 OR products_tax_class_id='') ");

        if(count($digital_products))
        {
            $fp = fopen($csv_file, 'w');
            if(is_resource($fp)) fputcsv($fp, array('products_id', 'products_model', 'products_price', 'products_price_updated'),';');

            $tax_rate = $tax->data[$tax_class];

            foreach($digital_products as $dp)
            {
                $updated_price = ($dp['products_price'] / (($tax_rate +100) / 100));
                $csv_data = array(
                    $dp['products_id'],
                    $dp['products_model'],
                    $dp['products_price'],
                    $updated_price
                );
                if(is_resource($fp)) fputcsv($fp, $csv_data, ';');

                $idb->Execute("UPDATE ".TABLE_PRODUCTS." SET products_price = ?, products_tax_class_id = ? WHERE products_id = ?", array($updated_price, $tax_class, $dp['products_id']));
            }
        }
        */

        $idb->Execute("UPDATE ".TABLE_TAX_CLASS." SET is_digital_tax = 1 WHERE tax_class_id = ?", array($tax_class));

    }


    static function _installMailTemplates($idb, $lng, $max_id=0, $tpl_id)
    {
        if ($max_id==0) return false;

        $exists = $idb->GetOne('SELECT 1 FROM '.TABLE_MAIL_TEMPLATES_CONTENT.' WHERE tpl_id=? AND language_code=?', array($tpl_id, $lng));
        if($exists) {
            WizardLogger::getInstance()->log('Skipped installMailTemplates for tpl_id='.$tpl_id.', lng='.$lng);
            return;
        }

        $mail_dir = __DIR__.'/languages/'.$lng.'/mails';

        for ($i=1;$i<$max_id+1;$i++) {

            if (file_exists($mail_dir.'/'.$i.'_'.$lng.'_txt.txt')) {

                $file_prefix = $i.'_'.$lng.'_';

                $html_content   = self::_getFileContent($mail_dir.'/'.$file_prefix.'html.txt');
                $txt_content    = self::_getFileContent($mail_dir.'/'.$file_prefix.'txt.txt');
                $subject        = self::_getFileContent($mail_dir.'/'.$file_prefix.'subject.txt');

                $insert_array=array();
                $insert_array['tpl_id']         = $tpl_id;
                $insert_array['language_code']  = $lng;
                $insert_array['mail_body_html'] = $html_content;
                $insert_array['mail_body_txt']  = $txt_content;
                $insert_array['mail_subject']   = $subject;

                $idb->AutoExecute(TABLE_MAIL_TEMPLATES_CONTENT, $insert_array);
            }
        }
    }

}

global $language;
if($language)
{
    $langFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . "wizard_{$language->content_language}.yml";
    if (file_exists($langFile))
    {

        $contents = file_get_contents($langFile);
        $languageParts = explode("\n", $contents);

        foreach ($languageParts as $part)
        {
            $delimiterPos = strpos($part, "=", 0);

            if ($delimiterPos === false)
            {
                continue;
            }
            $systemParts = substr($part, 0, $delimiterPos);
            $value = substr($part, $delimiterPos + 1);

            list($plugin, $type, $key) = explode(".", $systemParts);
            if (!defined($key))
            {
                $value = str_replace(array("\r", "\n"), '', $value);
                define($key, $value);
            }
        }
    }
}
Wizard::getInstance()->registerScript(new Update500xTo51x());

    