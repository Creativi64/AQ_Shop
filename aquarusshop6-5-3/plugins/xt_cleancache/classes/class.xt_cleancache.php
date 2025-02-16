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

class cleancache {

    const SQL_DELETE_LIMIT = 1000;
	
    protected $type = false;
	/**
	 * 
	 */
	public function __construct(){

	}

    /**
     * @throws Exception
     */
    public function cleanTemplateCached($type = 'all_files'): void
    {
        if($type == 'all') $type = 'all_files';
        $this->cleanup($type);
    }

    /**
     * @param string $type
     * @return void
     * @throws Exception
     */
	public function cleanup($type = 'all_files'): void
    {
        global $db, $xtPlugin;

        $return = false;
        ($plugin_code = $xtPlugin->PluginCode('cleancache.cleanTemplateCached:top')) ? eval($plugin_code) : false;
        if($return)
        {
            return;
        }

        if((int)$type > 0)
        {
            $type_id = (int)$type;
            $type = $db->GetOne("SELECT type FROM " . TABLE_CLEANCACHE . " WHERE id=?", [$type_id]);
            if (!$type)
            {
                ($plugin_code = $xtPlugin->PluginCode('cleancache.cleanTemplateCached:type_false')) ? eval($plugin_code) : false;
                error_log(__CLASS__.':'.__FUNCTION__.": no cache type found for given id [$type_id]");
                throw new Exception("no cache type found for given id [$type]");
            }
        }

		$this->type = $type;
		switch ($type){
            case 'all_files':
                // alle Dateisystem caches
                $types = $db->GetArray("SELECT type FROM " . TABLE_CLEANCACHE . " WHERE `type_class`= 'files' and `type` != 'all_files' ");
                foreach($types as $local_type)
                {
                    $this->cleanup($local_type['type']);
                }
                break;

            // Dateisystem bereinigen
            case 'cache_css':
                $this->cleanCSSCache();
                break;
            case 'cache_js':
                $this->cleanJSCache();
                break;

            case 'dir_templates_c':
                $this->clearDirTemplates_C();
                break;

            case 'dir_cache':
                $this->clearDirCache();
                break;

            case 'dir_templates_c_cache':
                $this->clearDirTemplates_C();
                $this->clearDirCache();
                break;

            case 'cache_category':
                $this->cleanCategoryCache();
                break;
            case 'cache_product':
                $this->cleanProductCache();
                break;
            case 'cache_manufacturers':
                $this->cleanManufacturerCache();
                break;

            case 'adodb_cache':
                $this->cleanAdodbCache();
                break;

            case 'xt_cache':
                $this->cleanXtCache();
                break;
            case 'xt_cache_language':
                $this->cleanXtCache('language_content');
                break;

            case 'plg_hookpoints':
                $this->cleanPluginHookPoints();
                break;
            case 'cache_feed':
                $this->cleanFeedCache();
                break;

            case 'opcache':
                $this->cleanOpCache();
                break;


            case 'all_db':
                $types = $db->GetArray("SELECT type FROM " . TABLE_CLEANCACHE . " WHERE `type_class`= 'db' and `type` != 'all_db' ");
                foreach($types as $local_type)
                {
                    $this->cleanup($local_type['type']);
                }
                break;
                break;

            // Datenbank bereinigen
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
	}

    /**
     *
     */
    public function cleanPluginHookPoints(): void
    {
        global $xtPlugin;

        if(!empty($xtPlugin->getHookDir()))
        {
            self::rrmdir($xtPlugin->getHookDir());
        }
    }

    static function rrmdir($dir): void
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir."/".$object))
                        self::rrmdir($dir."/".$object);
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
    public function cleanXtCache($which = ''): void
    {
        if(class_exists('xt_cache'))
        {
            xt_cache::deleteCache($which);
        }
    }

	/**
	 * 
	 */
	public function clearDirTemplates_C(): void
    {
		$patterns = [
		    _SRV_WEBROOT."templates_c/*.php" ,
            _SRV_WEBROOT."templates_c/wrt*" ,
        ];

        self::unlinkFilesByGlobPattern( $patterns);
	}

    public function clearDirCache(): void
    {
        $patterns = [
            _SRV_WEBROOT."cache/*.php" ,
        ];

        self::unlinkFilesByGlobPattern( $patterns);
    }

    public function cleanOpCache(): void
    {
        if(function_exists('opcache_reset'))
        {
            opcache_reset();
        }
    }
	
	/**
	 * 
	 */
	public function cleanFeedCache(): void
    {
		$dir = _SRV_WEBROOT.'cache/';
		
		$files = array('xt_newpluginfeed.xml','xt_newsfeed.xml','xt_toppluginsfeed.xml');

		foreach ($files as $k=>$v){
			$filename = $dir.$v;
			if (file_exists($filename))     unlink($filename);
		}
	}

    public function cleanCSSCache(): void
    {
        $patterns = [
            _SRV_WEBROOT."cache/*.css",
            _SRV_WEBROOT."cache/*.lesscache"
        ];
        self::unlinkFilesByGlobPattern($patterns);
    }

    public function cleanJSCache(): void
    {
        $patterns = [
            _SRV_WEBROOT."cache/*.js"
        ];
        self::unlinkFilesByGlobPattern($patterns);
    }
	
	/**
	 * 
	 */
	public function cleanCategoryCache(): void
    {
        $patterns = [
            _SRV_WEBROOT."cache/*box_categories_recursive*",
            _SRV_WEBROOT."templates_c/*box_categories_recursive*",
            _SRV_WEBROOT."templates_c/*categorie_listing*"
        ];
        self::unlinkFilesByGlobPattern($patterns);
	}

    public function cleanProductCache(): void
    {
        $patterns = [
            _SRV_WEBROOT . 'cache/__product_*__*product.html.php',
            _SRV_WEBROOT."templates_c/*product*",
        ];
        self::unlinkFilesByGlobPattern($patterns);
    }

    public function cleanManufacturerCache(): void
    {
        $patterns = [
            _SRV_WEBROOT . 'cache/*manufacturer*',
            _SRV_WEBROOT."templates_c/*manufacturer*",
        ];
        self::unlinkFilesByGlobPattern($patterns);
    }



    /**
     *
     */
    public function cleanCleanCacheLog(): void
    {
        global $db;

        $db->Execute("TRUNCATE ".TABLE_CLEANCACHE_LOGS);
    }

    /**
     *
     */
    public function cleanAdodblogSql(): void
    {
        global $db;

        $db->Execute("TRUNCATE adodb_logsql");
    }

    /**
     *
     */
    public function cleanAdodbCache(): void
    {
        global $db;

        $db->cacheFlush();
    }

    /** System Log entries in db table system_logs
     *
     */
    private function cleanSystemLog($message_source): void
    {
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
        $params[] = self::SQL_DELETE_LIMIT;
        $affected_rows = 0;
        for($i=0; $i<1000; $i++) {
            $db->Execute("DELETE FROM " . TABLE_SYSTEM_LOG . " WHERE message_source in ($params_place_holder) LIMIT ?", $params);
            $affected_rows = $db->Affected_Rows();
            if($affected_rows == 0)
                break;
        }
        if($affected_rows)
            echo 'noch Reste nach 1000 x 1000 gelöschten Einträgen. Bitte nochmals starten<br>';

    }

    public function cleanCronLog(): void
    {
        $this->cleanSystemLog(array('cronjob','xt_cron'));
    }
    public function cleanXtExportLog(): void
    {
        $this->cleanSystemLog('xt_export');
    }
    public function cleanEmailLog(): void
    {
        $this->cleanSystemLog('email');
    }
    public function cleanImageProcessingLog(): void
    {
        $this->cleanSystemLog('ImageProcessing');
    }
    public function cleanXtImExportLog(): void
    {
        $this->cleanSystemLog('xt_im_export');
    }

	public function addLogs(): void
    {
		global $db;
		
		$data = array();

		$data['type'] = $this->type;
		$data['change_trigger'] = 'user';
		$data['last_run'] = date('Y-m-d h:i:s');
		
		$db->AutoExecute(TABLE_CLEANCACHE_LOGS,$data,'INSERT');
	}
	
	/**
	 * @param $type string
	 */
	public function updateLastRun($type): void
    {
		global $db;
		
		$db->Execute('UPDATE '.TABLE_CLEANCACHE.' SET last_run = ?  WHERE type = ?', [date ('Y-m-d H:i:s', time()), $type]);
	}

    /**
     * @param $patterns string|array
     * @return void
     */
    static function unlinkFilesByGlobPattern($patterns): void
    {
        if(is_string($patterns))
        {
            $tmp = [$patterns];
            $patterns = $tmp;
        }


        foreach ($patterns as $pattern)
        {
            array_map('unlink', glob($pattern));
        }
    }
}
