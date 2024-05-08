<?php
/* Smarty version 4.3.2, created on 2024-05-04 14:40:28
  from '/homepages/2/d41324517/htdocs/aquarus_shop/templates/__xtAdmin/xtCore/pages/order.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_66362cbc0e9ac4_56552461',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '7bce99085b1ccd1fe8c767f69d836565da4fbcc2' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/__xtAdmin/xtCore/pages/order.html',
      1 => 1697144119,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_66362cbc0e9ac4_56552461 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),));
?>

<!-- Content Wrapper. Contains page content -->
<div class="xcontent-wrapper">
	<!-- Content Header (Page header) -->


	<!-- Main content -->
	<section class="invoice">
		<!-- title row -->
		<div class="row">
			<div class="col-xs-12">
				<h2 class="page-header">
					<i class="fas fa-shopping-cart"></i> <?php echo smarty_function_txt(array('key'=>TEXT_ORDER_ID),$_smarty_tpl);?>
:
					<?php echo $_smarty_tpl->tpl_vars['order_data']->value['orders_id'];?>
 | <?php echo smarty_function_txt(array('key'=>TEXT_ORDERS_STATUS),$_smarty_tpl);?>
:
					<?php echo $_smarty_tpl->tpl_vars['order_data']->value['orders_status'];?>
 <span class="pull-right"><?php echo $_smarty_tpl->tpl_vars['order_data']->value['date_purchased'];?>
</span>
				</h2>
			</div>
			<!-- /.col -->
		</div>
		<!-- info row -->
		<div class="row invoice-info">
			<div class="col-sm-3 invoice-col">
				<b><?php echo smarty_function_txt(array('key'=>TEXT_BILLING_ADDRESS),$_smarty_tpl);?>
</b>
				<address>
					<?php if ($_smarty_tpl->tpl_vars['order_data']->value['billing_company']) {?> <?php echo $_smarty_tpl->tpl_vars['order_data']->value['billing_company'];?>
<br /><?php }?>
					<?php if ($_smarty_tpl->tpl_vars['order_data']->value['billing_company_2']) {?> <?php echo $_smarty_tpl->tpl_vars['order_data']->value['billing_company_2'];?>
<br /><?php }?>
					<?php if ($_smarty_tpl->tpl_vars['order_data']->value['billing_company_3']) {?> <?php echo $_smarty_tpl->tpl_vars['order_data']->value['billing_company_3'];?>
<br /><?php }?>
					<?php echo $_smarty_tpl->tpl_vars['order_data']->value['billing_title'];?>
 <?php echo $_smarty_tpl->tpl_vars['order_data']->value['billing_firstname'];?>

					<?php echo $_smarty_tpl->tpl_vars['order_data']->value['billing_lastname'];?>
<br />
					<?php echo $_smarty_tpl->tpl_vars['order_data']->value['billing_street_address'];?>
<br />
					<?php if ($_smarty_tpl->tpl_vars['order_data']->value['billing_address_addition']) {
echo $_smarty_tpl->tpl_vars['order_data']->value['billing_address_addition'];?>
<br /><?php }?>
					<?php if ($_smarty_tpl->tpl_vars['order_data']->value['billing_suburb']) {?> <?php echo $_smarty_tpl->tpl_vars['order_data']->value['billing_suburb'];?>
<br /><?php }?>
					<?php echo $_smarty_tpl->tpl_vars['order_data']->value['billing_postcode'];?>
 <?php echo $_smarty_tpl->tpl_vars['order_data']->value['billing_city'];?>
<br />
					<?php echo $_smarty_tpl->tpl_vars['order_data']->value['billing_country'];?>
 (<?php echo $_smarty_tpl->tpl_vars['order_data']->value['billing_country_code'];?>
)<br />
					<?php if ($_smarty_tpl->tpl_vars['order_data']->value['billing_phone']) {?>
					<b><?php echo smarty_function_txt(array('key'=>TEXT_BILLING_PHONE),$_smarty_tpl);?>
</b> <?php ob_start();
echo $_smarty_tpl->tpl_vars['order_data']->value['billing_phone'];
$_prefixVariable1 = ob_get_clean();
echo $_prefixVariable1;?>
<br />
					<?php }?>
				</address>
			</div>
			<!-- /.col -->
			<div class="col-sm-3 invoice-col">
				<b><?php echo smarty_function_txt(array('key'=>TEXT_DELIVERY_ADDRESS),$_smarty_tpl);?>
</b>
				<address>
					<?php if ($_smarty_tpl->tpl_vars['order_data']->value['delivery_company']) {?> <?php echo $_smarty_tpl->tpl_vars['order_data']->value['delivery_company'];?>
<br /><?php }?>
					<?php if ($_smarty_tpl->tpl_vars['order_data']->value['delivery_company_2']) {?>
					<?php echo $_smarty_tpl->tpl_vars['order_data']->value['delivery_company_2'];?>
<br /><?php }?> <?php if ($_smarty_tpl->tpl_vars['order_data']->value['delivery_company_3']) {?> <?php echo $_smarty_tpl->tpl_vars['order_data']->value['delivery_company_3'];?>
<br /><?php }?>
					<?php echo $_smarty_tpl->tpl_vars['order_data']->value['delivery_title'];?>
 <?php echo $_smarty_tpl->tpl_vars['order_data']->value['delivery_firstname'];?>

					<?php echo $_smarty_tpl->tpl_vars['order_data']->value['delivery_lastname'];?>
<br />
					<?php echo $_smarty_tpl->tpl_vars['order_data']->value['delivery_street_address'];?>
<br />
					<?php if ($_smarty_tpl->tpl_vars['order_data']->value['delivery_address_addition']) {
echo $_smarty_tpl->tpl_vars['order_data']->value['delivery_address_addition'];?>
<br /><?php }?>
					<?php if ($_smarty_tpl->tpl_vars['order_data']->value['delivery_suburb']) {?> <?php echo $_smarty_tpl->tpl_vars['order_data']->value['delivery_suburb'];?>
<br /><?php }?>
					<?php echo $_smarty_tpl->tpl_vars['order_data']->value['delivery_postcode'];?>
 <?php echo $_smarty_tpl->tpl_vars['order_data']->value['delivery_city'];?>
<br />
					<?php echo $_smarty_tpl->tpl_vars['order_data']->value['delivery_country'];?>
 (<?php echo $_smarty_tpl->tpl_vars['order_data']->value['delivery_country_code'];?>
)<br />
				</address>
			</div>
			<!-- /.col -->
			<div class="col-sm-3 invoice-col">
				<?php if ($_smarty_tpl->tpl_vars['order_customer']->value['customers_dob']) {?>
					<b><?php echo smarty_function_txt(array('key'=>TEXT_CUSTOMERS_DOB),$_smarty_tpl);?>
:</b> <?php echo $_smarty_tpl->tpl_vars['order_customer']->value['customers_dob'];?>
 <span <?php if ($_smarty_tpl->tpl_vars['order_customer']->value['customers_age'] < $_smarty_tpl->tpl_vars['minAge']->value) {?>class="warning"<?php }?>>(<?php echo $_smarty_tpl->tpl_vars['order_customer']->value['customers_age'];?>
 <?php echo smarty_function_txt(array('key'=>TEXT_YEARS),$_smarty_tpl);?>
)</span><br />
				<?php }?>
				<b><?php echo smarty_function_txt(array('key'=>'TEXT_customers_email_address'),$_smarty_tpl);?>
: </b><a href="mailto:<?php echo $_smarty_tpl->tpl_vars['order_data']->value['customers_email_address'];?>
"><?php echo $_smarty_tpl->tpl_vars['order_data']->value['customers_email_address'];?>
</a><br />
				<b><?php echo smarty_function_txt(array('key'=>TABTEXT_CUSTOMERS_STATUS),$_smarty_tpl);?>
 (<?php echo smarty_function_txt(array('key'=>TEXT_ORDER),$_smarty_tpl);?>
): </b><?php echo $_smarty_tpl->tpl_vars['order_data']->value['customers_status_name'];?>
<br />
				<b><?php echo smarty_function_txt(array('key'=>TABTEXT_CUSTOMERS_STATUS),$_smarty_tpl);?>
: </b><?php ob_start();
echo $_smarty_tpl->tpl_vars['order_customer']->value['customers_status_name'];
$_prefixVariable2 = ob_get_clean();
echo $_prefixVariable2;?>
<br />
				<?php if ($_smarty_tpl->tpl_vars['order_data']->value['delivery_phone']) {?>
					<b><?php echo smarty_function_txt(array('key'=>TEXT_PHONE),$_smarty_tpl);?>
: </b><?php echo $_smarty_tpl->tpl_vars['order_data']->value['delivery_phone'];?>
<br />
				<?php }?>
				<?php if ($_smarty_tpl->tpl_vars['order_data']->value['delivery_mobile_phone']) {?>
					<b><?php echo smarty_function_txt(array('key'=>TEXT_MOBILE_PHONE),$_smarty_tpl);?>
: </b><?php echo $_smarty_tpl->tpl_vars['order_data']->value['delivery_mobile_phone'];?>
<br />
				<?php }?>
				<?php if ($_smarty_tpl->tpl_vars['order_data']->value['customers_cid']) {?>
					<b><?php echo smarty_function_txt(array('key'=>TEXT_CUSTOMERS_CID),$_smarty_tpl);?>
: </b><?php echo $_smarty_tpl->tpl_vars['order_data']->value['customers_cid'];?>
<br />
				<?php }?>
				<?php if ($_smarty_tpl->tpl_vars['order_data']->value['customers_vat_id']) {?>
					<b><?php echo smarty_function_txt(array('key'=>TEXT_CUSTOMERS_VAT_ID),$_smarty_tpl);?>
: </b><?php echo $_smarty_tpl->tpl_vars['order_data']->value['customers_vat_id'];?>
<br />
				<?php }?>
				<b><?php echo smarty_function_txt(array('key'=>TEXT_STORE_LANGUAGE),$_smarty_tpl);?>
: </b><?php ob_start();
echo $_smarty_tpl->tpl_vars['order_data']->value['language_text'];
$_prefixVariable3 = ob_get_clean();
echo $_prefixVariable3;?>
<br />
				<?php if ($_smarty_tpl->tpl_vars['order_data']->value['billing_mobile_phone']) {?>
					<b><?php echo smarty_function_txt(array('key'=>TEXT_BILLING_MOBILE_PHONE),$_smarty_tpl);?>
: </b><?php ob_start();
echo $_smarty_tpl->tpl_vars['order_data']->value['billing_mobile_phone'];
$_prefixVariable4 = ob_get_clean();
echo $_prefixVariable4;?>
<br />
				<?php }?>
				<b><?php echo smarty_function_txt(array('key'=>"HEADING_CUSTOMER"),$_smarty_tpl);?>
-ID: </b>
				<?php if ($_smarty_tpl->tpl_vars['order_data']->value['customers_id'] > 0) {?>
				<?php echo $_smarty_tpl->tpl_vars['order_data']->value['customers_id'];?>

				&nbsp;
				<a href="javascript:void(0)" onclick="openCustomersDetails(<?php echo $_smarty_tpl->tpl_vars['order_data']->value['customers_id'];?>
)"><i class="fas fa-info-circle" qtip="<?php echo smarty_function_txt(array('key'=>'TEXT_CUSTOMER_DETAILS'),$_smarty_tpl);?>
"></i></a>
				&nbsp;
				<a href="javascript:void(0)" onclick="openCustomersOrders(<?php echo $_smarty_tpl->tpl_vars['order_data']->value['customers_id'];?>
)"><i class="fas fa-shopping-cart" qtip="<?php echo smarty_function_txt(array('key'=>'HEADING_ORDER'),$_smarty_tpl);?>
"></i></a>
				&nbsp;
				<a href="javascript:void(0)" onclick="openCustomersAddresses(<?php echo $_smarty_tpl->tpl_vars['order_data']->value['customers_id'];?>
)"><i class="fas fa-address-book" qtip="<?php echo smarty_function_txt(array('key'=>'HEADING_ADDRESS'),$_smarty_tpl);?>
"></i></a>
				<?php } else { ?> no customers ID ??
				<?php }?>
				<br />
				
			</div>
			<!-- /.col -->
			<!-- /.col -->
			<div class="col-sm-3 invoice-col">
				<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['order_data']->value['order_info_options'], 'info', false, NULL, 'aussen', array (
));
$_smarty_tpl->tpl_vars['info']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['info']->value) {
$_smarty_tpl->tpl_vars['info']->do_else = false;
?>
					<?php if ($_smarty_tpl->tpl_vars['info']->value['topic']) {?>
                    	<?php echo $_smarty_tpl->tpl_vars['info']->value['text'];?>
<br />
                    <?php } elseif ($_smarty_tpl->tpl_vars['info']->value['msgCls']) {?>
						<span class="admin-order-info <?php echo $_smarty_tpl->tpl_vars['info']->value['msgCls'];?>
" ><?php echo $_smarty_tpl->tpl_vars['info']->value['text'];?>
</span><br />
					<?php } else { ?>
                    	<b><?php echo $_smarty_tpl->tpl_vars['info']->value['text'];?>
:</b> <?php echo $_smarty_tpl->tpl_vars['info']->value['value'];?>
<br />
                    <?php }?>
				<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
			</div>
			<!-- /.col -->
		</div>
		<!-- /.row -->

		<!-- Table row -->
		<div class="row">
			<div class="col-xs-12 table-responsive">
				<table class="table table-striped">
					<thead>
						<tr>
							<th><?php echo smarty_function_txt(array('key'=>TEXT_PRODUCTS_QUANTITY),$_smarty_tpl);?>
</th>
							<th><?php echo smarty_function_txt(array('key'=>TEXT_PRODUCTS_NAME),$_smarty_tpl);?>
</th>
							<th><?php echo smarty_function_txt(array('key'=>TEXT_PRODUCTS_MODEL),$_smarty_tpl);?>
</th>
							<th><?php echo smarty_function_txt(array('key'=>TEXT_PRODUCTS_SINGLEPRICE),$_smarty_tpl);?>
</th>
							<th><?php echo smarty_function_txt(array('key'=>TEXT_PRODUCTS_DISCOUNT),$_smarty_tpl);?>
</th>
							<th><span class="right"><?php echo smarty_function_txt(array('key'=>TEXT_PRODUCTS_FINALPRICE),$_smarty_tpl);?>
</span></th>
						</tr>
					</thead>
					<tbody>
						<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['order_products']->value, 'data', false, NULL, 'aussen', array (
));
$_smarty_tpl->tpl_vars['data']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['data']->value) {
$_smarty_tpl->tpl_vars['data']->do_else = false;
?>
						<tr>
							<td><?php echo $_smarty_tpl->tpl_vars['data']->value['products_quantity'];?>
</td>
							<td><a href="<?php echo $_smarty_tpl->tpl_vars['data']->value['cart_product']['products_link'];?>
" onclick="navigator.clipboard.writeText(this.href); this.classList.add(`blink-fast`); return false" style="font-size: 1.2em;color:inherit"><i ext:qtip="<?php echo smarty_function_txt(array('key'=>TEXT_COPY_ADDRESS),$_smarty_tpl);?>
"  class="far fa-clipboard"></i></a>&nbsp;&nbsp;<a href="<?php echo $_smarty_tpl->tpl_vars['data']->value['cart_product']['products_link'];?>
" target="_blank" style="color:inherit"><?php echo $_smarty_tpl->tpl_vars['data']->value['products_name'];?>
</a></td>
							<td><?php echo $_smarty_tpl->tpl_vars['data']->value['products_model'];?>
</td>
							<td><?php echo $_smarty_tpl->tpl_vars['data']->value['products_price']['formated'];
if ($_smarty_tpl->tpl_vars['data']->value['products_price_before_discount']) {?><br /><span class="price-old"><?php echo $_smarty_tpl->tpl_vars['data']->value['products_price_before_discount'];?>
</span><?php }?></td>
							<td><?php if ($_smarty_tpl->tpl_vars['data']->value['products_discount'] > 0) {
echo $_smarty_tpl->tpl_vars['data']->value['products_discount'];?>

								%<?php } else { ?>0 %<?php }?></td>
							<td><span class="right"><?php echo $_smarty_tpl->tpl_vars['data']->value['products_final_price']['data']['PRICE']['formated'];?>
</span></td>
						</tr>
						<?php echo $_smarty_tpl->tpl_vars['data']->value['products_information']['content_admin'];?>
 <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
					</tbody>
				</table>
			</div>
			<!-- /.col -->
		</div>
		<!-- /.row -->

		<div class="row">
			<!-- accepted payments column -->
			<div class="col-xs-6">
				<b><?php echo smarty_function_txt(array('key'=>TEXT_PAYMENT_CODE),$_smarty_tpl);?>
:</b> <?php ob_start();
echo $_smarty_tpl->tpl_vars['order_data']->value['payment_code'];
$_prefixVariable5 = ob_get_clean();
echo $_prefixVariable5;?>
<br />
				<b><?php echo smarty_function_txt(array('key'=>TEXT_SHIPPING_CODE),$_smarty_tpl);?>
:</b> <?php ob_start();
echo $_smarty_tpl->tpl_vars['order_data']->value['shipping_code'];
$_prefixVariable6 = ob_get_clean();
echo $_prefixVariable6;?>
<br />
			</div>
			<!-- /.col -->
			<div class="col-xs-6">
				<div class="table-responsive" id="order_total">
					<table class="table">
						<tr>
							<th style="width: 50%"><?php echo smarty_function_txt(array('key'=>TEXT_PRODUCT_TOTAL),$_smarty_tpl);?>
:</th>
							<td><?php echo $_smarty_tpl->tpl_vars['order_total']->value['product_total']['formated'];?>
</td>
						</tr>

						<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['order_total_data']->value, 'sub_data', false, NULL, 'aussen', array (
));
$_smarty_tpl->tpl_vars['sub_data']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['sub_data']->value) {
$_smarty_tpl->tpl_vars['sub_data']->do_else = false;
?>
						<tr>
							<td><?php echo $_smarty_tpl->tpl_vars['sub_data']->value['orders_total_name'];?>
:</td>
							<td><?php echo $_smarty_tpl->tpl_vars['sub_data']->value['orders_total_price']['formated'];?>
</td>
						</tr>
						<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['order_total']->value['total_tax'], 'tax_data', false, NULL, 'aussen', array (
));
$_smarty_tpl->tpl_vars['tax_data']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['tax_data']->value) {
$_smarty_tpl->tpl_vars['tax_data']->do_else = false;
?>
						<tr>
							<td><?php echo smarty_function_txt(array('key'=>TEXT_PRODUCT_TAX),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['tax_data']->value['tax_key'];?>
%:</td>
							<td><?php echo $_smarty_tpl->tpl_vars['tax_data']->value['tax_value']['formated'];?>
</td>
						</tr>
						<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

						<tr>
							<th><?php echo smarty_function_txt(array('key'=>TEXT_TOTAL),$_smarty_tpl);?>
:</th>
							<td><?php echo $_smarty_tpl->tpl_vars['order_total']->value['total']['formated'];?>
</td>
						</tr>
						<?php if ($_smarty_tpl->tpl_vars['order_total']->value['total_weight'] > 0) {?>
						<tr>
							<td><?php echo smarty_function_txt(array('key'=>TEXT_PRODUCTS_WEIGHT),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['order_total']->value['total_weight'];?>
 kg</td>
							<td>&nbsp;</td>
						</tr>
						<?php }?>
					</table>
				</div>
			</div>
			<!-- /.col -->
		</div>
		<!-- /.row -->
	</section>
	<!-- /.content -->
	<div class="clearfix"></div>
</div>
<!-- /.content-wrapper -->
<?php }
}
