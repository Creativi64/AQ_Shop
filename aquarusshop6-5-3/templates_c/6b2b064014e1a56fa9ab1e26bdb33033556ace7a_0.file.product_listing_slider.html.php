<?php
/* Smarty version 4.3.2, created on 2024-05-08 20:28:39
  from '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/product_listing/product_listing_slider.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_663bc457f3bf60_02031039',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '6b2b064014e1a56fa9ab1e26bdb33033556ace7a' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/product_listing/product_listing_slider.html',
      1 => 1697144246,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:includes/product_info_label.html' => 1,
  ),
),false)) {
function content_663bc457f3bf60_02031039 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.hook.php','function'=>'smarty_function_hook',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.img.php','function'=>'smarty_function_img',),2=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/vendor/smarty/smarty/libs/plugins/modifier.truncate.php','function'=>'smarty_modifier_truncate',),3=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),4=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/vendor/smarty/smarty/libs/plugins/function.math.php','function'=>'smarty_function_math',),5=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.form.php','function'=>'smarty_function_form',),6=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.link.php','function'=>'smarty_function_link',),));
if ($_smarty_tpl->tpl_vars['product_listing']->value) {?>
    <div class="product-listing product-slider carousel-outer-wrap <?php echo $_smarty_tpl->tpl_vars['code']->value;?>
">

		<?php $_smarty_tpl->_assignInScope('unique_id', md5($_smarty_tpl->tpl_vars['heading_text']->value));?>

        <?php if ($_smarty_tpl->tpl_vars['heading_text']->value) {?>
            <h2 class="headline"
                data-toggle="collapse"
                data-target="#slider-<?php echo $_smarty_tpl->tpl_vars['unique_id']->value;?>
"
                aria-expanded="true"
                aria-controls="slider-<?php echo $_smarty_tpl->tpl_vars['unique_id']->value;?>
">
                <?php echo $_smarty_tpl->tpl_vars['heading_text']->value;?>

            </h2>
        <?php }?>

        <div id="slider-<?php echo $_smarty_tpl->tpl_vars['unique_id']->value;?>
" class="slider<?php if ($_smarty_tpl->tpl_vars['heading_text']->value) {?> collapse collapse-auto-toggle-xs in<?php }?>">
            <div class="listing equalize-no-panels owl">
                <div id="productCarousel-<?php echo $_smarty_tpl->tpl_vars['unique_id']->value;?>
" class="productCarousel">

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
                        <div class="col col-<?php echo $_smarty_tpl->tpl_vars['key']->value+1;
if ((isset($_smarty_tpl->tpl_vars['__smarty_foreach_listing']->value['first']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_listing']->value['first'] : null)) {?> col-first<?php } elseif ((isset($_smarty_tpl->tpl_vars['__smarty_foreach_listing']->value['last']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_listing']->value['last'] : null)) {?> col-last<?php }?>">
                            <div class="section panel panel-default<?php if ($_smarty_tpl->tpl_vars['module_data']->value['flag_has_specials'] == 1) {?> special-price<?php }
if ($_smarty_tpl->tpl_vars['module_data']->value['date_available'] != '') {?> available-soon<?php }?>">
                                <?php echo smarty_function_hook(array('key'=>'product_listing_slider_section_panel_top','module_data'=>$_smarty_tpl->tpl_vars['module_data']->value),$_smarty_tpl);?>

                                <div class="section-body panel-body">
                                    <p class="image text-center">
                                        <?php if (!$_smarty_tpl->tpl_vars['module_data']->value['products_image'] || $_smarty_tpl->tpl_vars['module_data']->value['products_image'] == 'product:noimage.gif') {?>
                                            <a href="<?php echo $_smarty_tpl->tpl_vars['module_data']->value['products_link'];?>
" class="vertical-helper image-link no-image">
                                                <i class="no-image-icon"></i>
                                            </a>
                                        <?php } else { ?>
                                            <a href="<?php echo $_smarty_tpl->tpl_vars['module_data']->value['products_link'];?>
" class="vertical-helper image-link">
                                                <img alt="<?php if ($_smarty_tpl->tpl_vars['module_data']->value['products_image_data']['media_name']) {
echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['module_data']->value['products_image_data']['media_name'], ENT_QUOTES, 'UTF-8', true);
} else {
echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['module_data']->value['products_name'], ENT_QUOTES, 'UTF-8', true);
}
if ($_smarty_tpl->tpl_vars['module_data']->value['products_image_data']['copyright_holder']) {?> &copy; <?php echo $_smarty_tpl->tpl_vars['module_data']->value['products_image_data']['copyright_holder'];
}?>" title="<?php if ($_smarty_tpl->tpl_vars['module_data']->value['products_image_data']['media_name']) {
echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['module_data']->value['products_image_data']['media_name'], ENT_QUOTES, 'UTF-8', true);
} else {
echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['module_data']->value['products_name'], ENT_QUOTES, 'UTF-8', true);
}
if ($_smarty_tpl->tpl_vars['module_data']->value['products_image_data']['copyright_holder']) {?> &copy; <?php echo $_smarty_tpl->tpl_vars['module_data']->value['products_image_data']['copyright_holder'];
}?>" class="img-responsive" src="<?php echo smarty_function_img(array('img'=>$_smarty_tpl->tpl_vars['module_data']->value['products_image'],'type'=>'m_info','path_only'=>true),$_smarty_tpl);?>
" data-src="<?php echo smarty_function_img(array('img'=>$_smarty_tpl->tpl_vars['module_data']->value['products_image'],'type'=>'m_info','path_only'=>true),$_smarty_tpl);?>
">
                                            </a>
                                        <?php }?>
                                    </p>
                                    <div class="title">
                                        <p class="h4"><a href="<?php echo $_smarty_tpl->tpl_vars['module_data']->value['products_link'];?>
"><?php echo $_smarty_tpl->tpl_vars['module_data']->value['products_name'];?>
</a></p>
                                        <?php if (trim(preg_replace('!<[^>]*?>!', ' ', $_smarty_tpl->tpl_vars['module_data']->value['products_short_description'] ?: '')) != '') {?>
                                            <p class="desc desc-short text-middle hidden-xs"><?php echo smarty_modifier_truncate(preg_replace('!<[^>]*?>!', ' ', $_smarty_tpl->tpl_vars['module_data']->value['products_short_description'] ?: ''),85,'...');?>
</p>
                                        <?php } elseif (trim(preg_replace('!<[^>]*?>!', ' ', $_smarty_tpl->tpl_vars['module_data']->value['products_description'] ?: '')) != '') {?>
                                            <p class="desc desc-long text-middle hidden-xs"><?php echo smarty_modifier_truncate(preg_replace('!<[^>]*?>!', ' ', $_smarty_tpl->tpl_vars['module_data']->value['products_description'] ?: ''),85,'...');?>
</p>
                                        <?php }?>
                                    </div>
                                    <?php if ($_smarty_tpl->tpl_vars['module_data']->value['review_stars_rating']) {?>
                                        <div class="product-reviews cursor-pointer" onclick="document.location.href='<?php echo $_smarty_tpl->tpl_vars['module_data']->value['link_reviews_list'];?>
'"  data-toggle="popover" data-placement="bottom" data-content="<?php if ($_smarty_tpl->tpl_vars['module_data']->value['products_rating_count']) {?><i class='fa fa-heart-o'></i> <?php echo $_smarty_tpl->tpl_vars['module_data']->value['products_rating_count'];?>
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
                                    <?php if ((defined('AQ_SHOW_ARTICEL_PRICE') ? constant('AQ_SHOW_ARTICEL_PRICE') : null) != '1' && $_smarty_tpl->tpl_vars['module_data']->value['products_price']['plain'] != '0' || round((float) $_smarty_tpl->tpl_vars['module_data']->value['products_quantity'], (int) 0, (int) 1) > '0') {?>
                                        <?php if ((defined('_CUST_STATUS_SHOW_PRICE') ? constant('_CUST_STATUS_SHOW_PRICE') : null) == '1' && $_smarty_tpl->tpl_vars['module_data']->value['products_price']['formated'] != '') {?>
                                            <p class="product-price"><?php echo $_smarty_tpl->tpl_vars['module_data']->value['products_price']['formated'];?>
</p>
                                            <?php if ($_smarty_tpl->tpl_vars['module_data']->value['base_price']) {?>
                                                <p class="vpe"><?php echo $_smarty_tpl->tpl_vars['module_data']->value['base_price']['price'];?>
 <?php echo smarty_function_txt(array('key'=>TEXT_SHIPPING_BASE_PER),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['module_data']->value['base_price']['vpe']['name'];?>
</p>
                                            <?php }?>
                                            <?php if ($_smarty_tpl->tpl_vars['module_data']->value['products_shipping_link']) {?>
                                                <p class="product-tax-shipping"><?php echo $_smarty_tpl->tpl_vars['module_data']->value['products_tax_info']['tax_desc'];?>
 <a href="<?php echo $_smarty_tpl->tpl_vars['module_data']->value['products_shipping_link'];?>
" target="_blank" rel="nofollow"><?php echo smarty_function_txt(array('key'=>TEXT_EXCL_SHIPPING),$_smarty_tpl);?>
</a></p>
                                            <?php } else { ?>
                                                <p class="product-tax-shipping"><?php echo $_smarty_tpl->tpl_vars['module_data']->value['products_tax_info']['tax_desc'];?>
 <?php echo smarty_function_txt(array('key'=>TEXT_EXCL_SHIPPING),$_smarty_tpl);?>
</p>
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
                                <footer class="section-footer">
                                    <?php echo smarty_function_hook(array('key'=>'product_listing_slider_footer_top','module_data'=>$_smarty_tpl->tpl_vars['module_data']->value),$_smarty_tpl);?>

                                    <?php if ($_smarty_tpl->tpl_vars['module_data']->value['allow_add_cart'] == 'true') {?>
                                        <?php echo smarty_function_form(array('type'=>'form','name'=>'product','action'=>'dynamic','link_params'=>'getParams','method'=>'post'),$_smarty_tpl);?>

                                        <?php echo smarty_function_form(array('type'=>'hidden','name'=>'action','value'=>'add_product'),$_smarty_tpl);?>

                                        <?php echo smarty_function_form(array('type'=>'hidden','name'=>'product','value'=>$_smarty_tpl->tpl_vars['module_data']->value['products_id']),$_smarty_tpl);?>

                                        <?php echo smarty_function_form(array('type'=>'hidden','name'=>'qty','value'=>1),$_smarty_tpl);?>

                                        <?php echo smarty_function_form(array('type'=>'hidden','name'=>'gotoCart','value'=>(defined('_STORE_GOTO_CART_DIRECTLY') ? constant('_STORE_GOTO_CART_DIRECTLY') : null)),$_smarty_tpl);?>

                                    <?php }?>
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
                                                <span class="sr-only"><?php echo smarty_function_txt(array('key'=>BUTTON_ADD_CART),$_smarty_tpl);?>
</span>
                                            </button>
                                        <?php }?>
                                    </div>
                                    <?php if ($_smarty_tpl->tpl_vars['module_data']->value['allow_add_cart'] == 'true') {?>
                                        <?php echo smarty_function_form(array('type'=>'formend'),$_smarty_tpl);?>

                                    <?php }?>
                                    <?php echo smarty_function_hook(array('key'=>'product_listing_slider_footer_bottom','module_data'=>$_smarty_tpl->tpl_vars['module_data']->value),$_smarty_tpl);?>

                                </footer>
                                <?php $_smarty_tpl->_subTemplateRender("file:includes/product_info_label.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('position'=>'slider','isSpecial'=>$_smarty_tpl->tpl_vars['module_data']->value['flag_has_specials'],'dateAvailable'=>$_smarty_tpl->tpl_vars['module_data']->value['date_available']), 0, true);
?>
                                <?php echo smarty_function_hook(array('key'=>'product_listing_slider_section_panel_bottom','module_data'=>$_smarty_tpl->tpl_vars['module_data']->value),$_smarty_tpl);?>

                            </div>
                        </div><!-- .col -->
                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

                    <?php if ($_smarty_tpl->tpl_vars['show_more']->value && $_smarty_tpl->tpl_vars['code']->value && $_smarty_tpl->tpl_vars['code']->value != 'xt_auto_cross_sell' && $_smarty_tpl->tpl_vars['code']->value != 'xt_cross_selling') {?>
                        <div class="col col-<?php echo $_smarty_tpl->tpl_vars['key']->value+1;
if ((isset($_smarty_tpl->tpl_vars['__smarty_foreach_listing']->value['first']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_listing']->value['first'] : null)) {?> col-first<?php } elseif ((isset($_smarty_tpl->tpl_vars['__smarty_foreach_listing']->value['last']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_listing']->value['last'] : null)) {?> col-last<?php }?>">
                            <div class="section panel panel-default">
                                <div class="section-body vertical-helper">
                                    <p class="image text-center text-big title">
                                        <a class="" href="<?php echo smarty_function_link(array('page'=>$_smarty_tpl->tpl_vars['code']->value,'conn'=>'SSL'),$_smarty_tpl);?>
" title="<?php echo smarty_function_txt(array('key'=>'TEXT_MORE'),$_smarty_tpl);?>
">
                                            <i class="fa fa-plus-circle fa-5x"></i><br/><?php echo smarty_function_txt(array('key'=>"TEXT_MORE"),$_smarty_tpl);?>

                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php }?>

                </div><!-- .productCarousel -->
            </div><!-- .listing -->
        </div><!-- .listing-outer -->

    </div>
<?php }
echo $_smarty_tpl->tpl_vars['error_listing']->value;?>

<?php }
}
