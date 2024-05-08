<?php
/* Smarty version 4.3.2, created on 2024-04-20 23:42:34
  from '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/404.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_662436ca901e31_52709447',
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
),false)) {
function content_662436ca901e31_52709447 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),));
?>
<h1><?php echo smarty_function_txt(array('key'=>TEXT_PAGE_NOT_FOUND),$_smarty_tpl);?>
</h1>

<p class="alert alert-warning"><span class="glyphicon glyphicon-question-sign"></span>&nbsp;&nbsp;<?php echo smarty_function_txt(array('key'=>TEXT_PAGE_NOT_FOUND_INTRO),$_smarty_tpl);?>
</p>


<?php $_smarty_tpl->_subTemplateRender("file:xtCore/pages/search.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('type'=>"tpl_include"), 0, false);
}
}
