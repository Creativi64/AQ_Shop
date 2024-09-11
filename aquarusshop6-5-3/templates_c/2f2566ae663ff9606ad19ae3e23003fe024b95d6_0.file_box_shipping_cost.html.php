<?php
/* Smarty version 5.1.0, created on 2024-09-10 08:53:21
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes/box_shipping_cost.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_66dfece1ef31f7_42188754',
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
))) {
function content_66dfece1ef31f7_42188754 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes';
?><div class="shipping-preview">
	<p class="headline"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SHIPPING_COST_PREVIEW), $_smarty_tpl);?>
</p>

	<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'form','name'=>'coupons_calc','action'=>'dynamic','link_params'=>'getParams','method'=>'post'), $_smarty_tpl);?>

	<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'hidden','name'=>'action','value'=>'select_shipping_cost'), $_smarty_tpl);?>

	<table class="table table-condensed table-bordered">
		<tr> <td class="border-L"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SHIPPING_COST_PREVIEW_COUNTRY), $_smarty_tpl);?>
:</td>
			<td class="border-L text-right">
				<?php if ($_smarty_tpl->getValue('count_countries') == 1) {?>
				<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('coupons_country'), 'item_data', false, 'key_data');
$foreach2DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('key_data')->value => $_smarty_tpl->getVariable('item_data')->value) {
$foreach2DoElse = false;
?>
				<input type="hidden" value="<?php echo $_smarty_tpl->getValue('item_data')['countries_iso_code_2'];?>
"><?php echo $_smarty_tpl->getValue('item_data')['countries_name'];?>

				<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
				<?php } else { ?>
				<select class="form-control show-tick" name="coupons_country"  onchange="this.form.submit();" style="width:200px;">
					<option value=""><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SHIPPING_COST_SELECT_COUNTRY), $_smarty_tpl);?>
</option>
					<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('coupons_country'), 'item_data', false, 'key_data');
$foreach3DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('key_data')->value => $_smarty_tpl->getVariable('item_data')->value) {
$foreach3DoElse = false;
?>
					<option value="<?php echo $_smarty_tpl->getValue('item_data')['countries_iso_code_2'];?>
" <?php if ($_smarty_tpl->getValue('item_data')['countries_iso_code_2'] == $_smarty_tpl->getValue('selected_country')) {?> selected="selected" <?php }?> ><?php echo $_smarty_tpl->getValue('item_data')['countries_name'];?>
</option>
					<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
				</select>
				<?php }?>
			</td>
		</tr>
		<?php if ($_smarty_tpl->getValue('count_shipping') != 0) {?>
		<tr>
			<td class="border-L"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SHIPPING_COST_PREVIEW_SHIPPING), $_smarty_tpl);?>
:</td>
			<td class="border-L text-right">
				<?php if ($_smarty_tpl->getValue('count_shipping') == 1) {?>
				<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('coupons_shipping'), 'item_data', false, 'key_data');
$foreach4DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('key_data')->value => $_smarty_tpl->getVariable('item_data')->value) {
$foreach4DoElse = false;
?>
				<input type="hidden" value="<?php echo $_smarty_tpl->getValue('item_data')['shipping_name'];?>
"><?php echo $_smarty_tpl->getValue('item_data')['shipping_name'];?>

				<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
				<?php } else { ?>
				<select class="form-control show-tick" name="coupons_shipping"  onchange="this.form.submit();" style="width:200px;">
					<option value=""><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SHIPPING_COST_SELECT_SHIPPING), $_smarty_tpl);?>
</option>
					<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('coupons_shipping'), 'item_data', false, 'key_data');
$foreach5DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('key_data')->value => $_smarty_tpl->getVariable('item_data')->value) {
$foreach5DoElse = false;
?>
					<option value="<?php echo $_smarty_tpl->getValue('item_data')['shipping_code'];?>
" <?php if ($_smarty_tpl->getValue('item_data')['shipping_code'] == $_smarty_tpl->getValue('selected_coupons_shipping')) {?> selected="selected" <?php }?> ><?php echo $_smarty_tpl->getValue('item_data')['shipping_name'];?>
</option>
					<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
				</select>
				<?php }?>
			</td>
		</tr>
		<?php }?>
		<tr>
			<td><b><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SHIPPING_COST_PREVIEW_SHIPPINGPRICE), $_smarty_tpl);?>
:</b></td>
			<td class="text-right"><b><?php echo $_smarty_tpl->getValue('cost');?>
</b></td>
	</table>
	<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'formend'), $_smarty_tpl);?>

</div><?php }
}
