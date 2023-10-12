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


class Update61xTo62x extends ExecutableScript {

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
        return '6.1.0';
    }


    public function getTargetShopVersion() {
        return '6.2.0';
    }
    public function getSkippableShopVersion(){
        return '6.2.0';
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
            ->assignTemplateVar('message', TEXT_FINISH_INSTRUCTIONS_61.
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

    public function _installSQL( $idb, $filename, $prefix='', $language_code='',$store_id='', $additionalSqlLines = false) {
        $query = '';
        // open sql
        $filename = dirname(__FILE__) . DS .$filename;
        $sql_content = $this->_getFileContent($filename);
        if($additionalSqlLines)
        {
            $sql_content .= PHP_EOL.$additionalSqlLines.PHP_EOL.' ';
        }

        // replace windows linefeeds
        $sql_content = str_replace("\r\n","\n",$sql_content);
        $queries = array();
        $chars = strlen($sql_content);
        for ($i = 0; $i < $chars; $i++) {
            // check if char is ; and next \n
            if ($sql_content[$i]==';' && $sql_content[$i+1]=="\n") {
                $query .= $sql_content[$i];
                $queries[] = $query;
                $query = '';
                $i++;
            } else {
                if ($sql_content[$i]=='-' && $sql_content[$i+1]=='-') {
                    // skip to next \n
                    for ($ii=$i;$ii<$chars;$ii++) {

                        if ($sql_content[$ii]=="\n") {
                            break;
                        } else {
                            $i++;
                        }

                    }
                } else {
                    if (!isset($query)) $query='';
                    $query.=$sql_content[$i];
                }
            }
        }

        foreach ($queries as $key => $val) {
            $query = trim($val);
            $query = str_replace('##_',$prefix,$query);
            if ($store_id!='') $query = str_replace('**_REPLACE_STORE_ID_**',$store_id,$query);

            // ok, now search vor OTHER INSERT INTO statements, and break them up
            if (substr($query,0,6)=='INSERT') {
                $check_qry = substr($query,7);

                if (strstr($check_qry,'INSERT')) {
                    $qry = explode('INSERT',$check_qry);
                    foreach ($qry as $k => $v) {
                        $queries[]='INSERT '.$v;
                    }
                    unset ($queries[$key]);
                } else {
                    $queries[$key]=$query;
                }
            } else {
                $queries[$key]=$query;
            }
        }
        return $queries;

    }


    /**
    * @param WizardPage $page
    */
    public function doUpdate(WizardPage $page)
    {
        global $store_handler, $language;

        $install_prefix = DB_PREFIX.'_';

        $idb = Wizard::getInstance()->getDatabaseObject();



        $sql_version = $idb->GetOne("SELECT VERSION() AS Version");
        if(version_compare($sql_version, '5.5') == -1)
        {
            die('Systemanforderung  mysql 5.5 minimum nicht erfüllt. <a href="https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917573/Systemanforderungen" target="_blank">Handbuch</a>');
        }

        // _STORE_DEFAULT_PRODUCT_CONDITION
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($idb, $store_handler) {
                try{
                    $stores = $store_handler->getStores();
                    foreach ($stores as $store)
                    {
                        $table = DB_PREFIX.'_config_'.$store['id'];
                        $idb->Execute("INSERT INTO `".$table."` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_DEFAULT_PRODUCT_CONDITION', 'NewCondition', '1', '150', 'dropdown', 'products_conditions')");
                    }
                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-error'>".TEXT_QUERY_FAILED." " . $e->getMessage() . "</span><br/>");
                    return false;
                }

                return true;
            })
            ->setDescription(TEXT_UPDATING . " DB _config_x add  _STORE_DEFAULT_PRODUCT_CONDITION<br/>")
            ->setAbortOnError(true)
        );

        // _STORE_ACCOUNT_MIN_AGE  fix, update 6.1 only wrote to xt_config_1
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($idb, $store_handler) {
                try{
                    // _STORE_ACCOUNT_MIN_AGE
                    $stores = $store_handler->getStores();
                    foreach ($stores as $store)
                    {
                        $table = DB_PREFIX.'_config_'.$store['id'];
                        $_key_exists = $idb->GetOne('SELECT 1 FROM '.$table." WHERE config_key = '_STORE_ACCOUNT_MIN_AGE'");
                        if(!$_key_exists)
                        {
                            $idb->Execute('INSERT INTO ' . $table . " (config_key, config_value, group_id, sort_order, type) VALUES ('_STORE_ACCOUNT_MIN_AGE', '0', '5', '-5', NULL)");
                        }
                    }
                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-error'>".TEXT_QUERY_FAILED." " . $e->getMessage() . "</span><br/>");
                    return false;
                }

                return true;
            })
            ->setDescription(TEXT_UPDATING . " DB _config_x _STORE_ACCOUNT_MIN_AGE<br/>")
            ->setAbortOnError(true)
        );

        // _STORE_VAT_CHECK_TYPE  textfield not textarea
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($idb, $store_handler) {
                try{
                    $stores = $store_handler->getStores();
                    foreach ($stores as $store)
                    {
                        $table = DB_PREFIX.'_config_'.$store['id'];
                        $idb->Execute('UPDATE ' . $table . " SET `config_value` = 'format' WHERE `config_key` = '_STORE_VAT_CHECK_TYPE' AND `config_value` != 'live'");
                    }
                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-error'>".TEXT_QUERY_FAILED." " . $e->getMessage() . "</span><br/>");
                    return false;
                }

                return true;
            })
            ->setDescription(TEXT_UPDATING . " DB _config_x update _STORE_VAT_CHECK_TYPE <br/>")
            ->setAbortOnError(true)
        );

        // _STORE_META_ROBOTS  textfield not textarea
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($idb, $store_handler) {
                try{
                    $stores = $store_handler->getStores();
                    foreach ($stores as $store)
                    {
                        $table = DB_PREFIX.'_config_'.$store['id'];
                        $idb->Execute('UPDATE ' . $table . " SET `type` = 'textfield' WHERE `config_key` = '_STORE_META_ROBOTS'");
                    }
                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-error'>".TEXT_QUERY_FAILED." " . $e->getMessage() . "</span><br/>");
                    return false;
                }

                return true;
            })
            ->setDescription(TEXT_UPDATING . " DB _config_x make _STORE_META_ROBOTS textfield<br/>")
            ->setAbortOnError(true)
        );

        // _STORE_HREFLANG_DEFAULT  'keine Auswahl' hinzufügen
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($idb, $store_handler) {
                try{
                    $stores = $store_handler->getStores();
                    foreach ($stores as $store)
                    {
                        $table = DB_PREFIX.'_config_'.$store['id'];
                        $idb->Execute('UPDATE ' . $table . " SET `url` = 'language_codes&add_empty' WHERE `config_key` = '_STORE_HREFLANG_DEFAULT'");
                        $storeLng = $idb->GetOne("SELECT `config_value` FROM " . $table . " WHERE `config_key` = '_STORE_LANGUAGE' ");
                        $idb->Execute('UPDATE ' . $table . " SET `config_value` = ? WHERE `config_key` = '_STORE_HREFLANG_DEFAULT' AND (`config_value` IS NULL OR LENGTH(`config_value`)=0) ", [$storeLng]);
                    }
                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-error'>".TEXT_QUERY_FAILED." " . $e->getMessage() . "</span><br/>");
                    return false;
                }

                return true;
            })
            ->setDescription(TEXT_UPDATING . " DB _config_x make _STORE_HREFLANG_DEFAULT add 'No selection' to dropdown, set default to store langauge<br/>")
            ->setAbortOnError(true)
        );



        // fix orders_status_history
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($idb, $store_handler) {
                try{
                    $plg = new plugin();
                    if(!$plg->_FieldExists('customer_show_comment',TABLE_ORDERS_STATUS_HISTORY))
                    {
                        $idb->Execute("ALTER TABLE `" . TABLE_ORDERS_STATUS_HISTORY . "` ADD COLUMN `customer_show_comment` INT(1) DEFAULT 0 ");

                        if ($plg->_FieldExists('customer_show_content', TABLE_ORDERS_STATUS_HISTORY))
                        {
                            $idb->Execute("UPDATE `" . TABLE_ORDERS_STATUS_HISTORY . "` SET `customer_show_comment` = `customer_show_content`");
                            $idb->Execute("ALTER TABLE `" . TABLE_ORDERS_STATUS_HISTORY . "` DROP COLUMN `customer_show_content`");
                        }
                    }
                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-error'>".TEXT_QUERY_FAILED." " . $e->getMessage() . "</span><br/>");
                    return false;
                }

                return true;
            })
            ->setDescription(TEXT_UPDATING . " DB FIX customer_show_comment/customer_show_content<br/>")
            ->setAbortOnError(true)
        );



        $update_sql = 'update.sql';
        if(version_compare($sql_version, '5.6') == -1)
        {
            // before mysql 5.6 use different sql script
            // $update_sql = 'update.55.sql';
        }
        $res = $this->_installSQL(Wizard::getInstance()->getDatabaseObject(), $update_sql, $install_prefix,'');

        $update_store_sql = 'update_store.sql';
        foreach ($store_handler->getStores() as $store)
        {
            $res = array_merge($res, $this->_installSQL(Wizard::getInstance()->getDatabaseObject(), $update_store_sql, $install_prefix,'', $store['id']));
        }

        foreach ($res as $key => $val) {
            $this->addControllerCommand(ControllerCommand::factory()
                ->setAction(function(ControllerCommand $command) use ($val) {
                    $idb = Wizard::getInstance()->getDatabaseObject();
                    try{
                        $idb->Execute($val);
                    }catch (Exception $e){
                        /*'1060 - Duplicate column name','1062 - Duplicate entry', '1050- table alredy exists','1091 -  Can't DROP '%s'; check that column/key exists'*/
                        $allowed_error_codes = array(1062,1061,1060,1050,1091, 1054, 1091);
                        if (in_array($e->getCode(),$allowed_error_codes)) {
                            $command->setErrorMessage("<span class='msg-skipped'>".TEXT_QUERY_SKIPPED."  " . $e->getMessage() . "</span><br/>");
                        }else{
                            $command->setErrorMessage("<span class='msg-error'>".TEXT_QUERY_FAILED." " . $e->getMessage() . "</span><br/>");
                            return false;
                        }
                    }

                    return true;
                })
                ->setDescription(TEXT_EXECUTING_QUERY.$val . "<br/>")
                ->setAbortOnError(true)
            );

        }

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
        $page->setTemplate('scripts/update-5.0.0x-5.1.x/templates/recalculateDigitalTax.html');
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
Wizard::getInstance()->registerScript(new Update61xTo62x());

    