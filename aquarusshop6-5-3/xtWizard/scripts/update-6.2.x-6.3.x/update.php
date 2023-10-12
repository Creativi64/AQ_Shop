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


class Update62xTo63x extends ExecutableScript {

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
        return '6.2.0';
    }


    public function getTargetShopVersion() {
        return '6.3.0';
    }
    public function getSkippableShopVersion(){
        return '6.3.0';
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
            ->assignTemplateVar('message', TEXT_FINISH_INSTRUCTIONS_63.
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
        $sql_content .= PHP_EOL.' ';

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

        /** @var ADOConnection $idb */
        $idb = Wizard::getInstance()->getDatabaseObject();

        $sql_version = $idb->GetOne("SELECT VERSION() AS Version");
        if(version_compare($sql_version, '5.6') == -1)
        {
            die('Systemanforderung  mysql 5.6 minimum nicht erfüllt. <a href="https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917573/Systemanforderungen" target="_blank">Handbuch</a>');
        }

        $update_sql = 'update.sql';
        $res = $this->_installSQL(Wizard::getInstance()->getDatabaseObject(), $update_sql, $install_prefix,'', '');

        $update_store_sql = 'update_store.sql';
        foreach ($store_handler->getStores() as $store)
        {
            $res = array_merge($res, $this->_installSQL(Wizard::getInstance()->getDatabaseObject(), $update_store_sql, $install_prefix,'', $store['id']));
        }

        foreach ($res as $key => $val) {
            $this->addControllerCommand(ControllerCommand::factory()
                ->setAction(function(ControllerCommand $command) use ($val, $idb) {
                    try{
                        $idb->Execute($val);
                    }catch (Exception $e){
                        /*'1060 - Duplicate column name','1062 - Duplicate entry', '1050- table alredy exists','1091 -  Can't DROP '%s'; check that column/key exists'*/
                        $allowed_error_codes = array(1062,1061,1060,1050,1091, 1054, 1091);
                        if (in_array($e->getCode(),$allowed_error_codes)) {
                            $command->setErrorMessage("<br/><span class='msg-skipped'>".TEXT_QUERY_SKIPPED."  " . $e->getMessage() . "</span>");
                        }else{
                            $command->setErrorMessage("<span class='msg-error'>".TEXT_QUERY_FAILED." " . $e->getMessage() . ' | '.$e->getCode()."</span><br/>");
                            return false;
                        }
                    }

                    return true;
                })
                ->setDescription(TEXT_EXECUTING_QUERY.' '.$val)
                ->setAbortOnError(true)
            );
        }

        if(!isset($_REQUEST["ajaxLoad"]))
        {
            $myisam_tbls_sql = "SELECT table_name as tbl_name
            FROM information_schema.tables
            WHERE engine = 'MyISAM'
                  AND table_type = 'BASE TABLE'
                  AND table_schema = ? ORDER BY tbl_name";
            $myisam_tbls = $idb->GetArray($myisam_tbls_sql, array(_SYSTEM_DATABASE_DATABASE));
            $_SESSION['myisam_tables'] = array();
            foreach ($myisam_tbls as $tbl)
            {
                $_SESSION['myisam_tables'][] = $tbl['tbl_name'];
            }
        }



        $msg = TEXT_UPDATE_CONFIG_PHP;
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($idb, $msg) {
                try{
                    $config_file = _SRV_WEBROOT . 'conf/config.php';

                    if(!file_exists($config_file)) throw new Exception($config_file.' nicht gefunden / not found');

                    $config_str = implode("",file($config_file));
                    $fp = fopen($config_file,'w+');
                    $config_str = str_replace('?>','',$config_str);
                    $config_str = preg_replace("/define\('DB_STORAGE_ENGINE','(innodb|myisam)'\);/", '',$config_str);
                    $config_str = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $config_str);
                    $config_str .= PHP_EOL . "define('DB_STORAGE_ENGINE','InnoDB');" . PHP_EOL;
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


        $dir = _SRV_WEBROOT.'media/.ckfinder';
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function (ControllerCommand $command) use ($dir) {
                $ret = true;
                try
                {
                    if(is_dir($dir))
                    {
                        $ret = Wizard::rrmdir($dir);
                    }
                }
                catch (Exception $e){}

                return $ret;
            })
            ->setDescription(TEXT_REMOVE_DIRECTORY . ': ' . $dir)
            ->setAbortOnError(false)
        );

        $page->assignTemplateVar('processing',TEXT_UPDATING,true);
        $this->execAsyncAction($page);
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

    /**
     * @param WizardPage $page
     */
    public function my8patch(WizardPage $page)
    {
        global $store_handler, $language;

        $install_prefix = DB_PREFIX.'_';

        /** @var ADOConnection $idb */
        $idb = Wizard::getInstance()->getDatabaseObject();

        if(count($_SESSION['myisam_tables']))
        {
            foreach ($_SESSION['myisam_tables'] as $tbl)
            {
                $this->addControllerCommand(ControllerCommand::factory()
                    ->setAction(function (ControllerCommand $command) use ($tbl, $idb) {
                        $query = '';
                        try
                        {
                            $query = 'ALTER TABLE ' . $tbl . ' ENGINE=InnoDB;';
                            $idb->Execute($query);

                        } catch (Exception $e)
                        {
                            $allowed_error_codes = array(1062, 1061, 1060, 1050, 1091, 1054, 1091);
                            if (in_array($e->getCode(), $allowed_error_codes))
                            {
                                $command->setErrorMessage("<span class='msg-skipped'>" . TEXT_QUERY_SKIPPED . "  " . $query . '<br />' . $e->getMessage() . "</span><br/>");
                            }
                            else
                            {
                                $command->setErrorMessage("<span class='msg-error'>" . TEXT_QUERY_FAILED . " " . $e->getMessage() . ' | ' . $e->getCode() . "</span><br/>");
                                return false;
                            }
                        }

                        return true;
                    })
                    ->setDescription(TEXT_CHANGING_TABLE_ENGINE . ': ' . $tbl)
                    ->setAbortOnError(false)
                );
            }
        }
        else
        {
            $this->addControllerCommand(ControllerCommand::factory()
                ->setAction(function (ControllerCommand $command)  {
                    return true;
                })
                ->setDescription(TEXT_CHANGING_TABLE_ENGINE . ': ' . TEXT_NO_MYISAM_TABLES)
                ->setAbortOnError(false)
            );
        }

        if(!isset($_REQUEST["ajaxLoad"]))
        {
            $fix_table_cols = $this->get_cols_to_fix();
            $_SESSION['fix_cols'] = $fix_table_cols;
        }

        global $customers_status, $language;
        if(empty($customers_status)) $customers_status = new customers_status();
        if(empty($language)) $language = new language();
        $customers_status_list = $customers_status->_getStatusList('admin');

        if(count($_SESSION['fix_cols']))
        {
            foreach ($_SESSION['fix_cols'] as $table => $cols)
            {
                foreach ($cols as $col)
                {
                    $this->addControllerCommand(ControllerCommand::factory()
                        ->setAction(function (ControllerCommand $command) use ($table, $col, $customers_status, $customers_status_list, $idb) {
                            $query = '';
                            try
                            {

                                $table = DB_PREFIX . $table;
                                $table_exists = $idb->GetOne("SELECT table_name as tbl_name
                                FROM information_schema.tables
                                WHERE table_name = ?
                                AND table_schema = ?", [$table, _SYSTEM_DATABASE_DATABASE]);
                                if ($table_exists)
                                {

                                    if (strpos($col, '##CUST_STATUS##'))
                                    {
                                        foreach ($customers_status_list as $cs)
                                        {
                                            $cust_col = str_replace('##CUST_STATUS##', $cs['id'], $col);
                                            $msgs[] = $this->updCol($cust_col, $table);
                                        }
                                    }
                                    else
                                    {
                                        $msgs[] = $this->updCol($col, $table);
                                    }
                                }


                            } catch (Exception $e)
                            {
                                $allowed_error_codes = array(1062, 1061, 1060, 1050, 1091, 1054, 1091);
                                if (in_array($e->getCode(), $allowed_error_codes))
                                {
                                    $command->setErrorMessage("<span class='msg-skipped'>" . TEXT_QUERY_SKIPPED . "  " . $query . '<br />' . $e->getMessage() . "</span><br/>");
                                }
                                else
                                {
                                    $command->setErrorMessage("<span class='msg-error'>" . TEXT_QUERY_FAILED . " " . $e->getMessage() . ' | ' . $e->getCode() . "</span><br/>");
                                    return false;
                                }
                            }

                            return true;
                        })
                        ->setDescription(TEXT_CHANGING_COLUMN_DEFINITION . ': ' .DB_PREFIX. $table . '.' . $col)
                        ->setAbortOnError(false)
                    );
                }
            }
        }
        else
        {
            $this->addControllerCommand(ControllerCommand::factory()
                ->setAction(function (ControllerCommand $command)  {
                    return true;
                })
                ->setDescription(TEXT_CHANGING_COLUMN_DEFINITION . ': ' . TEXT_NO_COLS_TO_CHANGE)
                ->setAbortOnError(false)
            );
        }

        $page->assignTemplateVar('processing',TEXT_UPDATE_DB_MY8,true);
        $this->execAsyncAction($page);

    }

    private function get_cols_to_fix()
    {
        return [
            '_acl_groups' => [ 'status' ],
            '_acl_user' => [ 'status' ],
            '_categories' => [
                'top_category',
                'categories_status',
                'start_page_category' ],
            '_categories_permission' => [ 'permission' ],
            '_config_group' => [ 'visible' ],
            '_content' => [ 'content_status' ],
            '_content_block' => [ 'block_status', 'block_protected' ],
            '_content_permission' => [ 'permission' ],
            '_countries' => [ 'status' ],
            '_countries_permission' => [ 'permission' ],
            '_cron' => [ 'running_status', 'active_status' ],
            '_customers' => [ 'password_type', 'account_type' ],
            '_customers_basket' => [ 'status' ],
            '_customers_status' => [
                'customers_status_show_price',
                'customers_status_show_price_tax',
                'customers_status_add_tax_ot',
                'customers_status_graduated_prices',
                'customers_fsk18',
                'customers_fsk18_display'
            ],
            '_failed_login' => [ 'check_type' ],
            '_federal_states' => [ 'status' ],
            '_feed' => [
                'feed_mail_flag',
                'feed_ftp_flag',
                'feed_ftp_passiv',
                'feed_save',
                'feed_pw_flag',
                'feed_p_slave'
            ],
            '_languages' => [ 'language_status', 'allow_edit', 'font_size', 'font_position' ],
            '_language_content' => [ 'translated', 'readonly' ],
            '_manufacturers' => [ 'manufacturers_status' ],
            '_manufacturers_permission' => [ 'permission' ],
            '_media_gallery' => [ 'status' ],
            '_orders' => [ 'account_type', 'allow_tax' ],
            '_orders_products' => [ 'allow_tax' ],
            '_orders_total' => [ 'allow_tax' ],
            '_orders_status_history' => [ 'customer_notified', 'customer_show_comment' ],
            '_payment' => [ 'payment_cost_info', 'status', 'plugin_required' ],
            '_payment_cost' => [ 'payment_cost_discount', 'payment_cost_percent', 'payment_allowed' ],
            '_plugin_code' => [ 'code_status', 'plugin_status' ],
            '_plugin_products' => [ 'plugin_status' ],
            '_products' => [
                'price_flag_graduated_all',
                'price_flag_graduated_##CUST_STATUS##',
                'products_fsk18',
                'products_digital',
                'flag_has_specials',
                'products_serials',
                'products_status'
            ],
            '_products_permission' => [ 'permission' ],
            '_products_price_special' =>  [
                'status',
                'group_permission_all',
                'group_permission_##CUST_STATUS##',
            ],
            '_products_serials' => [ 'status' ],
            '_products_to_categories' => [ 'master_link' ],
            '_sales_stats' => [ 'sales_stat_type' ],
            '_shipping' => [ 'status', 'use_shipping_zone' ],
            '_shipping_cost' => [ 'shipping_allowed' ],
            '_stores' => [ 'shop_ssl', 'shop_status' ],
            '_system_log' => [ 'popuptrigger' ],
            '_seo_stop_words' => [ 'replace_word' ],
            '_tax_class' => [ 'is_digital_tax' ],
            '_mail_templates_attachment' => [ 'attachment_status' ],
            '_pdf_manager' => [ 'template_use_be_lng' ],
        ];
    }

    /**
     * @param $colName
     * @param $table
     * @return stdClass
     */
    private function updCol($colName, $table)
    {
        global $db;

        $ret =  new stdClass();
        $ret->col = $colName;
        $ret->table = $table;
        $ret->msg = '';
        $ret->success = true;

        $col = $db->GetArray("SELECT *
                    FROM information_schema.columns
                    WHERE table_name = ?
                          AND table_schema = ? 
                          AND column_name = ?", [$table, _SYSTEM_DATABASE_DATABASE, $colName]);
        if(is_array($col) && count($col))
        {
            $col = $col[0];

            if(strpos($col['COLUMN_DEFAULT'],'1'))
                $default = 1;
            else
                $default = 0;

            GLOBAL $ADODB_THROW_EXCEPTIONS;
            $ADODB_THROW_EXCEPTIONS = true;
            try
            {
                $fixValuesStm = 'UPDATE IGNORE ' . $col['TABLE_NAME'] . '
                                SET `' . $col['COLUMN_NAME'] . '` = 0 WHERE `' . $col['COLUMN_NAME'] . '` IS NULL ;';
                $db->Execute($fixValuesStm);

                $alterQuery = 'ALTER TABLE ' . $col['TABLE_NAME'] . '
                                CHANGE COLUMN `' . $col['COLUMN_NAME'] . '` `' . $col['COLUMN_NAME'] . '` TINYINT UNSIGNED NOT NULL DEFAULT ' . $default . ' ;';
                $db->Execute($alterQuery);
            }
            catch(Exception $e)
            {
                $ret->msg = $e->getMessage();
                $ret->success = false;
            }
        }

        return $ret;
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
Wizard::getInstance()->registerScript(new Update62xTo63x());

    