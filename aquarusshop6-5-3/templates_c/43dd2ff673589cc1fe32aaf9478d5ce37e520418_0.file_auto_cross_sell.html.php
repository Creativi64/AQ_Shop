<?php
/* Smarty version 5.1.0, created on 2024-09-09 20:48:10
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_auto_cross_sell/templates/auto_cross_sell.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_66df42eaac9994_31549465',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '43dd2ff673589cc1fe32aaf9478d5ce37e520418' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_auto_cross_sell/templates/auto_cross_sell.html',
      1 => 1722634727,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:xtCore/pages/product_listing/product_listing_slider.html' => 1,
  ),
))) {
function content_66df42eaac9994_31549465 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_auto_cross_sell/templates';
$_smarty_tpl->renderSubTemplate("file:xtCore/pages/product_listing/product_listing_slider.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('code'=>"xt_auto_cross_sell",'heading_text'=>(defined('TEXT_HEADING_AUTO_CROSS_SELL') ? constant('TEXT_HEADING_AUTO_CROSS_SELL') : null),'product_listing'=>$_smarty_tpl->getValue('_auto_cross_sell'),'page'=>"cart"), (int) 0, $_smarty_current_dir);
}
}
