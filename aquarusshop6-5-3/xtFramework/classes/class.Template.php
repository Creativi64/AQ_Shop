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

include_once (_SRV_WEBROOT._SRV_WEB_FRAMEWORK.'library/smarty/xt_plugins/smarty_internal_compile_include_xt.php');

class Template {
	var $template_path;
	var $tpl_path;
	var $tpl_short_path;
	var $tpl_root_path;
	var $system_template = _SYSTEM_TEMPLATE;
	var $selected_template = _STORE_TEMPLATE;
	var $abs_tpl_path;
	var $rel_tpl_path;
	var $abs_path;
	var $rel_path;
	var $CacheIDParams = array();
    var $check_int;
    /**
     * @var Smarty
     */
    var $content_smarty;

    function _setTemplate($tpl_name=''){
		global $customers_status;

		if(!empty($tpl_name))
		$this->selected_template = $tpl_name;
	}

	function setTplShortPath(){
		$path = str_replace(_SRV_WEB._SRV_WEB_TEMPLATES, '', $this->tpl_path);
		$this->tpl_short_path = $path;
	}

	function setTplPath($path){
		$this->tpl_path = $this->cleanPath($path);
	}

	function setTplRootPath($path){
		$this->tpl_root_path = $this->cleanPath($path);
	}

    function getTemplate ($global_smarty,$template,$assignArray,$assign_to = false, $use_cache = false) {
        global $current_category_id, $xtPlugin, $language,$page,$currency, $_cache_id_settings, $tpl_replace_paths;
        global $is_cronjob_processing, $is_pro_version;

        $file_name_suffix = false;
        $file_name_prefix = false;

        $tpl_replace_paths = array('html' => array());

        ($plugin_code = $xtPlugin->PluginCode('class.template:getTemplate_top')) ? eval($plugin_code) : false;

        if(!$is_pro_version)
        {
            include _SRV_WEBROOT._SRV_WEB_CORE.'tpl_replaces_pathes.php';
            if(in_array($template, $tpl_replace_paths['html']))
            {
                $file_name_prefix = 'xtFree/';
            }
        }

        if($file_name_prefix)
        {
            $template = str_replace('//','/',$file_name_prefix.$template);
        }
        if($file_name_suffix)
        {
            $pi = pathinfo($template);
            $add_sep = $pi['dirname'] != DIRECTORY_SEPARATOR ? DIRECTORY_SEPARATOR : '';
            $template = $pi['dirname'].$add_sep.$pi["filename"].$file_name_suffix.'.'.$pi["extension"];
        }

		try
        {
			if ($assign_to!==false)
            {
			    global ${$global_smarty};
            }

            $cache_id = '';

            if (!is_object($this->content_smarty) || USER_POSITION == 'admin' || isset($is_cronjob_processing))
            {
                $this->content_smarty = new Smarty();
                $this->content_smarty->setCaching(Smarty::CACHING_OFF);
            }
            else if((SMARTY_USE_CACHE == 'true' && $use_cache !== false) || $use_cache === true)
            {
                $cache_id = $this->getTemplateCacheID($template);
                $this->content_smarty->setCaching(Smarty::CACHING_LIFETIME_SAVED);
                $this->content_smarty->setCacheLifetime(SMARTY_CACHE_LIFETIME);
            }
            else {
                $this->content_smarty->setCaching(Smarty::CACHING_OFF);
            }

            $this->content_smarty->setErrorReporting(E_ALL & ~E_NOTICE);

            if(SMARTY_USE_COMPILE_CHECK === false)
                    $this->content_smarty->setCompileCheck(Smarty::COMPILECHECK_OFF);

			if($this->check_int=='chkint' || !$this->check_int)
            {
			    $this->getTemplatePath($template, '', '', 'default', 'chkint');
            }


			$tmp_tpl_path = $this->tpl_path;
			$tmp_tpl_root_path = $this->tpl_root_path;

			$content_smarty = $this->content_smarty;
            $content_smarty->compile_dir = _SRV_WEBROOT.'templates_c';

            $template_dirs = array( 'store' => './'._SRV_WEB_TEMPLATES._STORE_TEMPLATE.'/', 'system' => './'._SRV_WEB_TEMPLATES._SYSTEM_TEMPLATE.'/');
            foreach($template_dirs as $k => $tpl_dir)
            {
                if(!array_key_exists($k, $this->content_smarty->getTemplateDir()))
                {
                    $this->content_smarty->addTemplateDir($tpl_dir, $k);
                }
            }

			$content_smarty->addPluginsDir(array(_SRV_WEBROOT.'xtFramework/library/smarty/xt_plugins'));

			$content_smarty->assign('language', $language->code);
			$content_smarty->assign('tpl_path', $tmp_tpl_path);
            if (isset($page->page_name))
            {
                $content_smarty->assign('page', $page->page_name);
            }
            if (_STORE_IMAGES_PATH_FULL == 'true')
            {
                $path_base_url = _SYSTEM_BASE_URL;
            }
            else
            {
                $path_base_url = '';
            }
			$content_smarty->assign('tpl_url_path', $path_base_url._SRV_WEB._SRV_WEB_TEMPLATES.$this->selected_template.'/');
		    $content_smarty->assign('tpl_path', _SRV_WEBROOT._SRV_WEB_TEMPLATES.$this->selected_template.'/');
		    $content_smarty->assign('tpl_url_path_system', $path_base_url._SRV_WEB._SRV_WEB_TEMPLATES.$this->system_template.'/');
		    $content_smarty->assign('tpl_path_system', _SRV_WEBROOT._SRV_WEB_TEMPLATES.$this->system_template.'/');
			$content_smarty->assign('selected_template', $this->selected_template);
            $content_smarty->assign('is_pro_version', $is_pro_version);
            $content_smarty->assignGlobal('activeModules', array_keys($xtPlugin->active_modules));

            ($plugin_code = $xtPlugin->PluginCode('class.template:getTemplate_assign_default')) ? eval($plugin_code) : false;

            // mobile assigns
            if (isset($_SESSION['isMobile']) or isset($_SESSION['isTablet']))
            {
                $content_smarty->assign('language_count', count($language->_getLanguageList()));
                $content_smarty->assign('currency_count', count($currency->_getCurrencyList()));
            }

            if (is_array($assignArray))
            {
                foreach ($assignArray as $assign_key => $assign_val)
                {
                    if (!empty($assign_key) /* && (!empty($assign_val) || $assign_val===false)*/)
                    {
                        $content_smarty->assign($assign_key, $assign_val);
                    }
                }
            }

            if ($template == '/index.html' || $template == 'xtFree/index.html')
            {
                ($plugin_code = $xtPlugin->PluginCode('class.template:getTemplate_load_smartyfilter')) ? eval($plugin_code) : false;
                $this->registerOutputFilter();
                $content_smarty->loadFilter('output', 'note');
            }

            $template = $tmp_tpl_root_path.$template;
            $template = $this->cleanPath($template);

            if (!empty($cache_id))
            {
                $module = $content_smarty->fetch($template, $cache_id);
            }
            else
            {
                $module = $content_smarty->fetch($template);
            }

            if(USER_POSITION == 'store' && defined('_SYSTEM_HTML_MINIFY_OPTION') && constant('_SYSTEM_HTML_MINIFY_OPTION')==1)
            {
                $module = Minify_HTML::minify($module, array('xhtml'=>false));
            }

            if ($assign_to)
            {
                ${$global_smarty}->assign($assign_to, $module);
            }
            else
            {
                return $module;
            }
        }
        catch(Exception $e)
        {
            return 'Smarty-Exception: '.$e->getMessage();
        }
	}

	function getDefaultTemplate ($default_template = '',$template_path = '') {

		if ($default_template == '' or $default_template == 'default') {
			$files = array ();
			if ($template_path == 'product_listing/')
				return _STORE_TEMPLATE_PRODUCT_LISTING;

			if ($template_path == 'categorie_listing/')
				return _STORE_TEMPLATE_CATEGORY_LISTING;

		} else {
			return $default_template;
		}
	}
	
	function setAdditionalCacheID($id) {
		$this->CacheIDParams[]=$id;
	}

	function getTemplateCacheID ($template) {
		global $current_category_id,$category, $store_handler,$customers_status,$currency,$language,$_cache_id_settings,$xtPlugin;
		$param = @array_merge($_GET,$_POST);
        $cat_tpl = isset($category->current_category_data['categories_template']) ? $category->current_category_data['categories_template'] :'';
        if ($template == '/module/categorie_listing/'.$cat_tpl)
			unset($param['page']);

		unset($param['g-recaptcha-response']);
        unset($param['customer_message']);
		
		//$cache_id_params = str_replace('/','_',$template).'_'.$current_category_id.implode('_',$param).'_'.$language->languages_id.'_'.$customers_status->customers_status_id.'_'.$currency->code.'_'.$store_handler->shop_id;
		$cache_id_params = $current_category_id.implode('_',$param).'_'.$language->languages_id.'_'.$customers_status->customers_status_id.'_'.$currency->code.'_'.$store_handler->shop_id;


		// individual cache ID settings
		if (isset($_cache_id_settings[$template]) && CACHE_ID_OVERRIDE == 'true') {
		
			$cache_setting = $_cache_id_settings[$template];			
			$cache_id_params = array();

			if (in_array('category',$cache_setting)) {
				$cache_id_params[]='cat';
				$cache_id_params[]='c_'.$current_category_id;
			}
			
			if (in_array('language',$cache_setting)) $cache_id_params[]='l_'.$language->languages_id;
			if (in_array('currency',$cache_setting)) $cache_id_params[]=$currency->code;
			if (in_array('shop',$cache_setting)) $cache_id_params[]='s_'.$store_handler->shop_id;
			if (in_array('customer_group',$cache_setting)) $cache_id_params[]='g_'.$customers_status->customers_status_id;
			if (in_array('sorting',$cache_setting)) {
				if (key_exists('sorting', $param)) {
				    $list = new products_list_no_loader();
				    if ($list->isSortDropdownDefault($list->getSortDropdown(), $param['sorting'])) {
                        $cache_id_params[]=$param['sorting'];
                    }

                }
			}
			if (in_array('listing_page',$cache_setting)) {
				if (key_exists('next_page', $param)) $cache_id_params[]='page_'.(int)$param['next_page'];
			}
			
			($plugin_code = $xtPlugin->PluginCode('class.template:getTemplateCacheID_cache_id_settings')) ? eval($plugin_code) : false;
			
			if (count($this->CacheIDParams) >0) {
				$cache_id_params = array_merge($cache_id_params,$this->CacheIDParams);
			}
			
			
			$cache_id_params = implode('|', $cache_id_params);
			
			return $cache_id_params;
			
		}
		

				
		if (count($this->CacheIDParams) >0) {
			$cache_id_params.='_'.implode('_',$this->CacheIDParams);
		}

		//echo $template.' Cache ID:' .$cache_id_params.'<br>';
		
		return $cache_id_params;
	}

    function isTemplateCache ($templateFile) {
		$this->content_smarty = new Smarty();
		
		$this->content_smarty->caching = true;
        $this->content_smarty->setCaching(Smarty::CACHING_LIFETIME_SAVED);
        $this->content_smarty->setCacheLifetime(SMARTY_CACHE_LIFETIME);
        $this->content_smarty->cache_modified_check = SMARTY_CACHE_CHECK;

        $template_dirs = array( 'store' => './'._SRV_WEB_TEMPLATES._STORE_TEMPLATE.'/', 'system' => './'._SRV_WEB_TEMPLATES._SYSTEM_TEMPLATE.'/');
        foreach($template_dirs as $k => $tpl_dir)
        {
            if(!array_key_exists($k, $this->content_smarty->getTemplateDir()))
            {
                $this->content_smarty->addTemplateDir($tpl_dir, $k);
		    }
        }
        $cacheID = $this->getTemplateCacheID($templateFile);
   
        if (is_file(_SRV_WEBROOT._SRV_WEB_TEMPLATES.$this->selected_template.$templateFile))
        {
            $tpl_dir = _SRV_WEBROOT._SRV_WEB_TEMPLATES.$this->selected_template;
        }
        elseif (is_file(_SRV_WEBROOT._SRV_WEB_TEMPLATES.$this->system_template.$templateFile))
        {
            $tpl_dir = _SRV_WEBROOT._SRV_WEB_TEMPLATES.$this->system_template;
        }
        elseif (is_file($this->tpl_root_path . $templateFile))
        {
            $tpl_dir = $this->tpl_root_path;
        }
        else {
            return false;
        }

        if ($this->content_smarty->isCached($tpl_dir.$templateFile, $cacheID))
        {
			return true;
        }
        else {
			return false;
		}
	}

	function getCachedTemplate ($template) {
		// set cache ID
		$cache_id = $this->getTemplateCacheID($template);
		$this->content_smarty->assign('language', $_SESSION['language']);

        if (is_file(_SRV_WEBROOT._SRV_WEB_TEMPLATES.$this->selected_template.$template))
        {
            $tpl_dir = _SRV_WEBROOT._SRV_WEB_TEMPLATES.$this->selected_template;
        }
        elseif (is_file(_SRV_WEBROOT._SRV_WEB_TEMPLATES.$this->system_template.$template))
        {
            $tpl_dir = _SRV_WEBROOT._SRV_WEB_TEMPLATES.$this->system_template;
        }
        elseif (is_file($this->tpl_root_path.$template))
        {
            $tpl_dir = $this->tpl_root_path;
        }
        else {
            return $template.' not found in '.$this->system_template . ' and '. $this->selected_template;
        }

        return $this->content_smarty->fetch($tpl_dir.$template, $cache_id);
	}

	function getTemplatePath($file, $dir='', $subdir='', $type='default', $check_int = 'checkext'){
		global $xtPlugin;

		if($check_int=='checkint')
		$this->check_int = $check_int;
		
		if($subdir)
		$subdir = $subdir.'/';

        $tpl_check = $this->checkPathHirarchie ($file, $dir, $subdir, $type, "selected");

        if($tpl_check == false){
            $tpl_check = $this->checkPathHirarchie ($file, $dir, $subdir, $type, "system");
        }

		$this->setTplShortPath();
	}

	function checkPathHirarchie ($file, $dir='', $subdir='', $type='default', $tpl = "selected") {
		global $xtPlugin;

		if($tpl=="selected"){
			$tpl = $this->selected_template.'/';
		}elseif($tpl=="system"){
			$tpl = $this->system_template.'/';
		}

		if($type=='default'){
	        if (file_exists($this->cleanPath(_SRV_WEBROOT._SRV_WEB_TEMPLATES.$tpl.$dir.'/'.$subdir.$file))) {
				$this->setTplPath(_SRV_WEB._SRV_WEB_TEMPLATES.$tpl.$dir.'/'.$subdir);
				$this->setTplRootPath(_SRV_WEBROOT._SRV_WEB_TEMPLATES.$tpl.$dir.'/'.$subdir);
	            return true;
	        }

            if (file_exists($this->cleanPath(_SRV_WEBROOT._SRV_WEB_TEMPLATES._STORE_TEMPLATE.$dir.'/'.$subdir.$file))) {
                $this->setTplPath(_SRV_WEB._SRV_WEB_TEMPLATES._STORE_TEMPLATE.$dir.'/'.$subdir);
                $this->setTplRootPath(_SRV_WEBROOT._SRV_WEB_TEMPLATES._STORE_TEMPLATE.$dir.'/'.$subdir);
                return true;
            }

            if (file_exists($this->cleanPath(_SRV_WEBROOT._SRV_WEB_TEMPLATES._SYSTEM_TEMPLATE.$dir.'/'.$subdir.$file))) {
                $this->setTplPath(_SRV_WEB._SRV_WEB_TEMPLATES._SYSTEM_TEMPLATE.$dir.'/'.$subdir);
                $this->setTplRootPath(_SRV_WEBROOT._SRV_WEB_TEMPLATES._SYSTEM_TEMPLATE.$dir.'/'.$subdir);
                return true;
            }
		}


        if($type=='plugin'){
            /**
             *  reihenfolge suche
             *  - templates/selected-template/plugins/xyz/
             *  - templates/system-template/plugins/xyz/
             *  - plugins/xyz/templates
             *
             */
            if (file_exists($this->cleanPath(_SRV_WEBROOT._SRV_WEB_TEMPLATES.$this->selected_template.'/'._SRV_WEB_PLUGINS.$dir.'/'.$subdir.$file))) {
                $this->setTplPath(_SRV_WEB._SRV_WEB_TEMPLATES.$this->selected_template.'/'._SRV_WEB_PLUGINS.$dir.'/'.$subdir);
                $this->setTplRootPath(_SRV_WEBROOT._SRV_WEB_TEMPLATES.$this->selected_template.'/'._SRV_WEB_PLUGINS.$dir.'/'.$subdir);
                return true;
            }
            else if (file_exists($this->cleanPath(_SRV_WEBROOT._SRV_WEB_TEMPLATES.$this->system_template.'/'._SRV_WEB_PLUGINS.$dir.'/'.$subdir.$file))) {
                $this->setTplPath(_SRV_WEB._SRV_WEB_TEMPLATES.$this->system_template.'/'._SRV_WEB_PLUGINS.$dir.'/'.$subdir);
                $this->setTplRootPath(_SRV_WEBROOT._SRV_WEB_TEMPLATES.$this->system_template.'/'._SRV_WEB_PLUGINS.$dir.'/'.$subdir);
                return true;
            }
            else if (file_exists($this->cleanPath(_SRV_WEBROOT._SRV_WEB_PLUGINS.$dir.'/'._SRV_WEB_TEMPLATES.$subdir.$file))) {
                $this->setTplPath(_SRV_WEB._SRV_WEB_PLUGINS.$dir.'/'._SRV_WEB_TEMPLATES.$subdir);
                $this->setTplRootPath(_SRV_WEBROOT._SRV_WEB_PLUGINS.$dir.'/'._SRV_WEB_TEMPLATES.$subdir);
	            return true;
	        }
        }

		if($type=='shipping'){
			$tpl_check = $this->checkPathHirarchie ($file, $dir, $subdir, 'plugin', $tpl);

			if($tpl_check == false)
			$tpl_check = $this->checkPathHirarchie ($file, $dir, $subdir, 'shipping_default', $tpl);
			
			return true;
		}

		if($type=='shipping_default'){
			if (file_exists($this->cleanPath(_SRV_WEBROOT._SRV_WEB_TEMPLATES.$tpl.'/'._SRV_WEB_CORE.'pages/shipping/'.$file))) {
				$this->setTplPath(_SRV_WEB._SRV_WEB_TEMPLATES.$tpl.'/'._SRV_WEB_CORE.'pages/shipping/');
				$this->setTplRootPath(_SRV_WEBROOT._SRV_WEB_TEMPLATES.$tpl.'/'._SRV_WEB_CORE.'pages/shipping/');
	            return true;
	        }

            if (file_exists($this->cleanPath(_SRV_WEBROOT._SRV_WEB_TEMPLATES._SYSTEM_TEMPLATE.'/'._SRV_WEB_CORE.'pages/shipping/'.$file))) {
                $this->setTplPath(_SRV_WEB._SRV_WEB_TEMPLATES._SYSTEM_TEMPLATE.'/'._SRV_WEB_CORE.'pages/shipping/');
                $this->setTplRootPath(_SRV_WEBROOT._SRV_WEB_TEMPLATES._SYSTEM_TEMPLATE.'/'._SRV_WEB_CORE.'pages/shipping/');
                return true;
            }
		}

		if($type=='payment'){

			$tpl_check = $this->checkPathHirarchie ($file, $dir, $subdir, 'plugin', $tpl);

			if($tpl_check == false)
			$tpl_check = $this->checkPathHirarchie ($file, $dir, $subdir, 'payment_default', $tpl);
			
			return true;
		}

		if($type=='payment_default'){
			if (file_exists($this->cleanPath(_SRV_WEBROOT._SRV_WEB_TEMPLATES.$tpl.'/'._SRV_WEB_CORE.'pages/payment/'.$file))) {
				$this->setTplPath(_SRV_WEB._SRV_WEB_TEMPLATES.$tpl.'/'._SRV_WEB_CORE.'pages/payment/');
				$this->setTplRootPath(_SRV_WEBROOT._SRV_WEB_TEMPLATES.$tpl.'/'._SRV_WEB_CORE.'pages/payment/');
	            return true;
	        }
            if (file_exists($this->cleanPath(_SRV_WEBROOT._SRV_WEB_TEMPLATES._SYSTEM_TEMPLATE.'/'._SRV_WEB_CORE.'pages/payment/'.$file))) {
                $this->setTplPath(_SRV_WEB._SRV_WEB_TEMPLATES._SYSTEM_TEMPLATE.'/'._SRV_WEB_CORE.'pages/payment/');
                $this->setTplRootPath(_SRV_WEBROOT._SRV_WEB_TEMPLATES._SYSTEM_TEMPLATE.'/'._SRV_WEB_CORE.'pages/payment/');
                return true;
            }
		}

		($plugin_code = $xtPlugin->PluginCode('class.template:checkPathHirarchie_bottom')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

	    return false;
	}

	function cleanPath($path){

		$path = preg_replace('%//%','/',$path);
		$path = str_replace('//','/',$path);

		return $path;
	}

	private function registerOutputFilter()
    {
        if(!function_exists('smarty_outputfilter_note'))
        {
            function smarty_outputfilter_note($tpl_output, Smarty_Internal_Template $smarty)
            {

                global $xtPlugin, $p_cop, $is_pro_version;

                $p_cop = '';
                ($plugin_code = $xtPlugin->PluginCode('shop_copr')) ? eval($plugin_code) : false;

                if(!$is_pro_version && empty($p_cop))
                {
                    $copy_year = date('Y');
                    $cop = '<div class="copyright">xt:Commerce '.constant('_SYSTEM_VERSION').' &copy; ' . $copy_year . ' <a href="https://www.xt-commerce.com" rel="noreferrer" target="_blank">xt:Commerce</a></div>';
                }

                if (defined('_LIC_IS_DEMO') && constant('_LIC_IS_DEMO')=='1') $p_cop.='<div style="background: #FF0000; color:#ffffff; font-size:20px;">xt:Commerce '.constant('_SYSTEM_VERSION').' Demo-Version - NUR FÜR TESTZWECKE</div>';

                if (strstr($tpl_output, '[<copyright>]'))
                {
                    if (strstr($tpl_output, '[<p_copyright>]'))
                    {
                        $tpl_output = str_replace('[<p_copyright>]', $p_cop, $tpl_output);
                        return str_replace('[<copyright>]', $cop, $tpl_output);
                    }
                    else
                    {
                        return str_replace('[<copyright>]', $p_cop . $cop, $tpl_output);
                    }
                }
                else
                {
                    return $tpl_output . $p_cop . $cop;
                }

            }
        }
    }


}


