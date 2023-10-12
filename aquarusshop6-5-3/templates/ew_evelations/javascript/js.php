<?php

use ew_evelations\plugin as ew_evelations_plugin;

$xtMinify->add_resource(_SRV_WEB_TEMPLATES . _STORE_TEMPLATE . '/javascript/script.js', 100, 'footer');
$xtMinify->add_resource(_SRV_WEB_TEMPLATES . _STORE_TEMPLATE . '/javascript/affix.js', 110, 'footer');

?>

<script type="text/javascript">
    /* <![CDATA[ */
    //language vars
    var TEXT_EW_EVELATIONS_STILL = '<?php echo TEXT_EW_EVELATIONS_STILL ?>';
    var TEXT_EW_EVELATIONS_CHARACTERS_AVAILABLE = '<?php echo TEXT_EW_EVELATIONS_CHARACTERS_AVAILABLE ?>';

    //config
    var CONFIG_EW_EVELATIONS_PLUGIN_ANIMATIONS = <?php echo ew_evelations_plugin::check_conf('CONFIG_EW_EVELATIONS_PLUGIN_ANIMATIONS') ? 'true' : 'false' ?>;
    var CONFIG_EW_EVELATIONS_PLUGIN_FLOATINGNAVIGATION = <?php echo ew_evelations_plugin::check_conf('CONFIG_EW_EVELATIONS_PLUGIN_FLOATINGNAVIGATION') ? 'true' : 'false' ?>;
    var CONFIG_EW_EVELATIONS_PLUGIN_SIDEBUTTONS = <?php echo ew_evelations_plugin::check_conf('CONFIG_EW_EVELATIONS_PLUGIN_SIDEBUTTONS') ? 'true' : 'false' ?>;
    var CONFIG_EW_EVELATIONS_PLUGIN_FLOATING = <?php echo ew_evelations_plugin::check_conf('CONFIG_EW_EVELATIONS_PLUGIN_FLOATING') ? 'true' : 'false' ?>;
    var CONFIG_EW_EVELATIONS_PLUGIN_MEGANAV =  <?php echo ew_evelations_plugin::check_conf('CONFIG_EW_EVELATIONS_PLUGIN_MEGANAV') ? 'true' : 'false' ?>;
    /* ]]> */
</script>
