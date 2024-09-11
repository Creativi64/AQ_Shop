<?php
/* Smarty version 5.1.0, created on 2024-09-10 14:06:52
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/account.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_66e0365c4756d8_76432967',
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
))) {
function content_66e0365c4756d8_76432967 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages';
?><h1><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PAGE_TITLE_ACCOUNT), $_smarty_tpl);?>
</h1>
<?php echo $_smarty_tpl->getValue('message');?>


<?php if ($_smarty_tpl->getValue('registered_customer') != true) {?>
<p class="h3"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_ACCOUNT_TITLE_WELCOME), $_smarty_tpl);?>
</p>
<p><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_ACCOUNT_WELCOME), $_smarty_tpl);?>
</p>
<p><a href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'customer','paction'=>'login','conn'=>'SSL'), $_smarty_tpl);?>
" class="btn btn-success"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_ACCOUNT_LOGIN), $_smarty_tpl);?>
</a></p>
<?php }?>

<?php if ($_smarty_tpl->getValue('registered_customer') == true) {?>
<p class="h3"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_ACCOUNT_TITLE), $_smarty_tpl);?>
</p>
<ul class="btn-list">
	<?php if ($_SESSION['customer']->customers_status != (defined('_STORE_CUSTOMERS_STATUS_ID_GUEST') ? constant('_STORE_CUSTOMERS_STATUS_ID_GUEST') : null) && $_SESSION['customer']->customers_status != 0) {?><li><a class="button" href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'customer','paction'=>'edit_customer','conn'=>'SSL'), $_smarty_tpl);?>
"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_ACCOUNT_EDIT), $_smarty_tpl);?>
</a></li><?php }?>
	<li><a class="button" href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'customer','paction'=>'address_overview','conn'=>'SSL'), $_smarty_tpl);?>
"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_ACCOUNT_ADRESSBOOK), $_smarty_tpl);?>
</a></li>
	<?php if ($_smarty_tpl->getValue('show_gdpr_download')) {?><li><a class="button" href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'customer','paction'=>'dsgvo_download','conn'=>'SSL'), $_smarty_tpl);?>
" download="dsgvo_report.xml"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_DOWNLOAD_DSGVO_REPORT), $_smarty_tpl);?>
</a></li><?php }?>
	<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'account_tpl_account_data'), $_smarty_tpl);?>

</ul>
<?php }?>

<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'account_tpl_module_data'), $_smarty_tpl);?>


<?php if ($_smarty_tpl->getValue('order_data')) {?>
<br />
<p class="h3"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_ACCOUNT_ORDERS), $_smarty_tpl);?>
 <?php if ($_smarty_tpl->getValue('order_data')) {?> & <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_ACCOUNT_DOWNLOADS), $_smarty_tpl);
}?></p>
<?php $_smarty_tpl->renderSubTemplate("file:xtCore/pages/order_history_block.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('type'=>"tpl_include"), (int) 0, $_smarty_current_dir);
}?>

<ul class="btn-list">
<?php if ($_smarty_tpl->getValue('download_flag') == true) {?>
    <li><a class="btn btn-warning" href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'customer','paction'=>'download_overview','conn'=>'SSL'), $_smarty_tpl);?>
"><span class="glyphicon glyphicon-download"></span> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_ACCOUNT_DOWNLOADS_MORE), $_smarty_tpl);?>
</a></li>
<?php }
if ($_smarty_tpl->getValue('order_data')) {?>
    <li><a class="btn btn-primary" href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'customer','paction'=>'order_overview','conn'=>'SSL'), $_smarty_tpl);?>
"><span class="glyphicon glyphicon-time"></span> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_ACCOUNT_ALL_ORDERS), $_smarty_tpl);?>
</a></li>
<?php }?>
</ul>

<div class="clearfix"></div>

<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'account_tpl_module_data_bottom'), $_smarty_tpl);?>

<?php }
}
