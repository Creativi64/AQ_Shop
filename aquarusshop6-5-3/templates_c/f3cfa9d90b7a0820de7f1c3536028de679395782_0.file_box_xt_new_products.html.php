<?php
/* Smarty version 5.1.0, created on 2024-09-09 19:47:32
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_new_products/templates/boxes/box_xt_new_products.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_66df34b48d3397_88566517',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f3cfa9d90b7a0820de7f1c3536028de679395782' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_new_products/templates/boxes/box_xt_new_products.html',
      1 => 1722634742,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:includes/box_sidebar_products.html' => 1,
  ),
))) {
function content_66df34b48d3397_88566517 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_new_products/templates/boxes';
$_smarty_tpl->renderSubTemplate("file:includes/box_sidebar_products.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('code'=>"xt_new_products",'heading_text'=>(defined('TEXT_HEADING_NEW_PRODUCTS') ? constant('TEXT_HEADING_NEW_PRODUCTS') : null),'product_listing'=>$_smarty_tpl->getValue('_new_products'),'classes'=>'','active'=>'','_show_more_link'=>$_smarty_tpl->getValue('_show_more_link'),'_show_page_link'=>(defined('ACTIVATE_XT_NEW_PRODUCTS_PAGE') ? constant('ACTIVATE_XT_NEW_PRODUCTS_PAGE') : null)), (int) 0, $_smarty_current_dir);
}
}
