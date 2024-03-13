<?php
/* Smarty version 4.3.2, created on 2024-03-13 18:01:06
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive_AQ/xtCore/pages/navigation/nav_count.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_65f1dbd2067e68_93421490',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'b894ca963183ed386d836ea3264f52076b6c871a' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive_AQ/xtCore/pages/navigation/nav_count.html',
      1 => 1697144245,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_65f1dbd2067e68_93421490 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),));
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
