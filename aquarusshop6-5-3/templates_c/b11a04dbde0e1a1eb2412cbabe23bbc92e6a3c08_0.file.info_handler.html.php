<?php
/* Smarty version 4.3.0, created on 2023-10-08 04:18:35
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive_AQ/xtCore/pages/info_handler.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.0',
  'unifunc' => 'content_6522117ba8c265_29889504',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'b11a04dbde0e1a1eb2412cbabe23bbc92e6a3c08' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive_AQ/xtCore/pages/info_handler.html',
      1 => 1691797589,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6522117ba8c265_29889504 (Smarty_Internal_Template $_smarty_tpl) {
if ($_smarty_tpl->tpl_vars['error']->value) {?>
    <div class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['error']->value, 'error_data', false, NULL, 'aussen', array (
));
$_smarty_tpl->tpl_vars['error_data']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['error_data']->value) {
$_smarty_tpl->tpl_vars['error_data']->do_else = false;
?>
            <p class="item"><span class="glyphicon glyphicon-exclamation-sign"></span>&nbsp;&nbsp;<?php echo $_smarty_tpl->tpl_vars['error_data']->value['info_message'];?>
</p>
        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    </div>
<?php }?>

<?php if ($_smarty_tpl->tpl_vars['warning']->value) {?>
    <div class="alert alert-warning alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['warning']->value, 'warning_data', false, NULL, 'aussen', array (
));
$_smarty_tpl->tpl_vars['warning_data']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['warning_data']->value) {
$_smarty_tpl->tpl_vars['warning_data']->do_else = false;
?>
            <p class="item"><span class="glyphicon glyphicon-bullhorn"></span>&nbsp;&nbsp;<?php echo $_smarty_tpl->tpl_vars['warning_data']->value['info_message'];?>
</p>
        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    </div>
<?php }?>

<?php if ($_smarty_tpl->tpl_vars['success']->value) {?>
    <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['success']->value, 'success_data', false, NULL, 'aussen', array (
));
$_smarty_tpl->tpl_vars['success_data']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['success_data']->value) {
$_smarty_tpl->tpl_vars['success_data']->do_else = false;
?>
            <p class="item"><span class="glyphicon glyphicon-ok"></span>&nbsp;&nbsp;<?php echo $_smarty_tpl->tpl_vars['success_data']->value['info_message'];?>
</p>
        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    </div>
<?php }?>

<?php if ($_smarty_tpl->tpl_vars['info']->value) {?>
    <div class="alert alert-info alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
	    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['info']->value, 'info_data', false, NULL, 'aussen', array (
));
$_smarty_tpl->tpl_vars['info_data']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['info_data']->value) {
$_smarty_tpl->tpl_vars['info_data']->do_else = false;
?>
            <p class="item"><span class="glyphicon glyphicon-pushpin"></span>&nbsp;&nbsp;<?php echo $_smarty_tpl->tpl_vars['info_data']->value['info_message'];?>
</p>
	    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    </div>
<?php }
}
}
