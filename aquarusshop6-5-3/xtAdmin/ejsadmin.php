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

include '../xtFramework/admin/main.php';

global $xtc_acl, $store_handler, $xtPlugin;

if (!$xtc_acl->isLoggedIn()) {
    header('location: login.php');
    exit;
}

$store_handler->checkAdminSSL();
$store_handler->redirectAdminSSL();

include_once _SRV_WEBROOT.'conf/config_froala.php';

if(!defined('FROALA_CDN_VERSION')) define('FROALA_CDN_VERSION', 'latest');


?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "https://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>xt:Admin <?php echo _SYSTEM_VERSION;?> Admin, angemeldet als <?php echo $xtc_acl->getUsername(); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="shortcut icon" href="./favicon.ico" type="image/x-icon">
<link rel="icon" href="./favicon.png" type="image/png">

<link rel="stylesheet" type="text/css" href="../xtFramework/library/ext/resources/css/ext-all.css" />
<link rel="stylesheet" type="text/css" href="../xtFramework/library/ext/resources/css/Ext.ux.form.LovCombo.css" />
<link rel="stylesheet" type="text/css" href="../xtFramework/library/ext/ux/grid/RowActions.css" />
<link rel="stylesheet" type="text/css" href="../xtFramework/library/ext/resources/css/Ext.ux.form.IconCombo.css" />
<link rel="stylesheet" href="../xtFramework/library/amcharts/css/style.css" type="text/css">
<link rel="stylesheet" type="text/css" href="../xtFramework/library/jquery-admin/ui/base-xt/jquery-ui-1.9.2.custom.css" >

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous"/>
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link rel="stylesheet" href="../xtFramework/library/vendor/alexanderpoellmann/paymentfont/css/paymentfont.min.css" type="text/css">
<link rel="stylesheet" type="text/css" href="css/admin.css" />
<link rel="stylesheet" type="text/css" href="css/icons.css" />

<link href="https://cdn.jsdelivr.net/npm/froala-editor@<?php echo FROALA_CDN_VERSION; ?>/css/froala_editor.pkgd.min.css" rel="stylesheet" type="text/css" />

<?php
include('css/css_flags.php');
($plugin_code = $xtPlugin->PluginCode('ejsadmin.php:css_styles')) ? eval($plugin_code) : false;
?>


<script type="text/javascript" src="../xtFramework/library/ext/adapter/ext/ext-base.js"></script>
<script type="text/javascript" src="../xtFramework/library/ext/ext-all.js"></script>
<script type="text/javascript" src="../xtFramework/library/ext/ux/menu/EditableItem.js"></script>
<script type="text/javascript" src="../xtFramework/library/ext/ux/menu/RangeMenu.js"></script>
<script type="text/javascript" src="../xtFramework/library/ext/ux/grid/GridFilters.js"></script>
<script type="text/javascript" src="../xtFramework/library/ext/ux/grid/filter/Filter.js"></script>
<script type="text/javascript" src="../xtFramework/library/ext/ux/grid/filter/StringFilter.js"></script>
<script type="text/javascript" src="../xtFramework/library/ext/ux/grid/filter/DateFilter.js"></script>
<script type="text/javascript" src="../xtFramework/library/ext/ux/grid/filter/ListFilter.js"></script>
<script type="text/javascript" src="../xtFramework/library/ext/ux/grid/filter/NumericFilter.js"></script>
<script type="text/javascript" src="../xtFramework/library/ext/ux/grid/filter/BooleanFilter.js"></script>
<script type="text/javascript" src="../xtFramework/library/ext/ux/grid/RowActions.js"></script>
<script type="text/javascript" src="../xtFramework/library/ext/ux/grid/CheckColumn.js"></script>
<script type="text/javascript" src="../xtFramework/library/ext/ux/form/Ext.ux.form.LovCombo.js"></script>
<script type="text/javascript" src="../xtFramework/library/ext/ux/form/Ext.ux.form.RadioGroup.js"></script>
<script type="text/javascript" src="../xtFramework/library/ext/ux/form/Ext.ux.Multiselect.js"></script>
<script type="text/javascript" src="../xtFramework/library/amcharts/amcharts.js"></script>
<script type="text/javascript" src="../xtFramework/library/amcharts/pie.js"></script>
<script type="text/javascript" src="../xtFramework/library/amcharts/serial.js"></script>
<script type="text/javascript" src="../xtFramework/library/ext/ux/view/data-view-plugins.js"></script>
<script type="text/javascript" src="../xtFramework/library/ext/ux/view/ImageDragZone.js"></script>
<script type="text/javascript" src="../xtFramework/library/ext/ux/form/Ext.ux.form.IconCombo.js"></script>
<?php if(_SYSTEM_AUTOLOAD=='true'){ ?>
<script type="text/javascript" src="../xtFramework/library/ext/ux/form/Ext.ux.AutoloadPatch.js"></script>
<?php } ?>

<link rel="stylesheet" type="text/css" href="../xtFramework/library/ext/ux/css/Multiselect.css"/>
<script type="text/javascript" src="../xtFramework/library/ext/ux/form/Ext.ux.Multiselect.js"></script>
<script type="text/javascript" src="../xtFramework/library/ext/ux/view/DDView.js"></script>
<script type="text/javascript" src="../xtFramework/library/ext_plugin/ext-searchfield.js"></script>
<script type="text/javascript" src="../xtFramework/library/ext_plugin/ext-checkboxfield.js"></script>
<script type="text/javascript" src="../xtFramework/library/ext_plugin/ext-helptreenodeui.js"></script>
<script type="text/javascript" src="../xtFramework/library/ext_plugin/ext-lovcombo2.js"></script>
<script type="text/javascript" src="../xtFramework/library/ext_plugin/ext-LocalStorageProvider.js"></script>
<link rel="stylesheet" type="text/css" href="../xtFramework/library/ext/ux/view/chooser.css" />
<script type="text/javascript" src="../xtFramework/library/ext/ux/view/chooser.js"></script>
    <script type="text/javascript" >
        Ext.DatePicker.prototype.startDay = 1;
    </script>

<?php
// exjs 'i18n'
global $language;
if($language->code == 'de')
{
    ?>
    <script type="text/javascript" src="../xtFramework/library/ext_plugin/ext-lang-de.js"></script>
    <?php
}
?>

<script type="text/javascript" src="../xtFramework/library/ckfinder3/ckfinder.js"></script>

<script type="text/javascript" src="../xtFramework/library/ext/ux/Ext.ux.BrowseButton.js"></script>
<script type="text/javascript" src="../xtFramework/library/ext/ux/TabCloseMenu.js"></script>
<script type="text/javascript" src="../xtFramework/library/ext/ux/miframe.js"></script>
<script type="text/javascript" src="../xtFramework/library/ext/ux/Ext.ux.plugins.js"></script>
<script type="text/javascript" src="../xtFramework/library/jquery-admin/jquery-2.2.4.min.js"></script>
<script type="text/javascript" src="../xtFramework/library/jquery-admin/jquery-ui-1.9.2.custom.min.js"></script>

<script type="text/javascript" src="../xtFramework/library/jquery-admin/jquery.form.min.js"></script>
<script type="text/javascript" src="../xtFramework/library/ext/ux/form/Ext.ux.Amchart.js"></script>
<?php
$froala_load_on_focus_only = false;
$froala_height_min = 200;
$froala_height_max = 400;
if(defined('FROALA_LOAD_ON_FOCUS_ONLY') && FROALA_LOAD_ON_FOCUS_ONLY)  // kommt aus conf/config_froala.php, siehe oben beim css
    $froala_load_on_focus_only = true;
if(defined('FROALA_HEIGHT_MIN'))  // kommt aus conf/config_froala.php, siehe oben beim css
    $froala_height_min = constant('FROALA_HEIGHT_MIN');
if(defined('FROALA_HEIGHT_MAX'))  // kommt aus conf/config_froala.php, siehe oben beim css
    $froala_height_max = constant('FROALA_HEIGHT_MAX');

echo '<script> 
const froalaLanguage = "'.$language->code.'";
let froalaLoadOnFocusOnly = '.($froala_load_on_focus_only ? 'true' :  'false').' ;
let froalaHeightMin = '.$froala_height_min.';
let froalaHeightMax = '.$froala_height_max.';

</script>';

echo '<link href="https://cdn.jsdelivr.net/npm/froala-editor@'.FROALA_CDN_VERSION.'/css/froala_editor.pkgd.min.css" rel="stylesheet" type="text/css" />';
echo '<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/froala-editor@'.FROALA_CDN_VERSION.'/js/froala_editor.pkgd.min.js"></script>';

echo '<script type="text/javascript" src="../xtFramework/library/ext/ux/Ext.ux.FroalaEditor.js?v='.time().'"></script>';
$froala_language_file = $language->code == 'en' ? 'en_gb' : $language->code;
echo '<script type="text/javascript" src="../xtFramework/library/ext/ux/froala_languages/'.$froala_language_file.'.js"></script>';

($plugin_code = $xtPlugin->PluginCode('ejsadmin.php:more_js')) ? eval($plugin_code) : false;
?>

<?php include _SRV_WEBROOT_ADMIN.'ejsadmin.js.php'; ?>  
<script type="text/javascript" src="../xtAdmin/reloadData.js"></script>

</head>
<body>

<div id="header">
	<?php ($plugin_code = $xtPlugin->PluginCode('ejsadmin.php:header_tag')) ? eval($plugin_code) : false; ?>
    <?php if ($store_handler->_lic_license_isdemo == '1') { ?>
    <div id="account_status">
        <div class="info-box">
            <span class="info-box-icon info-box-orange"><i class="far fa-clock"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Test-Version bis: <?php echo $store_handler->_lic_trial_end_time; ?></span>
                <span class="info-box-number"><a href="https://addons.xt-commerce.com/de/Shopsoftware/xt:Commerce-6-Einzelshoplizenz.html" target="_blank">Vollversion kaufen</a> | <a href="https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/1025540098/xt+Commerce+Test-Version+FAQ" target="_blank">FAQ</a></span>
            </div>
        </div>
    </div>
<?php } ?>
</div>

<div id="west"></div>
<div id="north"><img src="images/layout/backend.png" /></div>

</body>
</html>
