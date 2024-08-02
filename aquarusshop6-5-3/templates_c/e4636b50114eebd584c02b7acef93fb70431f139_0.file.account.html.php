<?php
/* Smarty version 4.3.2, created on 2024-07-17 21:26:11
  from '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/account.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_66981ad34912d2_17907022',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'e4636b50114eebd584c02b7acef93fb70431f139' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/account.html',
      1 => 1697144119,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:xtCore/pages/order_history_block.html' => 1,
  ),
),false)) {
function content_66981ad34912d2_17907022 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.link.php','function'=>'smarty_function_link',),2=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.hook.php','function'=>'smarty_function_hook',),));
?>
<h1><?php echo smarty_function_txt(array('key'=>TEXT_PAGE_TITLE_ACCOUNT),$_smarty_tpl);?>
</h1>
<?php echo $_smarty_tpl->tpl_vars['message']->value;?>


<?php if ($_smarty_tpl->tpl_vars['registered_customer']->value != true) {?>
<p class="h3"><?php echo smarty_function_txt(array('key'=>TEXT_ACCOUNT_TITLE_WELCOME),$_smarty_tpl);?>
</p>
<p><?php echo smarty_function_txt(array('key'=>TEXT_ACCOUNT_WELCOME),$_smarty_tpl);?>
</p>
<p><a href="<?php echo smarty_function_link(array('page'=>'customer','paction'=>'login','conn'=>'SSL'),$_smarty_tpl);?>
" class="btn btn-success"><?php echo smarty_function_txt(array('key'=>TEXT_ACCOUNT_LOGIN),$_smarty_tpl);?>
</a></p>
<?php }?>

<?php if ($_smarty_tpl->tpl_vars['registered_customer']->value == true) {?>
<p class="h3"><?php echo smarty_function_txt(array('key'=>TEXT_ACCOUNT_TITLE),$_smarty_tpl);?>
</p>
<ul class="btn-list">
	<?php if ($_SESSION['customer']->customers_status != (defined('_STORE_CUSTOMERS_STATUS_ID_GUEST') ? constant('_STORE_CUSTOMERS_STATUS_ID_GUEST') : null) && $_SESSION['customer']->customers_status != 0) {?><li><a class="button" href="<?php echo smarty_function_link(array('page'=>'customer','paction'=>'edit_customer','conn'=>'SSL'),$_smarty_tpl);?>
"><?php echo smarty_function_txt(array('key'=>TEXT_ACCOUNT_EDIT),$_smarty_tpl);?>
</a></li><?php }?>
	<li><a class="button" href="<?php echo smarty_function_link(array('page'=>'customer','paction'=>'address_overview','conn'=>'SSL'),$_smarty_tpl);?>
"><?php echo smarty_function_txt(array('key'=>TEXT_ACCOUNT_ADRESSBOOK),$_smarty_tpl);?>
</a></li>
	<?php if ($_smarty_tpl->tpl_vars['show_gdpr_download']->value) {?><li><a class="button" href="<?php echo smarty_function_link(array('page'=>'customer','paction'=>'dsgvo_download','conn'=>'SSL'),$_smarty_tpl);?>
" download="dsgvo_report.xml"><?php echo smarty_function_txt(array('key'=>TEXT_DOWNLOAD_DSGVO_REPORT),$_smarty_tpl);?>
</a></li><?php }?>
	<?php echo smarty_function_hook(array('key'=>'account_tpl_account_data'),$_smarty_tpl);?>

</ul>
<?php }?>

<?php echo smarty_function_hook(array('key'=>'account_tpl_module_data'),$_smarty_tpl);?>


<?php if ($_smarty_tpl->tpl_vars['order_data']->value) {?>
<br />
<p class="h3"><?php echo smarty_function_txt(array('key'=>TEXT_ACCOUNT_ORDERS),$_smarty_tpl);?>
 <?php if ($_smarty_tpl->tpl_vars['order_data']->value) {?> & <?php echo smarty_function_txt(array('key'=>TEXT_ACCOUNT_DOWNLOADS),$_smarty_tpl);
}?></p>
<?php $_smarty_tpl->_subTemplateRender("file:xtCore/pages/order_history_block.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('type'=>"tpl_include"), 0, false);
}?>

<ul class="btn-list">
<?php if ($_smarty_tpl->tpl_vars['download_flag']->value == true) {?>
    <li><a class="btn btn-warning" href="<?php echo smarty_function_link(array('page'=>'customer','paction'=>'download_overview','conn'=>'SSL'),$_smarty_tpl);?>
"><span class="glyphicon glyphicon-download"></span> <?php echo smarty_function_txt(array('key'=>TEXT_ACCOUNT_DOWNLOADS_MORE),$_smarty_tpl);?>
</a></li>
<?php }
if ($_smarty_tpl->tpl_vars['order_data']->value) {?>
    <li><a class="btn btn-primary" href="<?php echo smarty_function_link(array('page'=>'customer','paction'=>'order_overview','conn'=>'SSL'),$_smarty_tpl);?>
"><span class="glyphicon glyphicon-time"></span> <?php echo smarty_function_txt(array('key'=>TEXT_ACCOUNT_ALL_ORDERS),$_smarty_tpl);?>
</a></li>
<?php }?>
</ul>

<div class="clearfix"></div>

<?php echo smarty_function_hook(array('key'=>'account_tpl_module_data_bottom'),$_smarty_tpl);?>

<?php }
}
