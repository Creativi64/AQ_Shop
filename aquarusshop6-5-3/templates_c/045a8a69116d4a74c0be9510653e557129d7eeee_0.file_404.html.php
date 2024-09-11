<?php
/* Smarty version 5.1.0, created on 2024-09-09 21:01:04
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/404.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_66df45f098cad3_81882772',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '045a8a69116d4a74c0be9510653e557129d7eeee' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/404.html',
      1 => 1697144119,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:xtCore/pages/search.html' => 1,
  ),
))) {
function content_66df45f098cad3_81882772 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages';
?><h1><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PAGE_NOT_FOUND), $_smarty_tpl);?>
</h1>

<p class="alert alert-warning"><span class="glyphicon glyphicon-question-sign"></span>&nbsp;&nbsp;<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PAGE_NOT_FOUND_INTRO), $_smarty_tpl);?>
</p>


<?php $_smarty_tpl->renderSubTemplate("file:xtCore/pages/search.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('type'=>"tpl_include"), (int) 0, $_smarty_current_dir);
}
}
