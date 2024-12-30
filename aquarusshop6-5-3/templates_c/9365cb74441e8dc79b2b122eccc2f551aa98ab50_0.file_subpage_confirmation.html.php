<?php
/* Smarty version 5.4.1, created on 2024-12-03 17:04:25
  from 'file:xtCore/pages/checkout/subpage_confirmation.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.1',
  'unifunc' => 'content_674f2c092dc283_62922514',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '9365cb74441e8dc79b2b122eccc2f551aa98ab50' => 
    array (
      0 => 'xtCore/pages/checkout/subpage_confirmation.html',
      1 => 1697144245,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_674f2c092dc283_62922514 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/checkout';
?><div id="checkout-confirmation" class="row">
	<div class="col col-sm-4 col-md-3">
		<div class="well shipping-address address">
            <p class="headline-underline clearfix">
                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SHIPPING_ADDRESS), $_smarty_tpl);?>

                <a title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_EDIT), $_smarty_tpl);?>
" class="btn btn-xs btn-primary pull-right" href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'checkout','paction'=>'shipping','conn'=>'SSL'), $_smarty_tpl);?>
">
                    <span class="glyphicon glyphicon-pencil"></span>
                    <span class="sr-only"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_EDIT), $_smarty_tpl);?>
</span>
                </a>
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
		<?php if ($_smarty_tpl->getValue('shipping_info')['shipping_name']) {?>
		<div class="well">
            <p class="headline-underline clearfix">
                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SHIPPING_METHOD), $_smarty_tpl);?>

                <a title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_EDIT), $_smarty_tpl);?>
" class="btn btn-xs btn-primary pull-right" href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'checkout','paction'=>'shipping','conn'=>'SSL'), $_smarty_tpl);?>
">
                    <span class="glyphicon glyphicon-pencil"></span>
                    <span class="sr-only"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_EDIT), $_smarty_tpl);?>
</span>
                </a>
            </p>
			<?php if ($_smarty_tpl->getValue('shipping_info')['shipping_name']) {?><p class="bold"><?php echo $_smarty_tpl->getValue('shipping_info')['shipping_name'];?>
</p><?php }?>
			<?php if ($_smarty_tpl->getValue('shipping_info')['shipping_desc']) {?><p><?php echo $_smarty_tpl->getValue('shipping_info')['shipping_desc'];?>
</p><?php }?>
			<?php if ($_smarty_tpl->getValue('shipping_info')['shipping_info']) {?><p><?php echo $_smarty_tpl->getValue('shipping_info')['shipping_info'];?>
</p><?php }?>
		</div>
		<?php }?>
		<div class="well payment-address address">
            <p class="headline-underline clearfix">
                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PAYMENT_ADDRESS), $_smarty_tpl);?>

                <a title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_EDIT), $_smarty_tpl);?>
" class="btn btn-xs btn-primary pull-right" href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'checkout','paction'=>'payment','conn'=>'SSL'), $_smarty_tpl);?>
">
                    <span class="glyphicon glyphicon-pencil"></span>
                    <span class="sr-only"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_EDIT), $_smarty_tpl);?>
</span>
                </a>
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
		</div>
		<div class="well">
            <p class="headline-underline clearfix">
                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PAYMENT_METHOD), $_smarty_tpl);?>

                <a title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_EDIT), $_smarty_tpl);?>
" class="btn btn-xs btn-primary pull-right" href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'checkout','paction'=>'payment','conn'=>'SSL'), $_smarty_tpl);?>
">
                    <span class="glyphicon glyphicon-pencil"></span>
                    <span class="sr-only"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_EDIT), $_smarty_tpl);?>
</span>
                </a>
            </p>
			<?php if ($_smarty_tpl->getValue('payment_info')['payment_name']) {?><p class="bold"><?php echo $_smarty_tpl->getValue('payment_info')['payment_name'];?>
</p><?php }?>
			<?php if ($_smarty_tpl->getValue('payment_info')['payment_desc']) {?><p><?php echo $_smarty_tpl->getValue('payment_info')['payment_desc'];?>
</p><?php }?>
    		<?php if ($_smarty_tpl->getValue('payment_info')['payment_info']) {?><p><?php echo $_smarty_tpl->getValue('payment_info')['payment_info'];?>
</p><?php }?>
		</div>
		<div class="well">
			<p class="headline-underline clearfix">
                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_COMMENTS), $_smarty_tpl);?>

                <a title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_EDIT), $_smarty_tpl);?>
" class="btn btn-xs btn-primary pull-right" href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'checkout','paction'=>'payment','conn'=>'SSL'), $_smarty_tpl);?>
">
                    <span class="glyphicon glyphicon-pencil"></span>
                    <span class="sr-only"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_EDIT), $_smarty_tpl);?>
</span>
                </a>
            </p>
			<?php if ($_SESSION['order_comments'] == '') {?>
			<p><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_NO_REMARK), $_smarty_tpl);?>
</p>
			<?php } else { ?>
			<p><?php echo nl2br((string) $_SESSION['order_comments'], (bool) 1);?>
</p>
			<?php }?>
		</div>
        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'tpl_checkout_confirmation_info_boxes'), $_smarty_tpl);?>

	</div>
	<div class="col col-sm-8 col-md-9">
        <h1><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_CONFIRMATION_DESC), $_smarty_tpl);?>
</h1>
		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'checkout_tpl_info'), $_smarty_tpl);?>

        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'form','name'=>'process','action'=>'checkout','method'=>'post','conn'=>'SSL','id'=>'checkout-form'), $_smarty_tpl);?>

        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'hidden','name'=>'action','value'=>'process'), $_smarty_tpl);?>


        <div class="alert alert-warning">
            <?php if ((defined('_STORE_TERMSCOND_CHECK') ? constant('_STORE_TERMSCOND_CHECK') : null) == 'true') {?>
                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('content')->handle(array('cont_id'=>3,'is_id'=>'false'), $_smarty_tpl);?>

                <p class="checkbox">
                    <label>
                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'checkbox','name'=>'conditions_accepted','class'=>"xt-form-required"), $_smarty_tpl);?>

                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_TERMSANDCOND_CONFIRMATION_5), $_smarty_tpl);?>
 <a href="<?php echo $_smarty_tpl->getValue('_content_3')['content_link'];?>
" target="_blank"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_TERMSANDCOND_CONFIRMATION_2), $_smarty_tpl);?>
</a><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_DOT), $_smarty_tpl);?>

                    </label>
                </p>
            <?php } else { ?>
                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('content')->handle(array('cont_id'=>3,'is_id'=>'false'), $_smarty_tpl);?>

                <p><span class="glyphicon glyphicon-ok"></span> <strong><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_TERMSANDCOND_CONFIRMATION_4), $_smarty_tpl);?>
 <a href="<?php echo $_smarty_tpl->getValue('_content_3')['content_link'];?>
" target="_blank"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_TERMSANDCOND_CONFIRMATION_2), $_smarty_tpl);?>
</a><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_DOT), $_smarty_tpl);?>
</strong></p>
            <?php }?>
            <?php if ($_smarty_tpl->getValue('show_digital_checkbox') == 'true') {?>
                <p class="checkbox">
                    <label>
                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'checkbox','name'=>'withdrawal_reject_accepted','class'=>"xt-form-required"), $_smarty_tpl);?>

                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_DIGITALCOND_CHECK), $_smarty_tpl);?>

                    </label>
                </p>
            <?php }?>
            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'checkout_tpl_form'), $_smarty_tpl);?>

        </div>

        <div class="div-table table-hover table-bordered">
            <div class="row th">
                <div class="col col-md-8">
                    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_ARTICLE), $_smarty_tpl);?>

                </div>
                <div class="col col-md-2 hidden-xs hidden-sm text-right">
                    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SINGLE_PRICE), $_smarty_tpl);?>

                </div>
                <div class="col col-md-2 hidden-xs hidden-sm text-right">
                    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_TOTAL_PRICE), $_smarty_tpl);?>

                </div>
            </div>
            <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('data'), 'product', false, 'key');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('key')->value => $_smarty_tpl->getVariable('product')->value) {
$foreach0DoElse = false;
?>
                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'hidden','name'=>'products_key[]','value'=>$_smarty_tpl->getValue('product')['products_key']), $_smarty_tpl);?>

                <div class="row tr">
                    <div class="col col-xs-5 col-md-2">
                        <p class="image">
                            <?php if (!$_smarty_tpl->getValue('product')['products_image'] || $_smarty_tpl->getValue('product')['products_image'] == 'product:noimage.gif') {?>
                                <a href="<?php echo $_smarty_tpl->getValue('product')['products_link'];?>
" class="vertical-helper image-link no-image img-thumbnail">
                                    <i class="no-image-icon"></i>
                                </a>
                            <?php } else { ?>
                                <a href="<?php echo $_smarty_tpl->getValue('product')['products_link'];?>
" class="vertical-helper image-link img-thumbnail" target="_blank"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('img')->handle(array('img'=>$_smarty_tpl->getValue('product')['products_image'],'type'=>'m_thumb','class'=>"productImageBorder img-responsive",'alt'=>htmlspecialchars((string)$_smarty_tpl->getValue('product')['products_name'], ENT_QUOTES, 'UTF-8', true)), $_smarty_tpl);?>
</a>
                            <?php }?>
                        </p>
                    </div>
                    <div class="col col-xs-7 col-md-6">
                        <p class="product-name break-word bold"><a href="<?php echo $_smarty_tpl->getValue('product')['products_link'];?>
" target="_blank"><?php echo $_smarty_tpl->getValue('product')['products_name'];?>
</a></p>
                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'cart_xt_options','pid'=>$_smarty_tpl->getValue('product')['products_id']), $_smarty_tpl);?>

                        <?php if ($_smarty_tpl->getValue('product')['products_short_description']) {?>
                            <p class="product-description text-muted hidden-xs"><?php echo $_smarty_tpl->getSmarty()->getModifierCallback('truncate')(preg_replace('!<[^>]*?>!', ' ', (string) $_smarty_tpl->getValue('product')['products_short_description']),100,'...');?>
 (<a href="<?php echo $_smarty_tpl->getValue('product')['products_link'];?>
" target="_blank"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_MORE), $_smarty_tpl);?>
</a>)</p>
                        <?php } elseif ($_smarty_tpl->getValue('product')['products_description']) {?>
                            <p class="product-description text-muted hidden-xs"><?php echo $_smarty_tpl->getSmarty()->getModifierCallback('truncate')(preg_replace('!<[^>]*?>!', ' ', (string) $_smarty_tpl->getValue('product')['products_description']),100,'...');?>
 (<a href="<?php echo $_smarty_tpl->getValue('product')['products_link'];?>
" target="_blank"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_MORE), $_smarty_tpl);?>
</a>)</p>
                        <?php }?>
                        <ul class="label-list fixed-padding">
                            <?php if ($_smarty_tpl->getValue('product')['products_information']) {?><li><span class="badge"><?php echo $_smarty_tpl->getSmarty()->getModifierCallback('replace')($_smarty_tpl->getSmarty()->getModifierCallback('replace')($_smarty_tpl->getSmarty()->getModifierCallback('replace')($_smarty_tpl->getSmarty()->getModifierCallback('replace')($_smarty_tpl->getSmarty()->getModifierCallback('replace')($_smarty_tpl->getSmarty()->getModifierCallback('replace')($_smarty_tpl->getValue('product')['products_information'],'<tr class="contentrow1">',''),'<tr class="contentrow2">',''),'<td>',''),'<td class="left" colspan="4">',''),'</td>',''),'</tr>','');?>
</span></li><?php }?>
                            <?php if ($_smarty_tpl->getValue('product')['products_model'] != '') {?><li><span class="badge"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PRODUCTS_MODEL), $_smarty_tpl);?>
 <?php echo $_smarty_tpl->getValue('product')['products_model'];?>
</span></li><?php }?>
                            <?php if ($_smarty_tpl->getValue('product')['products_weight'] > 0) {?><li><span class="badge"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PRODUCTS_WEIGHT), $_smarty_tpl);?>
 <?php echo $_smarty_tpl->getSmarty()->getModifierCallback('weight_format')($_smarty_tpl->getValue('product')['products_weight'],"kg");?>
</span></li><?php }?>
                            <?php if ($_smarty_tpl->getValue('product')['shipping_status_data']['name']) {?><li><span class="badge"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SHIPPING_STATUS), $_smarty_tpl);?>
 <?php echo $_smarty_tpl->getValue('product')['shipping_status_data']['name'];?>
</span></li><?php }?>
                            <?php if ($_smarty_tpl->getValue('product')['base_price']) {?><li><span class="badge"><?php echo $_smarty_tpl->getSmarty()->getModifierCallback('number_format_prec')($_smarty_tpl->getValue('product')['products_vpe_value']);?>
 <?php echo $_smarty_tpl->getValue('product')['base_price']['vpe']['name'];?>
 / <?php echo $_smarty_tpl->getValue('product')['base_price']['price'];?>
 <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SHIPPING_BASE_PER), $_smarty_tpl);?>
 <?php echo $_smarty_tpl->getValue('product')['base_price']['vpe']['name'];?>
</span></li><?php }?>
                        </ul>

                                                <div class="hidden-md hidden-lg text-left-xs">
                            <span class="text-middle"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SINGLE_PRICE), $_smarty_tpl);?>
:</span><br />
                            <p class="product-price">
                                    <?php echo $_smarty_tpl->getValue('product')['products_price']['formated'];?>

                                <?php if ($_smarty_tpl->getValue('product')['_cart_discount']) {?>&nbsp;<span class="price-old"><?php echo $_smarty_tpl->getValue('product')['_original_products_price']['formated'];?>
</span>&nbsp;<span class="small">(-<?php echo $_smarty_tpl->getValue('product')['_cart_discount'];?>
 %)</span>
                                <?php }?>
                            </p>
                            <span class="text-middle"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_TOTAL_PRICE), $_smarty_tpl);?>
:</span><br />
                            <p class="product-price final-price"><?php echo $_smarty_tpl->getValue('product')['products_final_price']['formated'];?>
</p>
                        </div>
                        
                        <div class="form-inline">
                            <div class="form-group form-group-sm">
                                <div class="input-group">

                                    <input type="text"
                                           class="form-control disabled"
                                           disabled="disabled"
                                           id="form-qty-<?php echo $_smarty_tpl->getValue('product')['products_id'];?>
"
                                           value="<?php echo $_smarty_tpl->getValue('product')['products_quantity'];?>
"
                                           style="width: 6em" />
                                    <label for="form-qty-<?php echo $_smarty_tpl->getValue('product')['products_id'];?>
" class="input-group-addon text-regular"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('sys_status')->handle(array('id'=>$_smarty_tpl->getValue('product')['products_unit']), $_smarty_tpl);?>
</label>
                                </div>

                            </div>
                            <a href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'cart'), $_smarty_tpl);?>
" class="btn btn-sm btn-primary">
                                <span class="glyphicon glyphicon-pencil"></span>
                                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_EDIT), $_smarty_tpl);?>

                            </a>
                        </div>
                    </div>
                    <div class="col col-md-2 hidden-xs hidden-sm text-right">
                        <p class="product-price">
                                <?php echo $_smarty_tpl->getValue('product')['products_price']['formated'];?>

                            <?php if ($_smarty_tpl->getValue('product')['_cart_discount']) {?><br />
                            <span class="price-old"><?php echo $_smarty_tpl->getValue('product')['_original_products_price']['formated'];?>
</span><br />
                            <span class="small">(-<?php echo $_smarty_tpl->getValue('product')['_cart_discount'];?>
 %)</span>
                            <?php }?>
                        </p>
                    </div>
                    <div class="col col-md-2 hidden-xs hidden-sm text-right">
                        <p class="product-price final-price"><?php echo $_smarty_tpl->getValue('product')['products_final_price']['formated'];?>
</p>
                    </div>
                </div>
            <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
            <div class="row tfoot text-right">
                <div class="col col-md-offset-5 col-xs-5 col-md-5">
                    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'checkout_tpl_form_total_lines_top'), $_smarty_tpl);?>

                    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'checkout_tpl_form_total_lines'), $_smarty_tpl);?>

                    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SUB_TOTAL), $_smarty_tpl);?>

                </div>
                <div class="col col-xs-7 col-md-2">
                    <?php echo $_smarty_tpl->getValue('sub_total');?>

                </div>
            </div>
            <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('sub_data'), 'total');
$foreach1DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('total')->value) {
$foreach1DoElse = false;
?>
                <div class="row tfoot text-right">
                    <div class="col col-md-offset-5 col-xs-5 col-md-5">
                        <?php echo $_smarty_tpl->getValue('total')['products_name'];?>

                    </div>
                    <div class="col col-xs-7 col-md-2">
                        <?php echo $_smarty_tpl->getValue('total')['products_price']['formated'];?>

                    </div>
                </div>
            <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
            <div class="row tfoot bold text-right">
                <div class="col col-md-offset-5 col-xs-5 col-md-5">
                    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_TOTAL), $_smarty_tpl);?>

                </div>
                <div class="col col-xs-7 col-md-2">
                    <span id="grand-total-value"><?php echo $_smarty_tpl->getValue('total');?>
</span>
                </div>
            </div>
            <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('tax'), 'tax_data');
$foreach2DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('tax_data')->value) {
$foreach2DoElse = false;
?>
                <div class="row tfoot text-right">
                    <div class="col col-md-offset-5 col-xs-5 col-md-5">
                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_TAX), $_smarty_tpl);?>
 <?php echo $_smarty_tpl->getValue('tax_data')['tax_key'];?>
%
                    </div>
                    <div class="col col-xs-7 col-md-2">
                        <?php echo $_smarty_tpl->getSmarty()->getModifierCallback('replace')($_smarty_tpl->getValue('tax_data')['tax_value']['formated'],"*",'');?>

                    </div>
                </div>
            <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
            <?php if ($_smarty_tpl->getValue('discount')) {?>
                <div class="row tfoot text-right">
                    <div class="col col-md-offset-5 col-xs-5 col-md-5">
                        <strong><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_DISCOUNT_MADE), $_smarty_tpl);?>
</strong>
                    </div>
                    <div class="col col-xs-7 col-md-2">
                        <?php echo $_smarty_tpl->getValue('discount')['formated'];?>

                    </div>
                </div>
            <?php }?>

            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'checkout_tpl_form_total_lines_bottom'), $_smarty_tpl);?>

        </div><!-- .div-table -->

        <br />

        <?php if ($_smarty_tpl->getValue('payment_info')['payment_cost_info'] == '1' && $_smarty_tpl->getValue('language') == 'de') {?>
            <p class="alert alert-info"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_ORDER_CONFIRMATION_BUTTON_LAW), $_smarty_tpl);?>
</p>
        <?php }?>
        <?php if ($_smarty_tpl->getValue('post_form') == '1') {?>
            <p class="alert alert-info"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_INFO_PAY_NEXT_STEP), $_smarty_tpl);?>
</p>
        <?php }?>

        <div class="clearfix">
            <a href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'checkout','paction'=>'payment','conn'=>'SSL'), $_smarty_tpl);?>
" class="btn btn-default pull-left">
                <span class="glyphicon glyphicon-chevron-left"></span>
                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>BUTTON_BACK), $_smarty_tpl);?>

            </a>
            <?php if ((null !== ($_smarty_tpl->getValue('button_changed_html') ?? null))) {?>
                <?php echo $_smarty_tpl->getValue('button_changed_html');?>

            <?php } else { ?>
                <button type="submit" class="btn btn-success preloader pull-right">
                    <span class="glyphicon glyphicon-flag"></span>
                    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>BUTTON_CONFIRM_ORDER), $_smarty_tpl);?>

                </button>
            <?php }?>
        </div>

        <br />

        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'checkout_tpl_confiramtion'), $_smarty_tpl);?>
        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'checkout_tpl_confirmation'), $_smarty_tpl);?>

        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'formend'), $_smarty_tpl);?>

        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'checkout_tpl_bottom'), $_smarty_tpl);?>

	</div>
</div><!-- #checkout-confirmation .row -->
<?php }
}
