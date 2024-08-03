<?php defined('_VALID_CALL') or die('Direct Access is not allowed.');

/**
 * Output in display php bottom some debug info
 */

use ew_adventury\plugin as ew_adventury_plugin;
use ew_adventury\Template as template;

if (class_exists('ew_adventury\plugin') && ew_adventury_plugin::status()) {

    if (ew_adventury_plugin::isDebugMode()) {
        global $logHandler;

        function _ewCacheInfo()
        {
            // global $template;
            $data = [];
            $data['enabled'] = ew_adventury_plugin::isFileCacheAllowed();
            $data['data']['test'] = (string)microtime();
            $data['template'] = new template();
            // $data['template'] = $template;
            $data['templateFile'] = 'ew_adventury_debug_cache_test.html';
            $data['template']->getTemplatePath($data['templateFile'], 'ew_adventury_plugin', 'tests', 'plugin');
            $data['cacheFound'] = $data['template']->isTemplateCache($data['templateFile']);
            if (!$data['enabled'] || !$data['cacheFound']) {
                $data['renderedWithCache'] = false;
                $data['rendered'] = trim($data['template']->getTemplate(__FUNCTION__, $data['templateFile'], $data['data']));
            } else {
                $data['renderedWithCache'] = true;
                $data['rendered'] = trim($data['template']->getCachedTemplate($data['templateFile']));
            }
            $data['ioMatch'] = $data['data']['test'] === $data['rendered'];
            return $data;
        }

        $tpl_object = new template();
        $tpl = 'ew_adventury_debugbar.html';
        $tpl_data = array();
        $tpl_object->getTemplatePath($tpl, 'ew_adventury_plugin', 'hooks', 'plugin');
        $logHandler->parseTime(false);

        $tpl_data = array(
            'request_ip'         => ew_adventury_plugin::getUserIP(),
            'response_ip'        => ew_adventury_plugin::getServerIP(),
            'parse_time'         => $logHandler->timer_total,
            'sys_cache'          => ew_adventury_plugin::isFileCacheAllowed(),
            'sys_cache_lifetime' => defined('CACHE_LIFETIME') ? round((CACHE_LIFETIME / 60)) . 'm' : 0,
            'sys_cache_info'     => _ewCacheInfo(),
            'template'           => ew_adventury_plugin::templateName(),
            'pluginRootUrl'      => ew_adventury_plugin::getPluginRootURL(),
        );

        echo $tpl_object->getTemplate('ew_adventury_debugbar', $tpl, $tpl_data); //print output
        unset($tpl_object, $tpl_data, $tpl);
    }

}
