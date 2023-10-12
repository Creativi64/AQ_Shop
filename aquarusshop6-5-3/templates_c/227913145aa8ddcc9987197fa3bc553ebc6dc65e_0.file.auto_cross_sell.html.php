<?php
/* Smarty version 4.3.0, created on 2023-10-10 00:10:38
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/plugins/xt_auto_cross_sell/templates/auto_cross_sell.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.0',
  'unifunc' => 'content_65247a5e153ad5_39757413',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '227913145aa8ddcc9987197fa3bc553ebc6dc65e' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/plugins/xt_auto_cross_sell/templates/auto_cross_sell.html',
      1 => 1687006038,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:xtCore/pages/product_listing/product_listing_slider.html' => 1,
  ),
),false)) {
function content_65247a5e153ad5_39757413 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:xtCore/pages/product_listing/product_listing_slider.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('code'=>"xt_auto_cross_sell",'heading_text'=>(defined('TEXT_HEADING_AUTO_CROSS_SELL') ? constant('TEXT_HEADING_AUTO_CROSS_SELL') : null),'product_listing'=>$_smarty_tpl->tpl_vars['_auto_cross_sell']->value,'page'=>"cart"), 0, false);
}
}
