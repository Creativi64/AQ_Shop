<?php
/* Smarty version 4.3.2, created on 2024-03-20 20:18:43
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/plugins/xt_new_products/templates/boxes/box_xt_new_products.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_65fb3693d1d2f8_98468758',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '86b7b249d49e0fc805f5152c6dcd4ac65cb53e75' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/plugins/xt_new_products/templates/boxes/box_xt_new_products.html',
      1 => 1697144095,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:includes/box_sidebar_products.html' => 1,
  ),
),false)) {
function content_65fb3693d1d2f8_98468758 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:includes/box_sidebar_products.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('code'=>"xt_new_products",'heading_text'=>(defined('TEXT_HEADING_NEW_PRODUCTS') ? constant('TEXT_HEADING_NEW_PRODUCTS') : null),'product_listing'=>$_smarty_tpl->tpl_vars['_new_products']->value,'classes'=>'','active'=>'','_show_more_link'=>$_smarty_tpl->tpl_vars['_show_more_link']->value,'_show_page_link'=>(defined('ACTIVATE_XT_NEW_PRODUCTS_PAGE') ? constant('ACTIVATE_XT_NEW_PRODUCTS_PAGE') : null)), 0, false);
}
}
