<?php
/* Smarty version 5.1.0, created on 2024-08-03 01:14:38
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_auto_cross_sell/templates/auto_cross_sell.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_66ad685eb20031_45406788',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '3902a79a1c029d253e943b2a6c325fd5de1b36cc' => 
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
function content_66ad685eb20031_45406788 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_auto_cross_sell/templates';
$_smarty_tpl->renderSubTemplate("file:xtCore/pages/product_listing/product_listing_slider.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('code'=>"xt_auto_cross_sell",'heading_text'=>(defined('TEXT_HEADING_AUTO_CROSS_SELL') ? constant('TEXT_HEADING_AUTO_CROSS_SELL') : null),'product_listing'=>$_smarty_tpl->getValue('_auto_cross_sell'),'page'=>"cart"), (int) 0, $_smarty_current_dir);
}
}