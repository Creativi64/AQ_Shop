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

class Update42To5 extends ExecutableScript {

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
        return '4.2';
    }


    public function getTargetShopVersion() {
        return '5.0.8';
    }
    public function getSkippableShopVersion(){
        return '5.0.0';
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



    public function displayFinishInstructionsPage(WizardPage $page)
    {
        $page
            ->setTemplate('templates/info_page.html')
            ->assignTemplateVar('message', TEXT_FINISH_INSTRUCTIONS_508.
                '<br /><p class="alert alert-info">'.TEXT_COPY_VERSION_INFO.'</p>'.
                '<p class="alert">'.TEXT_INFO_PLACE_LICENSE.'</p>')
            ->assignTemplateVar('title', 'INFO')
            ->assignTemplateVar('next', true);
    }


    /**
     * Complete action
     * @param WizardPage $page
     */
    public function displayFinishPage(WizardPage $page) {
        
		$url = Wizard::getInstance()->buildUrl(array(
			Wizard::PARAM_SCRIPT => 'StartPageScript',
            Wizard::PARAM_PAGE => 'update',
        ));
        // Redirect to update page
        Wizard::getInstance()->redirect($url);
    }
    
    protected function getFirstStoreID($idb,$prefix){
        $idb->Execute("SELECT * FROM ". $prefix ."_stores ORDER BY shop_id LIMIT 0,1");
        $idb->fileds['shop_id'];
    }
    
    protected function _getFileContent($filename) {
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

        if(defined('DEV_INSTALL')) { $sql_content = "\r\nSELECT 1;\r\n"; }

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

    function InitialSQL(WizardPage $page){

        $install_prefix = DB_PREFIX.'_';

        $sql_version = $this->db()->GetOne("SELECT VERSION() AS Version");
        if(version_compare($sql_version, '5.5') == -1)
        {
            die('Systemanforderung  mysql 5.5 minimum nicht erf�llt. <a href="https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917573/Systemanforderungen" target="_blank">Handbuch</a>');
        }

        $update_sql = 'update.sql';
        if(version_compare($sql_version, '5.6') == -1)
        {
            // before mysql 5.6 use different sql script
            $update_sql = 'update.55.sql';
        }
        $res = $this->_installSQL(Wizard::getInstance()->getDatabaseObject(), $update_sql, $install_prefix,'');
        
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
			
        global $store_handler,$language;

        $extraSql = false;
        /*
        $disabledPlugins = Wizard::getInstance()->getDatabaseObject()->GetArray("SELECT plugin_id FROM ".$install_prefix."plugin_products WHERE plugin_status=0");
        foreach($disabledPlugins as $plg)
        {
            $extraSql.= PHP_EOL.PHP_EOL.'UPDATE ##_plugin_code SET plugin_status=0 WHERE plugin_id='.$plg['plugin_id'].';'.PHP_EOL;
        }
        */
        $extraSql.= PHP_EOL."DELETE FROM ##_config WHERE config_key = '_SYSTEM_VERSION';".PHP_EOL;
    
        $install_prefix = DB_PREFIX.'_';
        foreach ($store_handler->getStores() as $store){
            $res = $this->_installSQL(Wizard::getInstance()->getDatabaseObject(), 'update_each_store.sql', $install_prefix,'',$store['id'], $extraSql);
            foreach ($res as $key => $val) {
                $this->addControllerCommand(ControllerCommand::factory()
                    ->setAction(function(ControllerCommand $command) use ($val) {
                        $idb = Wizard::getInstance()->getDatabaseObject();
                        try{
                            $idb->Execute($val);
                        }catch (Exception $e){
                            /*'1060 - Duplicate column name','1062 - Duplicate entry', '1050- table alredy exists','1091 -  Can't DROP '%s'; check that column/key exists'*/
                            $allowed_error_codes = array(1062,1061,1060,1050,1091,1054, 1091);
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
        }
            
        $page->assignTemplateVar('processing',TEXT_EXECUTING_DB_UPDATE_SCRIPT,true);
        $this->execAsyncAction($page);
    }

    /**
    * Rebuild categries data
    * @param WizardPage $page
    */
    public function rebuildCategoryTree(WizardPage $page) {
        global $store_handler;

        //$page->setSuccessMessage('ok, im nächsten Schritt wird versucht, die Datei /versioninfo.php zu aktualisieren');

        $install_prefix = DB_PREFIX.'_';
        $colExists = Wizard::getInstance()->getDatabaseObject()->GetOne("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='"._SYSTEM_DATABASE_DATABASE."' AND COLUMN_NAME='categories_level' AND TABLE_NAME='".$install_prefix."categories'");
        if(!$colExists)
        {
            $val[] = "ALTER TABLE `" . $install_prefix . "categories` ADD COLUMN `categories_level` TINYINT UNSIGNED NOT NULL DEFAULT '0';";
            $val[] = "ALTER TABLE `" . $install_prefix . "categories` ADD INDEX `bb_nested_set_performance_categories_left` (`categories_left`);";

            $msg = TEXT_EXECUTING_QUERY . implode('<br/>', $val). "<br/>";

            $this->addControllerCommand(ControllerCommand::factory()
                ->setAction(function (ControllerCommand $command) use ($val)
                {
                    $idb = Wizard::getInstance()->getDatabaseObject();
                    try
                    {
                        $idb->Execute($val[0]);
                        $idb->Execute($val[1]);
                        $filename =  dirname(__FILE__) . DS .'/class.nested_set_update.php';
                        $meth_exists = method_exists('getCategorySQL_query','getCategorySQL_query');
                        if (file_exists($filename) && $meth_exists)
                        {
                            include_once $filename;
                            $tree = new nested_set_update();
                            $tree->buildNestedSet();
                        }
                        else
                        {
                            throw new Exception(__FUNCTION__." in ".__FILE__." at ".__LINE__);
                        }


                    } catch (Exception $e)
                    {
                        /*'1060 - Duplicate column name','1062 - Duplicate entry', '1050- table alredy exists','1091 -  Can't DROP '%s'; check that column/key exists'*/
                        $allowed_error_codes = array(1062, 1061, 1060, 1050, 1091, 1054, 1091);
                        if (in_array($e->getCode(), $allowed_error_codes))
                        {
                            $command->setErrorMessage("<span class='msg-skipped'>" . TEXT_QUERY_SKIPPED . "  " . $e->getMessage() . "</span><br/>");
                        }
                        else
                        {
                            $command->setErrorMessage("<span class='msg-error'>" . TEXT_QUERY_FAILED . " " . $e->getMessage() . "</span><br/>");
                            return false;
                        }
                    }

                    return true;
                })
                ->setDescription($msg)
                ->setAbortOnError(true)
            );
        }
        else
        {
            $val[] = "ALTER TABLE `" . $install_prefix . "categories` ADD INDEX `bb_nested_set_performance_categories_left` (`categories_left`);";

            $msg = TEXT_EXECUTING_QUERY . implode('<br/>', $val). "<br/>";

            $this->addControllerCommand(ControllerCommand::factory()
                ->setAction(function (ControllerCommand $command) use ($val)
                {
                    $idb = Wizard::getInstance()->getDatabaseObject();
                    try
                    {
                        $idb->Execute($val[0]);
                        $filename =  dirname(__FILE__) . DS .'/class.nested_set_update.php';
                        $meth_exists = method_exists('getCategorySQL_query','getCategorySQL_query');
                        if (file_exists($filename) && $meth_exists)
                        {
                            include_once $filename;
                            $tree = new nested_set_update();
                            $tree->buildNestedSet();
                        }
                        else
                        {
                            throw new Exception(__FUNCTION__." in ".__FILE__." at ".__LINE__);
                        }

                    } catch (Exception $e)
                    {
                        /*'1060 - Duplicate column name','1062 - Duplicate entry', '1050- table alredy exists','1091 -  Can't DROP '%s'; check that column/key exists'*/
                        $allowed_error_codes = array(1062, 1061, 1060, 1050, 1091, 1054, 1091);
                        if (in_array($e->getCode(), $allowed_error_codes))
                        {
                            $command->setErrorMessage("<span class='msg-skipped'>" . TEXT_QUERY_SKIPPED . "  " . $e->getMessage() . "</span><br/>");
                        }
                        else
                        {
                            $command->setErrorMessage("<span class='msg-error'>" . TEXT_QUERY_FAILED . " " . $e->getMessage() . "</span><br/>");
                            return false;
                        }
                    }
                    try
                    {
                        $filename =  dirname(__FILE__) . DS .'/class.nested_set_update.php';
                        $meth_exists = method_exists('getCategorySQL_query','getCategorySQL_query');
                        if (file_exists($filename) && $meth_exists)
                        {
                            include_once $filename;
                            $tree = new nested_set_update();
                            $tree->buildNestedSet();
                        }
                        else
                        {
                            throw new Exception(__FUNCTION__." in ".__FILE__." at ".__LINE__);
                        }

                    } catch (Exception $e)
                    {
                        $command->setErrorMessage("<span class='msg-skipped'>" . TEXT_QUERY_SKIPPED . "  " . $e->getMessage() . "</span><br/>");
                    }

                    return true;
                })
                ->setDescription($msg)
                ->setAbortOnError(true)
            );
        }

        $page->assignTemplateVar('processing',TEXT_REBUILDING_CATEGORIES,true);
        $this->execAsyncAction($page);
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
Wizard::getInstance()->registerScript(new Update42To5());

    