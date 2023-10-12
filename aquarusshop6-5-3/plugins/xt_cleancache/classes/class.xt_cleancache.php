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

if (!defined("TABLE_CLEANCACHE"))
    define('TABLE_CLEANCACHE', DB_PREFIX.'_clean_cache');
if (!defined("TABLE_CLEANCACHE_LOGS"))
    define('TABLE_CLEANCACHE_LOGS', DB_PREFIX.'_clean_cache_logs');

class cleancache{
	
    protected $type = false;
	/**
	 * 
	 */
	public function __construct(){

	}
	
	/**
	 * @param  $type
	 */
	public function cleanTemplateCached($type = 'all')
    {
        global $db, $xtPlugin;

        ($plugin_code = $xtPlugin->PluginCode('cleancache.cleanTemplateCached:top')) ? eval($plugin_code) : false;

        if((int)$type > 0)
        {
            $type = $db->GetOne("SELECT type FROM " . TABLE_CLEANCACHE . " WHERE id=?", array((int)$type));
            if ($type == false)
            {
                $ret = true;
                ($plugin_code = $xtPlugin->PluginCode('cleancache.cleanTemplateCached:type_false')) ? eval($plugin_code) : false;
                if ($ret) return " - no cache type found for given id [$type] -";
            }
        }

		$this->type = $type;
		switch ($type){
			case 'all':
				$this->cleanCategoryCache();
				$this->cleanFeedCache();
                $this->cleanCSSCache();
                $this->cleanJSCache();
                $this->cleanSEOCache();
                $this->clearTemplatesCache();
				break;
			case 'cache_feed':
				$this->cleanFeedCache();
				break;
			case 'cache_category':
				$this->cleanCategoryCache();
				break;
            case 'cache_css':
                $this->cleanCSSCache();
                break;
            case 'cache_js':
                $this->cleanJSCache();
                break;
            case 'templates_c':
            	$this->clearTemplatesCache();
            	break;
            case 'cache_seo':
                $this->cleanSEOCache();
                break;
            case 'adodb_cache':
                $this->cleanAdodbCache();
                break;
            case 'adodb_logsql':
                $this->cleanAdodblogSql();
                break;
            case 'system_log_cronjob':
                $this->cleanCronLog();
                break;
            case 'system_log_xt_export':
                $this->cleanXtExportLog();
                break;
            case 'system_log_xt_im_export':
                $this->cleanXtImExportLog();
                break;
            case 'system_log_email':
                $this->cleanEmailLog();
                break;
            case 'system_log_ImageProcessing':
                $this->cleanImageProcessingLog();
                break;
            case 'xt_cache':
                $this->cleanXtCache();
                break;
            case 'plg_hookpoints':
                $this->cleanPluginHookPoints();
                break;
            case 'clean_cache_logs':
                $this->cleanCleanCacheLog();
                break;
            default:
                ($plugin_code = $xtPlugin->PluginCode('cleancache.cleanTemplateCached:switch_type')) ? eval($plugin_code) : false;
        }

        ($plugin_code = $xtPlugin->PluginCode('cleancache.cleanTemplateCached:after_switch_type')) ? eval($plugin_code) : false;

		$this->addLogs();
		$this->updateLastRun($type);

        ($plugin_code = $xtPlugin->PluginCode('cleancache.cleanTemplateCached:bottoom')) ? eval($plugin_code) : false;
        return true;
	}

    /**
     *
     */
    public function cleanPluginHookPoints()
    {
        global $xtPlugin;

        if(!empty($xtPlugin->getHookDir()))
        {
            $this->rrmdir($xtPlugin->getHookDir());
        }
    }

    private function rrmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir."/".$object))
                        $this->rrmdir($dir."/".$object);
                    else
                        unlink($dir."/".$object);
                }
            }
            rmdir($dir);
        }
    }

	/**
	 *
	 */
    public function cleanXtCache()
    {
        if(class_exists('xt_cache'))
        {
            xt_cache::deleteCache();
        }
    }

	/**
	 * 
	 */
	public function clearTemplatesCache()
    {
		$patterns = [
		    _SRV_WEBROOT."templates_c/*.php" ,
            _SRV_WEBROOT."templates_c/wrt*" ,
            _SRV_WEBROOT."cache/*.php" ,
        ];

		foreach ($patterns as $pattern)
		{
            array_map('unlink', glob($pattern));
		}
	}
	
	/**
	 * 
	 */
	public function cleanFeedCache(){
		$dir = _SRV_WEBROOT.'cache/';
		
		$files = array('xt_newpluginfeed.xml','xt_newsfeed.xml','xt_toppluginsfeed.xml');

		foreach ($files as $k=>$v){
			$filename = $dir.$v;
			if (file_exists($filename))     unlink($filename);
		}	

	}

    public function cleanCSSCache()
    {
        $patterns = [
            _SRV_WEBROOT."cache/*.css",
            _SRV_WEBROOT."cache/*.lesscache"
        ];

        foreach ($patterns as $pattern)
        {
            array_map('unlink', glob($pattern));
        }
    }

    public function cleanJSCache()
    {
        $patterns = [
            _SRV_WEBROOT."cache/*.js"
        ];

        foreach ($patterns as $pattern)
        {
            array_map('unlink', glob($pattern));
        }
    }

    public function cleanSEOCache() {

        $dir = _SRV_WEBROOT.'cache/';
        $files = $this->getSEOFiles();

        if (is_array($files)) {
            foreach ($files as $key => $val) {
                if (file_exists($dir.$val)) {
                    unlink($dir.$val);
                }
            }
        }
    }
	
	/**
	 * 
	 */
	public function cleanCategoryCache(){

		$dir = _SRV_WEBROOT.'cache/';
		$files = $this->getSmartyCacheFiles();
		foreach ($files as $k=>$v){
			$filename = $dir.$v;
			unlink($filename);
		}
		
	}

    /**
     *
     */
    public function cleanCleanCacheLog(){
        global $db;

        $db->Execute("TRUNCATE ".TABLE_CLEANCACHE_LOGS);
    }

    /**
     *
     */
    public function cleanAdodblogSql()
    {
        global $db;

        $db->Execute("TRUNCATE adodb_logsql");
    }

    /**
     *
     */
    public function cleanAdodbCache(){
        global $db;

        $db->cacheFlush();
    }

    /** System Log entries in db table system_logs
     *
     */
    private function cleanSystemLog($message_source){
        global $db;
        if(empty($message_source)) return;

        if(is_array($message_source))
        {
            $ar = array();
            foreach($message_source as $a){
                $ar[] = '?';
        }
            $params = $message_source;
            $params_place_holder = implode(',', $ar);
    }
        else {
            $params_place_holder = '?';
            $params = array($message_source);
        }
        $db->Execute("DELETE FROM ".TABLE_SYSTEM_LOG. " WHERE message_source in ($params_place_holder)", $params);
    }

    public function cleanCronLog()
    {
        $this->cleanSystemLog(array('cronjob','xt_cron'));
    }
    public function cleanXtExportLog()
    {
        $this->cleanSystemLog('xt_export');
    }
    public function cleanEmailLog()
    {
        $this->cleanSystemLog('email');
    }
    public function cleanImageProcessingLog()
    {
        $this->cleanSystemLog('ImageProcessing');
    }
    public function cleanXtImExportLog()
    {
        $this->cleanSystemLog('xt_im_export');
    }

	/**
	 * 
	 */
	public function addLogs(){
		global $db;
		
		$data = array();

		$data['type'] = $this->type;
		$data['change_trigger'] = 'user';
		$data['last_run'] = date('Y-m-d h:i:s');
		
		$db->AutoExecute(TABLE_CLEANCACHE_LOGS,$data,'INSERT');
	}
	
	/**
	 * @param unknown_type $type
	 */
	public function updateLastRun($type){
		global $db;
		
		$db->Execute('UPDATE '.TABLE_CLEANCACHE.' SET last_run = '.$db->DBTimeStamp(time()).' WHERE type = "'.$type. '"');
	}
	
	private function getSmartyCacheFiles() {
		$FilesArray = array();
		$hDir = _SRV_WEBROOT.'cache/';
		if ($handle = opendir($hDir)) {
			while (false !== ($file = readdir($handle))) {
				if (strpos($file,'categorie_listing.html') !== false) {
					$FilesArray[] =  $file;
				}
			}
			closedir($handle);
		}
		return $FilesArray;
		
	}

    /**
     *
     */
    private function getCSSFiles(){
        $hDir = _SRV_WEBROOT.'cache/';
        $FilesArray = array();
        if ($handle = opendir($hDir)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && strpos($file,'.css') !== false) {
                    $FilesArray[] =  $file;
                }
            }
            closedir($handle);
        }
        return $FilesArray;
    }
    
    /**
     * 
     * @return array
     */
    private function getTemplateCacheFiles($hDir) {


        $FilesArray = array();

        if(empty($hDir)) return $FilesArray;

    	if ($handle = opendir($hDir)) {
    		while (false !== ($file = readdir($handle))) {
    			if ($file != "." && $file != ".." && strpos($file,'.php') !== false) {
    				$FilesArray[] =  $file;
    			}
    		}
    		closedir($handle);
    	}
    	return $FilesArray;
    }

    /**
     *
     * @return array
     */
    private function getLessCacheFiles($hDir, $ext = 'lesscache') {


        $FilesArray = array();

        if(empty($hDir)) return $FilesArray;

        if ($handle = opendir($hDir)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && strpos($file,'.'.$ext) !== false) {
                    $FilesArray[] =  $file;
                }
            }
            closedir($handle);
        }
        return $FilesArray;
    }

    /**
     *
     */
    private function getJsFiles(){
        $hDir = _SRV_WEBROOT.'cache/';
        $FilesArray = array();
        if ($handle = opendir($hDir)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && strpos($file,'.js') !== false) {
                    $FilesArray[] =  $file;
                }
            }
            closedir($handle);
        }
        return $FilesArray;
    }

    private function getSEOFiles(){
        $hDir = _SRV_WEBROOT.'cache/';
        $FilesArray = array();
        if ($handle = opendir($hDir)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && strpos($file,'seo_optimization') !== false) {
                    $FilesArray[] =  $file;
                }
            }
            closedir($handle);
        }
        return $FilesArray;
    }
}
