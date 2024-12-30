<?php
/* Smarty version 5.4.1, created on 2024-12-16 23:07:20
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/account_history_info.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.1',
  'unifunc' => 'content_6760a498579ba7_18598943',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '7d14596785cb9b8f700c8beec8212daaa7e7c61c' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/account_history_info.html',
      1 => 1697144119,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6760a498579ba7_18598943 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages';
?><div id="account-history-info">
	<h1>
        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PAGE_TITLE_ACCOUNT_HISTORY_INFO), $_smarty_tpl);?>

        <small>- <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_ORDER_NUMBER), $_smarty_tpl);?>
: <?php echo $_smarty_tpl->getValue('order_data')['orders_id'];?>
</small>
    </h1>

	<div class="row">
		<div class="col col-sm-4 col-md-3">
			<div class="well">
				<p class="headline-underline"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SHIPPING_ADDRESS), $_smarty_tpl);?>
</p>
				<?php if ($_smarty_tpl->getValue('order_data')['delivery_company']) {
echo $_smarty_tpl->getValue('order_data')['delivery_company'];?>
<br /><?php }?>
				<?php if ($_smarty_tpl->getValue('order_data')['delivery_company_2']) {
echo $_smarty_tpl->getValue('order_data')['delivery_company_2'];?>
<br /><?php }?>
				<?php if ($_smarty_tpl->getValue('order_data')['delivery_company_3']) {
echo $_smarty_tpl->getValue('order_data')['delivery_company_3'];?>
<br /><?php }?>
				<?php echo $_smarty_tpl->getValue('order_data')['delivery_title'];?>
 <?php echo $_smarty_tpl->getValue('order_data')['delivery_firstname'];?>
 <?php echo $_smarty_tpl->getValue('order_data')['delivery_lastname'];?>
<br />
				<?php echo $_smarty_tpl->getValue('order_data')['delivery_street_address'];?>
<br />
				<?php if ($_smarty_tpl->getValue('order_data')['delivery_suburb']) {
echo $_smarty_tpl->getValue('order_data')['delivery_suburb'];?>
<br /><?php }?>
				<?php echo $_smarty_tpl->getValue('order_data')['delivery_postcode'];?>
 <?php echo $_smarty_tpl->getValue('order_data')['delivery_city'];?>
<br />
				<?php if ($_smarty_tpl->getValue('order_data')['delivery_state']) {
echo $_smarty_tpl->getValue('order_data')['delivery_state'];?>
<br /><?php }?>
				<?php echo $_smarty_tpl->getValue('order_data')['delivery_country'];?>

			</div><!-- .box -->
			<div class="well">
				<p class="headline-underline"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PAYMENT_ADDRESS), $_smarty_tpl);?>
</p>
				<?php if ($_smarty_tpl->getValue('order_data')['billing_company']) {
echo $_smarty_tpl->getValue('order_data')['billing_company'];?>
<br /><?php }?>
				<?php if ($_smarty_tpl->getValue('order_data')['billing_company_2']) {
echo $_smarty_tpl->getValue('order_data')['billing_company_2'];?>
<br /><?php }?>
				<?php if ($_smarty_tpl->getValue('order_data')['billing_company_3']) {
echo $_smarty_tpl->getValue('order_data')['billing_company_3'];?>
<br /><?php }?>
				<?php echo $_smarty_tpl->getValue('order_data')['billing_title'];?>
 <?php echo $_smarty_tpl->getValue('order_data')['billing_firstname'];
echo $_smarty_tpl->getValue('order_data')['billing_lastname'];?>
<br />
				<?php echo $_smarty_tpl->getValue('order_data')['billing_street_address'];?>
<br />
				<?php if ($_smarty_tpl->getValue('order_data')['billing_suburb']) {
echo $_smarty_tpl->getValue('order_data')['billing_suburb'];?>
<br /><?php }?>
				<?php echo $_smarty_tpl->getValue('order_data')['billing_postcode'];?>
 <?php echo $_smarty_tpl->getValue('order_data')['billing_city'];?>
<br />
				<?php if ($_smarty_tpl->getValue('order_data')['billing_state']) {
echo $_smarty_tpl->getValue('order_data')['billing_state'];?>
<br /><?php }?>
				<?php echo $_smarty_tpl->getValue('order_data')['billing_country'];?>

			</div><!-- .box -->
			<?php if ($_smarty_tpl->getValue('order_data')['shipping_method'] != '') {?>
			<div class="well">
				<p class="headline-underline"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SHIPPING_METHOD), $_smarty_tpl);?>
</p>
				<?php echo $_smarty_tpl->getValue('order_data')['shipping_method'];?>

			</div><!-- .box -->
			<?php }?>
			<?php if ($_smarty_tpl->getValue('order_data')['payment_name']) {?>
			<div class="well">
				<p class="headline-underline"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PAYMENT_METHOD), $_smarty_tpl);?>
</p>
				<?php echo $_smarty_tpl->getValue('order_data')['payment_name'];?>

			</div><!-- .box -->
			<?php }?>
			<?php if ($_smarty_tpl->getValue('order_data')['payment_info'] != '') {?>
			<div class="well">
				<p class="headline-underline"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PAYMENT_INFO), $_smarty_tpl);?>
</p>
				<?php echo $_smarty_tpl->getValue('order_data')['payment_info'];?>

			</div><!-- .box -->
			<?php }?>
			<?php if ($_smarty_tpl->getValue('order_data')['comments'] != '') {?>
			<div class="well">
				<p class="headline-underline"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_ORDER_COMMENTS), $_smarty_tpl);?>
</p>
				<?php echo nl2br((string) $_smarty_tpl->getValue('order_data')['comments'], (bool) 1);?>

			</div><!-- .box -->
			<?php }?>
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'account_history_info_left'), $_smarty_tpl);?>

		</div>
        
		<div class="col col-sm-8 col-md-9">

			<?php echo $_smarty_tpl->getValue('message');?>

            <p class="pull-right clear"><a href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'customer','paction'=>'order_overview','conn'=>'SSL'), $_smarty_tpl);?>
"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('css_button')->handle(array('text'=>(defined('BUTTON_BACK') ? constant('BUTTON_BACK') : null)), $_smarty_tpl);?>
</a></p>
            
			<p class="headline"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_ORDER_DATE), $_smarty_tpl);?>
</p>
			<p><?php echo $_smarty_tpl->getValue('order_data')['date_purchased'];?>
</p>
			<hr />
			<p class="headline"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_ORDER_STATUS_HISTORY), $_smarty_tpl);?>
</p>
			<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('order_history'), 'history_values', false, NULL, 'aussen', array (
));
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('history_values')->value) {
$foreach0DoElse = false;
?>
			    <p><?php echo $_smarty_tpl->getSmarty()->getModifierCallback('date_format')($_smarty_tpl->getValue('history_values')['date_added'],"%d.%m.%Y - %H:%M");?>
  - <?php echo $_smarty_tpl->getValue('history_values')['status_name'];?>
</p>
			    <?php if ($_smarty_tpl->getValue('history_values')['customer_show_comment'] == '1') {?>
			        <p><?php echo $_smarty_tpl->getValue('history_values')['comments'];?>
</p>
			    <?php }?>
			<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
            <br />



			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'account_history_info_main_table_top'), $_smarty_tpl);?>

			<div id="cart-table" class="div-table table-hover table-bordered">
				<div class="row th">
					<div class="col col-sm-8">
						<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_ARTICLE), $_smarty_tpl);?>

					</div>
					<div class="col col-sm-2 hidden-xs text-right">
						<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SINGLE_PRICE), $_smarty_tpl);?>

					</div>
					<div class="col col-sm-2 hidden-xs text-right">
						<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_TOTAL_PRICE), $_smarty_tpl);?>

					</div>
				</div>

				<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('order_products'), 'data', false, 'key', 'aussen', array (
));
$foreach1DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('key')->value => $_smarty_tpl->getVariable('data')->value) {
$foreach1DoElse = false;
?>
				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'account_history_info_foreach_order_products_top'), $_smarty_tpl);?>


				<div class="row tr">
					<div class="col col-xs-5 col-sm-2">
						<p class="image">

							<?php $_smarty_tpl->assign('products_image', $_smarty_tpl->getValue('data')['products_image'], false, NULL);?>
							<?php if (!empty($_smarty_tpl->getValue('data')['cart_product']) && !empty($_smarty_tpl->getValue('data')['cart_product']['products_image'])) {?>
								<?php $_smarty_tpl->assign('prodcuts_image', $_smarty_tpl->getValue('data')['cart_product']['products_image'], false, NULL);?>
							<?php }?>

							<?php if (!'products_image' || $_smarty_tpl->getValue('products_image') == 'product:noimage.gif') {?>
								<?php if ($_smarty_tpl->getValue('data')['is_product']) {?>
								<a href="<?php echo $_smarty_tpl->getValue('data')['products_link'];?>
" class="vertical-helper image-link no-image img-thumbnail">
									<i class="no-image-icon"></i>
								</a>
								<?php } else { ?>
								<span class="vertical-helper image-link no-image img-thumbnail">
									<i class="no-image-icon"></i>
								</span>
								<?php }?>
							<?php } elseif ($_smarty_tpl->getValue('data')['is_product']) {?>
							<a href="<?php echo $_smarty_tpl->getValue('data')['products_link'];?>
" class="vertical-helper image-link img-thumbnail"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('img')->handle(array('img'=>$_smarty_tpl->getValue('products_image'),'type'=>'m_thumb','class'=>"productImageBorder img-responsive",'alt'=>htmlspecialchars((string)$_smarty_tpl->getValue('data')['cart_product']['products_name'], ENT_QUOTES, 'UTF-8', true)), $_smarty_tpl);?>
</a>
							<?php } else { ?>
							<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('img')->handle(array('img'=>$_smarty_tpl->getValue('products_image'),'type'=>'m_thumb','class'=>"productImageBorder img-responsive",'alt'=>htmlspecialchars((string)$_smarty_tpl->getValue('data')['cart_product']['products_name'], ENT_QUOTES, 'UTF-8', true)), $_smarty_tpl);?>

							<?php }?>
						</p>
					</div>
					<div class="col col-xs-7 col-sm-6 col-lg-6">
						<?php if ($_smarty_tpl->getValue('data')['is_product']) {?>
						<p class="product-name break-word bold"><a href="<?php echo $_smarty_tpl->getValue('data')['products_link'];?>
"><?php echo $_smarty_tpl->getValue('data')['cart_product']['products_name'];?>
</a></p>
						<?php } else { ?>
						<p class="product-name break-word bold"><?php echo $_smarty_tpl->getValue('data')['cart_product']['products_name'];?>
</p>
						<?php }?>
						<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'account_history_info_bundle'), $_smarty_tpl);?>

						<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'account_history_info_xt_options','pid'=>$_smarty_tpl->getValue('data')['products_id']), $_smarty_tpl);?>

						<?php if ($_smarty_tpl->getValue('data')['cart_product']['products_short_description']) {?>
						<p class="product-description text-muted"><?php echo $_smarty_tpl->getSmarty()->getModifierCallback('truncate')(preg_replace('!<[^>]*?>!', ' ', (string) $_smarty_tpl->getValue('data')['cart_product']['products_short_description']),100,'...');?>
 (<a href="<?php echo $_smarty_tpl->getValue('data')['products_link'];?>
"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_MORE), $_smarty_tpl);?>
</a>)</p>
						<?php } elseif ($_smarty_tpl->getValue('data')['cart_product']['products_description']) {?>
						<p class="product-description text-muted"><?php echo $_smarty_tpl->getSmarty()->getModifierCallback('truncate')(preg_replace('!<[^>]*?>!', ' ', (string) $_smarty_tpl->getValue('data')['cart_product']['products_description']),100,'...');?>
 (<a href="<?php echo $_smarty_tpl->getValue('data')['products_link'];?>
"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_MORE), $_smarty_tpl);?>
</a>)</p>
						<?php }?>
						<ul class="label-list fixed-padding">
							<?php if ($_smarty_tpl->getValue('data')['products_information']['content']) {?><li><span class="badge"><?php echo $_smarty_tpl->getSmarty()->getModifierCallback('replace')($_smarty_tpl->getSmarty()->getModifierCallback('replace')($_smarty_tpl->getSmarty()->getModifierCallback('replace')($_smarty_tpl->getSmarty()->getModifierCallback('replace')($_smarty_tpl->getSmarty()->getModifierCallback('replace')($_smarty_tpl->getSmarty()->getModifierCallback('replace')($_smarty_tpl->getValue('data')['products_information']['content'],'<tr class="contentrow1">',''),'<tr class="contentrow2">',''),'<td>',''),'<td class="left" colspan="4">',''),'</td>',''),'</tr>','');?>
</span></li><?php }?>
							<?php if ($_smarty_tpl->getValue('data')['products_model'] != '') {?><li><span class="badge"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PRODUCTS_MODEL), $_smarty_tpl);?>
 <?php echo $_smarty_tpl->getValue('data')['products_model'];?>
</span></li><?php }?>
							<?php if ($_smarty_tpl->getValue('data')['products_weight'] > 0) {?><li><span class="badge"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PRODUCTS_WEIGHT), $_smarty_tpl);?>
 <?php echo $_smarty_tpl->getSmarty()->getModifierCallback('number_format')($_smarty_tpl->getValue('data')['products_weight'],2,",",".");?>
 kg</span></li><?php }?>
							<?php if ($_smarty_tpl->getValue('data')['cart_product']['base_price']) {?><li><span class="badge"><?php echo $_smarty_tpl->getSmarty()->getModifierCallback('number_format_prec')($_smarty_tpl->getValue('data')['cart_product']['products_vpe_value']);?>
 <?php echo $_smarty_tpl->getValue('data')['cart_product']['base_price']['vpe']['name'];?>
 / <?php echo $_smarty_tpl->getValue('data')['cart_product']['base_price']['price'];?>
 <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SHIPPING_BASE_PER), $_smarty_tpl);?>
 <?php echo $_smarty_tpl->getValue('data')['cart_product']['base_price']['vpe']['name'];?>
</span></li><?php }?>
						</ul>

												<div class="hidden-sm hidden-md hidden-lg text-left-xs">
							<span><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SINGLE_PRICE), $_smarty_tpl);?>
:</span><br />
							<p class="product-price">
								<?php echo $_smarty_tpl->getValue('data')['products_price']['formated'];?>

							</p>
							<span><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_TOTAL_PRICE), $_smarty_tpl);?>
:</span><br />
							<p class="product-price final-price"><?php echo $_smarty_tpl->getValue('data')['products_final_price']['formated'];?>
</p>
						</div>
						
						<div class="form-inline">
							<?php if ($_smarty_tpl->getValue('data')['is_product']) {?>
							<div class="form-group form-group-sm">
								<div class="input-group">

									<input type="number"
										   class="form-control"
										   <?php if ((defined('_STORE_ALLOW_DECIMAL_QUANTITIY') ? constant('_STORE_ALLOW_DECIMAL_QUANTITIY') : null) == 'false') {?>min="1"<?php }?>
									required="required"
									step="any"
									name="qty[]"
									autocomplete="off"
									id="form-qty-<?php echo $_smarty_tpl->getValue('data')['products_id'];?>
"
									value="<?php echo $_smarty_tpl->getValue('data')['products_quantity'];?>
"
									placeholder="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_QTY), $_smarty_tpl);?>
"
									style="width: 6em"
									disabled="disabled" />
									<label for="form-qty-<?php echo $_smarty_tpl->getValue('data')['products_id'];?>
" class="input-group-addon text-regular"><?php echo $_smarty_tpl->getValue('data')['cart_product']['products_unit_name'];?>
</label>
								</div>
							</div>
							<?php }?>
							<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'account_history_info_product'), $_smarty_tpl);?>

						</div>
					</div>
					<div class="col col-sm-2 hidden-xs text-right">
						<p class="product-price">
							<?php echo $_smarty_tpl->getValue('data')['products_price']['formated'];?>

							<?php if ($_smarty_tpl->getValue('data')['_cart_discount_data']['plain']) {?><br />
							<span class="price-old"><?php echo $_smarty_tpl->getValue('data')['_original_products_price']['formated'];?>
</span><br />
							<span class="small">(-<?php echo $_smarty_tpl->getValue('data')['_cart_discount'];?>
 %)</span>
							<?php }?>
						</p>
					</div>
					<div class="col col-sm-2 hidden-xs text-right">
						<p class="product-price final-price"><?php echo $_smarty_tpl->getValue('data')['products_final_price']['formated'];?>
</p>
					</div>
				</div>
				<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'account_history_info_total_top'), $_smarty_tpl);?>

				<div class="row tfoot bold text-right">

					<div class="col col-sm-offset-4 col-xs-7 col-sm-6">
						<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SUB_TOTAL), $_smarty_tpl);?>

					</div>
					<div class="col  col-xs-5 col-sm-2">
						<?php echo $_smarty_tpl->getValue('total')['product_total']['formated'];?>

					</div>

					<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('order_total_data'), 'order_total_values', false, NULL, 'aussen', array (
));
$foreach2DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('order_total_values')->value) {
$foreach2DoElse = false;
?>
					<div class="col col-sm-offset-4 col-xs-7 col-sm-6">
						<?php echo $_smarty_tpl->getValue('order_total_values')['orders_total_name'];?>

					</div>
					<div class="col  col-xs-5 col-sm-2">
						<?php echo $_smarty_tpl->getValue('order_total_values')['orders_total_price']['formated'];?>

					</div>
					<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>

					<div class="col col-sm-offset-4 col-xs-7 col-sm-6">
						<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_TOTAL), $_smarty_tpl);?>

					</div>
					<div class="col  col-xs-5 col-sm-2">
						<?php echo $_smarty_tpl->getValue('total')['total']['formated'];?>

					</div>

					<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('total')['total_tax'], 'tax_data', false, NULL, 'aussen', array (
));
$foreach3DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('tax_data')->value) {
$foreach3DoElse = false;
?>
					<div class="col col-sm-offset-4 col-xs-7 col-sm-6">
						<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_TAX), $_smarty_tpl);?>
 <?php echo $_smarty_tpl->getValue('tax_data')['tax_key'];?>
%
					</div>
					<div class="col  col-xs-5 col-sm-2">
						<?php echo $_smarty_tpl->getValue('tax_data')['tax_value']['formated'];?>

					</div>
					<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>

				</div>
				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'account_history_info_total_tpl'), $_smarty_tpl);?>

				<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('cart_tax'), 'tax_data', false, NULL, 'aussen', array (
));
$foreach4DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('tax_data')->value) {
$foreach4DoElse = false;
?>
				<div class="row tfoot text-right">
					<div class="col col-sm-offset-4  col-xs-7 col-sm-6">
						<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_TAX), $_smarty_tpl);?>
 <?php echo $_smarty_tpl->getValue('tax_data')['tax_key'];?>
%
						<?php if ($_smarty_tpl->getValue('shipping_link')) {?><br /><span class="shipping-link"><a href="<?php echo $_smarty_tpl->getValue('shipping_link');?>
" target="_blank" rel="nofollow"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_EXCL_SHIPPING), $_smarty_tpl);?>
</a></span><?php }?>
					</div>
					<div class="col  col-xs-5 col-sm-2 text-right">
						<?php echo $_smarty_tpl->getSmarty()->getModifierCallback('replace')($_smarty_tpl->getValue('tax_data')['tax_value']['formated'],"*",'');?>

					</div>
				</div>
				<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>

				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'account_history_info_total_bottom'), $_smarty_tpl);?>

			</div><!-- .div-table -->
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'account_history_info_main_table_bottom'), $_smarty_tpl);?>


            <p class="pull-left"><br /><a href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'customer','paction'=>'order_overview','conn'=>'SSL'), $_smarty_tpl);?>
"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('css_button')->handle(array('text'=>(defined('BUTTON_BACK') ? constant('BUTTON_BACK') : null)), $_smarty_tpl);?>
</a></p>
		</div>
	</div><!-- .row -->

	<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'account_history_info_bottom'), $_smarty_tpl);?>


</div><!-- #account-history-info --><?php }
}
