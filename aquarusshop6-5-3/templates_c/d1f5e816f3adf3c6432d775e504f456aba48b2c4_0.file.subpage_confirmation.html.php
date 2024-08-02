<?php
/* Smarty version 4.3.2, created on 2024-07-22 18:47:40
  from '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/checkout/subpage_confirmation.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_669e8d2c1e7ad7_53962220',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd1f5e816f3adf3c6432d775e504f456aba48b2c4' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/checkout/subpage_confirmation.html',
      1 => 1697144245,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_669e8d2c1e7ad7_53962220 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.link.php','function'=>'smarty_function_link',),2=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.hook.php','function'=>'smarty_function_hook',),3=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.form.php','function'=>'smarty_function_form',),4=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.content.php','function'=>'smarty_function_content',),5=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.img.php','function'=>'smarty_function_img',),6=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/vendor/smarty/smarty/libs/plugins/modifier.truncate.php','function'=>'smarty_modifier_truncate',),7=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/vendor/smarty/smarty/libs/plugins/modifier.replace.php','function'=>'smarty_modifier_replace',),8=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/modifier.weight_format.php','function'=>'smarty_modifier_weight_format',),9=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/modifier.number_format_prec.php','function'=>'smarty_modifier_number_format_prec',),10=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.sys_status.php','function'=>'smarty_function_sys_status',),));
?>
<div id="checkout-confirmation" class="row">
	<div class="col col-sm-4 col-md-3">
		<div class="well shipping-address address">
            <p class="headline-underline clearfix">
                <?php echo smarty_function_txt(array('key'=>TEXT_SHIPPING_ADDRESS),$_smarty_tpl);?>

                <a title="<?php echo smarty_function_txt(array('key'=>TEXT_EDIT),$_smarty_tpl);?>
" class="btn btn-xs btn-primary pull-right" href="<?php echo smarty_function_link(array('page'=>'checkout','paction'=>'shipping','conn'=>'SSL'),$_smarty_tpl);?>
">
                    <span class="glyphicon glyphicon-pencil"></span>
                    <span class="sr-only"><?php echo smarty_function_txt(array('key'=>TEXT_EDIT),$_smarty_tpl);?>
</span>
                </a>
            </p>
			<?php if ($_smarty_tpl->tpl_vars['shipping_address']->value['customers_company']) {?><p><?php echo $_smarty_tpl->tpl_vars['shipping_address']->value['customers_company'];?>
</p><?php }?>
            <?php if ($_smarty_tpl->tpl_vars['shipping_address']->value['customers_company_2']) {?><p><?php echo $_smarty_tpl->tpl_vars['shipping_address']->value['customers_company_2'];?>
</p><?php }?>
            <?php if ($_smarty_tpl->tpl_vars['shipping_address']->value['customers_company_3']) {?><p><?php echo $_smarty_tpl->tpl_vars['shipping_address']->value['customers_company_3'];?>
</p><?php }?>
			<p><?php if ($_smarty_tpl->tpl_vars['shipping_address']->value['customers_title']) {
echo $_smarty_tpl->tpl_vars['shipping_address']->value['customers_title'];?>
 <?php }
echo $_smarty_tpl->tpl_vars['shipping_address']->value['customers_firstname'];?>
 <?php echo $_smarty_tpl->tpl_vars['shipping_address']->value['customers_lastname'];?>
</p>
			<p><?php echo $_smarty_tpl->tpl_vars['shipping_address']->value['customers_street_address'];?>
</p>
            <?php if ($_smarty_tpl->tpl_vars['shipping_address']->value['customers_address_addition']) {?></p><?php echo $_smarty_tpl->tpl_vars['shipping_address']->value['customers_address_addition'];?>
</p><?php }?>
			<?php if ($_smarty_tpl->tpl_vars['shipping_address']->value['customers_suburb']) {?><p><?php echo $_smarty_tpl->tpl_vars['shipping_address']->value['customers_suburb'];?>
</p><?php }?>
			<p><?php echo $_smarty_tpl->tpl_vars['shipping_address']->value['customers_postcode'];?>
 <?php echo $_smarty_tpl->tpl_vars['shipping_address']->value['customers_city'];?>
</p>
			<p><?php echo $_smarty_tpl->tpl_vars['shipping_address']->value['customers_country'];?>
</p>
		</div>
		<?php if ($_smarty_tpl->tpl_vars['shipping_info']->value['shipping_name']) {?>
		<div class="well">
            <p class="headline-underline clearfix">
                <?php echo smarty_function_txt(array('key'=>TEXT_SHIPPING_METHOD),$_smarty_tpl);?>

                <a title="<?php echo smarty_function_txt(array('key'=>TEXT_EDIT),$_smarty_tpl);?>
" class="btn btn-xs btn-primary pull-right" href="<?php echo smarty_function_link(array('page'=>'checkout','paction'=>'shipping','conn'=>'SSL'),$_smarty_tpl);?>
">
                    <span class="glyphicon glyphicon-pencil"></span>
                    <span class="sr-only"><?php echo smarty_function_txt(array('key'=>TEXT_EDIT),$_smarty_tpl);?>
</span>
                </a>
            </p>
			<?php if ($_smarty_tpl->tpl_vars['shipping_info']->value['shipping_name']) {?><p class="bold"><?php echo $_smarty_tpl->tpl_vars['shipping_info']->value['shipping_name'];?>
</p><?php }?>
			<?php if ($_smarty_tpl->tpl_vars['shipping_info']->value['shipping_desc']) {?><p><?php echo $_smarty_tpl->tpl_vars['shipping_info']->value['shipping_desc'];?>
</p><?php }?>
			<?php if ($_smarty_tpl->tpl_vars['shipping_info']->value['shipping_info']) {?><p><?php echo $_smarty_tpl->tpl_vars['shipping_info']->value['shipping_info'];?>
</p><?php }?>
		</div>
		<?php }?>
		<div class="well payment-address address">
            <p class="headline-underline clearfix">
                <?php echo smarty_function_txt(array('key'=>TEXT_PAYMENT_ADDRESS),$_smarty_tpl);?>

                <a title="<?php echo smarty_function_txt(array('key'=>TEXT_EDIT),$_smarty_tpl);?>
" class="btn btn-xs btn-primary pull-right" href="<?php echo smarty_function_link(array('page'=>'checkout','paction'=>'payment','conn'=>'SSL'),$_smarty_tpl);?>
">
                    <span class="glyphicon glyphicon-pencil"></span>
                    <span class="sr-only"><?php echo smarty_function_txt(array('key'=>TEXT_EDIT),$_smarty_tpl);?>
</span>
                </a>
            </p>
			<?php if ($_smarty_tpl->tpl_vars['payment_address']->value['customers_company']) {?><p><?php echo $_smarty_tpl->tpl_vars['payment_address']->value['customers_company'];?>
</p><?php }?>
            <?php if ($_smarty_tpl->tpl_vars['payment_address']->value['customers_company_2']) {?><p><?php echo $_smarty_tpl->tpl_vars['payment_address']->value['customers_company_2'];?>
</p><?php }?>
            <?php if ($_smarty_tpl->tpl_vars['payment_address']->value['customers_company_3']) {?><p><?php echo $_smarty_tpl->tpl_vars['payment_address']->value['customers_company_3'];?>
</p><?php }?>
			<p><?php if ($_smarty_tpl->tpl_vars['payment_address']->value['customers_title']) {
echo $_smarty_tpl->tpl_vars['payment_address']->value['customers_title'];?>
 <?php }
echo $_smarty_tpl->tpl_vars['payment_address']->value['customers_firstname'];?>
 <?php echo $_smarty_tpl->tpl_vars['payment_address']->value['customers_lastname'];?>
</p>
			<p><?php echo $_smarty_tpl->tpl_vars['payment_address']->value['customers_street_address'];?>
</p>
            <?php if ($_smarty_tpl->tpl_vars['payment_address']->value['customers_address_addition']) {?></p><?php echo $_smarty_tpl->tpl_vars['payment_address']->value['customers_address_addition'];?>
</p><?php }?>
			<?php if ($_smarty_tpl->tpl_vars['payment_address']->value['customers_suburb']) {?><p><?php echo $_smarty_tpl->tpl_vars['payment_address']->value['customers_suburb'];?>
</p><?php }?>
			<p><?php echo $_smarty_tpl->tpl_vars['payment_address']->value['customers_postcode'];?>
 <?php echo $_smarty_tpl->tpl_vars['payment_address']->value['customers_city'];?>
</p>
			<p><?php echo $_smarty_tpl->tpl_vars['payment_address']->value['customers_country'];?>
</p>
		</div>
		<div class="well">
            <p class="headline-underline clearfix">
                <?php echo smarty_function_txt(array('key'=>TEXT_PAYMENT_METHOD),$_smarty_tpl);?>

                <a title="<?php echo smarty_function_txt(array('key'=>TEXT_EDIT),$_smarty_tpl);?>
" class="btn btn-xs btn-primary pull-right" href="<?php echo smarty_function_link(array('page'=>'checkout','paction'=>'payment','conn'=>'SSL'),$_smarty_tpl);?>
">
                    <span class="glyphicon glyphicon-pencil"></span>
                    <span class="sr-only"><?php echo smarty_function_txt(array('key'=>TEXT_EDIT),$_smarty_tpl);?>
</span>
                </a>
            </p>
			<?php if ($_smarty_tpl->tpl_vars['payment_info']->value['payment_name']) {?><p class="bold"><?php echo $_smarty_tpl->tpl_vars['payment_info']->value['payment_name'];?>
</p><?php }?>
			<?php if ($_smarty_tpl->tpl_vars['payment_info']->value['payment_desc']) {?><p><?php echo $_smarty_tpl->tpl_vars['payment_info']->value['payment_desc'];?>
</p><?php }?>
    		<?php if ($_smarty_tpl->tpl_vars['payment_info']->value['payment_info']) {?><p><?php echo $_smarty_tpl->tpl_vars['payment_info']->value['payment_info'];?>
</p><?php }?>
		</div>
		<div class="well">
			<p class="headline-underline clearfix">
                <?php echo smarty_function_txt(array('key'=>TEXT_COMMENTS),$_smarty_tpl);?>

                <a title="<?php echo smarty_function_txt(array('key'=>TEXT_EDIT),$_smarty_tpl);?>
" class="btn btn-xs btn-primary pull-right" href="<?php echo smarty_function_link(array('page'=>'checkout','paction'=>'payment','conn'=>'SSL'),$_smarty_tpl);?>
">
                    <span class="glyphicon glyphicon-pencil"></span>
                    <span class="sr-only"><?php echo smarty_function_txt(array('key'=>TEXT_EDIT),$_smarty_tpl);?>
</span>
                </a>
            </p>
			<?php if ($_SESSION['order_comments'] == '') {?>
			<p><?php echo smarty_function_txt(array('key'=>TEXT_NO_REMARK),$_smarty_tpl);?>
</p>
			<?php } else { ?>
			<p><?php echo nl2br((string) $_SESSION['order_comments'], (bool) 1);?>
</p>
			<?php }?>
		</div>
        <?php echo smarty_function_hook(array('key'=>'tpl_checkout_confirmation_info_boxes'),$_smarty_tpl);?>

	</div>
	<div class="col col-sm-8 col-md-9">
        <h1><?php echo smarty_function_txt(array('key'=>TEXT_CONFIRMATION_DESC),$_smarty_tpl);?>
</h1>
		<?php echo smarty_function_hook(array('key'=>'checkout_tpl_info'),$_smarty_tpl);?>

        <?php echo smarty_function_form(array('type'=>'form','name'=>'process','action'=>'checkout','method'=>'post','conn'=>'SSL','id'=>'checkout-form'),$_smarty_tpl);?>

        <?php echo smarty_function_form(array('type'=>'hidden','name'=>'action','value'=>'process'),$_smarty_tpl);?>


        <div class="alert alert-warning">
            <?php if ((defined('_STORE_TERMSCOND_CHECK') ? constant('_STORE_TERMSCOND_CHECK') : null) == 'true') {?>
                <?php echo smarty_function_content(array('cont_id'=>3,'is_id'=>'false'),$_smarty_tpl);?>

                <p class="checkbox">
                    <label>
                        <?php echo smarty_function_form(array('type'=>'checkbox','name'=>'conditions_accepted','class'=>"xt-form-required"),$_smarty_tpl);?>

                        <?php echo smarty_function_txt(array('key'=>TEXT_TERMSANDCOND_CONFIRMATION_5),$_smarty_tpl);?>
 <a href="<?php echo $_smarty_tpl->tpl_vars['_content_3']->value['content_link'];?>
" target="_blank"><?php echo smarty_function_txt(array('key'=>TEXT_TERMSANDCOND_CONFIRMATION_2),$_smarty_tpl);?>
</a><?php echo smarty_function_txt(array('key'=>TEXT_DOT),$_smarty_tpl);?>

                    </label>
                </p>
            <?php } else { ?>
                <?php echo smarty_function_content(array('cont_id'=>3,'is_id'=>'false'),$_smarty_tpl);?>

                <p><span class="glyphicon glyphicon-ok"></span> <strong><?php echo smarty_function_txt(array('key'=>TEXT_TERMSANDCOND_CONFIRMATION_4),$_smarty_tpl);?>
 <a href="<?php echo $_smarty_tpl->tpl_vars['_content_3']->value['content_link'];?>
" target="_blank"><?php echo smarty_function_txt(array('key'=>TEXT_TERMSANDCOND_CONFIRMATION_2),$_smarty_tpl);?>
</a><?php echo smarty_function_txt(array('key'=>TEXT_DOT),$_smarty_tpl);?>
</strong></p>
            <?php }?>
            <?php if ($_smarty_tpl->tpl_vars['show_digital_checkbox']->value == 'true') {?>
                <p class="checkbox">
                    <label>
                        <?php echo smarty_function_form(array('type'=>'checkbox','name'=>'withdrawal_reject_accepted','class'=>"xt-form-required"),$_smarty_tpl);?>

                        <?php echo smarty_function_txt(array('key'=>TEXT_DIGITALCOND_CHECK),$_smarty_tpl);?>

                    </label>
                </p>
            <?php }?>
            <?php echo smarty_function_hook(array('key'=>'checkout_tpl_form'),$_smarty_tpl);?>

        </div>

        <div class="div-table table-hover table-bordered">
            <div class="row th">
                <div class="col col-md-8">
                    <?php echo smarty_function_txt(array('key'=>TEXT_ARTICLE),$_smarty_tpl);?>

                </div>
                <div class="col col-md-2 hidden-xs hidden-sm text-right">
                    <?php echo smarty_function_txt(array('key'=>TEXT_SINGLE_PRICE),$_smarty_tpl);?>

                </div>
                <div class="col col-md-2 hidden-xs hidden-sm text-right">
                    <?php echo smarty_function_txt(array('key'=>TEXT_TOTAL_PRICE),$_smarty_tpl);?>

                </div>
            </div>
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['data']->value, 'product', false, 'key');
$_smarty_tpl->tpl_vars['product']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['product']->value) {
$_smarty_tpl->tpl_vars['product']->do_else = false;
?>
                <?php echo smarty_function_form(array('type'=>'hidden','name'=>'products_key[]','value'=>$_smarty_tpl->tpl_vars['product']->value['products_key']),$_smarty_tpl);?>

                <div class="row tr">
                    <div class="col col-xs-5 col-md-2">
                        <p class="image">
                            <?php if (!$_smarty_tpl->tpl_vars['product']->value['products_image'] || $_smarty_tpl->tpl_vars['product']->value['products_image'] == 'product:noimage.gif') {?>
                                <a href="<?php echo $_smarty_tpl->tpl_vars['product']->value['products_link'];?>
" class="vertical-helper image-link no-image img-thumbnail">
                                    <i class="no-image-icon"></i>
                                </a>
                            <?php } else { ?>
                                <a href="<?php echo $_smarty_tpl->tpl_vars['product']->value['products_link'];?>
" class="vertical-helper image-link img-thumbnail" target="_blank"><?php echo smarty_function_img(array('img'=>$_smarty_tpl->tpl_vars['product']->value['products_image'],'type'=>'m_thumb','class'=>"productImageBorder img-responsive",'alt'=>htmlspecialchars((string)$_smarty_tpl->tpl_vars['product']->value['products_name'], ENT_QUOTES, 'UTF-8', true)),$_smarty_tpl);?>
</a>
                            <?php }?>
                        </p>
                    </div>
                    <div class="col col-xs-7 col-md-6">
                        <p class="product-name break-word bold"><a href="<?php echo $_smarty_tpl->tpl_vars['product']->value['products_link'];?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['product']->value['products_name'];?>
</a></p>
                        <?php echo smarty_function_hook(array('key'=>'cart_xt_options','pid'=>$_smarty_tpl->tpl_vars['product']->value['products_id']),$_smarty_tpl);?>

                        <?php if ($_smarty_tpl->tpl_vars['product']->value['products_short_description']) {?>
                            <p class="product-description text-muted hidden-xs"><?php echo smarty_modifier_truncate(preg_replace('!<[^>]*?>!', ' ', $_smarty_tpl->tpl_vars['product']->value['products_short_description'] ?: ''),100,'...');?>
 (<a href="<?php echo $_smarty_tpl->tpl_vars['product']->value['products_link'];?>
" target="_blank"><?php echo smarty_function_txt(array('key'=>TEXT_MORE),$_smarty_tpl);?>
</a>)</p>
                        <?php } elseif ($_smarty_tpl->tpl_vars['product']->value['products_description']) {?>
                            <p class="product-description text-muted hidden-xs"><?php echo smarty_modifier_truncate(preg_replace('!<[^>]*?>!', ' ', $_smarty_tpl->tpl_vars['product']->value['products_description'] ?: ''),100,'...');?>
 (<a href="<?php echo $_smarty_tpl->tpl_vars['product']->value['products_link'];?>
" target="_blank"><?php echo smarty_function_txt(array('key'=>TEXT_MORE),$_smarty_tpl);?>
</a>)</p>
                        <?php }?>
                        <ul class="label-list fixed-padding">
                            <?php if ($_smarty_tpl->tpl_vars['product']->value['products_information']) {?><li><span class="badge"><?php echo smarty_modifier_replace(smarty_modifier_replace(smarty_modifier_replace(smarty_modifier_replace(smarty_modifier_replace(smarty_modifier_replace($_smarty_tpl->tpl_vars['product']->value['products_information'],'<tr class="contentrow1">',''),'<tr class="contentrow2">',''),'<td>',''),'<td class="left" colspan="4">',''),'</td>',''),'</tr>','');?>
</span></li><?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['product']->value['products_model'] != '') {?><li><span class="badge"><?php echo smarty_function_txt(array('key'=>TEXT_PRODUCTS_MODEL),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['product']->value['products_model'];?>
</span></li><?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['product']->value['products_weight'] > 0) {?><li><span class="badge"><?php echo smarty_function_txt(array('key'=>TEXT_PRODUCTS_WEIGHT),$_smarty_tpl);?>
 <?php echo smarty_modifier_weight_format($_smarty_tpl->tpl_vars['product']->value['products_weight'],"kg");?>
</span></li><?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['product']->value['shipping_status_data']['name']) {?><li><span class="badge"><?php echo smarty_function_txt(array('key'=>TEXT_SHIPPING_STATUS),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['product']->value['shipping_status_data']['name'];?>
</span></li><?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['product']->value['base_price']) {?><li><span class="badge"><?php echo smarty_modifier_number_format_prec($_smarty_tpl->tpl_vars['product']->value['products_vpe_value']);?>
 <?php echo $_smarty_tpl->tpl_vars['product']->value['base_price']['vpe']['name'];?>
 / <?php echo $_smarty_tpl->tpl_vars['product']->value['base_price']['price'];?>
 <?php echo smarty_function_txt(array('key'=>TEXT_SHIPPING_BASE_PER),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['product']->value['base_price']['vpe']['name'];?>
</span></li><?php }?>
                        </ul>

                                                <div class="hidden-md hidden-lg text-left-xs">
                            <span class="text-middle"><?php echo smarty_function_txt(array('key'=>TEXT_SINGLE_PRICE),$_smarty_tpl);?>
:</span><br />
                            <p class="product-price">
                                    <?php echo $_smarty_tpl->tpl_vars['product']->value['products_price']['formated'];?>

                                <?php if ($_smarty_tpl->tpl_vars['product']->value['_cart_discount']) {?>&nbsp;<span class="price-old"><?php echo $_smarty_tpl->tpl_vars['product']->value['_original_products_price']['formated'];?>
</span>&nbsp;<span class="small">(-<?php echo $_smarty_tpl->tpl_vars['product']->value['_cart_discount'];?>
 %)</span>
                                <?php }?>
                            </p>
                            <span class="text-middle"><?php echo smarty_function_txt(array('key'=>TEXT_TOTAL_PRICE),$_smarty_tpl);?>
:</span><br />
                            <p class="product-price final-price"><?php echo $_smarty_tpl->tpl_vars['product']->value['products_final_price']['formated'];?>
</p>
                        </div>
                        
                        <div class="form-inline">
                            <div class="form-group form-group-sm">
                                <div class="input-group">

                                    <input type="text"
                                           class="form-control disabled"
                                           disabled="disabled"
                                           id="form-qty-<?php echo $_smarty_tpl->tpl_vars['product']->value['products_id'];?>
"
                                           value="<?php echo $_smarty_tpl->tpl_vars['product']->value['products_quantity'];?>
"
                                           style="width: 6em" />
                                    <label for="form-qty-<?php echo $_smarty_tpl->tpl_vars['product']->value['products_id'];?>
" class="input-group-addon text-regular"><?php echo smarty_function_sys_status(array('id'=>$_smarty_tpl->tpl_vars['product']->value['products_unit']),$_smarty_tpl);?>
</label>
                                </div>

                            </div>
                            <a href="<?php echo smarty_function_link(array('page'=>'cart'),$_smarty_tpl);?>
" class="btn btn-sm btn-primary">
                                <span class="glyphicon glyphicon-pencil"></span>
                                <?php echo smarty_function_txt(array('key'=>TEXT_EDIT),$_smarty_tpl);?>

                            </a>
                        </div>
                    </div>
                    <div class="col col-md-2 hidden-xs hidden-sm text-right">
                        <p class="product-price">
                                <?php echo $_smarty_tpl->tpl_vars['product']->value['products_price']['formated'];?>

                            <?php if ($_smarty_tpl->tpl_vars['product']->value['_cart_discount']) {?><br />
                            <span class="price-old"><?php echo $_smarty_tpl->tpl_vars['product']->value['_original_products_price']['formated'];?>
</span><br />
                            <span class="small">(-<?php echo $_smarty_tpl->tpl_vars['product']->value['_cart_discount'];?>
 %)</span>
                            <?php }?>
                        </p>
                    </div>
                    <div class="col col-md-2 hidden-xs hidden-sm text-right">
                        <p class="product-price final-price"><?php echo $_smarty_tpl->tpl_vars['product']->value['products_final_price']['formated'];?>
</p>
                    </div>
                </div>
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
            <div class="row tfoot text-right">
                <div class="col col-md-offset-5 col-xs-5 col-md-5">
                    <?php echo smarty_function_hook(array('key'=>'checkout_tpl_form_total_lines_top'),$_smarty_tpl);?>

                    <?php echo smarty_function_hook(array('key'=>'checkout_tpl_form_total_lines'),$_smarty_tpl);?>

                    <?php echo smarty_function_txt(array('key'=>TEXT_SUB_TOTAL),$_smarty_tpl);?>

                </div>
                <div class="col col-xs-7 col-md-2">
                    <?php echo $_smarty_tpl->tpl_vars['sub_total']->value;?>

                </div>
            </div>
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['sub_data']->value, 'total');
$_smarty_tpl->tpl_vars['total']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['total']->value) {
$_smarty_tpl->tpl_vars['total']->do_else = false;
?>
                <div class="row tfoot text-right">
                    <div class="col col-md-offset-5 col-xs-5 col-md-5">
                        <?php echo $_smarty_tpl->tpl_vars['total']->value['products_name'];?>

                    </div>
                    <div class="col col-xs-7 col-md-2">
                        <?php echo $_smarty_tpl->tpl_vars['total']->value['products_price']['formated'];?>

                    </div>
                </div>
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
            <div class="row tfoot bold text-right">
                <div class="col col-md-offset-5 col-xs-5 col-md-5">
                    <?php echo smarty_function_txt(array('key'=>TEXT_TOTAL),$_smarty_tpl);?>

                </div>
                <div class="col col-xs-7 col-md-2">
                    <span id="grand-total-value"><?php echo $_smarty_tpl->tpl_vars['total']->value;?>
</span>
                </div>
            </div>
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['tax']->value, 'tax_data');
$_smarty_tpl->tpl_vars['tax_data']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['tax_data']->value) {
$_smarty_tpl->tpl_vars['tax_data']->do_else = false;
?>
                <div class="row tfoot text-right">
                    <div class="col col-md-offset-5 col-xs-5 col-md-5">
                        <?php echo smarty_function_txt(array('key'=>TEXT_TAX),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['tax_data']->value['tax_key'];?>
%
                    </div>
                    <div class="col col-xs-7 col-md-2">
                        <?php echo smarty_modifier_replace($_smarty_tpl->tpl_vars['tax_data']->value['tax_value']['formated'],"*",'');?>

                    </div>
                </div>
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
            <?php if ($_smarty_tpl->tpl_vars['discount']->value) {?>
                <div class="row tfoot text-right">
                    <div class="col col-md-offset-5 col-xs-5 col-md-5">
                        <strong><?php echo smarty_function_txt(array('key'=>TEXT_DISCOUNT_MADE),$_smarty_tpl);?>
</strong>
                    </div>
                    <div class="col col-xs-7 col-md-2">
                        <?php echo $_smarty_tpl->tpl_vars['discount']->value['formated'];?>

                    </div>
                </div>
            <?php }?>

            <?php echo smarty_function_hook(array('key'=>'checkout_tpl_form_total_lines_bottom'),$_smarty_tpl);?>

        </div><!-- .div-table -->

        <br />

        <?php if ($_smarty_tpl->tpl_vars['payment_info']->value['payment_cost_info'] == '1' && $_smarty_tpl->tpl_vars['language']->value == 'de') {?>
            <p class="alert alert-info"><?php echo smarty_function_txt(array('key'=>TEXT_ORDER_CONFIRMATION_BUTTON_LAW),$_smarty_tpl);?>
</p>
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['post_form']->value == '1') {?>
            <p class="alert alert-info"><?php echo smarty_function_txt(array('key'=>TEXT_INFO_PAY_NEXT_STEP),$_smarty_tpl);?>
</p>
        <?php }?>

        <div class="clearfix">
            <a href="<?php echo smarty_function_link(array('page'=>'checkout','paction'=>'payment','conn'=>'SSL'),$_smarty_tpl);?>
" class="btn btn-default pull-left">
                <span class="glyphicon glyphicon-chevron-left"></span>
                <?php echo smarty_function_txt(array('key'=>BUTTON_BACK),$_smarty_tpl);?>

            </a>
            <?php if ((isset($_smarty_tpl->tpl_vars['button_changed_html']->value))) {?>
                <?php echo $_smarty_tpl->tpl_vars['button_changed_html']->value;?>

            <?php } else { ?>
                <button type="submit" class="btn btn-success preloader pull-right">
                    <span class="glyphicon glyphicon-flag"></span>
                    <?php echo smarty_function_txt(array('key'=>BUTTON_CONFIRM_ORDER),$_smarty_tpl);?>

                </button>
            <?php }?>
        </div>

        <br />

        <?php echo smarty_function_hook(array('key'=>'checkout_tpl_confiramtion'),$_smarty_tpl);?>
        <?php echo smarty_function_hook(array('key'=>'checkout_tpl_confirmation'),$_smarty_tpl);?>

        <?php echo smarty_function_form(array('type'=>'formend'),$_smarty_tpl);?>

        <?php echo smarty_function_hook(array('key'=>'checkout_tpl_bottom'),$_smarty_tpl);?>

	</div>
</div><!-- #checkout-confirmation .row -->
<?php }
}
