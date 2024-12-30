<?php
/* Smarty version 5.4.1, created on 2024-12-30 01:19:48
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/product/product.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.1',
  'unifunc' => 'content_6771e724bcbab3_05033035',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '7dc063f92db9a20f001bf2d8f2648eb56c3a175a' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/product/product.html',
      1 => 1697144245,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:includes/product_info_label.html' => 2,
    'file:xtCore/pages/files/product_public_files.html' => 1,
  ),
))) {
function content_6771e724bcbab3_05033035 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/product';
$_smarty_tpl->getCompiled()->nocache_hash = '6942802986771e72497b0d4_23936130';
echo $_smarty_tpl->getValue('message');?>

<div id="product" class="detail">
    <div class="pinfo row<?php if ($_smarty_tpl->getValue('flag_has_specials') == 1) {?> special-price<?php }
if ($_smarty_tpl->getValue('date_available') != '') {?> available-soon<?php }?>">
        <div class="col col-sm-7 col-md-8">
            <a name="product_images" style="line-height: 0px; text-decoration: none" >&nbsp;</a>
            <div class="lightgallery product-images row">
                <div class="col<?php if (!$_smarty_tpl->getValue('more_images') || $_smarty_tpl->getSmarty()->getModifierCallback('count')($_smarty_tpl->getValue('more_images')) == 0) {?> col-sm-12<?php } else { ?> col-md-10 col-md-push-2<?php }?>">
                    <div class="image product-image center">
                        <?php if (!$_smarty_tpl->getValue('products_image') || $_smarty_tpl->getValue('products_image') == 'product:noimage.gif') {?>
                            <span class="vertical-helper image-link no-image img-thumbnail img-label-wrap">
                                <i class="no-image-icon"></i>
                                <?php $_smarty_tpl->renderSubTemplate("file:includes/product_info_label.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 9999, $_smarty_tpl->cache_lifetime, array('position'=>"product-page",'isSpecial'=>$_smarty_tpl->getValue('flag_has_specials'),'dateAvailable'=>$_smarty_tpl->getValue('date_available')), (int) 0, $_smarty_current_dir);
?>
                                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'tpl_product_no_img','module_data'=>$_smarty_tpl->getValue('product')), $_smarty_tpl);?>

                            </span>
                        <?php } else { ?>
                            <figure>
                                <a href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('img')->handle(array('img'=>$_smarty_tpl->getValue('products_image'),'type'=>'m_org','path_only'=>true), $_smarty_tpl);?>
"class="image-link cursor-zoom" data-type="main">
                                    <span class="img-thumbnail img-label-wrap">
                                        <img src="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('img')->handle(array('img'=>$_smarty_tpl->getValue('products_image'),'type'=>'m_info','path_only'=>true), $_smarty_tpl);?>
" alt="<?php if ($_smarty_tpl->getValue('products_image_data')['media_name']) {
echo htmlspecialchars((string)$_smarty_tpl->getValue('products_image_data')['media_name'], ENT_QUOTES, 'UTF-8', true);
} else {
echo htmlspecialchars((string)$_smarty_tpl->getValue('products_name'), ENT_QUOTES, 'UTF-8', true);
}
if ($_smarty_tpl->getValue('products_image_data')['copyright_holder']) {?> &copy; <?php echo $_smarty_tpl->getValue('products_image_data')['copyright_holder'];
}?>" title="<?php if ($_smarty_tpl->getValue('products_image_data')['media_name']) {
echo htmlspecialchars((string)$_smarty_tpl->getValue('products_image_data')['media_name'], ENT_QUOTES, 'UTF-8', true);
} else {
echo htmlspecialchars((string)$_smarty_tpl->getValue('products_name'), ENT_QUOTES, 'UTF-8', true);
}
if ($_smarty_tpl->getValue('products_image_data')['copyright_holder']) {?> &copy; <?php echo $_smarty_tpl->getValue('products_image_data')['copyright_holder'];
}?>" class="img-responsive image-link">
                                        <?php $_smarty_tpl->renderSubTemplate("file:includes/product_info_label.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 9999, $_smarty_tpl->cache_lifetime, array('position'=>"product-page",'isSpecial'=>$_smarty_tpl->getValue('flag_has_specials'),'dateAvailable'=>$_smarty_tpl->getValue('date_available')), (int) 0, $_smarty_current_dir);
?>
                                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'tpl_product_img','module_data'=>$_smarty_tpl->getValue('product')), $_smarty_tpl);?>

                                    </span>
                                </a>
                                <?php if ($_smarty_tpl->getValue('products_image_data')['copyright_holder']) {?>
                                <span class="copyright text-small" style="display: inline-flex">&copy; <?php echo $_smarty_tpl->getValue('products_image_data')['copyright_holder'];?>
</span>
                                <?php }?>
                            </figure>
                        <?php }?>
                    </div>
                </div>
                <?php if ($_smarty_tpl->getValue('more_images') && $_smarty_tpl->getSmarty()->getModifierCallback('count')($_smarty_tpl->getValue('more_images')) != 0) {?>
                    <div class="col col-md-2 col-md-pull-10">
                        <div class="more-images row listing equalize-nothing">
                            <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('more_images'), 'img_data', false, 'index', 'aussen', array (
));
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('index')->value => $_smarty_tpl->getVariable('img_data')->value) {
$foreach0DoElse = false;
?>
                            <figure class="section col<?php if ($_smarty_tpl->getValue('index') > 3) {?> hidden<?php }
if ($_smarty_tpl->getValue('index') == 3 && $_smarty_tpl->getSmarty()->getModifierCallback('count')($_smarty_tpl->getValue('more_images')) > 4) {?> show-more<?php }?> col-xs-3 col-md-12">
                                <a href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('img')->handle(array('img'=>$_smarty_tpl->getValue('img_data')['file'],'type'=>'m_org','path_only'=>true), $_smarty_tpl);?>
" class="vertical-helper image-link text-center img-thumbnail" >
                                    <img src="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('img')->handle(array('img'=>$_smarty_tpl->getValue('img_data')['file'],'type'=>'m_thumb','path_only'=>true), $_smarty_tpl);?>
" alt="<?php if ($_smarty_tpl->getValue('img_data')['data']['media_name']) {
echo htmlspecialchars((string)$_smarty_tpl->getValue('img_data')['data']['media_name'], ENT_QUOTES, 'UTF-8', true);
} else {
echo htmlspecialchars((string)$_smarty_tpl->getValue('products_name'), ENT_QUOTES, 'UTF-8', true);
}
if ($_smarty_tpl->getValue('img_data')['data']['copyright_holder']) {?> &copy; <?php echo $_smarty_tpl->getValue('img_data')['data']['copyright_holder'];
}?>" title="<?php if ($_smarty_tpl->getValue('img_data')['data']['media_name']) {
echo htmlspecialchars((string)$_smarty_tpl->getValue('img_data')['data']['media_name'], ENT_QUOTES, 'UTF-8', true);
} else {
echo htmlspecialchars((string)$_smarty_tpl->getValue('products_name'), ENT_QUOTES, 'UTF-8', true);
}
if ($_smarty_tpl->getValue('img_data')['data']['copyright_holder']) {?> &copy; <?php echo $_smarty_tpl->getValue('img_data')['data']['copyright_holder'];
}?>" class="productImageBorder img-responsive" width="auto" height="auto">
                                </a>
                            </figure>
                            <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
                        </div>
                    </div>
                <?php }?>
                <hr class="visible-xs">
            </div>
            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'products_images_tpl'), $_smarty_tpl);?>

        </div>
        <div class="col col-sm-5 col-md-4">

            <?php if ($_smarty_tpl->getValue('product_outdated') == 1) {?>
            <div class="alert alert-error alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                <p class="item"><span class="glyphicon glyphicon-bullhorn"></span>&nbsp;&nbsp;<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>'PRODUCT_OUTDATED_SHORT'), $_smarty_tpl);?>
</p>
            </div>
            <?php }?>
            <?php if ($_smarty_tpl->getValue('product_outdated_soon') == 1) {?>
            <div class="alert alert-warning alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                <p class="item"><span class="glyphicon glyphicon-bullhorn"></span>&nbsp;&nbsp;<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>'PRODUCT_OUTDATED_SOON_SHORT'), $_smarty_tpl);?>
</p>
            </div>
            <?php }?>

            <a name="product_info" style="line-height: 0px; text-decoration: none" >&nbsp;</a>
            <div class="product-info">
                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'product_info_top','product'=>$_smarty_tpl->getValue('product')), $_smarty_tpl);?>

                <?php if ($_smarty_tpl->getValue('manufacturer')['manufacturers_image']) {?>
                    <p class="product-manufacturer image pull-right">
                        <a href="<?php echo $_smarty_tpl->getValue('manufacturer')['link'];?>
"><img src="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('img')->handle(array('img'=>$_smarty_tpl->getValue('manufacturer')['manufacturers_image'],'type'=>'m_manufacturer_thumb','path_only'=>true), $_smarty_tpl);?>
"
                             alt="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>HEADING_PRODUCTS_MANUFACTURERS), $_smarty_tpl);?>
: <?php echo htmlspecialchars((string)$_smarty_tpl->getValue('manufacturer')['manufacturers_name'], ENT_QUOTES, 'UTF-8', true);?>
"
                             title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>HEADING_PRODUCTS_MANUFACTURERS), $_smarty_tpl);?>
: <?php echo htmlspecialchars((string)$_smarty_tpl->getValue('manufacturer')['manufacturers_name'], ENT_QUOTES, 'UTF-8', true);?>
"
                             class="img-responsive img-thumbnail"
                        ></a>
                    </p>
                <?php }?>
                <h1 class="title h3 text-word-wrap"><?php echo $_smarty_tpl->getValue('products_name');?>
</h1>
                <?php if ($_smarty_tpl->getValue('products_model') != '') {?>
                    <p class="product-model float-sm-left">
                        <span class="text-small"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PRODUCTS_MODEL), $_smarty_tpl);?>
:</span>
                        <span class="badge badge-lighter"><?php echo $_smarty_tpl->getValue('products_model');?>
</span>
                    </p>
                <?php }?>
                <!-- <?php if ($_smarty_tpl->getValue('products_ean') != '') {?>
                    <p class="product-model float-sm-left">
                        <span class="text-small"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PRODUCTS_EAN), $_smarty_tpl);?>
:</span>
                        <span class="badge badge-lighter"><?php echo $_smarty_tpl->getValue('products_ean');?>
</span>
                    </p>
                <?php }?> /-->
                <?php if ($_smarty_tpl->getValue('manufacturer')['manufacturers_name']) {?>
                    <p class="product-manufacturer name">
                        <span class="text-small"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>HEADING_PRODUCTS_MANUFACTURERS), $_smarty_tpl);?>
:</span>
                        <span class="badge badge-lighter"><a href="<?php echo $_smarty_tpl->getValue('manufacturer')['link'];?>
"><?php echo $_smarty_tpl->getValue('manufacturer')['manufacturers_name'];?>
</a></span>
                    </p>
                <?php }?>
                <?php if ($_smarty_tpl->getValue('products_url')) {?>
                <p class="product-url name">
                    <span class="text-small"><a href="<?php echo $_smarty_tpl->getValue('products_url');?>
" target="_blank"><?php echo $_smarty_tpl->getValue('products_url');?>
</a>&nbsp;<span class="text-small glyphicon glyphicon-new-window"></span></span>
                </p>
                <?php }?>
                <div class="clearfix"></div>
                <?php if ((defined('AQ_SHOW_ARTICEL_PRICE') ? constant('AQ_SHOW_ARTICEL_PRICE') : null) != '1' && $_smarty_tpl->getValue('products_price')['plain'] != '0' || round((float) $_smarty_tpl->getValue('products_quantity'), (int) 0, (int) 1) > '0') {?>
                    <?php if ((defined('_CUST_STATUS_SHOW_PRICE') ? constant('_CUST_STATUS_SHOW_PRICE') : null) == '1' && $_smarty_tpl->getValue('products_price')['formated'] != '') {?>
                        <div>
                            <p class="product-price this">
                                <?php echo $_smarty_tpl->getValue('products_price')['formated'];?>

                                <br class="visible-xs">
                                <span class="product-tax"><?php echo $_smarty_tpl->getValue('products_tax_info')['tax_desc'];?>
</span>
                            </p>
                        </div>
                        <ul class="product-meta-info">
                            <?php if (!$_smarty_tpl->getValue('products_digital')) {?>
                                <?php if ($_smarty_tpl->getValue('products_shipping_link')) {?>
                                    <li class="product-shipping"><a href="<?php echo $_smarty_tpl->getValue('products_shipping_link');?>
" target="_blank" rel="nofollow"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_EXCL_SHIPPING), $_smarty_tpl);?>
</a></li>
                                <?php } else { ?>
                                    <li class="product-shipping"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_EXCL_SHIPPING), $_smarty_tpl);?>
</li>
                                <?php }?>
                            <?php }?>
                            <?php if ($_smarty_tpl->getValue('base_price')) {?>
                                <li class="vpe"><?php echo $_smarty_tpl->getSmarty()->getModifierCallback('number_format_prec')($_smarty_tpl->getValue('products_vpe_value'));?>
 <?php echo $_smarty_tpl->getValue('base_price')['vpe']['name'];?>
 / <?php echo $_smarty_tpl->getValue('base_price')['price'];?>
 <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SHIPPING_BASE_PER), $_smarty_tpl);?>
 <?php echo $_smarty_tpl->getValue('base_price')['vpe']['name'];?>
</li>
                            <?php }?>
                        </ul>
                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('price_table')->handle(array(), $_smarty_tpl);?>

                    <?php }?>
                <?php } else { ?>
                    <p><?php echo (defined('AQ_NO_PRICE_TEXT') ? constant('AQ_NO_PRICE_TEXT') : null);?>
</p>
                <?php }?>
                <ul class="product-meta-info bold">
                    <?php if ($_smarty_tpl->getValue('stock_image')) {?>
                        <li class="product-stock-image">
                            <img src="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('img')->handle(array('img'=>$_smarty_tpl->getValue('stock_image')['image'],'type'=>'t_img_stockrules','path_only'=>true), $_smarty_tpl);?>
"
                                 class="img-responsive cursor-help"
                                 alt="<?php echo $_smarty_tpl->getValue('stock_image')['name'];?>
"
                                 title="<?php echo $_smarty_tpl->getValue('stock_image')['name'];?>
"
                                 data-toggle="tooltip" />
                            <span class="sr-only"><?php echo $_smarty_tpl->getValue('stock_image')['name'];?>
</span>
                        </li>
                    <?php }?>
                    
                    <?php if ($_smarty_tpl->getValue('shipping_status_data')['name']) {?>
                        <li class="product-shipping-status"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SHIPPING_STATUS), $_smarty_tpl);?>
 <?php echo $_smarty_tpl->getValue('shipping_status_data')['name'];?>
</li>
                        <?php if ($_smarty_tpl->getValue('shipping_status_data')['image']) {?>
                        <li class="product-stock-image">
                            <img src="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('img')->handle(array('img'=>$_smarty_tpl->getValue('shipping_status_data')['image'],'type'=>'t_img_stockrules','path_only'=>true), $_smarty_tpl);?>
"
                                 class="img-responsive cursor-help"
                                 alt="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SHIPPING_STATUS), $_smarty_tpl);?>
 <?php echo $_smarty_tpl->getValue('shipping_status_data')['name'];?>
"
                                 title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SHIPPING_STATUS), $_smarty_tpl);?>
 <?php echo $_smarty_tpl->getValue('shipping_status_data')['name'];?>
"
                                 data-toggle="tooltip" />
                            <span class="sr-only"><?php echo $_smarty_tpl->getValue('shipping_status_data')['name'];?>
</span>
                        </li>
                        <?php }?>
                    <?php }?>

                    <?php if ($_smarty_tpl->getValue('show_stock')) {?>
                    <li class="product-stock"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_STOCK), $_smarty_tpl);?>
 <?php echo round((float) $_smarty_tpl->getValue('products_quantity'), (int) 0, (int) 1);?>
 <?php if ($_smarty_tpl->getValue('products_unit_name')) {
echo $_smarty_tpl->getValue('products_unit_name');
}?></li>
                    <?php }?>
                    
                    <?php if ($_smarty_tpl->getValue('products_weight') > 0) {?>
                        <li class="product-weight"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PRODUCTS_WEIGHT), $_smarty_tpl);?>
 <?php echo $_smarty_tpl->getSmarty()->getModifierCallback('weight_format')($_smarty_tpl->getValue('products_weight'),"kg");?>
</li>
                    <?php }?>
                </ul>
                <?php if ($_smarty_tpl->getValue('products_discount') != '') {?>
                    <p class="product-discount"><?php echo $_smarty_tpl->getValue('products_discount');?>
</p>
                <?php }?>

                <?php if ($_smarty_tpl->getValue('products_master_flag') == 1 || $_smarty_tpl->getValue('products_master_model') != '') {?>
                        <div class="ms-options clearfix">
                            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'product_info_tpl_options'), $_smarty_tpl);?>

                        </div>
                <?php }?>
                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'product_info_tpl_before_cart'), $_smarty_tpl);?>

                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'product_info_bundle'), $_smarty_tpl);?>

                <?php if ($_smarty_tpl->getValue('date_available') != '') {?>
                    <p class="box info"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PRODUCT_AVAILABLE), $_smarty_tpl);?>
 <strong><?php echo $_smarty_tpl->getSmarty()->getModifierCallback('date_format_intl')($_smarty_tpl->getValue('date_available_object'),"EEEE, dd.MM.Y");?>
</strong></p>
                <?php }?>
                <?php if ($_smarty_tpl->getValue('allow_add_cart') == 'true') {?>
                    <?php if ($_smarty_tpl->getValue('products_fsk18') == '1') {?>
                        <p class="box error"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PRODUCTS_FSK18_NOTE), $_smarty_tpl);?>
</p>
                    <?php }?>
                <?php }?>
                
                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>"notify_on_restock_tpl.notify_box"), $_smarty_tpl);?>

                
                <?php if ($_smarty_tpl->getValue('allow_add_cart') == 'true') {?>
                    <hr />
                    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'form','name'=>'product','id'=>"main_product_form",'action'=>'dynamic','link_params'=>'getParams','method'=>'post'), $_smarty_tpl);?>

                    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'hidden','name'=>'action','value'=>'add_product'), $_smarty_tpl);?>

                    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'hidden','name'=>'product','value'=>$_smarty_tpl->getValue('products_id')), $_smarty_tpl);?>

                    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'hidden','name'=>'gotoCart','value'=>(defined('_STORE_GOTO_CART_DIRECTLY') ? constant('_STORE_GOTO_CART_DIRECTLY') : null)), $_smarty_tpl);?>

                    <?php echo $_smarty_tpl->getValue('products_information');?>

                    <div class="add-to-cart clearfix right">

                        <div class="form-inline">

                                                        <?php $_smarty_tpl->assign('quantity_input', 'dropdown', false, NULL);?>

                                                        <?php if (empty($_smarty_tpl->getValue('qtyOptions'))) {?>
                                <?php $_smarty_tpl->assign('maxQuantity', 30, false, NULL);?>
                                <?php $_smarty_tpl->assign('qtyOptions', array(), false, NULL);?>
                                <?php $_smarty_tpl->assign('i', 0, false, NULL);?>
                                <?php
 while ($_smarty_tpl->getValue('i') < $_smarty_tpl->getValue('maxQuantity')) {?>
                                    <?php $_smarty_tpl->assign('n', $_smarty_tpl->getValue('i')+1, false, NULL);?>
                                    <?php $_tmp_array = $_smarty_tpl->getValue('qtyOptions') ?? [];
if (!(is_array($_tmp_array) || $_tmp_array instanceof ArrayAccess)) {
settype($_tmp_array, 'array');
}
$_tmp_array[$_smarty_tpl->getValue('i')] = array('id'=>$_smarty_tpl->getValue('n'),'text'=>$_smarty_tpl->getValue('n'));
$_smarty_tpl->assign('qtyOptions', $_tmp_array, false, NULL);?>
                                    <?php $_smarty_tpl->assign('i', $_smarty_tpl->getValue('i')+1, false, NULL);?>
                                <?php }?>

                            <?php }?>

                                                        <?php if (empty($_smarty_tpl->getValue('defaultQtyOption'))) {?>
                                <?php $_smarty_tpl->assign('defaultQtyOption', 1, false, NULL);?>
                            <?php }?>

                            <div class="input-group" style="display: inline-table;vertical-align: middle;">
                                <?php if ($_smarty_tpl->getValue('quantity_input') == 'number') {?>
                                    <input type="number"
                                           class="form-control btn-lg btn-qty"
                                           <?php if ((defined('_STORE_ALLOW_DECIMAL_QUANTITIY') ? constant('_STORE_ALLOW_DECIMAL_QUANTITIY') : null) == 'false') {?>min="1"<?php }?>
                                           size="1"
                                           required="required"
                                           step="any"
                                           name="qty"
                                           autocomplete="off"
                                           id="form-qty-<?php echo $_smarty_tpl->getValue('products_id');?>
"
                                           value="1"
                                           style="width: 6em; height: 2.7em;"
                                           placeholder="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_QTY), $_smarty_tpl);?>
">
                                <?php } elseif ($_smarty_tpl->getValue('quantity_input') == 'dropdown') {?>
                                    <div style="display: inline-table;vertical-align: middle;border-left: 1px solid #e0e0e0;border-top: 1px solid #e0e0e0;border-bottom: 1px solid #e0e0e0;">
                                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('params'=>"id='form-qty-".((string)$_smarty_tpl->getValue('products_id'))."' data-size='7' data-width='7em' data-style='btn btn-lg btn-default btn-qty'",'type'=>"select",'name'=>"qty",'value'=>$_smarty_tpl->getValue('qtyOptions'),'default'=>((string)$_smarty_tpl->getValue('defaultQtyOption'))), $_smarty_tpl);?>

                                    </div>
                                    <?php if ($_smarty_tpl->getValue('products_unit_name')) {?><label for="form-qty-<?php echo $_smarty_tpl->getValue('products_id');?>
" class="input-group-addon text-regular" style="width:auto"><?php echo $_smarty_tpl->getValue('products_unit_name');?>
</label><?php }?>
                                <?php }?>

                            </div>

                            <button type="submit" class="btn btn-lg btn-cart" data-toggle="tooltip" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>BUTTON_ADD_CART), $_smarty_tpl);?>
">
                                <span class="hidden-md"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_CART), $_smarty_tpl);?>
</span>
                                <span class="hidden-xs hidden-sm hidden-lg"><i class="fa fa-shopping-basket"></i></span>
                                <span class="">&nbsp;<i class="fa fa-plus"></i></span>
                            </button>
                        </div>


                    </div>
                    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'formend'), $_smarty_tpl);?>

                <?php }?>

				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'product_info_tpl_cart'), $_smarty_tpl);?>


                <?php if ($_smarty_tpl->getValue('review_stars_rating')) {?>
                    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('math')->handle(array('assign'=>'ratingValue','equation'=>'(x-1) / 100 * y','x'=>$_smarty_tpl->getValue('review_stars_rating'),'y'=>5,'format'=>'%.1f'), $_smarty_tpl);?>

                    <hr />
                    <div class="product-reviews">
                        <div class="cursor-pointer" onclick="document.location.href='<?php echo $_smarty_tpl->getValue('link_reviews_list');?>
'">
                            <div class="reviews_rating_light" data-toggle="popover" data-placement="bottom" data-content="<?php if ($_smarty_tpl->getValue('products_rating_count')) {?><i class='fa fa-heart-o'></i> <?php echo $_smarty_tpl->getValue('products_rating_count');?>
 <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_XT_REVIEWS_INFO), $_smarty_tpl);?>
<br /><i class='fa fa-star-o'></i> <?php echo $_smarty_tpl->getValue('ratingValue');?>
 <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PAGINATION_FROM), $_smarty_tpl);?>
 5<?php } else {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_XT_REVIEWS_NO_REVIEWS), $_smarty_tpl);
}?>">
                                <i></i><i></i><i></i><i></i><i></i>
                                <div class="reviews_rating_dark" style="width:<?php echo $_smarty_tpl->getValue('review_stars_rating');?>
%">
                                    <i></i><i></i><i></i><i></i><i></i>
                                </div>
                            </div>
                        </div>
                        <?php if ($_smarty_tpl->getValue('ratingValue') > 0) {?>
                        <span class="text-muted text-small"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_REVIEW_RATING), $_smarty_tpl);?>
: <span><?php echo $_smarty_tpl->getValue('ratingValue');?>
</span> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PAGINATION_FROM), $_smarty_tpl);?>
 5</span>
                        <?php } else { ?>
                        <span class="text-muted text-small"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_XT_REVIEWS_NO_REVIEWS), $_smarty_tpl);?>
</span>
                        <?php }?>
                        <div class="clearfix"></div>
                    </div>
                <?php }?>

                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'products_info_bottom'), $_smarty_tpl);?>

            </div>
        </div>
    </div><!-- .pinfo -->

    <div class="pcontent">

        <div id="options-list" class="info-panel">
            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'product_info_tpl_productlist'), $_smarty_tpl);?>

        </div>

        <?php if ($_smarty_tpl->getValue('products_description') && $_smarty_tpl->getSmarty()->getModifierCallback('trim')(preg_replace('!<[^>]*?>!', ' ', (string) $_smarty_tpl->getValue('products_description'))) != '') {?>
            <div id="description" class="textstyles text-word-wrap info-panel">
                <p class="headline"
                   data-toggle="collapse"
                   data-target="#description-collapse"
                   aria-expanded="true"
                   aria-controls="description-collapse">
                    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PRODUCTS_DESCRIPTION), $_smarty_tpl);?>

                </p>
                <div id="description-collapse" class="collapse collapse-auto-toggle-xs in">
                    <?php echo $_smarty_tpl->getValue('products_description');?>

                    <div class="clearfix"></div>
                </div>
            </div>
        <?php }?>

        <?php if ($_smarty_tpl->getSmarty()->getModifierCallback('count')($_smarty_tpl->getValue('media_files'))) {?>
            <div id="files" class="info-panel">
                <p class="headline"
                   data-toggle="collapse"
                   data-target="#files-collapse"
                   aria-expanded="true"
                   aria-controls="files-collapse">
                    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_DOWNLOADS), $_smarty_tpl);?>

                </p>
                <div id="files-collapse" class="collapse collapse-auto-toggle-xs in">
                    <?php $_smarty_tpl->renderSubTemplate("file:xtCore/pages/files/product_public_files.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 9999, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
                </div>
            </div>
        <?php }?>

        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'product_info_tpl'), $_smarty_tpl);?>

        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'product_info_tpl_tab_headline'), $_smarty_tpl);?>

        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'product_info_tpl_tab_box'), $_smarty_tpl);?>


        <?php if ($_smarty_tpl->getValue('review_stars_rating')) {?>
            <div id="reviews" class="info-panel">
                <p class="headline"
                   data-toggle="collapse"
                   data-target="#reviews-collapse"
                   aria-expanded="true"
                   aria-controls="reviews-collapse">
                    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_XT_REVIEWS_INFO), $_smarty_tpl);?>

                </p>
                <div id="reviews-collapse" class="collapse collapse-auto-toggle-xs in">
                    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'product_info_tpl_reviews'), $_smarty_tpl);?>

                </div>
            </div>
        <?php }?>

        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'product_info_tpl_tab_box_2'), $_smarty_tpl);?>


    </div>

</div><!-- #product -->
<?php }
}
