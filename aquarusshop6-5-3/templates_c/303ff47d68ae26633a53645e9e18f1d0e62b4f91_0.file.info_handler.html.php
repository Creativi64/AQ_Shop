<?php
/* Smarty version 4.3.2, created on 2024-05-04 20:15:54
  from '/homepages/2/d41324517/htdocs/aquarus_shop/templates/__xtAdmin/xtCore/pages/info_handler.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_66367b5a718104_34154584',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '303ff47d68ae26633a53645e9e18f1d0e62b4f91' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/__xtAdmin/xtCore/pages/info_handler.html',
      1 => 1697144119,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_66367b5a718104_34154584 (Smarty_Internal_Template $_smarty_tpl) {
if ($_smarty_tpl->tpl_vars['error']->value) {?>
    <div class="alert alert-danger alert-dismissible" role="alert">
        <button onclick="parent.contentTabs.remove(parent.contentTabs.getActiveTab())" type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
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
        <button onclick="parent.contentTabs.remove(parent.contentTabs.getActiveTab())" type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
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
        <button onclick="parent.contentTabs.remove(parent.contentTabs.getActiveTab())" type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
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
        <button onclick="parent.contentTabs.remove(parent.contentTabs.getActiveTab())" type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
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
