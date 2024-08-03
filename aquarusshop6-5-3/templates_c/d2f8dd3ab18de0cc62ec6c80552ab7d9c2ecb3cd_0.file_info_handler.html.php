<?php
/* Smarty version 5.1.0, created on 2024-08-03 01:14:37
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/info_handler.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_66ad685daaea80_02156780',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd2f8dd3ab18de0cc62ec6c80552ab7d9c2ecb3cd' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/info_handler.html',
      1 => 1697144119,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_66ad685daaea80_02156780 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages';
if ($_smarty_tpl->getValue('error')) {?>
    <div class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('error'), 'error_data', false, NULL, 'aussen', array (
));
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('error_data')->value) {
$foreach0DoElse = false;
?>
            <p class="item"><span class="glyphicon glyphicon-exclamation-sign"></span>&nbsp;&nbsp;<?php echo $_smarty_tpl->getValue('error_data')['info_message'];?>
</p>
        <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
    </div>
<?php }?>

<?php if ($_smarty_tpl->getValue('warning')) {?>
    <div class="alert alert-warning alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('warning'), 'warning_data', false, NULL, 'aussen', array (
));
$foreach1DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('warning_data')->value) {
$foreach1DoElse = false;
?>
            <p class="item"><span class="glyphicon glyphicon-bullhorn"></span>&nbsp;&nbsp;<?php echo $_smarty_tpl->getValue('warning_data')['info_message'];?>
</p>
        <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
    </div>
<?php }?>

<?php if ($_smarty_tpl->getValue('success')) {?>
    <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('success'), 'success_data', false, NULL, 'aussen', array (
));
$foreach2DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('success_data')->value) {
$foreach2DoElse = false;
?>
            <p class="item"><span class="glyphicon glyphicon-ok"></span>&nbsp;&nbsp;<?php echo $_smarty_tpl->getValue('success_data')['info_message'];?>
</p>
        <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
    </div>
<?php }?>

<?php if ($_smarty_tpl->getValue('info')) {?>
    <div class="alert alert-info alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
	    <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('info'), 'info_data', false, NULL, 'aussen', array (
));
$foreach3DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('info_data')->value) {
$foreach3DoElse = false;
?>
            <p class="item"><span class="glyphicon glyphicon-pushpin"></span>&nbsp;&nbsp;<?php echo $_smarty_tpl->getValue('info_data')['info_message'];?>
</p>
	    <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
    </div>
<?php }
}
}
