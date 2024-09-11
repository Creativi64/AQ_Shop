<?php
/* Smarty version 5.1.0, created on 2024-09-09 20:01:15
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_bestseller_products/templates/boxes/box_xt_bestseller_products.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_66df37eb6fbfd9_69069441',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5a3924aa5e10113f81fc5dfeaa0144168c31c7a8' => 
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
function content_66df37eb6fbfd9_69069441 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_bestseller_products/templates/boxes';
if ($_smarty_tpl->getValue('curr_url') != 'bestseller_products') {
$_smarty_tpl->renderSubTemplate("file:includes/box_sidebar_products.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('code'=>"xt_bestseller_products",'heading_text'=>(defined('TEXT_HEADING_BESTSELLER_PRODUCTS') ? constant('TEXT_HEADING_BESTSELLER_PRODUCTS') : null),'product_listing'=>$_smarty_tpl->getValue('_bestseller_products'),'classes'=>'','active'=>'','_show_more_link'=>$_smarty_tpl->getValue('_show_more_link'),'_show_page_link'=>(defined('ACTIVATE_XT_BESTSELLER_PRODUCTS_PAGE') ? constant('ACTIVATE_XT_BESTSELLER_PRODUCTS_PAGE') : null)), (int) 0, $_smarty_current_dir);
}
}
}
