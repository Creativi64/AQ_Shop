<?php
/* Smarty version 4.3.0, created on 2023-10-08 03:48:11
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/plugins/xt_bestseller_products/templates/boxes/box_xt_bestseller_products.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.0',
  'unifunc' => 'content_65220a5bc7ab82_07842444',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '3125149e9cba0cd446aa3546f8328f6ca73c0f1d' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/plugins/xt_bestseller_products/templates/boxes/box_xt_bestseller_products.html',
      1 => 1687006078,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:includes/box_sidebar_products.html' => 1,
  ),
),false)) {
function content_65220a5bc7ab82_07842444 (Smarty_Internal_Template $_smarty_tpl) {
if ($_smarty_tpl->tpl_vars['curr_url']->value != 'bestseller_products') {
$_smarty_tpl->_subTemplateRender("file:includes/box_sidebar_products.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('code'=>"xt_bestseller_products",'heading_text'=>(defined('TEXT_HEADING_BESTSELLER_PRODUCTS') ? constant('TEXT_HEADING_BESTSELLER_PRODUCTS') : null),'product_listing'=>$_smarty_tpl->tpl_vars['_bestseller_products']->value,'classes'=>'','active'=>'','_show_more_link'=>$_smarty_tpl->tpl_vars['_show_more_link']->value,'_show_page_link'=>(defined('ACTIVATE_XT_BESTSELLER_PRODUCTS_PAGE') ? constant('ACTIVATE_XT_BESTSELLER_PRODUCTS_PAGE') : null)), 0, false);
}
}
}
