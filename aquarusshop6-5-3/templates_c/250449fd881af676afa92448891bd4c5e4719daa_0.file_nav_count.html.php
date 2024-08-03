<?php
/* Smarty version 5.1.0, created on 2024-08-03 01:15:03
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/navigation/nav_count.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_66ad687764f682_74725297',
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
))) {
function content_66ad687764f682_74725297 (\Smarty\Template $_smarty_tpl) {
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
