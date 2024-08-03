<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if (isset($xtPlugin->active_modules['xt_ckeditor']))
{
    // additional plugins
    $cke_web_dir  = str_replace('/xtAdmin', '',_SRV_WEB )._SRV_WEB_PLUGINS . 'xt_ckeditor/plugins/';
    $cke_root_dir = _SRV_WEBROOT ._SRV_WEB_PLUGINS . 'xt_ckeditor/plugins/';

    $cke_plg_dirs = glob($cke_root_dir . '*' , GLOB_ONLYDIR);

    if(is_array($cke_plg_dirs))
    {
        $ck_plgs = [];
        $plugins_installed = [];
        $js_addAdditionalCkeditorPlugins = '';
        foreach ($cke_plg_dirs as $dir)
        {
            $plugin_name = basename($dir);
            $plugin_file = 'plugin.js';

            if(is_file($dir . '/' . $plugin_file))
            {
                $ck_plgs[$plugin_name] = '/..' . $cke_web_dir . $plugin_name . '/' . $plugin_file;
            }

            $plugins_installed[]=$plugin_name;

            $js_addAdditionalCkeditorPlugins .= '
                   console.debug("config_ckeditor.js > addAdditionalCkeditorPlugins > '.$plugin_name.'");
                   CKEDITOR.plugins.addExternal( "'.$plugin_name.'", "'.$ck_plgs[$plugin_name].'" );
                    ';

        }
        $js_addAdditionalCkeditorPlugins.='config.extraPlugins = "'.implode(',',$plugins_installed).'";';
    }

    // additional config
    $js_addAdditionalCkeditorConfig = trim(constant('_SYSTEM_CKEDITOR_CONFIG'));
    if(version_compare(_SYSTEM_VERSION, '6.2', '<'))
    {
        // vor 6.2 werden in build_define.inc.php Hochkommatas ersetzt
        $js_addAdditionalCkeditorConfig = str_replace(array('&quot;', '&apos;'), array('"', '\''), $js_addAdditionalCkeditorConfig);
    }
    if(!empty($js_addAdditionalCkeditorConfig))
    {
        $js_addAdditionalCkeditorConfig = '
        console.debug("config_ckeditor.js > addAdditionalCkeditorConfig");
        '.$js_addAdditionalCkeditorConfig;
    }

    echo '
<script type="application/javascript">

    // erzeugt durch xt_ckeditor
    function addAdditionalCkeditorPlugins(config)
    {
        try {
            '.$js_addAdditionalCkeditorPlugins.'
        }
        catch (e)
        {
            console.log(e);
        }
    }
    // erzeugt durch xt_ckeditor
    function addAdditionalCkeditorConfig(config)
    {
        try {
            '.$js_addAdditionalCkeditorConfig.'
        }
        catch (e)
        {
            console.log(e);
        }
    }

</script>


';
}