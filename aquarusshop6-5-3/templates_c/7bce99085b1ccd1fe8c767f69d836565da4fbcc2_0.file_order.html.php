<?php
/* Smarty version 5.4.1, created on 2024-12-14 15:34:16
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/templates/__xtAdmin/xtCore/pages/order.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.1',
  'unifunc' => 'content_675d9768156574_77921518',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '7bce99085b1ccd1fe8c767f69d836565da4fbcc2' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/__xtAdmin/xtCore/pages/order.html',
      1 => 1726071637,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_675d9768156574_77921518 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/__xtAdmin/xtCore/pages';
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
					<i class="fas fa-shopping-cart"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_ORDER_ID), $_smarty_tpl);?>
:
					<?php echo $_smarty_tpl->getValue('order_data')['orders_id'];?>
 | <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_ORDERS_STATUS), $_smarty_tpl);?>
:
					<?php echo $_smarty_tpl->getValue('order_data')['orders_status'];?>
 <span class="pull-right"><?php echo $_smarty_tpl->getValue('order_data')['date_purchased'];?>
</span>
				</h2>
			</div>
			<!-- /.col -->
		</div>
		<!-- info row -->
		<div class="row invoice-info">
			<div class="col-sm-3 invoice-col">
				<b><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_BILLING_ADDRESS), $_smarty_tpl);?>
</b>
				<address>
					<?php if ($_smarty_tpl->getValue('order_data')['billing_company']) {?> <?php echo $_smarty_tpl->getValue('order_data')['billing_company'];?>
<br /><?php }?>
					<?php if ($_smarty_tpl->getValue('order_data')['billing_company_2']) {?> <?php echo $_smarty_tpl->getValue('order_data')['billing_company_2'];?>
<br /><?php }?>
					<?php if ($_smarty_tpl->getValue('order_data')['billing_company_3']) {?> <?php echo $_smarty_tpl->getValue('order_data')['billing_company_3'];?>
<br /><?php }?>
					<?php echo $_smarty_tpl->getValue('order_data')['billing_title'];?>
 <?php echo $_smarty_tpl->getValue('order_data')['billing_firstname'];?>

					<?php echo $_smarty_tpl->getValue('order_data')['billing_lastname'];?>
<br />
					<?php echo $_smarty_tpl->getValue('order_data')['billing_street_address'];?>
<br />
					<?php if ($_smarty_tpl->getValue('order_data')['billing_address_addition']) {
echo $_smarty_tpl->getValue('order_data')['billing_address_addition'];?>
<br /><?php }?>
					<?php if ($_smarty_tpl->getValue('order_data')['billing_suburb']) {?> <?php echo $_smarty_tpl->getValue('order_data')['billing_suburb'];?>
<br /><?php }?>
					<?php echo $_smarty_tpl->getValue('order_data')['billing_postcode'];?>
 <?php echo $_smarty_tpl->getValue('order_data')['billing_city'];?>
<br />
					<?php echo $_smarty_tpl->getValue('order_data')['billing_country'];?>
 (<?php echo $_smarty_tpl->getValue('order_data')['billing_country_code'];?>
)<br />
					<?php if ($_smarty_tpl->getValue('order_data')['billing_phone']) {?>
						<b><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_BILLING_PHONE), $_smarty_tpl);?>
</b> <?php ob_start();
echo $_smarty_tpl->getValue('order_data')['billing_phone'];
$_prefixVariable1 = ob_get_clean();
echo $_prefixVariable1;?>
<br />
					<?php }?>
					<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>"tpl_order_details:billing_address_bottom"), $_smarty_tpl);?>

				</address>
			</div>
			<!-- /.col -->
			<div class="col-sm-3 invoice-col">
				<b><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_DELIVERY_ADDRESS), $_smarty_tpl);?>
</b>
				<address>
					<?php if ($_smarty_tpl->getValue('order_data')['delivery_company']) {?> <?php echo $_smarty_tpl->getValue('order_data')['delivery_company'];?>
<br /><?php }?>
					<?php if ($_smarty_tpl->getValue('order_data')['delivery_company_2']) {?>
						<?php echo $_smarty_tpl->getValue('order_data')['delivery_company_2'];?>
<br /><?php }?> <?php if ($_smarty_tpl->getValue('order_data')['delivery_company_3']) {?> <?php echo $_smarty_tpl->getValue('order_data')['delivery_company_3'];?>
<br /><?php }?>
					<?php echo $_smarty_tpl->getValue('order_data')['delivery_title'];?>
 <?php echo $_smarty_tpl->getValue('order_data')['delivery_firstname'];?>

					<?php echo $_smarty_tpl->getValue('order_data')['delivery_lastname'];?>
<br />
					<?php echo $_smarty_tpl->getValue('order_data')['delivery_street_address'];?>
<br />
					<?php if ($_smarty_tpl->getValue('order_data')['delivery_address_addition']) {
echo $_smarty_tpl->getValue('order_data')['delivery_address_addition'];?>
<br /><?php }?>
					<?php if ($_smarty_tpl->getValue('order_data')['delivery_suburb']) {?> <?php echo $_smarty_tpl->getValue('order_data')['delivery_suburb'];?>
<br /><?php }?>
					<?php echo $_smarty_tpl->getValue('order_data')['delivery_postcode'];?>
 <?php echo $_smarty_tpl->getValue('order_data')['delivery_city'];?>
<br />
					<?php echo $_smarty_tpl->getValue('order_data')['delivery_country'];?>
 (<?php echo $_smarty_tpl->getValue('order_data')['delivery_country_code'];?>
)<br />
					<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>"tpl_order_details:delivery_address_bottom"), $_smarty_tpl);?>

				</address>
			</div>
			<!-- /.col -->
			<div class="col-sm-3 invoice-col">
				<?php if ($_smarty_tpl->getValue('order_customer')['customers_dob']) {?>
					<b><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_CUSTOMERS_DOB), $_smarty_tpl);?>
:</b> <?php echo $_smarty_tpl->getValue('order_customer')['customers_dob'];?>
 <span <?php if ($_smarty_tpl->getValue('order_customer')['customers_age'] < $_smarty_tpl->getValue('minAge')) {?>class="warning"<?php }?>>(<?php echo $_smarty_tpl->getValue('order_customer')['customers_age'];?>
 <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_YEARS), $_smarty_tpl);?>
)</span><br />
				<?php }?>
				<b><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>'TEXT_customers_email_address'), $_smarty_tpl);?>
: </b><a href="mailto:<?php echo $_smarty_tpl->getValue('order_data')['customers_email_address'];?>
"><?php echo $_smarty_tpl->getValue('order_data')['customers_email_address'];?>
</a><br />
				<b><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TABTEXT_CUSTOMERS_STATUS), $_smarty_tpl);?>
 (<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_ORDER), $_smarty_tpl);?>
): </b><?php echo $_smarty_tpl->getValue('order_data')['customers_status_name'];?>
<br />
				<b><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TABTEXT_CUSTOMERS_STATUS), $_smarty_tpl);?>
: </b><?php ob_start();
echo $_smarty_tpl->getValue('order_customer')['customers_status_name'];
$_prefixVariable2 = ob_get_clean();
echo $_prefixVariable2;?>
<br />
				<?php if ($_smarty_tpl->getValue('order_data')['delivery_phone']) {?>
					<b><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PHONE), $_smarty_tpl);?>
: </b><?php echo $_smarty_tpl->getValue('order_data')['delivery_phone'];?>
<br />
				<?php }?>
				<?php if ($_smarty_tpl->getValue('order_data')['delivery_mobile_phone']) {?>
					<b><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_MOBILE_PHONE), $_smarty_tpl);?>
: </b><?php echo $_smarty_tpl->getValue('order_data')['delivery_mobile_phone'];?>
<br />
				<?php }?>
				<?php if ($_smarty_tpl->getValue('order_data')['customers_cid']) {?>
					<b><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_CUSTOMERS_CID), $_smarty_tpl);?>
: </b><?php echo $_smarty_tpl->getValue('order_data')['customers_cid'];?>
<br />
				<?php }?>
				<?php if ($_smarty_tpl->getValue('order_data')['customers_vat_id']) {?>
					<b><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_CUSTOMERS_VAT_ID), $_smarty_tpl);?>
: </b><?php echo $_smarty_tpl->getValue('order_data')['customers_vat_id'];?>
<br />
				<?php }?>
				<b><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_STORE_LANGUAGE), $_smarty_tpl);?>
: </b><?php ob_start();
echo $_smarty_tpl->getValue('order_data')['language_text'];
$_prefixVariable3 = ob_get_clean();
echo $_prefixVariable3;?>
<br />
				<?php if ($_smarty_tpl->getValue('order_data')['billing_mobile_phone']) {?>
					<b><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_BILLING_MOBILE_PHONE), $_smarty_tpl);?>
: </b><?php ob_start();
echo $_smarty_tpl->getValue('order_data')['billing_mobile_phone'];
$_prefixVariable4 = ob_get_clean();
echo $_prefixVariable4;?>
<br />
				<?php }?>
				<b><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>"HEADING_CUSTOMER"), $_smarty_tpl);?>
-ID: </b>
				<?php if ($_smarty_tpl->getValue('order_data')['customers_id'] > 0) {?>
					<?php echo $_smarty_tpl->getValue('order_data')['customers_id'];?>


					<?php if ($_smarty_tpl->getValue('customer_id_valid')) {?>
						<a href="javascript:void(0)" onclick="openCustomersDetails(<?php echo $_smarty_tpl->getValue('order_data')['customers_id'];?>
)"><i class="fas fa-info-circle" qtip="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>'TEXT_CUSTOMER_DETAILS'), $_smarty_tpl);?>
"></i></a>
					<?php } else { ?>
						<a href="javascript:void(0)" onclick="openCustomersDetails(<?php echo $_smarty_tpl->getValue('order_data')['customers_id'];?>
)"><i class="fas fa-info-circle" style="color: red" qtip="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>'TEXT_NO_VALID_CUSTOMER_ASSOC'), $_smarty_tpl);?>
"></i></a>
					<?php }?>

					&nbsp;
					<a href="javascript:void(0)" onclick="openCustomersOrders(<?php echo $_smarty_tpl->getValue('order_data')['customers_id'];?>
)"><i class="fas fa-shopping-cart" qtip="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>'HEADING_ORDER'), $_smarty_tpl);?>
"></i></a>
					&nbsp;
					<a href="javascript:void(0)" onclick="openCustomersAddresses(<?php echo $_smarty_tpl->getValue('order_data')['customers_id'];?>
)"><i class="fas fa-address-book" qtip="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>'HEADING_ADDRESS'), $_smarty_tpl);?>
"></i></a>

				<?php } else { ?> no customers ID ??
				<?php }?>
				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>"tpl_order_details:customers_details_bottom"), $_smarty_tpl);?>

				<br />

			</div>
			<!-- /.col -->
			<!-- /.col -->
			<div class="col-sm-3 invoice-col">
				<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('order_data')['order_info_options'], 'info', false, NULL, 'aussen', array (
));
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('info')->value) {
$foreach0DoElse = false;
?>
					<?php if ($_smarty_tpl->getValue('info')['topic']) {?>
						<?php echo $_smarty_tpl->getValue('info')['text'];?>
<br />
					<?php } elseif ($_smarty_tpl->getValue('info')['msgCls']) {?>
						<span class="admin-order-info <?php echo $_smarty_tpl->getValue('info')['msgCls'];?>
" ><?php echo $_smarty_tpl->getValue('info')['text'];?>
</span><br />
					<?php } else { ?>
						<b><?php echo $_smarty_tpl->getValue('info')['text'];?>
:</b> <?php echo $_smarty_tpl->getValue('info')['value'];?>
<br />
					<?php }?>
					<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>"tpl_order_details:order_info_options_loop"), $_smarty_tpl);?>

				<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
			</div>
			<!-- /.col -->
		</div>
		<!-- /.row -->

		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>"tpl_order_details:after_order_data"), $_smarty_tpl);?>


		<!-- Table row -->
		<div class="row">
			<div class="col-xs-12 table-responsive">
				<table class="table table-striped">
					<thead>
					<tr>
						<th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PRODUCTS_QUANTITY), $_smarty_tpl);?>
</th>
						<th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PRODUCTS_NAME), $_smarty_tpl);?>
</th>
						<th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PRODUCTS_MODEL), $_smarty_tpl);?>
</th>
						<th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PRODUCTS_SINGLEPRICE), $_smarty_tpl);?>
</th>
						<th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PRODUCTS_DISCOUNT), $_smarty_tpl);?>
</th>
						<th><span class="right"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PRODUCTS_FINALPRICE), $_smarty_tpl);?>
</span></th>
						<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>"tpl_order_details:products_headers"), $_smarty_tpl);?>

					</tr>
					</thead>
					<tbody>
					<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('order_products'), 'data', false, NULL, 'aussen', array (
));
$foreach1DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('data')->value) {
$foreach1DoElse = false;
?>
						<tr>
							<td><?php echo $_smarty_tpl->getValue('data')['products_quantity'];?>
</td>
							<td><a href="<?php echo $_smarty_tpl->getValue('data')['cart_product']['products_link'];?>
" onclick="navigator.clipboard.writeText(this.href); this.classList.add(`blink-fast`); return false" style="font-size: 1.2em;color:inherit"><i ext:qtip="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_COPY_ADDRESS), $_smarty_tpl);?>
"  class="far fa-clipboard"></i></a>&nbsp;&nbsp;<a href="<?php echo $_smarty_tpl->getValue('data')['cart_product']['products_link'];?>
" target="_blank" style="color:inherit"><?php echo $_smarty_tpl->getValue('data')['products_name'];?>
</a></td>
							<td><?php echo $_smarty_tpl->getValue('data')['products_model'];?>
</td>
							<td><?php echo $_smarty_tpl->getValue('data')['products_price']['formated'];
if ($_smarty_tpl->getValue('data')['products_price_before_discount']) {?><br /><span class="price-old"><?php echo $_smarty_tpl->getValue('data')['products_price_before_discount'];?>
</span><?php }?></td>
							<td><?php if ($_smarty_tpl->getValue('data')['products_discount'] > 0) {
echo $_smarty_tpl->getValue('data')['products_discount'];?>

									%<?php } else { ?>0 %<?php }?></td>
							<td><span class="right"><?php echo $_smarty_tpl->getValue('data')['products_final_price']['data']['PRICE']['formated'];?>
</span></td>
							<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>"tpl_order_details:products_columns_loop"), $_smarty_tpl);?>

						</tr>
						<?php echo $_smarty_tpl->getValue('data')['products_information']['content_admin'];?>

						<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>"tpl_order_details:products_loop"), $_smarty_tpl);?>

					<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
					</tbody>
				</table>
			</div>
			<!-- /.col -->
		</div>
		<!-- /.row -->

		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>"tpl_order_details:after_order_products"), $_smarty_tpl);?>


		<div class="row">
			<!-- accepted payments column -->
			<div class="col-xs-6">
				<b><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PAYMENT_CODE), $_smarty_tpl);?>
:</b> <?php ob_start();
echo $_smarty_tpl->getValue('order_data')['payment_code'];
$_prefixVariable5 = ob_get_clean();
echo $_prefixVariable5;?>
<br />
				<b><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SHIPPING_CODE), $_smarty_tpl);?>
:</b> <?php ob_start();
echo $_smarty_tpl->getValue('order_data')['shipping_code'];
$_prefixVariable6 = ob_get_clean();
echo $_prefixVariable6;?>
<br />
				<?php if ($_smarty_tpl->getValue('coupon_info')) {?><b><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_ORDER_EDIT_CURRENT_COUPON_CODE), $_smarty_tpl);?>
:</b> <?php echo $_smarty_tpl->getValue('coupon_info')['current_coupon_code_show'];?>
<br /><?php }?>
				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>"tpl_order_details:additional_information"), $_smarty_tpl);?>

			</div>
			<!-- /.col -->
			<div class="col-xs-6">
				<div class="table-responsive" id="order_total">
					<table class="table">
						<tr>
							<th style="width: 50%"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PRODUCT_TOTAL), $_smarty_tpl);?>
:</th>
							<td><?php echo $_smarty_tpl->getValue('order_total')['product_total']['formated'];?>
</td>
						</tr>

						<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('order_total_data'), 'sub_data', false, NULL, 'aussen', array (
));
$foreach2DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('sub_data')->value) {
$foreach2DoElse = false;
?>
							<tr>
								<td><?php echo $_smarty_tpl->getValue('sub_data')['orders_total_name'];?>
:</td>
								<td><?php echo $_smarty_tpl->getValue('sub_data')['orders_total_price']['formated'];?>
</td>
							</tr>
						<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?> <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('order_total')['total_tax'], 'tax_data', false, NULL, 'aussen', array (
));
$foreach3DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('tax_data')->value) {
$foreach3DoElse = false;
?>
							<tr>
								<td><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PRODUCT_TAX), $_smarty_tpl);?>
 <?php echo $_smarty_tpl->getValue('tax_data')['tax_key'];?>
%:</td>
								<td><?php echo $_smarty_tpl->getValue('tax_data')['tax_value']['formated'];?>
</td>
							</tr>
						<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>

						<tr>
							<th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_TOTAL), $_smarty_tpl);?>
:</th>
							<td><?php echo $_smarty_tpl->getValue('order_total')['total']['formated'];?>
</td>
						</tr>
						<?php if ($_smarty_tpl->getValue('order_total')['total_weight'] > 0) {?>
							<tr>
								<td><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PRODUCTS_WEIGHT), $_smarty_tpl);?>
 <?php echo $_smarty_tpl->getValue('order_total')['total_weight'];?>
 kg</td>
								<td>&nbsp;</td>
							</tr>
						<?php }?>
						<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>"tpl_order_details:after_totals"), $_smarty_tpl);?>

					</table>
				</div>
			</div>
			<!-- /.col -->
		</div>

		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>"tpl_order_details:after_order_totals"), $_smarty_tpl);?>

		<!-- /.row -->
	</section>
	<!-- /.content -->
	<div class="clearfix"></div>
</div>
<!-- /.content-wrapper -->
<?php }
}
