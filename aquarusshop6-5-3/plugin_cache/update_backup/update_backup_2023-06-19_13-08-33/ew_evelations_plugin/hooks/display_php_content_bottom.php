<?php defined('_VALID_CALL') or die('Direct Access is not allowed.');

/**
 * Output in display php bottom some debug info
 */

use ew_evelations\plugin as ew_evelations_plugin;
use ew_evelations\Template as template;

if (class_exists('ew_evelations\plugin') && ew_evelations_plugin::status()) {

    if (ew_evelations_plugin::isDebugMode()) {
        global $logHandler;

        function _ewCacheInfo()
        {
            // global $template;
            $data = [];
            $data['enabled'] = ew_evelations_plugin::isFileCacheAllowed();
            $data['data']['test'] = (string)microtime();
            $data['template'] = new template();
            // $data['template'] = $template;
            $data['templateFile'] = 'ew_evelations_debug_cache_test.html';
            $data['template']->getTemplatePath($data['templateFile'], 'ew_evelations_plugin', 'tests', 'plugin');
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
        $tpl = 'ew_evelations_debugbar.html';
        $tpl_data = array();
        $tpl_object->getTemplatePath($tpl, 'ew_evelations_plugin', 'hooks', 'plugin');
        $logHandler->parseTime(false);

        $tpl_data = array(
            'request_ip'         => ew_evelations_plugin::getUserIP(),
            'response_ip'        => ew_evelations_plugin::getServerIP(),
            'parse_time'         => $logHandler->timer_total,
            'sys_cache'          => ew_evelations_plugin::isFileCacheAllowed(),
            'sys_cache_lifetime' => defined('CACHE_LIFETIME') ? round((CACHE_LIFETIME / 60)) . 'm' : 0,
            'sys_cache_info'     => _ewCacheInfo(),
            'template'           => ew_evelations_plugin::templateName(),
            'pluginRootUrl'      => ew_evelations_plugin::getPluginRootURL(),
        );

        echo $tpl_object->getTemplate('ew_evelations_debugbar', $tpl, $tpl_data); //print output
        unset($tpl_object, $tpl_data, $tpl);
    }

}
