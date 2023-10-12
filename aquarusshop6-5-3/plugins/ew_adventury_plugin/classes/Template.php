<?php

namespace ew_adventury;

class Template extends \Template
{
    /**
     * @param      $global_smarty
     * @param      $template
     * @param      $assignArray
     * @param bool $assign_to
     * @param bool $use_cache
     * @return string
     */
    public function getTemplate($global_smarty, $template, $assignArray, $assign_to = false, $use_cache = false)
    {
        global $xtPlugin, $language, $page, $currency;

        // cache fix for xt:C v5.1.0 - v5.1.3
        if (version_compare(_SYSTEM_VERSION, '5.1.0', '>=') &&
            version_compare(_SYSTEM_VERSION, '5.1.3', '<=')) {
            try {
                if ($assign_to !== false) {
                    global ${$global_smarty};
                }

                $cache_id = '';

                if (!is_object($this->content_smarty)) {
                    $this->content_smarty = new \Smarty();
                    $this->content_smarty->setCaching(\Smarty::CACHING_OFF);
                } else {
                    $cache_id = $this->getTemplateCacheID($template);
                }

                if (constant('USE_CACHE') == 'true' && $use_cache) {
                    $this->content_smarty->setCaching(\Smarty::CACHING_LIFETIME_CURRENT);
                    $cache_id = $this->getTemplateCacheID($template);
                }

                if (!defined('USE_COMPILE_CHECK') || USE_COMPILE_CHECK === false)
                    $this->content_smarty->setCompileCheck(\Smarty::COMPILECHECK_OFF);

                if ($this->check_int == 'chkint' || !$this->check_int) {
                    $this->getTemplatePath($template, '', '', 'default', 'chkint');
                }


                $tmp_tpl_path = $this->tpl_path;
                $tmp_tpl_root_path = $this->tpl_root_path;

                $content_smarty = $this->content_smarty;

                $template_dirs = array('store' => './' . _SRV_WEB_TEMPLATES . _STORE_TEMPLATE . '/', 'system' => './' . _SRV_WEB_TEMPLATES . _SYSTEM_TEMPLATE . '/');
                foreach ($template_dirs as $k => $tpl_dir) {
                    if (!array_key_exists($k, $this->content_smarty->getTemplateDir())) {
                        $this->content_smarty->addTemplateDir($tpl_dir, $k);
                    }
                }

                $content_smarty->addPluginsDir(
                    array(
                        _SRV_WEBROOT . 'xtFramework/library/smarty/xt_plugins'
                    )
                );

                $content_smarty->assign('language', $language->code);
                $content_smarty->assign('tpl_path', $tmp_tpl_path);
                if (isset($page->page_name)) {
                    $content_smarty->assign('page', $page->page_name);
                }
                if (_STORE_IMAGES_PATH_FULL == 'true') {
                    $path_base_url = _SYSTEM_BASE_URL;
                } else {
                    $path_base_url = '';
                }
                $content_smarty->assign('tpl_url_path', $path_base_url . _SRV_WEB . _SRV_WEB_TEMPLATES . $this->selected_template . '/');
                $content_smarty->assign('tpl_path', _SRV_WEBROOT . _SRV_WEB_TEMPLATES . $this->selected_template . '/');
                $content_smarty->assign('tpl_url_path_system', $path_base_url . _SRV_WEB . _SRV_WEB_TEMPLATES . $this->system_template . '/');
                $content_smarty->assign('tpl_path_system', _SRV_WEBROOT . _SRV_WEB_TEMPLATES . $this->system_template . '/');
                $content_smarty->assign('selected_template', $this->selected_template);

                ($plugin_code = $xtPlugin->PluginCode('class.template:getTemplate_assign_default')) ? eval($plugin_code) : false;

                // mobile assigns
                if (isset($_SESSION['isMobile']) or isset($_SESSION['isTablet'])) {
                    $content_smarty->assign('language_count', count($language->_getLanguageList()));
                    $content_smarty->assign('currency_count', count($currency->_getCurrencyList()));
                }

                if (is_array($assignArray)) {
                    foreach ($assignArray as $assign_key => $assign_val) {
                        if (!empty($assign_key) && (!empty($assign_val) || $assign_val === false)) {
                            $content_smarty->assign($assign_key, $assign_val);
                        }
                    }
                }

                if ($template == '/index.html') {
                    ($plugin_code = $xtPlugin->PluginCode('class.template:getTemplate_load_smartyfilter')) ? eval($plugin_code) : false;

                    $content_smarty->loadFilter('output', 'note');

                }

                $template = $tmp_tpl_root_path . $template;
                $template = $this->cleanPath($template);

                if (isset($cache_id)) {
                    $module = $content_smarty->fetch($template, $cache_id);
                } else {
                    $module = $content_smarty->fetch($template);
                }

                if (USER_POSITION == 'store' && defined('_SYSTEM_HTML_MINIFY_OPTION') && constant('_SYSTEM_HTML_MINIFY_OPTION') == 1) {
                    $module = \Minify_HTML::minify($module, array('xhtml' => false));
                }


                if ($assign_to) {
                    ${$global_smarty}->assign($assign_to, $module);
                } else {
                    return $module;
                }
            } catch (\Exception $e) {
                return 'Smarty-Exception: ' . $e->getMessage();
            }
        }
        
        // cache fix for xt:C v6.1.2+
        if (version_compare(_SYSTEM_VERSION, '6.1.2', '>=')) {
            $use_cache = plugin::isSystemCacheEnabled();
        }

        // load default method for all other xt:C versions
        return parent::getTemplate($global_smarty, $template, $assignArray, $assign_to, $use_cache);
    }

    /**
     * Checks if template cache is available
     *
     * @param string $templateFile
     * @param bool $autoPluginPath
     * @return bool
     */
    public function isTemplateCache($templateFile, $autoPluginPath = true)
    {
        // cache fix for xt:C <= v5.1.3
        if (version_compare(_SYSTEM_VERSION, '5.1.3', '<=')) {
            $this->content_smarty = new \Smarty();

            if (USE_CACHE == 'false') {
                $this->content_smarty->caching = false;
                return false;
            } else {
                $this->content_smarty->caching = true;
                $this->content_smarty->cache_lifetime = CACHE_LIFETIME;
                $this->content_smarty->cache_modified_check = CACHE_CHECK;
            }

            if (version_compare(_SYSTEM_VERSION, '5.1.0', '>=')) {
                $template_dirs = [
                    'store' => './' . _SRV_WEB_TEMPLATES . _STORE_TEMPLATE . '/',
                    'system' => './' . _SRV_WEB_TEMPLATES . _SYSTEM_TEMPLATE . '/'
                ];
                foreach ($template_dirs as $k => $tpl_dir) {
                    if(!array_key_exists($k, $this->content_smarty->getTemplateDir())) {
                        $this->content_smarty->addTemplateDir($tpl_dir, $k);
                    }
                }
            }

            $cacheID = $this->getTemplateCacheID($templateFile);
            $rootPath = $autoPluginPath ? $this->tpl_root_path : _SRV_WEBROOT . _SRV_WEB_TEMPLATES . $this->selected_template;

            if (version_compare(_SYSTEM_VERSION, '5.0.0', '>=')) {
                $functionName = 'isCached';
            } else {
                $functionName = 'is_cached';
            }

            return $this->content_smarty->$functionName($rootPath . $templateFile, $cacheID);
        }

        // load default method for all other xt:C versions
        return parent::isTemplateCache($templateFile);
    }

    /**
     * Get the cached html- little (blind) changes by Jens Albert to use in plugin folder
     *
     * @param $templateFile
     * @return string
     */
     public function getCachedTemplate($templateFile)
     {
         // cache fix for xt:C <= v5.1.3
         if (version_compare(_SYSTEM_VERSION, '5.1.3', '<=')) {
             return $this->getTemplate(null, $templateFile, array());
         }

         // load default method for all other xt:C versions
         return parent::getCachedTemplate($templateFile);
     }
}
