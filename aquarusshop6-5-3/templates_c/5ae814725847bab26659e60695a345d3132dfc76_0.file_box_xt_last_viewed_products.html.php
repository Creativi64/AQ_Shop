<?php
/* Smarty version 5.1.0, created on 2024-09-10 11:23:39
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_last_viewed_products/templates/boxes/box_xt_last_viewed_products.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_66e0101b494722_67553385',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5ae814725847bab26659e60695a345d3132dfc76' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_last_viewed_products/templates/boxes/box_xt_last_viewed_products.html',
      1 => 1722634739,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:includes/box_sidebar_products.html' => 1,
  ),
))) {
function content_66e0101b494722_67553385 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_last_viewed_products/templates/boxes';
$_smarty_tpl->renderSubTemplate("file:includes/box_sidebar_products.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('code'=>"xt_last_viewed_products",'heading_text'=>(defined('XT_LAST_VIEWED_PRODUCTS_TITEL') ? constant('XT_LAST_VIEWED_PRODUCTS_TITEL') : null),'product_listing'=>$_smarty_tpl->getValue('_last_viewed_products'),'classes'=>'','active'=>''), (int) 0, $_smarty_current_dir);
}
}
