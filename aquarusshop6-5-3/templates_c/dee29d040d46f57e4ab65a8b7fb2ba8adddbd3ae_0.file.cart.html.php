<?php
/* Smarty version 4.3.0, created on 2023-10-10 00:10:37
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive_AQ/xtCore/pages/cart.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.0',
  'unifunc' => 'content_65247a5defad89_51559786',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'dee29d040d46f57e4ab65a8b7fb2ba8adddbd3ae' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive_AQ/xtCore/pages/cart.html',
      1 => 1691797589,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_65247a5defad89_51559786 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.form.php','function'=>'smarty_function_form',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),2=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.link.php','function'=>'smarty_function_link',),3=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.img.php','function'=>'smarty_function_img',),4=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.hook.php','function'=>'smarty_function_hook',),5=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/vendor/smarty/smarty/libs/plugins/modifier.truncate.php','function'=>'smarty_modifier_truncate',),6=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/vendor/smarty/smarty/libs/plugins/modifier.replace.php','function'=>'smarty_modifier_replace',),7=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/vendor/smarty/smarty/libs/plugins/modifier.number_format.php','function'=>'smarty_modifier_number_format',),8=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/modifier.number_format_prec.php','function'=>'smarty_modifier_number_format_prec',),9=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/modifier.weight_format.php','function'=>'smarty_modifier_weight_format',),10=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.box.php','function'=>'smarty_function_box',),));
?>
<div id="cart">

    <?php if ($_smarty_tpl->tpl_vars['show_cart_content']->value == true) {?>

        <?php echo smarty_function_form(array('type'=>'form','name'=>'cart','action'=>'dynamic','link_params'=>'getParams','method'=>'post','conn'=>'SSL'),$_smarty_tpl);?>

        <?php echo smarty_function_form(array('type'=>'hidden','name'=>'action','value'=>'update_product'),$_smarty_tpl);?>


        <div class="row">
            <div class="col col-md-6">
                <h1><?php echo smarty_function_txt(array('key'=>TEXT_CART),$_smarty_tpl);?>
</h1>
            </div>
            <div class="col col-md-6 text-right">
                <div class="btn-group">
                    <a href="javascript:history.back();" class="btn btn-default">
                        <i class="fa fa-chevron-left"></i>
                        <?php echo smarty_function_txt(array('key'=>BUTTON_BACK),$_smarty_tpl);?>

                    </a>
                    <button type="submit" class="btn btn-default hidden-xs">
                        <i class="fa fa-refresh"></i>
                        <?php echo smarty_function_txt(array('key'=>BUTTON_UPDATE),$_smarty_tpl);?>

                    </button>
                    <a class="btn btn-success" href="<?php echo smarty_function_link(array('page'=>'checkout','paction'=>'shipping','conn'=>'SSL'),$_smarty_tpl);?>
">
                        <?php echo smarty_function_txt(array('key'=>BUTTON_CHECKOUT),$_smarty_tpl);?>

                    </a>
                </div>
            </div>
        </div>

        <br />

        <?php echo $_smarty_tpl->tpl_vars['message']->value;?>


        <div id="cart-table" class="div-table table-hover table-bordered">
            <div class="row th">
                <div class="col col-sm-8">
                    <?php echo smarty_function_txt(array('key'=>TEXT_ARTICLE),$_smarty_tpl);?>

                </div>
                <div class="col col-sm-2 hidden-xs text-right">
                    <?php echo smarty_function_txt(array('key'=>TEXT_SINGLE_PRICE),$_smarty_tpl);?>

                </div>
                <div class="col col-sm-2 hidden-xs text-right">
                    <?php echo smarty_function_txt(array('key'=>TEXT_TOTAL_PRICE),$_smarty_tpl);?>

                </div>
            </div>
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['cart_data']->value, 'data', false, 'key', 'aussen', array (
));
$_smarty_tpl->tpl_vars['data']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['data']->value) {
$_smarty_tpl->tpl_vars['data']->do_else = false;
?>
                <?php echo smarty_function_form(array('type'=>'hidden','name'=>'products_key[]','value'=>$_smarty_tpl->tpl_vars['data']->value['products_key']),$_smarty_tpl);?>

                <div class="row tr">
                    <div class="col col-xs-5 col-sm-2">
                        <p class="image">
                            <?php if (!$_smarty_tpl->tpl_vars['data']->value['products_image'] || $_smarty_tpl->tpl_vars['data']->value['products_image'] == 'product:noimage.gif') {?>
                                <a href="<?php echo $_smarty_tpl->tpl_vars['data']->value['products_link'];?>
" class="vertical-helper image-link no-image img-thumbnail">
                                    <i class="no-image-icon"></i>
                                </a>
                            <?php } else { ?>
                                <a href="<?php echo $_smarty_tpl->tpl_vars['data']->value['products_link'];?>
" class="vertical-helper image-link img-thumbnail"><?php echo smarty_function_img(array('img'=>$_smarty_tpl->tpl_vars['data']->value['products_image'],'type'=>'m_thumb','class'=>"productImageBorder img-responsive",'alt'=>htmlspecialchars((string)$_smarty_tpl->tpl_vars['data']->value['products_name'], ENT_QUOTES, 'UTF-8', true)),$_smarty_tpl);?>
</a>
                            <?php }?>
                        </p>
                    </div>
                    <div class="col col-xs-7 col-sm-6 col-lg-6">
                        <p class="product-name break-word bold"><a href="<?php echo $_smarty_tpl->tpl_vars['data']->value['products_link'];?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['data']->value['products_name'];?>
</a></p>
                        <?php echo smarty_function_hook(array('key'=>'cart_bundle'),$_smarty_tpl);?>

                        <?php echo smarty_function_hook(array('key'=>'cart_xt_options','pid'=>$_smarty_tpl->tpl_vars['data']->value['products_id']),$_smarty_tpl);?>

                        <?php if ($_smarty_tpl->tpl_vars['data']->value['products_short_description']) {?>
                            <p class="product-description text-muted"><?php echo smarty_modifier_truncate(preg_replace('!<[^>]*?>!', ' ', $_smarty_tpl->tpl_vars['data']->value['products_short_description'] ?: ''),100,'...');?>
 (<a href="<?php echo $_smarty_tpl->tpl_vars['data']->value['products_link'];?>
"><?php echo smarty_function_txt(array('key'=>TEXT_MORE),$_smarty_tpl);?>
</a>)</p>
                        <?php } elseif ($_smarty_tpl->tpl_vars['data']->value['products_description']) {?>
                            <p class="product-description text-muted"><?php echo smarty_modifier_truncate(preg_replace('!<[^>]*?>!', ' ', $_smarty_tpl->tpl_vars['data']->value['products_description'] ?: ''),100,'...');?>
 (<a href="<?php echo $_smarty_tpl->tpl_vars['data']->value['products_link'];?>
"><?php echo smarty_function_txt(array('key'=>TEXT_MORE),$_smarty_tpl);?>
</a>)</p>
                        <?php }?>
                        <ul class="label-list fixed-padding">
                            <?php if ($_smarty_tpl->tpl_vars['data']->value['products_information']) {?><li><span class="badge"><?php echo smarty_modifier_replace(smarty_modifier_replace(smarty_modifier_replace(smarty_modifier_replace(smarty_modifier_replace(smarty_modifier_replace($_smarty_tpl->tpl_vars['data']->value['products_information'],'<tr class="contentrow1">',''),'<tr class="contentrow2">',''),'<td>',''),'<td class="left" colspan="4">',''),'</td>',''),'</tr>','');?>
</span></li><?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['data']->value['products_model'] != '') {?><li><span class="badge"><?php echo smarty_function_txt(array('key'=>TEXT_PRODUCTS_MODEL),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['data']->value['products_model'];?>
</span></li><?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['data']->value['products_weight'] > 0) {?><li><span class="badge"><?php echo smarty_function_txt(array('key'=>TEXT_PRODUCTS_WEIGHT),$_smarty_tpl);?>
 <?php echo smarty_modifier_number_format($_smarty_tpl->tpl_vars['data']->value['products_weight'],2,",",".");?>
 kg</span></li><?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['data']->value['shipping_status_data']['name']) {?><li><span class="badge"><?php echo smarty_function_txt(array('key'=>TEXT_SHIPPING_STATUS),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['data']->value['shipping_status_data']['name'];?>
</span></li><?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['data']->value['base_price']) {?><li><span class="badge"><?php echo smarty_modifier_number_format_prec($_smarty_tpl->tpl_vars['data']->value['products_vpe_value']);?>
 <?php echo $_smarty_tpl->tpl_vars['data']->value['base_price']['vpe']['name'];?>
 / <?php echo $_smarty_tpl->tpl_vars['data']->value['base_price']['price'];?>
 <?php echo smarty_function_txt(array('key'=>TEXT_SHIPPING_BASE_PER),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['data']->value['base_price']['vpe']['name'];?>
</span></li><?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['data']->value['stock_image']) {?>
                            <!--li class="product-stock-image">
                                <img src="<?php echo smarty_function_img(array('img'=>$_smarty_tpl->tpl_vars['data']->value['stock_image']['image'],'type'=>'t_img_stockrules','path_only'=>true),$_smarty_tpl);?>
"
                                     class="img-responsive cursor-help"
                                     alt="<?php echo $_smarty_tpl->tpl_vars['data']->value['stock_image']['name'];?>
"
                                     title="<?php echo $_smarty_tpl->tpl_vars['data']->value['stock_image']['name'];?>
"
                                     data-toggle="tooltip" />
                                <span class="sr-only"><?php echo $_smarty_tpl->tpl_vars['stock_image']->value['name'];?>
</span>
                            </li-->
                            <?php }?>
                        </ul>

                                                <div class="hidden-sm hidden-md hidden-lg text-left-xs">
                            <span><?php echo smarty_function_txt(array('key'=>TEXT_SINGLE_PRICE),$_smarty_tpl);?>
:</span><br />
                            <p class="product-price">
                                    <?php echo $_smarty_tpl->tpl_vars['data']->value['products_price']['formated'];?>

                            </p>
                            <span><?php echo smarty_function_txt(array('key'=>TEXT_TOTAL_PRICE),$_smarty_tpl);?>
:</span><br />
                            <p class="product-price final-price"><?php echo $_smarty_tpl->tpl_vars['data']->value['products_final_price']['formated'];?>
</p>
                        </div>
                        
                        <div class="form-inline">
                            <div class="form-group form-group-sm">
                                <div class="input-group">

                                    <input type="number"
                                           class="form-control"
                                           <?php if ((defined('_STORE_ALLOW_DECIMAL_QUANTITIY') ? constant('_STORE_ALLOW_DECIMAL_QUANTITIY') : null) == 'false') {?>min="1"<?php }?>
                                           required="required"
                                           step="any"
                                           name="qty[]"
                                           autocomplete="off"
                                           id="form-qty-<?php echo $_smarty_tpl->tpl_vars['data']->value['products_id'];?>
"
                                           value="<?php echo $_smarty_tpl->tpl_vars['data']->value['products_quantity'];?>
"
                                           placeholder="<?php echo smarty_function_txt(array('key'=>TEXT_QTY),$_smarty_tpl);?>
"
                                           style="width: 6em" />
                                    <label for="form-qty-<?php echo $_smarty_tpl->tpl_vars['data']->value['products_id'];?>
" class="input-group-addon text-regular"><?php echo $_smarty_tpl->tpl_vars['data']->value['products_unit_name'];?>
</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-sm btn-default">
                                <i class="fa fa-refresh"></i>
                                <?php echo smarty_function_txt(array('key'=>BUTTON_UPDATE),$_smarty_tpl);?>

                            </button>
                            <span data-toggle="buttons">
                                <label class="btn btn-sm btn-danger">
                                    <span class="glyphicon glyphicon-trash"></span>
                                    <span class="hidden-xs"><?php echo smarty_function_txt(array('key'=>TEXT_REMOVE),$_smarty_tpl);?>
</span>
                                    <?php echo smarty_function_form(array('type'=>'checkbox','name'=>'cart_delete[]','value'=>$_smarty_tpl->tpl_vars['data']->value['products_key'],'onchange'=>"this.form.submit();"),$_smarty_tpl);?>

                                </label>
                            </span>
                        </div>
                    </div>
                    <div class="col col-sm-2 hidden-xs text-right">
                        <p class="product-price">
                                <?php echo $_smarty_tpl->tpl_vars['data']->value['products_price']['formated'];?>

                            <?php if ($_smarty_tpl->tpl_vars['data']->value['_cart_discount_data']['plain']) {?><br />
                            <span class="price-old"><?php echo $_smarty_tpl->tpl_vars['data']->value['_original_products_price']['formated'];?>
</span><br />
                            <span class="small">(-<?php echo $_smarty_tpl->tpl_vars['data']->value['_cart_discount'];?>
 %)</span>
                            <?php }?>
                        </p>
                    </div>
                    <div class="col col-sm-2 hidden-xs text-right">
                        <p class="product-price final-price"><?php echo $_smarty_tpl->tpl_vars['data']->value['products_final_price']['formated'];?>
</p>
                    </div>
                </div>
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
            <?php echo smarty_function_hook(array('key'=>'cart_total_top'),$_smarty_tpl);?>

            <div class="row tfoot bold text-right">
                <div class="col col-sm-offset-4  col-xs-7 col-sm-6">
                    <?php echo smarty_function_txt(array('key'=>TEXT_SUB_TOTAL),$_smarty_tpl);?>

                </div>
                <div class="col  col-xs-5 col-sm-2">
                    <?php echo $_smarty_tpl->tpl_vars['cart_total']->value;?>

                </div>
            </div>
            <?php echo smarty_function_hook(array('key'=>'cart_total_tpl'),$_smarty_tpl);?>

            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['cart_tax']->value, 'tax_data', false, NULL, 'aussen', array (
));
$_smarty_tpl->tpl_vars['tax_data']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['tax_data']->value) {
$_smarty_tpl->tpl_vars['tax_data']->do_else = false;
?>
                <div class="row tfoot text-right">
                    <div class="col col-sm-offset-4  col-xs-7 col-sm-6">
                        <?php echo smarty_function_txt(array('key'=>TEXT_TAX),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['tax_data']->value['tax_key'];?>
%
                        <?php if ($_smarty_tpl->tpl_vars['shipping_link']->value) {?><br /><span class="shipping-link"><a href="<?php echo $_smarty_tpl->tpl_vars['shipping_link']->value;?>
" target="_blank" rel="nofollow"><?php echo smarty_function_txt(array('key'=>TEXT_EXCL_SHIPPING),$_smarty_tpl);?>
</a></span><?php }?>
                    </div>
                    <div class="col  col-xs-5 col-sm-2 text-right">
                        <?php echo smarty_modifier_replace($_smarty_tpl->tpl_vars['tax_data']->value['tax_value']['formated'],"*",'');?>

                    </div>
                </div>
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
            <?php if ($_smarty_tpl->tpl_vars['discount']->value) {?>
                <div class="row tfoot text-right">
                    <div class="col col-sm-offset-4  col-xs-7 col-sm-6">
                        <?php echo smarty_function_txt(array('key'=>TEXT_DISCOUNT_MADE),$_smarty_tpl);?>

                    </div>
                    <div class="col  col-xs-5 col-sm-2">
                        <?php echo $_smarty_tpl->tpl_vars['discount']->value['formated'];?>

                    </div>
                </div>
            <?php }?>
            <?php if ($_smarty_tpl->tpl_vars['cart_total_weight']->value > 0) {?>
                <div class="row tfoot text-right">
                    <div class="col col-sm-offset-4  col-xs-7 col-sm-6">
                        <?php echo smarty_function_txt(array('key'=>TEXT_PRODUCTS_WEIGHT),$_smarty_tpl);?>

                    </div>
                    <div class="col  col-xs-5 col-sm-2">
                    	<?php echo smarty_modifier_weight_format($_smarty_tpl->tpl_vars['cart_total_weight']->value,"kg");?>

                    </div>
                </div>
            <?php }?>
            <?php echo smarty_function_hook(array('key'=>'cart_total_bottom'),$_smarty_tpl);?>

        </div><!-- .div-table -->

        <br />

        <p class="visible-xs">
            <a class="btn btn-success btn-block btn-lg" href="<?php echo smarty_function_link(array('page'=>'checkout','paction'=>'shipping','conn'=>'SSL'),$_smarty_tpl);?>
">
                <?php echo smarty_function_txt(array('key'=>BUTTON_CHECKOUT),$_smarty_tpl);?>

            </a>
        </p>

        <div class="btn-group text-right-sm hidden-xs">
            <a href="javascript:history.back();" class="btn btn-default">
                <i class="fa fa-chevron-left"></i>
                <?php echo smarty_function_txt(array('key'=>BUTTON_BACK),$_smarty_tpl);?>

            </a>
            <button type="submit" class="btn btn-default">
                <i class="fa fa-refresh"></i>
                <?php echo smarty_function_txt(array('key'=>BUTTON_UPDATE),$_smarty_tpl);?>

            </button>
        </div>

        <div class="btn-group pull-right">
        <a class="btn btn-success pull-right hidden-xs" href="<?php echo smarty_function_link(array('page'=>'checkout','paction'=>'shipping','conn'=>'SSL'),$_smarty_tpl);?>
">
            <?php echo smarty_function_txt(array('key'=>BUTTON_CHECKOUT),$_smarty_tpl);?>

        </a>
	</div>

        <p class="pull-right">&nbsp;&nbsp;</p>

        <?php echo smarty_function_hook(array('key'=>'cart_tpl_form_paypal'),$_smarty_tpl);?>

        <?php echo smarty_function_hook(array('key'=>'cart_tpl_form'),$_smarty_tpl);?>


        <br class="clearfix" />
        <br />

        <?php echo smarty_function_form(array('type'=>'formend'),$_smarty_tpl);?>


    <?php } else { ?>
        <h1><?php echo smarty_function_txt(array('key'=>TEXT_CART),$_smarty_tpl);?>
</h1>
        <?php echo $_smarty_tpl->tpl_vars['message']->value;?>

        <a href="javascript:history.back();" class="btn btn-default">
            <i class="fa fa-chevron-left"></i>
            <?php echo smarty_function_txt(array('key'=>BUTTON_BACK),$_smarty_tpl);?>

        </a>
    <?php }?>

    <?php if ($_SESSION['cart']->type != 'virtual') {
echo smarty_function_box(array('name'=>'shipping_cost'),$_smarty_tpl);
}?>
    <?php echo smarty_function_hook(array('key'=>'cart_bottom'),$_smarty_tpl);?>


</div><!-- #cart -->
<?php }
}
