<?php
/* Smarty version 4.3.2, created on 2024-04-21 11:39:16
  from '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes/box_shipping_cost.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_6624dec41ab125_30603576',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2f2566ae663ff9606ad19ae3e23003fe024b95d6' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes/box_shipping_cost.html',
      1 => 1697144118,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6624dec41ab125_30603576 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.form.php','function'=>'smarty_function_form',),));
?>
<div class="shipping-preview">
	<p class="headline"><?php echo smarty_function_txt(array('key'=>TEXT_SHIPPING_COST_PREVIEW),$_smarty_tpl);?>
</p>

	<?php echo smarty_function_form(array('type'=>'form','name'=>'coupons_calc','action'=>'dynamic','link_params'=>'getParams','method'=>'post'),$_smarty_tpl);?>

	<?php echo smarty_function_form(array('type'=>'hidden','name'=>'action','value'=>'select_shipping_cost'),$_smarty_tpl);?>

	<table class="table table-condensed table-bordered">
		<tr> <td class="border-L"><?php echo smarty_function_txt(array('key'=>TEXT_SHIPPING_COST_PREVIEW_COUNTRY),$_smarty_tpl);?>
:</td>
			<td class="border-L text-right">
				<?php if ($_smarty_tpl->tpl_vars['count_countries']->value == 1) {?>
				<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['coupons_country']->value, 'item_data', false, 'key_data');
$_smarty_tpl->tpl_vars['item_data']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key_data']->value => $_smarty_tpl->tpl_vars['item_data']->value) {
$_smarty_tpl->tpl_vars['item_data']->do_else = false;
?>
				<input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['item_data']->value['countries_iso_code_2'];?>
"><?php echo $_smarty_tpl->tpl_vars['item_data']->value['countries_name'];?>

				<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
				<?php } else { ?>
				<select class="form-control show-tick" name="coupons_country"  onchange="this.form.submit();" style="width:200px;">
					<option value=""><?php echo smarty_function_txt(array('key'=>TEXT_SHIPPING_COST_SELECT_COUNTRY),$_smarty_tpl);?>
</option>
					<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['coupons_country']->value, 'item_data', false, 'key_data');
$_smarty_tpl->tpl_vars['item_data']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key_data']->value => $_smarty_tpl->tpl_vars['item_data']->value) {
$_smarty_tpl->tpl_vars['item_data']->do_else = false;
?>
					<option value="<?php echo $_smarty_tpl->tpl_vars['item_data']->value['countries_iso_code_2'];?>
" <?php if ($_smarty_tpl->tpl_vars['item_data']->value['countries_iso_code_2'] == $_smarty_tpl->tpl_vars['selected_country']->value) {?> selected="selected" <?php }?> ><?php echo $_smarty_tpl->tpl_vars['item_data']->value['countries_name'];?>
</option>
					<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
				</select>
				<?php }?>
			</td>
		</tr>
		<?php if ($_smarty_tpl->tpl_vars['count_shipping']->value != 0) {?>
		<tr>
			<td class="border-L"><?php echo smarty_function_txt(array('key'=>TEXT_SHIPPING_COST_PREVIEW_SHIPPING),$_smarty_tpl);?>
:</td>
			<td class="border-L text-right">
				<?php if ($_smarty_tpl->tpl_vars['count_shipping']->value == 1) {?>
				<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['coupons_shipping']->value, 'item_data', false, 'key_data');
$_smarty_tpl->tpl_vars['item_data']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key_data']->value => $_smarty_tpl->tpl_vars['item_data']->value) {
$_smarty_tpl->tpl_vars['item_data']->do_else = false;
?>
				<input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['item_data']->value['shipping_name'];?>
"><?php echo $_smarty_tpl->tpl_vars['item_data']->value['shipping_name'];?>

				<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
				<?php } else { ?>
				<select class="form-control show-tick" name="coupons_shipping"  onchange="this.form.submit();" style="width:200px;">
					<option value=""><?php echo smarty_function_txt(array('key'=>TEXT_SHIPPING_COST_SELECT_SHIPPING),$_smarty_tpl);?>
</option>
					<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['coupons_shipping']->value, 'item_data', false, 'key_data');
$_smarty_tpl->tpl_vars['item_data']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key_data']->value => $_smarty_tpl->tpl_vars['item_data']->value) {
$_smarty_tpl->tpl_vars['item_data']->do_else = false;
?>
					<option value="<?php echo $_smarty_tpl->tpl_vars['item_data']->value['shipping_code'];?>
" <?php if ($_smarty_tpl->tpl_vars['item_data']->value['shipping_code'] == $_smarty_tpl->tpl_vars['selected_coupons_shipping']->value) {?> selected="selected" <?php }?> ><?php echo $_smarty_tpl->tpl_vars['item_data']->value['shipping_name'];?>
</option>
					<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
				</select>
				<?php }?>
			</td>
		</tr>
		<?php }?>
		<tr>
			<td><b><?php echo smarty_function_txt(array('key'=>TEXT_SHIPPING_COST_PREVIEW_SHIPPINGPRICE),$_smarty_tpl);?>
:</b></td>
			<td class="text-right"><b><?php echo $_smarty_tpl->tpl_vars['cost']->value;?>
</b></td>
	</table>
	<?php echo smarty_function_form(array('type'=>'formend'),$_smarty_tpl);?>

</div><?php }
}