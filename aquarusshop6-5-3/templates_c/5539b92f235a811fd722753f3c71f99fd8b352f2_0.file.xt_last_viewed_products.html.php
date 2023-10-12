<?php
/* Smarty version 4.3.0, created on 2023-10-08 01:28:40
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/plugins/xt_last_viewed_products/templates/xt_last_viewed_products.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.0',
  'unifunc' => 'content_6521e9a8afe8e3_18572415',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5539b92f235a811fd722753f3c71f99fd8b352f2' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/plugins/xt_last_viewed_products/templates/xt_last_viewed_products.html',
      1 => 1687006052,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:xtCore/pages/product_listing/product_listing_slider.html' => 1,
  ),
),false)) {
function content_6521e9a8afe8e3_18572415 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:xtCore/pages/product_listing/product_listing_slider.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('heading_text'=>(defined('XT_LAST_VIEWED_PRODUCTS_TITEL') ? constant('XT_LAST_VIEWED_PRODUCTS_TITEL') : null),'product_listing'=>$_smarty_tpl->tpl_vars['_last_viewed_products']->value), 0, false);
}
}
