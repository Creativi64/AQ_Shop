<?php
/* Smarty version 4.3.0, created on 2023-06-19 13:53:19
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive/xtCore/pages/navigation/nav_count.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.0',
  'unifunc' => 'content_649041afceb863_44569796',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '68bb54eaad848123014ae2a140c5da1aebcb1a3d' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive/xtCore/pages/navigation/nav_count.html',
      1 => 1687006139,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_649041afceb863_44569796 (Smarty_Internal_Template $_smarty_tpl) {
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
