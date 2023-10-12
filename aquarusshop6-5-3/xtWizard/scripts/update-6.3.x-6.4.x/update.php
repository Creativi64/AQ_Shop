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


class Update63xTo64x extends ExecutableScript {

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
        return '6.3.0';
    }


    public function getTargetShopVersion() {
        return '6.4.0';
    }
    public function getSkippableShopVersion(){
        return '6.4.0';
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

        if(!isset($_REQUEST["ajaxLoad"]))
        {
            $_SESSION['type_idx_exists'] = $idb->GetOne("SELECT index_name FROM information_schema.statistics WHERE table_schema = '"._SYSTEM_DATABASE_DATABASE."' AND table_name = '".DB_PREFIX."_sales_stats' and column_name = 'sales_stat_type'");
        }

        $update_idx_sql = 'sales_stats.sql';

        if (empty($_SESSION['type_idx_exists']))
        {
            $res = array_merge($res, $this->_installSQL(Wizard::getInstance()->getDatabaseObject(), $update_idx_sql, $install_prefix,'', ''));
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



        // _STORE_CPS_HEADER
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($idb, $store_handler) {
                try{
                    $group_id = $idb->GetOne("SELECT group_id FROM ".TABLE_CONFIGURATION_GROUP." WHERE group_title = 'TEXT_CONF_SECURITY'");
                    if(!$group_id)
                    {
                        $idb->Execute("INSERT INTO ".TABLE_CONFIGURATION_GROUP." (group_title,sort_order,visible,url_h ) VALUES ('TEXT_CONF_SECURITY', 35, 1, 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/2703884289/Sicherheit')");
                        $group_id = $idb->Insert_ID();
                    }
                    $stores = $store_handler->getStores();
                    foreach ($stores as $store)
                    {
                        $table = DB_PREFIX.'_config_'.$store['id'];
                        $exists = $idb->GetOne("SELECT 1 FROM ".$table." WHERE config_key = '_STORE_CSP_HEADER'");
                        if(!$exists) $idb->Execute("INSERT INTO `".$table."` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`) VALUES ('_STORE_CSP_HEADER', '# wird zZ noch nicht verwendet / not yet used', ".$group_id.", '20', 'textarea')");
                    }
                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-error'>".TEXT_QUERY_FAILED." " . $e->getMessage() . "</span><br/>");
                    return false;
                }

                return true;
            })
            ->setDescription(TEXT_UPDATING . " DB _config_x add  _STORE_CSP_HEADER<br/>")
            ->setAbortOnError(true)
        );

        $page->assignTemplateVar('processing',TEXT_UPDATING,true);
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
Wizard::getInstance()->registerScript(new Update63xTo64x());

    