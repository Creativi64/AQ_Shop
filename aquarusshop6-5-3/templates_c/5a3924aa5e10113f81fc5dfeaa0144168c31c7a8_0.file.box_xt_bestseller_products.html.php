<?php
/* Smarty version 4.3.2, created on 2024-07-17 21:26:11
  from '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_bestseller_products/templates/boxes/box_xt_bestseller_products.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_66981ad35737f6_72528680',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5a3924aa5e10113f81fc5dfeaa0144168c31c7a8' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_bestseller_products/templates/boxes/box_xt_bestseller_products.html',
      1 => 1697144093,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:includes/box_sidebar_products.html' => 1,
  ),
),false)) {
function content_66981ad35737f6_72528680 (Smarty_Internal_Template $_smarty_tpl) {
if ($_smarty_tpl->tpl_vars['curr_url']->value != 'bestseller_products') {
$_smarty_tpl->_subTemplateRender("file:includes/box_sidebar_products.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('code'=>"xt_bestseller_products",'heading_text'=>(defined('TEXT_HEADING_BESTSELLER_PRODUCTS') ? constant('TEXT_HEADING_BESTSELLER_PRODUCTS') : null),'product_listing'=>$_smarty_tpl->tpl_vars['_bestseller_products']->value,'classes'=>'','active'=>'','_show_more_link'=>$_smarty_tpl->tpl_vars['_show_more_link']->value,'_show_page_link'=>(defined('ACTIVATE_XT_BESTSELLER_PRODUCTS_PAGE') ? constant('ACTIVATE_XT_BESTSELLER_PRODUCTS_PAGE') : null)), 0, false);
}
}
}
