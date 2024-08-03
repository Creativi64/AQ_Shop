<?php
/* Smarty version 5.1.0, created on 2024-08-03 01:15:06
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_last_viewed_products/templates/xt_last_viewed_products.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_66ad687a1912b3_31288622',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'a45aa3a00dc46183c8482257add47c640d96af83' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_last_viewed_products/templates/xt_last_viewed_products.html',
      1 => 1722634739,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:xtCore/pages/product_listing/product_listing_slider.html' => 1,
  ),
))) {
function content_66ad687a1912b3_31288622 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_last_viewed_products/templates';
$_smarty_tpl->renderSubTemplate("file:xtCore/pages/product_listing/product_listing_slider.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('heading_text'=>(defined('XT_LAST_VIEWED_PRODUCTS_TITEL') ? constant('XT_LAST_VIEWED_PRODUCTS_TITEL') : null),'product_listing'=>$_smarty_tpl->getValue('_last_viewed_products')), (int) 0, $_smarty_current_dir);
}
}
