<?php
/* Smarty version 4.3.2, created on 2024-07-17 20:12:10
  from '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/navigation/nav_count.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_6698097a6c0a67_42685878',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '250449fd881af676afa92448891bd4c5e4719daa' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/navigation/nav_count.html',
      1 => 1697144245,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6698097a6c0a67_42685878 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),));
if ($_smarty_tpl->tpl_vars['page_name']->value != '') {?><div><?php echo smarty_function_txt(array('key'=>TEXT_PAGINATION_TITLE),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['actual_page']->value;?>
 <?php echo smarty_function_txt(array('key'=>TEXT_PAGINATION_FROM),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['last_page']->value;?>
 <?php if ($_smarty_tpl->tpl_vars['page']->value == 'categorie') {
echo smarty_function_txt(array('key'=>TEXT_PAGINATION_IN),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['page_name']->value;
}?></div><?php }
}
}
