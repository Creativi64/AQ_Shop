<?php
/* Smarty version 4.3.0, created on 2023-10-08 12:04:37
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/plugins/xt_last_viewed_products/templates/boxes/box_xt_last_viewed_products.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.0',
  'unifunc' => 'content_65227eb5801a87_33674891',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '3f1e87e0381fb355bed7808f38015d7987e425ca' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/plugins/xt_last_viewed_products/templates/boxes/box_xt_last_viewed_products.html',
      1 => 1687006079,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:includes/box_sidebar_products.html' => 1,
  ),
),false)) {
function content_65227eb5801a87_33674891 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:includes/box_sidebar_products.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('code'=>"xt_last_viewed_products",'heading_text'=>(defined('XT_LAST_VIEWED_PRODUCTS_TITEL') ? constant('XT_LAST_VIEWED_PRODUCTS_TITEL') : null),'product_listing'=>$_smarty_tpl->tpl_vars['_last_viewed_products']->value,'classes'=>'','active'=>''), 0, false);
}
}
