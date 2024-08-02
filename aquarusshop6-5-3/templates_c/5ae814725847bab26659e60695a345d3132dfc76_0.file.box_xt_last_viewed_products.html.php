<?php
/* Smarty version 4.3.2, created on 2024-07-19 15:48:04
  from '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_last_viewed_products/templates/boxes/box_xt_last_viewed_products.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_669a6e94c9dff1_94097961',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5ae814725847bab26659e60695a345d3132dfc76' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_last_viewed_products/templates/boxes/box_xt_last_viewed_products.html',
      1 => 1697144095,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:includes/box_sidebar_products.html' => 1,
  ),
),false)) {
function content_669a6e94c9dff1_94097961 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:includes/box_sidebar_products.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('code'=>"xt_last_viewed_products",'heading_text'=>(defined('XT_LAST_VIEWED_PRODUCTS_TITEL') ? constant('XT_LAST_VIEWED_PRODUCTS_TITEL') : null),'product_listing'=>$_smarty_tpl->tpl_vars['_last_viewed_products']->value,'classes'=>'','active'=>''), 0, false);
}
}
