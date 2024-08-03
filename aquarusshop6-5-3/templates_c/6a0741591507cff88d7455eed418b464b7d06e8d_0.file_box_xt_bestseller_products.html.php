<?php
/* Smarty version 5.1.0, created on 2024-08-03 01:14:35
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_bestseller_products/templates/boxes/box_xt_bestseller_products.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_66ad685b7bbca4_65984800',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '6a0741591507cff88d7455eed418b464b7d06e8d' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_bestseller_products/templates/boxes/box_xt_bestseller_products.html',
      1 => 1722634729,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:includes/box_sidebar_products.html' => 1,
  ),
))) {
function content_66ad685b7bbca4_65984800 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_bestseller_products/templates/boxes';
if ($_smarty_tpl->getValue('curr_url') != 'bestseller_products') {
$_smarty_tpl->renderSubTemplate("file:includes/box_sidebar_products.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('code'=>"xt_bestseller_products",'heading_text'=>(defined('TEXT_HEADING_BESTSELLER_PRODUCTS') ? constant('TEXT_HEADING_BESTSELLER_PRODUCTS') : null),'product_listing'=>$_smarty_tpl->getValue('_bestseller_products'),'classes'=>'','active'=>'','_show_more_link'=>$_smarty_tpl->getValue('_show_more_link'),'_show_page_link'=>(defined('ACTIVATE_XT_BESTSELLER_PRODUCTS_PAGE') ? constant('ACTIVATE_XT_BESTSELLER_PRODUCTS_PAGE') : null)), (int) 0, $_smarty_current_dir);
}
}
}
