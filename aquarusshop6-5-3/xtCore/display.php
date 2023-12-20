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

global $page, $language, $xtPlugin, $logHandler, $xtMinify;

if ($page->page_name=='404') header('HTTP/1.0 404 Not Found');
header('Content-Type: text/html; charset='._SYSTEM_CHARSET);
header('X-Frame-Options: DENY');
($plugin_code = $xtPlugin->PluginCode('display.php:header')) ? eval($plugin_code) : false;

CookieRegistry::sendCookies();

$doctype_html4 = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'. PHP_EOL;
$doctype_html5 = "<!DOCTYPE html>". PHP_EOL;

($plugin_code = $xtPlugin->PluginCode('display.php:doctype')) ? eval($plugin_code) : false;

//html5 doctype
if(defined("_STORE_META_DOCTYPE_HTML") && strtolower(_STORE_META_DOCTYPE_HTML) == 'html5'){
    echo $doctype_html5;
    echo '<html lang="'.$language->code.'">'. PHP_EOL;
}else{
    echo $doctype_html4;
    echo '<html xmlns="https://www.w3.org/1999/xhtml" xml:lang="'.$language->code.'" lang="'.$language->code.'">'. PHP_EOL;
}
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<base href="<?php echo _SYSTEM_BASE_URL . _SRV_WEB; ?>" />
<?php
    global $meta_tags, $show_shop_content;
	$meta_tags->_showTags($show_shop_content);
	//tpl styles
	include _SRV_WEBROOT._SRV_WEB_CORE.'styles.php';
    if (file_exists(_SRV_WEBROOT._SRV_WEB_TEMPLATES._STORE_TEMPLATE.'/css/css.php'))
        include _SRV_WEBROOT._SRV_WEB_TEMPLATES._STORE_TEMPLATE.'/css/css.php';
    else
        include _SRV_WEBROOT._SRV_WEB_TEMPLATES._SYSTEM_TEMPLATE.'/css/css.php';

	include _SRV_WEBROOT._SRV_WEB_CORE.'javascript.php';
    if (file_exists(_SRV_WEBROOT._SRV_WEB_TEMPLATES._STORE_TEMPLATE.'/javascript/js.php'))
	include _SRV_WEBROOT._SRV_WEB_TEMPLATES._STORE_TEMPLATE.'/javascript/js.php';
    else
        include _SRV_WEBROOT._SRV_WEB_TEMPLATES._SYSTEM_TEMPLATE.'/javascript/js.php';

    $xtMinify->serveFile();

    if (file_exists(_SRV_WEBROOT._SRV_WEB_TEMPLATES._STORE_TEMPLATE.'/'._SRV_WEB_CORE.'json-ld.php'))
        include _SRV_WEBROOT._SRV_WEB_TEMPLATES._STORE_TEMPLATE.'/'._SRV_WEB_CORE.'json-ld.php';
    else if (file_exists(_SRV_WEBROOT._SRV_WEB_TEMPLATES._SYSTEM_TEMPLATE.'/'._SRV_WEB_CORE.'json-ld.php'))
        include _SRV_WEBROOT._SRV_WEB_TEMPLATES._SYSTEM_TEMPLATE.'/'._SRV_WEB_CORE.'json-ld.php';
    else
        include _SRV_WEBROOT._SRV_WEB_CORE.'json-ld.php';

    if (file_exists(_SRV_WEBROOT._SRV_WEB_TEMPLATES._STORE_TEMPLATE.'/'._SRV_WEB_CORE.'sm-tags.php'))
        include _SRV_WEBROOT._SRV_WEB_TEMPLATES._STORE_TEMPLATE.'/'._SRV_WEB_CORE.'sm-tags.php';
    else if (file_exists(_SRV_WEBROOT._SRV_WEB_TEMPLATES._SYSTEM_TEMPLATE.'/'._SRV_WEB_CORE.'sm-tags.php'))
        include _SRV_WEBROOT._SRV_WEB_TEMPLATES._SYSTEM_TEMPLATE.'/'._SRV_WEB_CORE.'sm-tags.php';
    else
        include _SRV_WEBROOT._SRV_WEB_CORE.'sm-tags.php';



    $body_class = $body_params = '';


    ($plugin_code = $xtPlugin->PluginCode('display.php:content_head')) ? eval($plugin_code) : false;

$fav = strtolower(_STORE_FAVICON);

if(strpos($fav,".ico")!==false){
    echo '<link rel="shortcut icon" href="'._SYSTEM_BASE_URL . _SRV_WEB.'media/logo/'._STORE_FAVICON.'" type="image/x-icon" />'. PHP_EOL;
}elseif(strpos($fav,".png")!==false){
    echo '<link rel="icon" href="'._SYSTEM_BASE_URL . _SRV_WEB. 'media/logo/' ._STORE_FAVICON.'" type="image/png" />'. PHP_EOL;
}elseif(strpos($fav,".gif")===false && strpos($fav,".jpg")===false){
    echo '<link rel="shortcut icon" href="'._SYSTEM_BASE_URL . _SRV_WEB.'media/logo/'._STORE_FAVICON.'.ico" type="image/x-icon" />'. PHP_EOL;
    echo '<link rel="icon" href="'._SYSTEM_BASE_URL . _SRV_WEB. 'media/logo/' ._STORE_FAVICON.'.png" type="image/png" />'. PHP_EOL;
}

$store_hreflang_def = constant('_STORE_HREFLANG_DEFAULT');
// href alternate
$langSwitchLinks = $language->getLanguageSwitchLinks(array(), $store_hreflang_def);
($plugin_code = $xtPlugin->PluginCode('display.php:getLanguageSwitchLinks_all')) ? eval($plugin_code) : false;
foreach ($langSwitchLinks as $lang => $lsl)
{
    echo '<link rel="alternate" hreflang="' . $lang . '" href="' . $lsl . '" />' . PHP_EOL;
}

if (array_key_exists($store_hreflang_def, $langSwitchLinks))
{
    $defLangLink = $language->getLanguageSwitchLinks(array($store_hreflang_def), $store_hreflang_def);
    ($plugin_code = $xtPlugin->PluginCode('display.php:getLanguageSwitchLinks_href_default')) ? eval($plugin_code) : false;
    echo '<link rel="alternate" hreflang="x-default" href="' . $defLangLink[$store_hreflang_def] . '" />' . PHP_EOL;
}
else if ( isset($store_hreflang_def) && $store_hreflang_def != '')
{
    $defLangLink = $language->getLanguageSwitchLinks(array(_STORE_LANGUAGE), $store_hreflang_def);
    ($plugin_code = $xtPlugin->PluginCode('display.php:getLanguageSwitchLinks_store_lang')) ? eval($plugin_code) : false;
    echo '<link rel="alternate" hreflang="x-default" href="' . $defLangLink[_STORE_LANGUAGE] . '" />' . PHP_EOL;
}

?>
</head>
<?php

if ($body_class!='') {
	$body = '<body class="'.$body_class.'" '.$body_params.'>';
} else {
	$body = '<body '.$body_params.'>';
}
($plugin_code = $xtPlugin->PluginCode('display.php:content_top')) ? eval($plugin_code) : false;

echo $body;
if (isset($installer_warning) && $installer_warning==true) {
    echo '<div id="installer_warning">'.WARNING_INSTALL.'</div>';
}
($plugin_code = $xtPlugin->PluginCode('display.php:body_top')) ? eval($plugin_code) : false;

if(DISPLAY_SQL_QUERIES_COUNT===true)
{
	global $sql_queries_count;
	echo '<div class="copyright">'.$sql_queries_count.' queries</div>';
}
if(_SYSTEM_PARSE_TIME=='true'){
    echo '<div class="copyright">'.$logHandler->parseTime(true).'</div>';
}

echo $show_shop_content;

($plugin_code = $xtPlugin->PluginCode('display.php:content_bottom')) ? eval($plugin_code) : false;


$xtMinify->serveFile('footer');

global $page;
$type = '';
if(_STORE_META_DOCTYPE_HTML != "html5"){
    $type = 'type="text/javascript" ';
}
if (CookieRegistry::emitCookieSettingsJs()) echo CookieRegistry::getCookieSettingsJs();
echo CookieRegistry::getBannerHtml();
($plugin_code = $xtPlugin->PluginCode('display.php:after_minify_serve_footer')) ? eval($plugin_code) : false;
unset($_SESSION['cartChanged']);
?>
</body>
</html>