<?php
/* Smarty version 4.3.0, created on 2023-10-08 03:48:11
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive_AQ/xtCore/pages/404.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.0',
  'unifunc' => 'content_65220a5ba2b522_42746334',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'bf76a6a11dc74ab360dc3dca3eb13a2167cf23fe' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive_AQ/xtCore/pages/404.html',
      1 => 1691797589,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:xtCore/pages/search.html' => 1,
  ),
),false)) {
function content_65220a5ba2b522_42746334 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),));
?>
<h1><?php echo smarty_function_txt(array('key'=>TEXT_PAGE_NOT_FOUND),$_smarty_tpl);?>
</h1>

<p class="alert alert-warning"><span class="glyphicon glyphicon-question-sign"></span>&nbsp;&nbsp;<?php echo smarty_function_txt(array('key'=>TEXT_PAGE_NOT_FOUND_INTRO),$_smarty_tpl);?>
</p>


<?php $_smarty_tpl->_subTemplateRender("file:xtCore/pages/search.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('type'=>"tpl_include"), 0, false);
}
}
