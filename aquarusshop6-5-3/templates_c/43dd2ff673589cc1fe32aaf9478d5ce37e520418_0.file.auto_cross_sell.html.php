<?php
/* Smarty version 4.3.2, created on 2024-07-18 01:38:06
  from '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_auto_cross_sell/templates/auto_cross_sell.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_669855de0337c0_96106507',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '43dd2ff673589cc1fe32aaf9478d5ce37e520418' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_auto_cross_sell/templates/auto_cross_sell.html',
      1 => 1710347892,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:xtCore/pages/product_listing/product_listing_slider.html' => 1,
  ),
),false)) {
function content_669855de0337c0_96106507 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:xtCore/pages/product_listing/product_listing_slider.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('code'=>"xt_auto_cross_sell",'heading_text'=>(defined('TEXT_HEADING_AUTO_CROSS_SELL') ? constant('TEXT_HEADING_AUTO_CROSS_SELL') : null),'product_listing'=>$_smarty_tpl->tpl_vars['_auto_cross_sell']->value,'page'=>"cart"), 0, false);
}
}
