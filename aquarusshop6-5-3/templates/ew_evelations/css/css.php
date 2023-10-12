<?php

use ew_evelations\plugin as ew_evelations_plugin;

// Plugin error messages
class_exists('ew_evelations\plugin') or die('Please install the required template plugin.');
ew_evelations_plugin::status() or die(ew_evelations_plugin::getPluginErrorMessage());
?>

    <!-- RESPONSIVE SETUP -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

<?php if (ew_evelations_plugin::isDebugMode()) : ?>
    <!-- PAGE LOAD TIMER INIT -->
    <script type="text/javascript">
        /* <![CDATA[ */
        var pageLoadTime = (new Date()).getTime();
        /* ]]> */
    </script>
<?php endif ?>

<?php if (ew_evelations_plugin::check_conf('CONFIG_EW_EVELATIONS_PLUGIN_WEBAPPICON')) : ?>

    <!-- WEB APP SUPPORT -->
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="HandheldFriendly" content="true"/>
    <?php if (($primaryColor = ew_evelations_plugin::getPrimaryColor()) !== null) : ?>
        <meta name="theme-color" content="<?php echo $primaryColor ?>">
        <meta name="apple-mobile-web-app-status-bar-style" content="<?php echo $primaryColor ?>">
        <meta name="msapplication-TileColor" content="<?php echo $primaryColor ?>">
        <meta name="msapplication-navbutton-color" content="<?php echo $primaryColor ?>">
    <?php endif; ?>
    <?php
    /**
     * WEB APP ICONS
     *
     * Info: For each shop client you could create its own icon files like 'iconname_[shop_id].png'
     *
     * @example for shop client id 1 name it 'webapp-icon-192_1.png'
     */
    ?>
    <link rel="apple-touch-icon" sizes="192x192" href="<?php echo ew_evelations_plugin::getWebAppIcon('webapp-icon-192', 'png') ?>">
    <link rel="apple-touch-icon" sizes="144x144" href="<?php echo ew_evelations_plugin::getWebAppIcon('webapp-icon-144', 'png') ?>">
    <link rel="apple-touch-icon" sizes="96x96" href="<?php echo ew_evelations_plugin::getWebAppIcon('webapp-icon-96', 'png') ?>">
    <link rel="apple-touch-icon-precomposed" href="<?php echo ew_evelations_plugin::getWebAppIcon('webapp-icon-96', 'png') ?>"/>
    <link rel="apple-touch-startup-image" href="<?php echo ew_evelations_plugin::getWebAppIcon('webapp-splashscreen', 'png') ?>">
    <meta name="msapplication-TileImage" content="<?php echo ew_evelations_plugin::getWebAppIcon('webapp-icon-144', 'png') ?>">

<?php endif; ?>

<link href="../templates/ew_evelations/fonts/ptsansnarrow/ptsansnarrow_regular_macroman-webfont.woff2"
        rel="preload"
        as="font"
        type="font/woff2"
        crossorigin="anonymous">
<link href="../templates/ew_evelations/fonts/ptsansnarrow/ptsansnarrow_bold_macroman-webfont.woff2"
        rel="preload"
        as="font"
        type="font/woff2"
        crossorigin="anonymous">
<link href="../plugins/ew_evelations_plugin/assets/components/shariff/fontawesome-webfont.woff2"
        rel="preload"
        as="font"
        type="font/woff2"
        crossorigin="anonymous">
