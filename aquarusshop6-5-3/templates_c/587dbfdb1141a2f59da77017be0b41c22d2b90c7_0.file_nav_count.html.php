<?php
/* Smarty version 5.1.0, created on 2024-08-03 01:07:41
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/navigation/nav_count.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_66ad66bd707808_47099698',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '587dbfdb1141a2f59da77017be0b41c22d2b90c7' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/navigation/nav_count.html',
      1 => 1697144245,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_66ad66bd707808_47099698 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/navigation';
if ($_smarty_tpl->getValue('page_name') != '') {?><div><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PAGINATION_TITLE), $_smarty_tpl);?>
 <?php echo $_smarty_tpl->getValue('actual_page');?>
 <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PAGINATION_FROM), $_smarty_tpl);?>
 <?php echo $_smarty_tpl->getValue('last_page');?>
 <?php if ($_smarty_tpl->getValue('page') == 'categorie') {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PAGINATION_IN), $_smarty_tpl);?>
 <?php echo $_smarty_tpl->getValue('page_name');
}?></div><?php }
}
}
