<?php
/* Smarty version 5.4.1, created on 2024-12-03 17:04:00
  from 'file:xtCore/pages/checkout/subpage_shipping.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.1',
  'unifunc' => 'content_674f2bf0d62212_20923052',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'dba4c2e59db06c692d02525a58f5f665773f2738' => 
    array (
      0 => 'xtCore/pages/checkout/subpage_shipping.html',
      1 => 1697144245,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_674f2bf0d62212_20923052 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/checkout';
?><div id="checkout-shipping" class="row">
	<div class="col col-sm-4 col-md-3">
		<div class="well shipping-address address">
            <p class="headline-underline clearfix">
                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SHIPPING_ADDRESS), $_smarty_tpl);?>

                <?php if ($_smarty_tpl->getValue('shipping_address')['allow_change'] == true && $_smarty_tpl->getValue('shipping_address')['address_class'] == 'shipping') {?>
                <a title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_EDIT), $_smarty_tpl);?>
" class="btn btn-xs btn-primary pull-right" href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'customer','paction'=>'edit_address','params'=>'adType=shipping&abID','params_value'=>$_smarty_tpl->getValue('shipping_address')['address_book_id'],'conn'=>'SSL'), $_smarty_tpl);?>
">
                    <span class="glyphicon glyphicon-pencil"></span>
                    <span class="sr-only"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_EDIT), $_smarty_tpl);?>
</span>
                </a>
                <?php }?>
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
			<?php if ($_smarty_tpl->getValue('shipping_address')['customers_suburb']) {?></p><?php echo $_smarty_tpl->getValue('shipping_address')['customers_suburb'];?>
</p><?php }?>
			<p><?php echo $_smarty_tpl->getValue('shipping_address')['customers_postcode'];?>
 <?php echo $_smarty_tpl->getValue('shipping_address')['customers_city'];?>
</p>
			<p><?php echo $_smarty_tpl->getValue('shipping_address')['customers_country'];?>
</p>
			<p><br /></p>
			<?php if ($_smarty_tpl->getValue('shipping_address')['allow_change'] == true) {?>
                <?php if ($_smarty_tpl->getSmarty()->getModifierCallback('count')($_smarty_tpl->getValue('address_data')) > 2) {?>
                    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'form','role'=>"form",'name'=>'shipping_address','action'=>'dynamic','link_params'=>'page_action=shipping','method'=>'post','conn'=>'SSL'), $_smarty_tpl);?>

                    <div class="form-group">
                        <label for="address_data"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SELECT_SHIPPING_ADDRESS), $_smarty_tpl);?>
</label>
                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'select','class'=>"form-control",'id'=>"address_data",'name'=>'adID','value'=>$_smarty_tpl->getValue('address_data'),'default'=>$_SESSION['customer']->customer_shipping_address['address_book_id'],'params'=>'onchange="this.form.submit();" data-style="btn-secondary"'), $_smarty_tpl);?>

                    </div>
                    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'hidden','name'=>'adType','value'=>'shipping'), $_smarty_tpl);?>

                    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'hidden','name'=>'action','value'=>'select_address'), $_smarty_tpl);?>

                    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'formend'), $_smarty_tpl);?>

                <?php }?>
			    <?php if ($_smarty_tpl->getValue('add_new_address') == 1) {?>
			        <!--<p><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_NEW_SHIPPING_ADDRESS), $_smarty_tpl);?>
</p>-->
			        <a class="btn btn-primary" href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'customer','paction'=>'edit_address','params'=>'adType=shipping','conn'=>'SSL'), $_smarty_tpl);?>
">
                        <span class="glyphicon glyphicon-plus"></span>
                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>BUTTON_ADD_ADDRESS), $_smarty_tpl);?>

                    </a>
			    <?php }?>
			<?php }?>
		</div>
        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'tpl_checkout_shipping_info_boxes'), $_smarty_tpl);?>

	</div>
	<div class="col col-sm-8 col-md-9">
		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('box')->handle(array('name'=>'xt_delivery_date','type'=>'user'), $_smarty_tpl);?>

		<h1><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SELECT_SHIPPING_DESC), $_smarty_tpl);?>
</h1>
        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'form','name'=>'shipping','action'=>'checkout','method'=>'post','conn'=>'SSL'), $_smarty_tpl);?>

        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'hidden','name'=>'action','value'=>'shipping'), $_smarty_tpl);?>


        <ul class="list-group">
		    <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('shipping_data'), 'sdata', false, 'kdata', 'aussen', array (
));
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('kdata')->value => $_smarty_tpl->getVariable('sdata')->value) {
$foreach0DoElse = false;
?>
			    <li class="list-group-item clearfix list-group-item-<?php echo $_smarty_tpl->getValue('kdata');?>
"><?php echo $_smarty_tpl->getValue('sdata')['shipping'];?>
</li>
		    <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
        </ul>

        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('box')->handle(array('name'=>'xt_checkout_options','type'=>'user'), $_smarty_tpl);?>


        <div class="clearfix">
            <a href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'cart','conn'=>'SSL'), $_smarty_tpl);?>
" class="btn btn-default pull-left">
                <i class="fa fa-chevron-left"></i>
                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>BUTTON_BACK), $_smarty_tpl);?>

            </a>
            <button id="<?php echo $_smarty_tpl->getValue('page_action');?>
-submit" type="submit" class="btn btn-success pull-right">
                <span class="glyphicon glyphicon-ok"></span>
                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>BUTTON_NEXT), $_smarty_tpl);?>

            </button>
        </div>

        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'checkout_tpl_shipping'), $_smarty_tpl);?>

        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'formend'), $_smarty_tpl);?>

	</div>
</div>
<?php }
}
