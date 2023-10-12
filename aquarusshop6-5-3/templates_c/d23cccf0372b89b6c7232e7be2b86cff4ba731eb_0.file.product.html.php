<?php
/* Smarty version 4.3.0, created on 2023-10-08 01:15:54
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive_AQ/xtCore/pages/product/product.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.0',
  'unifunc' => 'content_6521e6aa2e8430_80547136',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd23cccf0372b89b6c7232e7be2b86cff4ba731eb' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive_AQ/xtCore/pages/product/product.html',
      1 => 1691797603,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:includes/product_info_label.html' => 2,
    'file:xtCore/pages/files/product_public_files.html' => 1,
  ),
),false)) {
function content_6521e6aa2e8430_80547136 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/vendor/smarty/smarty/libs/plugins/modifier.count.php','function'=>'smarty_modifier_count',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.hook.php','function'=>'smarty_function_hook',),2=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.img.php','function'=>'smarty_function_img',),3=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),4=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/modifier.number_format_prec.php','function'=>'smarty_modifier_number_format_prec',),5=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.price_table.php','function'=>'smarty_function_price_table',),6=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/modifier.weight_format.php','function'=>'smarty_modifier_weight_format',),7=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.form.php','function'=>'smarty_function_form',),8=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/vendor/smarty/smarty/libs/plugins/function.math.php','function'=>'smarty_function_math',),));
echo $_smarty_tpl->tpl_vars['message']->value;?>

<div id="product" class="detail">
    <div class="pinfo row<?php if ($_smarty_tpl->tpl_vars['flag_has_specials']->value == 1) {?> special-price<?php }
if ($_smarty_tpl->tpl_vars['date_available']->value != '') {?> available-soon<?php }?>">
        <div class="col col-sm-7 col-md-8">
            <a name="product_images" style="line-height: 0px; text-decoration: none" >&nbsp;</a>
            <div class="lightgallery product-images row">
                <div class="col<?php if (!$_smarty_tpl->tpl_vars['more_images']->value || smarty_modifier_count($_smarty_tpl->tpl_vars['more_images']->value) == 0) {?> col-sm-12<?php } else { ?> col-md-10 col-md-push-2<?php }?>">
                    <div class="image product-image center">
                        <?php if (!$_smarty_tpl->tpl_vars['products_image']->value || $_smarty_tpl->tpl_vars['products_image']->value == 'product:noimage.gif') {?>
                            <span class="vertical-helper image-link no-image img-thumbnail img-label-wrap">
                                <i class="no-image-icon"></i>
                                <?php $_smarty_tpl->_subTemplateRender("file:includes/product_info_label.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('position'=>"product-page",'isSpecial'=>$_smarty_tpl->tpl_vars['flag_has_specials']->value,'dateAvailable'=>$_smarty_tpl->tpl_vars['date_available']->value), 0, false);
?>
                                <?php echo smarty_function_hook(array('key'=>'tpl_product_no_img','module_data'=>$_smarty_tpl->tpl_vars['product']->value),$_smarty_tpl);?>

                            </span>
                        <?php } else { ?>
                            <figure>
                                <a href="<?php echo smarty_function_img(array('img'=>$_smarty_tpl->tpl_vars['products_image']->value,'type'=>'m_org','path_only'=>true),$_smarty_tpl);?>
"class="image-link cursor-zoom" data-type="main">
                                    <span class="img-thumbnail img-label-wrap">
                                        <img src="<?php echo smarty_function_img(array('img'=>$_smarty_tpl->tpl_vars['products_image']->value,'type'=>'m_info','path_only'=>true),$_smarty_tpl);?>
" alt="<?php if ($_smarty_tpl->tpl_vars['products_image_data']->value['media_name']) {
echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['products_image_data']->value['media_name'], ENT_QUOTES, 'UTF-8', true);
} else {
echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['products_name']->value, ENT_QUOTES, 'UTF-8', true);
}
if ($_smarty_tpl->tpl_vars['products_image_data']->value['copyright_holder']) {?> &copy; <?php echo $_smarty_tpl->tpl_vars['products_image_data']->value['copyright_holder'];
}?>" title="<?php if ($_smarty_tpl->tpl_vars['products_image_data']->value['media_name']) {
echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['products_image_data']->value['media_name'], ENT_QUOTES, 'UTF-8', true);
} else {
echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['products_name']->value, ENT_QUOTES, 'UTF-8', true);
}
if ($_smarty_tpl->tpl_vars['products_image_data']->value['copyright_holder']) {?> &copy; <?php echo $_smarty_tpl->tpl_vars['products_image_data']->value['copyright_holder'];
}?>" class="img-responsive image-link">
                                        <?php $_smarty_tpl->_subTemplateRender("file:includes/product_info_label.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('position'=>"product-page",'isSpecial'=>$_smarty_tpl->tpl_vars['flag_has_specials']->value,'dateAvailable'=>$_smarty_tpl->tpl_vars['date_available']->value), 0, true);
?>
                                        <?php echo smarty_function_hook(array('key'=>'tpl_product_img','module_data'=>$_smarty_tpl->tpl_vars['product']->value),$_smarty_tpl);?>

                                    </span>
                                </a>
                                <?php if ($_smarty_tpl->tpl_vars['products_image_data']->value['copyright_holder']) {?>
                                <span class="copyright text-small" style="display: inline-flex">&copy; <?php echo $_smarty_tpl->tpl_vars['products_image_data']->value['copyright_holder'];?>
</span>
                                <?php }?>
                            </figure>
                        <?php }?>
                    </div>
                </div>
                <?php if ($_smarty_tpl->tpl_vars['more_images']->value && smarty_modifier_count($_smarty_tpl->tpl_vars['more_images']->value) != 0) {?>
                    <div class="col col-md-2 col-md-pull-10">
                        <div class="more-images row listing equalize-nothing">
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['more_images']->value, 'img_data', false, 'index', 'aussen', array (
));
$_smarty_tpl->tpl_vars['img_data']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['index']->value => $_smarty_tpl->tpl_vars['img_data']->value) {
$_smarty_tpl->tpl_vars['img_data']->do_else = false;
?>
                            <figure class="section col<?php if ($_smarty_tpl->tpl_vars['index']->value > 3) {?> hidden<?php }
if ($_smarty_tpl->tpl_vars['index']->value == 3 && smarty_modifier_count($_smarty_tpl->tpl_vars['more_images']->value) > 4) {?> show-more<?php }?> col-xs-3 col-md-12">
                                <a href="<?php echo smarty_function_img(array('img'=>$_smarty_tpl->tpl_vars['img_data']->value['file'],'type'=>'m_org','path_only'=>true),$_smarty_tpl);?>
" class="vertical-helper image-link text-center img-thumbnail" >
                                    <img src="<?php echo smarty_function_img(array('img'=>$_smarty_tpl->tpl_vars['img_data']->value['file'],'type'=>'m_thumb','path_only'=>true),$_smarty_tpl);?>
" alt="<?php if ($_smarty_tpl->tpl_vars['img_data']->value['data']['media_name']) {
echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['img_data']->value['data']['media_name'], ENT_QUOTES, 'UTF-8', true);
} else {
echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['products_name']->value, ENT_QUOTES, 'UTF-8', true);
}
if ($_smarty_tpl->tpl_vars['img_data']->value['data']['copyright_holder']) {?> &copy; <?php echo $_smarty_tpl->tpl_vars['img_data']->value['data']['copyright_holder'];
}?>" title="<?php if ($_smarty_tpl->tpl_vars['img_data']->value['data']['media_name']) {
echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['img_data']->value['data']['media_name'], ENT_QUOTES, 'UTF-8', true);
} else {
echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['products_name']->value, ENT_QUOTES, 'UTF-8', true);
}
if ($_smarty_tpl->tpl_vars['img_data']->value['data']['copyright_holder']) {?> &copy; <?php echo $_smarty_tpl->tpl_vars['img_data']->value['data']['copyright_holder'];
}?>" class="productImageBorder img-responsive" width="auto" height="auto">
                                </a>
                            </figure>
                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                        </div>
                    </div>
                <?php }?>
                <hr class="visible-xs">
            </div>
            <?php echo smarty_function_hook(array('key'=>'products_images_tpl'),$_smarty_tpl);?>

        </div>
        <div class="col col-sm-5 col-md-4">

            <?php if ($_smarty_tpl->tpl_vars['product_outdated']->value == 1) {?>
            <div class="alert alert-error alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                <p class="item"><span class="glyphicon glyphicon-bullhorn"></span>&nbsp;&nbsp;<?php echo smarty_function_txt(array('key'=>'PRODUCT_OUTDATED_SHORT'),$_smarty_tpl);?>
</p>
            </div>
            <?php }?>
            <?php if ($_smarty_tpl->tpl_vars['product_outdated_soon']->value == 1) {?>
            <div class="alert alert-warning alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                <p class="item"><span class="glyphicon glyphicon-bullhorn"></span>&nbsp;&nbsp;<?php echo smarty_function_txt(array('key'=>'PRODUCT_OUTDATED_SOON_SHORT'),$_smarty_tpl);?>
</p>
            </div>
            <?php }?>

            <a name="product_info" style="line-height: 0px; text-decoration: none" >&nbsp;</a>
            <div class="product-info">
                <?php echo smarty_function_hook(array('key'=>'product_info_top','product'=>$_smarty_tpl->tpl_vars['product']->value),$_smarty_tpl);?>

                <?php if ($_smarty_tpl->tpl_vars['manufacturer']->value['manufacturers_image']) {?>
                    <p class="product-manufacturer image pull-right">
                        <a href="<?php echo $_smarty_tpl->tpl_vars['manufacturer']->value['link'];?>
"><img src="<?php echo smarty_function_img(array('img'=>$_smarty_tpl->tpl_vars['manufacturer']->value['manufacturers_image'],'type'=>'m_manufacturer_thumb','path_only'=>true),$_smarty_tpl);?>
"
                             alt="<?php echo smarty_function_txt(array('key'=>HEADING_PRODUCTS_MANUFACTURERS),$_smarty_tpl);?>
: <?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['manufacturer']->value['manufacturers_name'], ENT_QUOTES, 'UTF-8', true);?>
"
                             title="<?php echo smarty_function_txt(array('key'=>HEADING_PRODUCTS_MANUFACTURERS),$_smarty_tpl);?>
: <?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['manufacturer']->value['manufacturers_name'], ENT_QUOTES, 'UTF-8', true);?>
"
                             class="img-responsive img-thumbnail"
                        ></a>
                    </p>
                <?php }?>
                <h1 class="title h3 text-word-wrap"><?php echo $_smarty_tpl->tpl_vars['products_name']->value;?>
</h1>
                <?php if ($_smarty_tpl->tpl_vars['products_model']->value != '') {?>
                    <p class="product-model float-sm-left">
                        <span class="text-small"><?php echo smarty_function_txt(array('key'=>TEXT_PRODUCTS_MODEL),$_smarty_tpl);?>
:</span>
                        <span class="badge badge-lighter"><?php echo $_smarty_tpl->tpl_vars['products_model']->value;?>
</span>
                    </p>
                <?php }?>
                <!-- <?php if ($_smarty_tpl->tpl_vars['products_ean']->value != '') {?>
                    <p class="product-model float-sm-left">
                        <span class="text-small"><?php echo smarty_function_txt(array('key'=>TEXT_PRODUCTS_EAN),$_smarty_tpl);?>
:</span>
                        <span class="badge badge-lighter"><?php echo $_smarty_tpl->tpl_vars['products_ean']->value;?>
</span>
                    </p>
                <?php }?> /-->
                <?php if ($_smarty_tpl->tpl_vars['manufacturer']->value['manufacturers_name']) {?>
                    <p class="product-manufacturer name">
                        <span class="text-small"><?php echo smarty_function_txt(array('key'=>HEADING_PRODUCTS_MANUFACTURERS),$_smarty_tpl);?>
:</span>
                        <span class="badge badge-lighter"><a href="<?php echo $_smarty_tpl->tpl_vars['manufacturer']->value['link'];?>
"><?php echo $_smarty_tpl->tpl_vars['manufacturer']->value['manufacturers_name'];?>
</a></span>
                    </p>
                <?php }?>
                <?php if ($_smarty_tpl->tpl_vars['products_url']->value) {?>
                <p class="product-url name">
                    <span class="text-small"><a href="<?php echo $_smarty_tpl->tpl_vars['products_url']->value;?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['products_url']->value;?>
</a>&nbsp;<span class="text-small glyphicon glyphicon-new-window"></span></span>
                </p>
                <?php }?>
                <div class="clearfix"></div>
                <?php if ((defined('AQ_SHOW_ARTICEL_PRICE') ? constant('AQ_SHOW_ARTICEL_PRICE') : null) != '1' && $_smarty_tpl->tpl_vars['products_price']->value['plain'] != '0' || round((float) $_smarty_tpl->tpl_vars['products_quantity']->value, (int) 0, (int) 1) > '0') {?>
                    <?php if ((defined('_CUST_STATUS_SHOW_PRICE') ? constant('_CUST_STATUS_SHOW_PRICE') : null) == '1' && $_smarty_tpl->tpl_vars['products_price']->value['formated'] != '') {?>
                        <div>
                            <p class="product-price this">
                                <?php echo $_smarty_tpl->tpl_vars['products_price']->value['formated'];?>

                                <br class="visible-xs">
                                <span class="product-tax"><?php echo $_smarty_tpl->tpl_vars['products_tax_info']->value['tax_desc'];?>
</span>
                            </p>
                        </div>
                        <ul class="product-meta-info">
                            <?php if (!$_smarty_tpl->tpl_vars['products_digital']->value) {?>
                                <?php if ($_smarty_tpl->tpl_vars['products_shipping_link']->value) {?>
                                    <li class="product-shipping"><a href="<?php echo $_smarty_tpl->tpl_vars['products_shipping_link']->value;?>
" target="_blank" rel="nofollow"><?php echo smarty_function_txt(array('key'=>TEXT_EXCL_SHIPPING),$_smarty_tpl);?>
</a></li>
                                <?php } else { ?>
                                    <li class="product-shipping"><?php echo smarty_function_txt(array('key'=>TEXT_EXCL_SHIPPING),$_smarty_tpl);?>
</li>
                                <?php }?>
                            <?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['base_price']->value) {?>
                                <li class="vpe"><?php echo smarty_modifier_number_format_prec($_smarty_tpl->tpl_vars['products_vpe_value']->value);?>
 <?php echo $_smarty_tpl->tpl_vars['base_price']->value['vpe']['name'];?>
 / <?php echo $_smarty_tpl->tpl_vars['base_price']->value['price'];?>
 <?php echo smarty_function_txt(array('key'=>TEXT_SHIPPING_BASE_PER),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['base_price']->value['vpe']['name'];?>
</li>
                            <?php }?>
                        </ul>
                        <?php echo smarty_function_price_table(array(),$_smarty_tpl);?>

                    <?php }?>
                <?php } else { ?>
                    <p><?php echo (defined('AQ_NO_PRICE_TEXT') ? constant('AQ_NO_PRICE_TEXT') : null);?>
</p>
                <?php }?>
                <ul class="product-meta-info bold">
                    <?php if ($_smarty_tpl->tpl_vars['stock_image']->value) {?>
                        <li class="product-stock-image">
                            <img src="<?php echo smarty_function_img(array('img'=>$_smarty_tpl->tpl_vars['stock_image']->value['image'],'type'=>'t_img_stockrules','path_only'=>true),$_smarty_tpl);?>
"
                                 class="img-responsive cursor-help"
                                 alt="<?php echo $_smarty_tpl->tpl_vars['stock_image']->value['name'];?>
"
                                 title="<?php echo $_smarty_tpl->tpl_vars['stock_image']->value['name'];?>
"
                                 data-toggle="tooltip" />
                            <span class="sr-only"><?php echo $_smarty_tpl->tpl_vars['stock_image']->value['name'];?>
</span>
                        </li>
                    <?php }?>
                    
                    <?php if ($_smarty_tpl->tpl_vars['shipping_status_data']->value['name']) {?>
                        <li class="product-shipping-status"><?php echo smarty_function_txt(array('key'=>TEXT_SHIPPING_STATUS),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['shipping_status_data']->value['name'];?>
</li>
                        <?php if ($_smarty_tpl->tpl_vars['shipping_status_data']->value['image']) {?>
                        <li class="product-stock-image">
                            <img src="<?php echo smarty_function_img(array('img'=>$_smarty_tpl->tpl_vars['shipping_status_data']->value['image'],'type'=>'t_img_stockrules','path_only'=>true),$_smarty_tpl);?>
"
                                 class="img-responsive cursor-help"
                                 alt="<?php echo smarty_function_txt(array('key'=>TEXT_SHIPPING_STATUS),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['shipping_status_data']->value['name'];?>
"
                                 title="<?php echo smarty_function_txt(array('key'=>TEXT_SHIPPING_STATUS),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['shipping_status_data']->value['name'];?>
"
                                 data-toggle="tooltip" />
                            <span class="sr-only"><?php echo $_smarty_tpl->tpl_vars['shipping_status_data']->value['name'];?>
</span>
                        </li>
                        <?php }?>
                    <?php }?>

                    <?php if ($_smarty_tpl->tpl_vars['show_stock']->value) {?>
                    <li class="product-stock"><?php echo smarty_function_txt(array('key'=>TEXT_STOCK),$_smarty_tpl);?>
 <?php echo round((float) $_smarty_tpl->tpl_vars['products_quantity']->value, (int) 0, (int) 1);?>
 <?php if ($_smarty_tpl->tpl_vars['products_unit_name']->value) {
echo $_smarty_tpl->tpl_vars['products_unit_name']->value;
}?></li>
                    <?php }?>
                    
                    <?php if ($_smarty_tpl->tpl_vars['products_weight']->value > 0) {?>
                        <li class="product-weight"><?php echo smarty_function_txt(array('key'=>TEXT_PRODUCTS_WEIGHT),$_smarty_tpl);?>
 <?php echo smarty_modifier_weight_format($_smarty_tpl->tpl_vars['products_weight']->value,"kg");?>
</li>
                    <?php }?>
                </ul>
                <?php if ($_smarty_tpl->tpl_vars['products_discount']->value != '') {?>
                    <p class="product-discount"><?php echo $_smarty_tpl->tpl_vars['products_discount']->value;?>
</p>
                <?php }?>

                <?php if ($_smarty_tpl->tpl_vars['products_master_flag']->value == 1 || $_smarty_tpl->tpl_vars['products_master_model']->value != '') {?>
                        <div class="ms-options clearfix">
                            <?php echo smarty_function_hook(array('key'=>'product_info_tpl_options'),$_smarty_tpl);?>

                        </div>
                <?php }?>
                <?php echo smarty_function_hook(array('key'=>'product_info_tpl_before_cart'),$_smarty_tpl);?>

                <?php echo smarty_function_hook(array('key'=>'product_info_bundle'),$_smarty_tpl);?>

                <?php if ($_smarty_tpl->tpl_vars['date_available']->value != '') {?>
                    <p class="box info"><?php echo smarty_function_txt(array('key'=>TEXT_PRODUCT_AVAILABLE),$_smarty_tpl);?>
 <strong><?php echo date_format_intl($_smarty_tpl->tpl_vars['date_available_object']->value,"EEEE, dd.MM.Y");?>
</strong></p>
                <?php }?>
                <?php if ($_smarty_tpl->tpl_vars['allow_add_cart']->value == 'true') {?>
                    <?php if ($_smarty_tpl->tpl_vars['products_fsk18']->value == '1') {?>
                        <p class="box error"><?php echo smarty_function_txt(array('key'=>TEXT_PRODUCTS_FSK18_NOTE),$_smarty_tpl);?>
</p>
                    <?php }?>
                <?php }?>
                
                <?php echo smarty_function_hook(array('key'=>"notify_on_restock_tpl.notify_box"),$_smarty_tpl);?>

                
                <?php if ($_smarty_tpl->tpl_vars['allow_add_cart']->value == 'true') {?>
                    <hr />
                    <?php echo smarty_function_form(array('type'=>'form','name'=>'product','id'=>"main_product_form",'action'=>'dynamic','link_params'=>'getParams','method'=>'post'),$_smarty_tpl);?>

                    <?php echo smarty_function_form(array('type'=>'hidden','name'=>'action','value'=>'add_product'),$_smarty_tpl);?>

                    <?php echo smarty_function_form(array('type'=>'hidden','name'=>'product','value'=>$_smarty_tpl->tpl_vars['products_id']->value),$_smarty_tpl);?>

                    <?php echo smarty_function_form(array('type'=>'hidden','name'=>'gotoCart','value'=>(defined('_STORE_GOTO_CART_DIRECTLY') ? constant('_STORE_GOTO_CART_DIRECTLY') : null)),$_smarty_tpl);?>

                    <?php echo $_smarty_tpl->tpl_vars['products_information']->value;?>

                    <div class="add-to-cart clearfix right">

                        <div class="form-inline">

                                                        <?php $_smarty_tpl->_assignInScope('quantity_input', 'dropdown');?>

                                                        <?php if (empty($_smarty_tpl->tpl_vars['qtyOptions']->value)) {?>
                                <?php $_smarty_tpl->_assignInScope('maxQuantity', 30);?>
                                <?php $_smarty_tpl->_assignInScope('qtyOptions', array());?>
                                <?php $_smarty_tpl->_assignInScope('i', 0);?>
                                <?php
 while ($_smarty_tpl->tpl_vars['i']->value < $_smarty_tpl->tpl_vars['maxQuantity']->value) {?>
                                    <?php $_smarty_tpl->_assignInScope('n', $_smarty_tpl->tpl_vars['i']->value+1);?>
                                    <?php $_tmp_array = isset($_smarty_tpl->tpl_vars['qtyOptions']) ? $_smarty_tpl->tpl_vars['qtyOptions']->value : array();
if (!(is_array($_tmp_array) || $_tmp_array instanceof ArrayAccess)) {
settype($_tmp_array, 'array');
}
$_tmp_array[$_smarty_tpl->tpl_vars['i']->value] = array('id'=>$_smarty_tpl->tpl_vars['n']->value,'text'=>$_smarty_tpl->tpl_vars['n']->value);
$_smarty_tpl->_assignInScope('qtyOptions', $_tmp_array);?>
                                    <?php $_smarty_tpl->_assignInScope('i', $_smarty_tpl->tpl_vars['i']->value+1);?>
                                <?php }?>

                            <?php }?>

                                                        <?php if (empty($_smarty_tpl->tpl_vars['defaultQtyOption']->value)) {?>
                                <?php $_smarty_tpl->_assignInScope('defaultQtyOption', 1);?>
                            <?php }?>

                            <div class="input-group" style="display: inline-table;vertical-align: middle;">
                                <?php if ($_smarty_tpl->tpl_vars['quantity_input']->value == 'number') {?>
                                    <input type="number"
                                           class="form-control btn-lg btn-qty"
                                           <?php if ((defined('_STORE_ALLOW_DECIMAL_QUANTITIY') ? constant('_STORE_ALLOW_DECIMAL_QUANTITIY') : null) == 'false') {?>min="1"<?php }?>
                                           size="1"
                                           required="required"
                                           step="any"
                                           name="qty"
                                           autocomplete="off"
                                           id="form-qty-<?php echo $_smarty_tpl->tpl_vars['products_id']->value;?>
"
                                           value="1"
                                           style="width: 6em; height: 2.7em;"
                                           placeholder="<?php echo smarty_function_txt(array('key'=>TEXT_QTY),$_smarty_tpl);?>
">
                                <?php } elseif ($_smarty_tpl->tpl_vars['quantity_input']->value == 'dropdown') {?>
                                    <div style="display: inline-table;vertical-align: middle;border-left: 1px solid #e0e0e0;border-top: 1px solid #e0e0e0;border-bottom: 1px solid #e0e0e0;">
                                        <?php echo smarty_function_form(array('params'=>"id='form-qty-".((string)$_smarty_tpl->tpl_vars['products_id']->value)."' data-size='7' data-width='7em' data-style='btn btn-lg btn-default btn-qty'",'type'=>"select",'name'=>"qty",'value'=>$_smarty_tpl->tpl_vars['qtyOptions']->value,'default'=>((string)$_smarty_tpl->tpl_vars['defaultQtyOption']->value)),$_smarty_tpl);?>

                                    </div>
                                    <?php if ($_smarty_tpl->tpl_vars['products_unit_name']->value) {?><label for="form-qty-<?php echo $_smarty_tpl->tpl_vars['products_id']->value;?>
" class="input-group-addon text-regular" style="width:auto"><?php echo $_smarty_tpl->tpl_vars['products_unit_name']->value;?>
</label><?php }?>
                                <?php }?>

                            </div>

                            <button type="submit" class="btn btn-lg btn-cart" data-toggle="tooltip" title="<?php echo smarty_function_txt(array('key'=>BUTTON_ADD_CART),$_smarty_tpl);?>
">
                                <span class="hidden-md"><?php echo smarty_function_txt(array('key'=>TEXT_CART),$_smarty_tpl);?>
</span>
                                <span class="hidden-xs hidden-sm hidden-lg"><i class="fa fa-shopping-basket"></i></span>
                                <span class="">&nbsp;<i class="fa fa-plus"></i></span>
                            </button>
                        </div>


                    </div>
                    <?php echo smarty_function_form(array('type'=>'formend'),$_smarty_tpl);?>

                <?php }?>

				<?php echo smarty_function_hook(array('key'=>'product_info_tpl_cart'),$_smarty_tpl);?>


                <?php if ($_smarty_tpl->tpl_vars['review_stars_rating']->value) {?>
                    <?php echo smarty_function_math(array('assign'=>'ratingValue','equation'=>'(x-1) / 100 * y','x'=>$_smarty_tpl->tpl_vars['review_stars_rating']->value,'y'=>5,'format'=>'%.1f'),$_smarty_tpl);?>

                    <hr />
                    <div class="product-reviews">
                        <div class="cursor-pointer" onclick="document.location.href='<?php echo $_smarty_tpl->tpl_vars['link_reviews_list']->value;?>
'">
                            <div class="reviews_rating_light" data-toggle="popover" data-placement="bottom" data-content="<?php if ($_smarty_tpl->tpl_vars['products_rating_count']->value) {?><i class='fa fa-heart-o'></i> <?php echo $_smarty_tpl->tpl_vars['products_rating_count']->value;?>
 <?php echo smarty_function_txt(array('key'=>TEXT_XT_REVIEWS_INFO),$_smarty_tpl);?>
<br /><i class='fa fa-star-o'></i> <?php echo $_smarty_tpl->tpl_vars['ratingValue']->value;?>
 <?php echo smarty_function_txt(array('key'=>TEXT_PAGINATION_FROM),$_smarty_tpl);?>
 5<?php } else {
echo smarty_function_txt(array('key'=>TEXT_XT_REVIEWS_NO_REVIEWS),$_smarty_tpl);
}?>">
                                <i></i><i></i><i></i><i></i><i></i>
                                <div class="reviews_rating_dark" style="width:<?php echo $_smarty_tpl->tpl_vars['review_stars_rating']->value;?>
%">
                                    <i></i><i></i><i></i><i></i><i></i>
                                </div>
                            </div>
                        </div>
                        <?php if ($_smarty_tpl->tpl_vars['ratingValue']->value > 0) {?>
                        <span class="text-muted text-small"><?php echo smarty_function_txt(array('key'=>TEXT_REVIEW_RATING),$_smarty_tpl);?>
: <span><?php echo $_smarty_tpl->tpl_vars['ratingValue']->value;?>
</span> <?php echo smarty_function_txt(array('key'=>TEXT_PAGINATION_FROM),$_smarty_tpl);?>
 5</span>
                        <?php } else { ?>
                        <span class="text-muted text-small"><?php echo smarty_function_txt(array('key'=>TEXT_XT_REVIEWS_NO_REVIEWS),$_smarty_tpl);?>
</span>
                        <?php }?>
                        <div class="clearfix"></div>
                    </div>
                <?php }?>

                <?php echo smarty_function_hook(array('key'=>'products_info_bottom'),$_smarty_tpl);?>

            </div>
        </div>
    </div><!-- .pinfo -->

    <div class="pcontent">

        <div id="options-list" class="info-panel">
            <?php echo smarty_function_hook(array('key'=>'product_info_tpl_productlist'),$_smarty_tpl);?>

        </div>

        <?php if ($_smarty_tpl->tpl_vars['products_description']->value && trim(preg_replace('!<[^>]*?>!', ' ', $_smarty_tpl->tpl_vars['products_description']->value ?: '')) != '') {?>
            <div id="description" class="textstyles text-word-wrap info-panel">
                <p class="headline"
                   data-toggle="collapse"
                   data-target="#description-collapse"
                   aria-expanded="true"
                   aria-controls="description-collapse">
                    <?php echo smarty_function_txt(array('key'=>TEXT_PRODUCTS_DESCRIPTION),$_smarty_tpl);?>

                </p>
                <div id="description-collapse" class="collapse collapse-auto-toggle-xs in">
                    <?php echo $_smarty_tpl->tpl_vars['products_description']->value;?>

                    <div class="clearfix"></div>
                </div>
            </div>
        <?php }?>

        <?php if (count($_smarty_tpl->tpl_vars['media_files']->value)) {?>
            <div id="files" class="info-panel">
                <p class="headline"
                   data-toggle="collapse"
                   data-target="#files-collapse"
                   aria-expanded="true"
                   aria-controls="files-collapse">
                    <?php echo smarty_function_txt(array('key'=>TEXT_DOWNLOADS),$_smarty_tpl);?>

                </p>
                <div id="files-collapse" class="collapse collapse-auto-toggle-xs in">
                    <?php $_smarty_tpl->_subTemplateRender("file:xtCore/pages/files/product_public_files.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
                </div>
            </div>
        <?php }?>

        <?php echo smarty_function_hook(array('key'=>'product_info_tpl'),$_smarty_tpl);?>

        <?php echo smarty_function_hook(array('key'=>'product_info_tpl_tab_headline'),$_smarty_tpl);?>

        <?php echo smarty_function_hook(array('key'=>'product_info_tpl_tab_box'),$_smarty_tpl);?>


        <?php if ($_smarty_tpl->tpl_vars['review_stars_rating']->value) {?>
            <div id="reviews" class="info-panel">
                <p class="headline"
                   data-toggle="collapse"
                   data-target="#reviews-collapse"
                   aria-expanded="true"
                   aria-controls="reviews-collapse">
                    <?php echo smarty_function_txt(array('key'=>TEXT_XT_REVIEWS_INFO),$_smarty_tpl);?>

                </p>
                <div id="reviews-collapse" class="collapse collapse-auto-toggle-xs in">
                    <?php echo smarty_function_hook(array('key'=>'product_info_tpl_reviews'),$_smarty_tpl);?>

                </div>
            </div>
        <?php }?>

        <?php echo smarty_function_hook(array('key'=>'product_info_tpl_tab_box_2'),$_smarty_tpl);?>


    </div>

</div><!-- #product -->
<?php }
}
