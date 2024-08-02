<?php
/* Smarty version 4.3.2, created on 2024-07-19 15:52:18
  from '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_last_viewed_products/templates/xt_last_viewed_products.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_669a6f92010772_27592531',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'a45aa3a00dc46183c8482257add47c640d96af83' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_last_viewed_products/templates/xt_last_viewed_products.html',
      1 => 1697144050,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:xtCore/pages/product_listing/product_listing_slider.html' => 1,
  ),
),false)) {
function content_669a6f92010772_27592531 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:xtCore/pages/product_listing/product_listing_slider.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('heading_text'=>(defined('XT_LAST_VIEWED_PRODUCTS_TITEL') ? constant('XT_LAST_VIEWED_PRODUCTS_TITEL') : null),'product_listing'=>$_smarty_tpl->tpl_vars['_last_viewed_products']->value), 0, false);
}
}
