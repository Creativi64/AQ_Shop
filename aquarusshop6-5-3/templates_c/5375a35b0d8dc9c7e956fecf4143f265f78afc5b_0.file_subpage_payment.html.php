<?php
/* Smarty version 5.1.0, created on 2024-09-10 16:57:55
  from 'file:xtCore/pages/checkout/subpage_payment.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_66e05e731ae7e7_48434614',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5375a35b0d8dc9c7e956fecf4143f265f78afc5b' => 
    array (
      0 => 'xtCore/pages/checkout/subpage_payment.html',
      1 => 1697144245,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_66e05e731ae7e7_48434614 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/checkout';
?><div id="checkout-payment" class="row">
	<div class="col col-sm-4 col-md-3">
		<div class="well shipping-address address">
			<p class="headline-underline"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SHIPPING_ADDRESS), $_smarty_tpl);?>
</p>
			<?php if ($_smarty_tpl->getValue('shipping_address')['customers_company']) {?><p><?php echo $_smarty_tpl->getValue('shipping_address')['customers_company'];?>
</p><?php }?>
			<?php if ($_smarty_tpl->getValue('shipping_address')['customers_company_2']) {?><p><?php echo $_smarty_tpl->getValue('shipping_address')['customers_company_2'];?>
</p><?php }?>
			<?php if ($_smarty_tpl->getValue('shipping_address')['customers_company_3']) {?><p><?php echo $_smarty_tpl->getValue('shipping_address')['customers_company_3'];?>
</p><?php }?>
			<p><?php if ($_smarty_tpl->getValue('shipping_address')['customers_title']) {
echo $_smarty_tpl->getValue('shipping_address')['customers_title'];?>
 <?php }
echo $_smarty_tpl->getValue('shipping_address')['customers_firstname'];?>
 <?php echo $_smarty_tpl->getValue('shipping_address')['customers_lastname'];?>
</p>
			<p><?php echo $_smarty_tpl->getValue('shipping_address')['customers_street_address'];?>
</p>
			<?php if ($_smarty_tpl->getValue('shipping_address')['customers_address_addition']) {?></p><?php echo $_smarty_tpl->getValue('shipping_address')['customers_address_addition'];?>
</p><?php }?>
			<?php if ($_smarty_tpl->getValue('shipping_address')['customers_suburb']) {?><p><?php echo $_smarty_tpl->getValue('shipping_address')['customers_suburb'];?>
</p><?php }?>
			<p><?php echo $_smarty_tpl->getValue('shipping_address')['customers_postcode'];?>
 <?php echo $_smarty_tpl->getValue('shipping_address')['customers_city'];?>
</p>
			<p><?php echo $_smarty_tpl->getValue('shipping_address')['customers_country'];?>
</p>
		</div>
		<div class="well payment-address address">
			<p class="headline-underline clearfix">
                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PAYMENT_ADDRESS), $_smarty_tpl);?>

                <?php if ($_smarty_tpl->getValue('payment_address')['allow_change'] == true && $_smarty_tpl->getValue('payment_address')['address_class'] == 'payment') {?>
                <a title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_EDIT), $_smarty_tpl);?>
" class="btn btn-xs btn-primary pull-right" href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'customer','paction'=>'edit_address','params'=>'adType=payment&abID','params_value'=>$_smarty_tpl->getValue('payment_address')['address_book_id'],'conn'=>'SSL'), $_smarty_tpl);?>
">
                    <span class="glyphicon glyphicon-pencil"></span>
                    <span class="sr-only"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_EDIT), $_smarty_tpl);?>
</span>
                </a>
                <?php }?>
            </p>
			<?php if ($_smarty_tpl->getValue('payment_address')['customers_company']) {?><p><?php echo $_smarty_tpl->getValue('payment_address')['customers_company'];?>
</p><?php }?>
			<?php if ($_smarty_tpl->getValue('payment_address')['customers_company_2']) {?><p><?php echo $_smarty_tpl->getValue('payment_address')['customers_company_2'];?>
</p><?php }?>
			<?php if ($_smarty_tpl->getValue('payment_address')['customers_company_3']) {?><p><?php echo $_smarty_tpl->getValue('payment_address')['customers_company_3'];?>
</p><?php }?>
			<p><?php if ($_smarty_tpl->getValue('payment_address')['customers_title']) {
echo $_smarty_tpl->getValue('payment_address')['customers_title'];?>
 <?php }
echo $_smarty_tpl->getValue('payment_address')['customers_firstname'];?>
 <?php echo $_smarty_tpl->getValue('payment_address')['customers_lastname'];?>
</p>
			<p><?php echo $_smarty_tpl->getValue('payment_address')['customers_street_address'];?>
</p>
			<?php if ($_smarty_tpl->getValue('payment_address')['customers_address_addition']) {?></p><?php echo $_smarty_tpl->getValue('payment_address')['customers_address_addition'];?>
</p><?php }?>
			<?php if ($_smarty_tpl->getValue('payment_address')['customers_suburb']) {?><p><?php echo $_smarty_tpl->getValue('payment_address')['customers_suburb'];?>
</p><?php }?>
			<p><?php echo $_smarty_tpl->getValue('payment_address')['customers_postcode'];?>
 <?php echo $_smarty_tpl->getValue('payment_address')['customers_city'];?>
</p>
			<p><?php echo $_smarty_tpl->getValue('payment_address')['customers_country'];?>
</p>
			<p><br /></p>
			<?php if ($_smarty_tpl->getValue('shipping_address')['allow_change'] == true) {?>
				<?php if ($_smarty_tpl->getSmarty()->getModifierCallback('count')($_smarty_tpl->getValue('address_data')) > 2) {?>
					<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'form','name'=>'payment_address','action'=>'dynamic','link_params'=>'page_action=payment','method'=>'post','conn'=>'SSL'), $_smarty_tpl);?>

					<div class="form-group">
						<label for="address_data"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SELECT_PAYMENT_ADDRESS), $_smarty_tpl);?>
</label>
						<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('id'=>"address_data",'class'=>"form-control",'type'=>'select','name'=>'adID','value'=>$_smarty_tpl->getValue('address_data'),'default'=>$_SESSION['customer']->customer_payment_address['address_book_id'],'params'=>'onchange="this.form.submit();" data-style="btn-secondary"'), $_smarty_tpl);?>

					</div>
					<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'hidden','name'=>'adType','value'=>'payment'), $_smarty_tpl);?>

					<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'hidden','name'=>'action','value'=>'select_address'), $_smarty_tpl);?>

					<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'formend'), $_smarty_tpl);?>

				<?php }?>
			    <?php if ($_smarty_tpl->getValue('add_new_address') == 1) {?>
			        <!--<p><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_NEW_PAYMENT_ADDRESS), $_smarty_tpl);?>
</p>-->
                    <a class="btn btn-primary" href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'customer','paction'=>'edit_address','params'=>'adType=payment','conn'=>'SSL'), $_smarty_tpl);?>
">
                        <span class="glyphicon glyphicon-plus"></span>
                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>BUTTON_ADD_ADDRESS), $_smarty_tpl);?>

                    </a>
			    <?php }?>
			<?php }?>
		</div>
		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'tpl_checkout_payment_info_boxes'), $_smarty_tpl);?>

	</div>
	<div class="col col-sm-8 col-md-9">
		<h1><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SELECT_PAYMENT_DESC), $_smarty_tpl);?>
</h1>
		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'form','role'=>"form",'name'=>'payment','action'=>'checkout','method'=>'post','conn'=>'SSL','id'=>'checkout_form'), $_smarty_tpl);?>

		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'hidden','name'=>'action','value'=>'payment'), $_smarty_tpl);?>


        <ul class="list-group">
		    <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('payment_data'), 'pdata', false, 'kdata', 'aussen', array (
));
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('kdata')->value => $_smarty_tpl->getVariable('pdata')->value) {
$foreach0DoElse = false;
?>
			<li class="list-group-item clearfix list-group-item-<?php echo $_smarty_tpl->getValue('kdata');?>
"><?php echo $_smarty_tpl->getValue('pdata')['payment'];?>
</li>
		    <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
        </ul>

        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'checkout_tpl_payment_comment'), $_smarty_tpl);?>


		<div class="well form-counter">
			<p class="h4"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_COMMENTS), $_smarty_tpl);?>
 <small><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_MAX_CHARACTERS), $_smarty_tpl);?>
</small></p>
            <textarea name="comments" id="comments" class="form-control autosizejs" cols="50" rows="3" placeholder="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_COMMENTS_DESC), $_smarty_tpl);?>
" maxlength="255"><?php echo $_SESSION['order_comments'];?>
</textarea>
		</div>

        <div class="clearfix">
            <a href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'checkout','paction'=>'shipping','conn'=>'SSL'), $_smarty_tpl);?>
" class="btn btn-default pull-left">
				<i class="fa fa-chevron-left"></i>
                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>BUTTON_BACK), $_smarty_tpl);?>

            </a>
			<?php if (!empty($_smarty_tpl->getValue('payment_data'))) {?>
            <button id="<?php echo $_smarty_tpl->getValue('page_action');?>
-submit" type="submit" class="btn btn-success pull-right">
				<span class="glyphicon glyphicon-ok"></span>
                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>BUTTON_NEXT), $_smarty_tpl);?>

            </button>
			<?php }?>
        </div>

        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'checkout_tpl_payment'), $_smarty_tpl);?>

        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'formend'), $_smarty_tpl);?>


    </div>
</div><!-- #checkout-payment .row -->
<?php }
}
