<?php
/* Smarty version 4.3.0, created on 2023-10-08 01:15:27
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive_AQ/includes/product_listing_base.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.0',
  'unifunc' => 'content_6521e68f0edbe4_57978547',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '33c568d2d984d4bfc3a2b1c2ce3faa0fc391f0b7' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive_AQ/includes/product_listing_base.html',
      1 => 1691797583,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:xtCore/pages/search.html' => 1,
    'file:includes/product_info_label.html' => 1,
  ),
),false)) {
function content_6521e68f0edbe4_57978547 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.img.php','function'=>'smarty_function_img',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.form.php','function'=>'smarty_function_form',),2=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.hook.php','function'=>'smarty_function_hook',),3=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.box.php','function'=>'smarty_function_box',),4=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/vendor/smarty/smarty/libs/plugins/modifier.truncate.php','function'=>'smarty_modifier_truncate',),5=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),6=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/vendor/smarty/smarty/libs/plugins/function.math.php','function'=>'smarty_function_math',),7=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/modifier.number_format_prec.php','function'=>'smarty_modifier_number_format_prec',),));
?>
<div class="product-listing product-listing-<?php echo $_smarty_tpl->tpl_vars['position']->value;
if ($_smarty_tpl->tpl_vars['listingSwitch']->value == 1) {?> product-listing-switch<?php }?>">
    <?php echo $_smarty_tpl->tpl_vars['categories']->value;?>

    <?php if ($_smarty_tpl->tpl_vars['product_listing']->value) {?>

        <?php if ($_smarty_tpl->tpl_vars['manufacturer']->value['MANUFACTURER']['manufacturers_image']) {?><div class="pull-right manufacturers-image"><?php echo smarty_function_img(array('img'=>$_smarty_tpl->tpl_vars['manufacturer']->value['MANUFACTURER']['manufacturers_image'],'type'=>'m_org','class'=>"img-responsive img-thumbnail"),$_smarty_tpl);?>
</div><?php }?>

        <?php if ($_smarty_tpl->tpl_vars['heading_text']->value) {?>
            <?php if (!$_GET['page'] || $_GET['page'] == 'index') {?>
                <h2 class="breaking-headline index-heading"><?php echo $_smarty_tpl->tpl_vars['heading_text']->value;?>
</h2>
            <?php } else { ?>
                <?php if ($_GET['page'] == 'cart') {?>
                    <h2 class="breaking-headline <?php echo $_GET['page'];?>
-heading"><?php echo $_smarty_tpl->tpl_vars['heading_text']->value;?>
</h2>
                <?php } else { ?>
                    <h1 class="breaking-headline <?php echo $_GET['page'];?>
-heading"><?php echo $_smarty_tpl->tpl_vars['heading_text']->value;?>
</h1>
                <?php }?>
            <?php }?>
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['manufacturer']->value['MANUFACTURER']['manufacturers_url']) {?><a class="small" href="<?php echo $_smarty_tpl->tpl_vars['manufacturer']->value['MANUFACTURER']['manufacturers_url'];?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['manufacturer']->value['MANUFACTURER']['manufacturers_url'];?>
</a><?php }?>

        <?php if ($_GET['page'] == 'search') {?>
            <?php $_smarty_tpl->_subTemplateRender("file:xtCore/pages/search.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('included'=>true,'type'=>"tpl_include"), 0, false);
?>
        <?php }?>

        <?php if ($_smarty_tpl->tpl_vars['PRODUCTS_DROPDOWN']->value) {
echo $_smarty_tpl->tpl_vars['PRODUCTS_DROPDOWN']->value;
}?>
        <?php if ($_smarty_tpl->tpl_vars['manufacturer']->value && trim($_smarty_tpl->tpl_vars['manufacturer']->value['MANUFACTURER']['manufacturers_description']) != '') {?><div class="manufacturers-description text-word-wrap"><?php echo $_smarty_tpl->tpl_vars['manufacturer']->value['MANUFACTURER']['manufacturers_description'];?>
</div><?php }?>

        <div class="clearfix"></div>

        <div class="row products-sort-pages top<?php if (!$_GET['page'] || $_GET['page'] == 'index') {?> pull-right<?php }?>">
            <?php if ($_smarty_tpl->tpl_vars['sort_dropdown']->value['options']) {?>
                <div class="col col-md-4 products-sort">
                    <?php echo smarty_function_form(array('type'=>'form','name'=>'sort_dropdown','action'=>'dynamic','method'=>'get'),$_smarty_tpl);?>

                        <input type="hidden" name=page value="<?php echo $_smarty_tpl->tpl_vars['page']->value;?>
">
                        <?php if ((isset($_smarty_tpl->tpl_vars['current_category_id']->value))) {
echo smarty_function_form(array('type'=>'hidden','name'=>'cat','value'=>$_smarty_tpl->tpl_vars['current_category_id']->value),$_smarty_tpl);
}?>
                        <?php if ((isset($_smarty_tpl->tpl_vars['current_manufacturer_id']->value))) {
echo smarty_function_form(array('type'=>'hidden','name'=>'mnf','value'=>$_smarty_tpl->tpl_vars['current_manufacturer_id']->value),$_smarty_tpl);
}?>
                        <?php echo smarty_function_form(array('class'=>"form-control",'type'=>'select','name'=>'sorting','value'=>$_smarty_tpl->tpl_vars['sort_dropdown']->value['options'],'default'=>$_smarty_tpl->tpl_vars['sort_default']->value,'onchange'=>'this.form.submit();'),$_smarty_tpl);?>

                        
                        <?php if ($_smarty_tpl->tpl_vars['MANUFACTURER_DROPDOWN']->value && $_smarty_tpl->tpl_vars['MANUFACTURER_DROPDOWN']->value['options']) {?>
                            <?php echo smarty_function_form(array('class'=>"form-control",'type'=>'select','name'=>'filter_id','value'=>$_smarty_tpl->tpl_vars['MANUFACTURER_DROPDOWN']->value['options'],'default'=>$_smarty_tpl->tpl_vars['filter_default']->value,'onchange'=>'this.form.submit();'),$_smarty_tpl);?>

                        <?php }?>

                        <?php echo smarty_function_hook(array('key'=>'product_listing_base_sort_form_bottom'),$_smarty_tpl);?>

                    <?php echo smarty_function_form(array('type'=>'formend'),$_smarty_tpl);?>

                </div><!-- .products-sort -->
                <div class="col col-md-8 navigation-pages">
                    <div class="btn-toolbar pull-right" role="toolbar">
                        <?php if ($_smarty_tpl->tpl_vars['listingSwitch']->value == 1) {?>
                            <?php echo smarty_function_box(array('name'=>'listing_switch','htmlonly'=>1),$_smarty_tpl);?>

                        <?php }?>
                        <?php echo $_smarty_tpl->tpl_vars['NAVIGATION_PAGES']->value;?>

                    </div>
                </div><!-- .navigation-pages -->
            <?php } else { ?>
                <div class="col col-md-12 navigation-pages">
                    <div class="btn-toolbar pull-right" role="toolbar">
                        <?php if ($_smarty_tpl->tpl_vars['listingSwitch']->value == 1) {?>
                            <?php echo smarty_function_box(array('name'=>'listing_switch','htmlonly'=>1),$_smarty_tpl);?>

                        <?php }?>
                        <?php echo $_smarty_tpl->tpl_vars['NAVIGATION_PAGES']->value;?>

                    </div>
                </div><!-- .navigation-pages -->
            <?php }?>
        </div><!-- .products-sort-pages -->
        <div class="clearfix"></div>

        <div class="listing row products">
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['product_listing']->value, 'module_data', false, 'key', 'listing', array (
  'first' => true,
  'last' => true,
  'index' => true,
  'iteration' => true,
  'total' => true,
));
$_smarty_tpl->tpl_vars['module_data']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['module_data']->value) {
$_smarty_tpl->tpl_vars['module_data']->do_else = false;
$_smarty_tpl->tpl_vars['__smarty_foreach_listing']->value['iteration']++;
$_smarty_tpl->tpl_vars['__smarty_foreach_listing']->value['index']++;
$_smarty_tpl->tpl_vars['__smarty_foreach_listing']->value['first'] = !$_smarty_tpl->tpl_vars['__smarty_foreach_listing']->value['index'];
$_smarty_tpl->tpl_vars['__smarty_foreach_listing']->value['last'] = $_smarty_tpl->tpl_vars['__smarty_foreach_listing']->value['iteration'] === $_smarty_tpl->tpl_vars['__smarty_foreach_listing']->value['total'];
?>
                <div class="col col-md-4 col-sm-6 col-<?php echo $_smarty_tpl->tpl_vars['key']->value+1;
if ((isset($_smarty_tpl->tpl_vars['__smarty_foreach_listing']->value['first']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_listing']->value['first'] : null)) {?> col-first<?php } elseif ((isset($_smarty_tpl->tpl_vars['__smarty_foreach_listing']->value['last']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_listing']->value['last'] : null)) {?> col-last<?php }?>">

                    <div class="section panel panel-default<?php if ($_smarty_tpl->tpl_vars['module_data']->value['flag_has_specials'] == 1) {?> special-price<?php }
if ($_smarty_tpl->tpl_vars['module_data']->value['date_available'] != '') {?> available-soon<?php }?>">
                        <?php echo smarty_function_hook(array('key'=>'product_listing_base_section_panel_top','module_data'=>$_smarty_tpl->tpl_vars['module_data']->value),$_smarty_tpl);?>

                        <div class="section-body panel-body">
                            <div class="row">
                                <div class="col col-sm-4">
                                    <p class="image text-center">
                                        <?php if (!$_smarty_tpl->tpl_vars['module_data']->value['products_image'] || $_smarty_tpl->tpl_vars['module_data']->value['products_image'] == 'product:noimage.gif') {?>
                                            <a href="<?php echo $_smarty_tpl->tpl_vars['module_data']->value['products_link'];?>
" class="vertical-helper image-link no-image">
                                                <i class="no-image-icon"></i>
                                            </a>
                                        <?php } else { ?>
                                            <a href="<?php echo $_smarty_tpl->tpl_vars['module_data']->value['products_link'];?>
" class="vertical-helper image-link"><?php ob_start();
if ($_smarty_tpl->tpl_vars['module_data']->value['products_image_data']['media_name']) {
echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['module_data']->value['products_image_data']['media_name'], ENT_QUOTES, 'UTF-8', true);
} else {
echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['module_data']->value['products_name'], ENT_QUOTES, 'UTF-8', true);
}
$_prefixVariable1=ob_get_clean();
ob_start();
if ($_smarty_tpl->tpl_vars['module_data']->value['products_image_data']['copyright_holder']) {
echo " &copy; ";
echo (string)$_smarty_tpl->tpl_vars['module_data']->value['products_image_data']['copyright_holder'];
}
$_prefixVariable2=ob_get_clean();
ob_start();
if ($_smarty_tpl->tpl_vars['module_data']->value['products_image_data']['media_name']) {
echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['module_data']->value['products_image_data']['media_name'], ENT_QUOTES, 'UTF-8', true);
} else {
echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['module_data']->value['products_name'], ENT_QUOTES, 'UTF-8', true);
}
$_prefixVariable3=ob_get_clean();
ob_start();
if ($_smarty_tpl->tpl_vars['module_data']->value['products_image_data']['copyright_holder']) {
echo " &copy; ";
echo (string)$_smarty_tpl->tpl_vars['module_data']->value['products_image_data']['copyright_holder'];
}
$_prefixVariable4=ob_get_clean();
echo smarty_function_img(array('img'=>$_smarty_tpl->tpl_vars['module_data']->value['products_image'],'type'=>'m_info','class'=>"productImageBorder img-responsive",'alt'=>$_prefixVariable1.$_prefixVariable2,'title'=>$_prefixVariable3.$_prefixVariable4),$_smarty_tpl);?>
</a>
                                        <?php }?>
                                    </p>
                                </div>
                                <div class="col col-sm-8">
                                    <div class="title">
                                        <p class="h4"><a href="<?php echo $_smarty_tpl->tpl_vars['module_data']->value['products_link'];?>
"><?php echo $_smarty_tpl->tpl_vars['module_data']->value['products_name'];?>
</a></p>
                                        <?php if (trim(preg_replace('!<[^>]*?>!', ' ', $_smarty_tpl->tpl_vars['module_data']->value['products_short_description'] ?: '')) != '') {?>
                                            <p class="desc desc-short text-middle hidden-xs"><?php echo smarty_modifier_truncate(preg_replace('!<[^>]*?>!', ' ', $_smarty_tpl->tpl_vars['module_data']->value['products_short_description'] ?: ''),75,'...');?>
</p>
                                        <?php } elseif (trim(preg_replace('!<[^>]*?>!', ' ', $_smarty_tpl->tpl_vars['module_data']->value['products_description'] ?: '')) != '') {?>
                                            <p class="desc desc-long text-middle hidden-xs"><?php echo smarty_modifier_truncate(preg_replace('!<[^>]*?>!', ' ', $_smarty_tpl->tpl_vars['module_data']->value['products_description'] ?: ''),75,'...');?>
</p>
                                        <?php }?>
                                    </div>
                                    <?php if ($_smarty_tpl->tpl_vars['module_data']->value['review_stars_rating']) {?>
                                        <div class="product-reviews cursor-pointer" onclick="document.location.href='<?php echo $_smarty_tpl->tpl_vars['module_data']->value['link_reviews_list'];?>
'" data-toggle="popover" data-placement="bottom" data-content="<?php if ($_smarty_tpl->tpl_vars['module_data']->value['products_rating_count']) {?><i class='fa fa-heart-o'></i> <?php echo $_smarty_tpl->tpl_vars['module_data']->value['products_rating_count'];?>
 <?php echo smarty_function_txt(array('key'=>TEXT_XT_REVIEWS_INFO),$_smarty_tpl);?>
<br /><i class='fa fa-star-o'></i> <?php echo smarty_function_math(array('equation'=>'(x-1) / 100 * y','x'=>$_smarty_tpl->tpl_vars['module_data']->value['review_stars_rating'],'y'=>5,'format'=>'%.1f'),$_smarty_tpl);?>
 <?php echo smarty_function_txt(array('key'=>TEXT_PAGINATION_FROM),$_smarty_tpl);?>
 5<?php } else {
echo smarty_function_txt(array('key'=>TEXT_XT_REVIEWS_NO_REVIEWS),$_smarty_tpl);
}?>">
                                            <div class="reviews_rating_light">
                                                <i></i><i></i><i></i><i></i><i></i>
                                                <div class="reviews_rating_dark" style="width:<?php echo $_smarty_tpl->tpl_vars['module_data']->value['review_stars_rating'];?>
%">
                                                    <i></i><i></i><i></i><i></i><i></i>
                                                </div>
                                            </div>
                                        </div>
                                    <?php }?>
                                    <?php if ($_smarty_tpl->tpl_vars['module_data']->value['date_available'] != '') {?>
                                        <p class="box info visible-v2 hidden-xs"><?php echo smarty_function_txt(array('key'=>TEXT_PRODUCT_AVAILABLE),$_smarty_tpl);?>
 <?php echo date_format_intl($_smarty_tpl->tpl_vars['module_data']->value['date_available_object'],"EEEE, dd.MM.Y");?>
</p>
                                    <?php }?>
                                    <?php if ((defined('AQ_SHOW_ARTICEL_PRICE') ? constant('AQ_SHOW_ARTICEL_PRICE') : null) != '1' && $_smarty_tpl->tpl_vars['module_data']->value['products_price']['plain'] != '0' || round((float) $_smarty_tpl->tpl_vars['module_data']->value['products_quantity'], (int) 0, (int) 1) > '0') {?>
                                        <?php if ((defined('_CUST_STATUS_SHOW_PRICE') ? constant('_CUST_STATUS_SHOW_PRICE') : null) == '1' && $_smarty_tpl->tpl_vars['module_data']->value['products_price']['formated'] != '') {?>
                                            <p class="product-price"><?php echo $_smarty_tpl->tpl_vars['module_data']->value['products_price']['formated'];?>
</p>
                                            <?php if ($_smarty_tpl->tpl_vars['module_data']->value['base_price']) {?>
                                                <p class="vpe"><?php echo smarty_modifier_number_format_prec($_smarty_tpl->tpl_vars['module_data']->value['products_vpe_value']);?>
 <?php echo $_smarty_tpl->tpl_vars['module_data']->value['base_price']['vpe']['name'];?>
 / <?php echo $_smarty_tpl->tpl_vars['module_data']->value['base_price']['price'];?>
 <?php echo smarty_function_txt(array('key'=>TEXT_SHIPPING_BASE_PER),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['module_data']->value['base_price']['vpe']['name'];?>
</p>
                                            <?php }?>

                                            <?php if ($_smarty_tpl->tpl_vars['module_data']->value['products_shipping_link']) {?>
                                                <p class="product-tax-shipping"><?php echo $_smarty_tpl->tpl_vars['module_data']->value['products_tax_info']['tax_desc'];?>
 <?php if (!$_smarty_tpl->tpl_vars['module_data']->value['products_digital']) {?><a href="<?php echo $_smarty_tpl->tpl_vars['module_data']->value['products_shipping_link'];?>
" target="_blank" rel="nofollow"><?php echo smarty_function_txt(array('key'=>TEXT_EXCL_SHIPPING),$_smarty_tpl);?>
</a><?php }?></p>
                                            <?php } else { ?>
                                                <p class="product-tax-shipping"><?php echo $_smarty_tpl->tpl_vars['module_data']->value['products_tax_info']['tax_desc'];?>
 <?php if (!$_smarty_tpl->tpl_vars['module_data']->value['products_digital']) {
echo smarty_function_txt(array('key'=>TEXT_EXCL_SHIPPING),$_smarty_tpl);
}?></p>
                                            <?php }?>
                                        <?php }?>
                                    <?php }?>

                                    <?php if ($_smarty_tpl->tpl_vars['module_data']->value['show_stock']) {?>
                                        <p class="product-stock"><?php echo smarty_function_txt(array('key'=>TEXT_STOCK),$_smarty_tpl);?>
 <?php echo round((float) $_smarty_tpl->tpl_vars['module_data']->value['products_quantity'], (int) 0, (int) 1);?>
 <?php if ($_smarty_tpl->tpl_vars['module_data']->value['products_unit_name']) {
echo $_smarty_tpl->tpl_vars['module_data']->value['products_unit_name'];
}?></p>
                                    <?php }?>

                                </div>
                            </div>
                        </div>
                        <footer class="section-footer">
                            <?php echo smarty_function_hook(array('key'=>'product_listing_base_footer_top','module_data'=>$_smarty_tpl->tpl_vars['module_data']->value),$_smarty_tpl);?>

                            <?php if ($_smarty_tpl->tpl_vars['module_data']->value['allow_add_cart'] == 'true') {?>
                                <?php ob_start();
echo "product_listing_add_cart_".((string)$_smarty_tpl->tpl_vars['module_data']->value['products_id']);
$_prefixVariable5 = ob_get_clean();
echo smarty_function_form(array('type'=>'form','name'=>'product','id'=>$_prefixVariable5,'action'=>'dynamic','link_params'=>'getParams','method'=>'post'),$_smarty_tpl);?>

                                <?php echo smarty_function_form(array('type'=>'hidden','name'=>'action','value'=>'add_product'),$_smarty_tpl);?>

                                <?php echo smarty_function_form(array('type'=>'hidden','name'=>'product','value'=>$_smarty_tpl->tpl_vars['module_data']->value['products_id']),$_smarty_tpl);?>

                                <?php echo smarty_function_form(array('type'=>'hidden','name'=>'qty','value'=>1),$_smarty_tpl);?>

                                <?php echo smarty_function_form(array('type'=>'hidden','name'=>'gotoCart','value'=>(defined('_STORE_GOTO_CART_DIRECTLY') ? constant('_STORE_GOTO_CART_DIRECTLY') : null)),$_smarty_tpl);?>

                            <?php }?>
                            <div class="row">
                                <div class="col col-sm-4"></div>
                                <div class="col col-sm-8">
                                    <div class="clearfix footer-buttons<?php if ($_smarty_tpl->tpl_vars['module_data']->value['allow_add_cart'] != 'true') {?> b-0<?php }?>">
                                        <a href="<?php echo $_smarty_tpl->tpl_vars['module_data']->value['products_link'];?>
" class="btn btn-sm btn-default pull-left" role="button">
                                            <?php echo smarty_function_txt(array('key'=>TEXT_MORE),$_smarty_tpl);?>
...
                                        </a>
                                        <?php if ($_smarty_tpl->tpl_vars['module_data']->value['allow_add_cart'] == 'true') {?>
                                            <button type="submit" class="btn btn-sm btn-cart pull-right" title="<?php echo smarty_function_txt(array('key'=>BUTTON_ADD_CART),$_smarty_tpl);?>
" data-toggle="tooltip">
                                                <i class="fa fa-shopping-basket"></i>
                                                <span class="visible-v2"><?php echo smarty_function_txt(array('key'=>BUTTON_ADD_CART),$_smarty_tpl);?>
</span>
                                            </button>
                                        <?php }?>
                                        <?php if (in_array('xt_notify_on_restock',$_smarty_tpl->tpl_vars['activeModules']->value) && $_smarty_tpl->tpl_vars['module_data']->value['products_quantity'] <= 0) {?>
                                        <button type="button" class="btn btn-sm btn-cart pull-right notify nor_popup_trigger" title="<?php echo smarty_function_txt(array('key'=>'NOR_REQUEST'),$_smarty_tpl);?>
" data-toggle="tooltip" onclick="javascript:nor_popup(<?php echo $_smarty_tpl->tpl_vars['module_data']->value['products_id'];?>
);">
                                            <i class="fa fa-envelope"></i><span class="visible-xs"><?php echo smarty_function_txt(array('key'=>'NOR_REQUEST'),$_smarty_tpl);?>
</span>
                                        </button>
                                        <?php }?>
                                    </div>
                                </div>
                            </div>
                            <?php if ($_smarty_tpl->tpl_vars['module_data']->value['allow_add_cart'] == 'true') {?>
                                <?php echo smarty_function_form(array('type'=>'formend'),$_smarty_tpl);?>

                            <?php }?>
                            <?php echo smarty_function_hook(array('key'=>'product_listing_base_footer_bottom','module_data'=>$_smarty_tpl->tpl_vars['module_data']->value),$_smarty_tpl);?>

                        </footer>
                        <?php $_smarty_tpl->_subTemplateRender("file:includes/product_info_label.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('position'=>$_smarty_tpl->tpl_vars['position']->value,'isSpecial'=>$_smarty_tpl->tpl_vars['module_data']->value['flag_has_specials'],'dateAvailable'=>$_smarty_tpl->tpl_vars['module_data']->value['date_available']), 0, true);
?>
                        <?php echo smarty_function_hook(array('key'=>'product_listing_base_section_panel_bottom','module_data'=>$_smarty_tpl->tpl_vars['module_data']->value),$_smarty_tpl);?>

                    </div>
                </div><!-- .col -->
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        </div><!-- .listing -->

        <?php if ($_smarty_tpl->tpl_vars['NAVIGATION_PAGES']->value) {?>
            <div class="row products-sort-pages bottom">
                <div class="col-md-12 navigation-pages text-right">
                    <?php echo $_smarty_tpl->tpl_vars['NAVIGATION_PAGES']->value;?>

                </div><!-- .navigation-pages -->
            </div><!-- .products-sort-pages -->
        <?php }?>
    <?php }?>

    <?php echo $_smarty_tpl->tpl_vars['error_listing']->value;?>


    <?php if ($_smarty_tpl->tpl_vars['category_data']->value['categories_description_bottom']) {?>
        <div id="categorie-description-bottom" class="textstyles text-word-wrap"><?php echo $_smarty_tpl->tpl_vars['category_data']->value['categories_description_bottom'];?>
</div>
    <?php }?>
</div>
<?php }
}
