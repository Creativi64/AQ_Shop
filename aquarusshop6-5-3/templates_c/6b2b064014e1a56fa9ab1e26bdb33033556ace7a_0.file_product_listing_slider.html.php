<?php
/* Smarty version 5.4.1, created on 2024-12-30 01:20:53
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/product_listing/product_listing_slider.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.1',
  'unifunc' => 'content_6771e765b639c4_81236532',
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
))) {
function content_6771e765b639c4_81236532 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/product_listing';
if ($_smarty_tpl->getValue('product_listing')) {?>
    <div class="product-listing product-slider carousel-outer-wrap <?php echo $_smarty_tpl->getValue('code');?>
">

		<?php $_smarty_tpl->assign('unique_id', $_smarty_tpl->getSmarty()->getModifierCallback('md5')($_smarty_tpl->getValue('heading_text')), false, NULL);?>

        <?php if ($_smarty_tpl->getValue('heading_text')) {?>
            <h2 class="headline"
                data-toggle="collapse"
                data-target="#slider-<?php echo $_smarty_tpl->getValue('unique_id');?>
"
                aria-expanded="true"
                aria-controls="slider-<?php echo $_smarty_tpl->getValue('unique_id');?>
">
                <?php echo $_smarty_tpl->getValue('heading_text');?>

            </h2>
        <?php }?>

        <div id="slider-<?php echo $_smarty_tpl->getValue('unique_id');?>
" class="slider<?php if ($_smarty_tpl->getValue('heading_text')) {?> collapse collapse-auto-toggle-xs in<?php }?>">
            <div class="listing equalize-no-panels owl">
                <div id="productCarousel-<?php echo $_smarty_tpl->getValue('unique_id');?>
" class="productCarousel">

                    <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('product_listing'), 'module_data', false, 'key', 'listing', array (
  'first' => true,
  'last' => true,
  'index' => true,
  'iteration' => true,
  'total' => true,
));
$foreach3DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('key')->value => $_smarty_tpl->getVariable('module_data')->value) {
$foreach3DoElse = false;
$_smarty_tpl->tpl_vars['__smarty_foreach_listing']->value['iteration']++;
$_smarty_tpl->tpl_vars['__smarty_foreach_listing']->value['index']++;
$_smarty_tpl->tpl_vars['__smarty_foreach_listing']->value['first'] = !$_smarty_tpl->tpl_vars['__smarty_foreach_listing']->value['index'];
$_smarty_tpl->tpl_vars['__smarty_foreach_listing']->value['last'] = $_smarty_tpl->tpl_vars['__smarty_foreach_listing']->value['iteration'] === $_smarty_tpl->tpl_vars['__smarty_foreach_listing']->value['total'];
?>
                        <div class="col col-<?php echo $_smarty_tpl->getValue('key')+1;
if (($_smarty_tpl->getValue('__smarty_foreach_listing')['first'] ?? null)) {?> col-first<?php } elseif (($_smarty_tpl->getValue('__smarty_foreach_listing')['last'] ?? null)) {?> col-last<?php }?>">
                            <div class="section panel panel-default<?php if ($_smarty_tpl->getValue('module_data')['flag_has_specials'] == 1) {?> special-price<?php }
if ($_smarty_tpl->getValue('module_data')['date_available'] != '') {?> available-soon<?php }?>">
                                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'product_listing_slider_section_panel_top','module_data'=>$_smarty_tpl->getValue('module_data')), $_smarty_tpl);?>

                                <div class="section-body panel-body">
                                    <p class="image text-center">
                                        <?php if (!$_smarty_tpl->getValue('module_data')['products_image'] || $_smarty_tpl->getValue('module_data')['products_image'] == 'product:noimage.gif') {?>
                                            <a href="<?php echo $_smarty_tpl->getValue('module_data')['products_link'];?>
" class="vertical-helper image-link no-image">
                                                <i class="no-image-icon"></i>
                                            </a>
                                        <?php } else { ?>
                                            <a href="<?php echo $_smarty_tpl->getValue('module_data')['products_link'];?>
" class="vertical-helper image-link">
                                                <img alt="<?php if ($_smarty_tpl->getValue('module_data')['products_image_data']['media_name']) {
echo htmlspecialchars((string)$_smarty_tpl->getValue('module_data')['products_image_data']['media_name'], ENT_QUOTES, 'UTF-8', true);
} else {
echo htmlspecialchars((string)$_smarty_tpl->getValue('module_data')['products_name'], ENT_QUOTES, 'UTF-8', true);
}
if ($_smarty_tpl->getValue('module_data')['products_image_data']['copyright_holder']) {?> &copy; <?php echo $_smarty_tpl->getValue('module_data')['products_image_data']['copyright_holder'];
}?>" title="<?php if ($_smarty_tpl->getValue('module_data')['products_image_data']['media_name']) {
echo htmlspecialchars((string)$_smarty_tpl->getValue('module_data')['products_image_data']['media_name'], ENT_QUOTES, 'UTF-8', true);
} else {
echo htmlspecialchars((string)$_smarty_tpl->getValue('module_data')['products_name'], ENT_QUOTES, 'UTF-8', true);
}
if ($_smarty_tpl->getValue('module_data')['products_image_data']['copyright_holder']) {?> &copy; <?php echo $_smarty_tpl->getValue('module_data')['products_image_data']['copyright_holder'];
}?>" class="img-responsive" src="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('img')->handle(array('img'=>$_smarty_tpl->getValue('module_data')['products_image'],'type'=>'m_info','path_only'=>true), $_smarty_tpl);?>
" data-src="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('img')->handle(array('img'=>$_smarty_tpl->getValue('module_data')['products_image'],'type'=>'m_info','path_only'=>true), $_smarty_tpl);?>
">
                                            </a>
                                        <?php }?>
                                    </p>
                                    <div class="title">
                                        <p class="h4"><a href="<?php echo $_smarty_tpl->getValue('module_data')['products_link'];?>
"><?php echo $_smarty_tpl->getValue('module_data')['products_name'];?>
</a></p>
                                        <?php if ($_smarty_tpl->getSmarty()->getModifierCallback('trim')(preg_replace('!<[^>]*?>!', ' ', (string) $_smarty_tpl->getValue('module_data')['products_short_description'])) != '') {?>
                                            <p class="desc desc-short text-middle hidden-xs"><?php echo $_smarty_tpl->getSmarty()->getModifierCallback('truncate')(preg_replace('!<[^>]*?>!', ' ', (string) $_smarty_tpl->getValue('module_data')['products_short_description']),85,'...');?>
</p>
                                        <?php } elseif ($_smarty_tpl->getSmarty()->getModifierCallback('trim')(preg_replace('!<[^>]*?>!', ' ', (string) $_smarty_tpl->getValue('module_data')['products_description'])) != '') {?>
                                            <p class="desc desc-long text-middle hidden-xs"><?php echo $_smarty_tpl->getSmarty()->getModifierCallback('truncate')(preg_replace('!<[^>]*?>!', ' ', (string) $_smarty_tpl->getValue('module_data')['products_description']),85,'...');?>
</p>
                                        <?php }?>
                                    </div>
                                    <?php if ($_smarty_tpl->getValue('module_data')['review_stars_rating']) {?>
                                        <div class="product-reviews cursor-pointer" onclick="document.location.href='<?php echo $_smarty_tpl->getValue('module_data')['link_reviews_list'];?>
'"  data-toggle="popover" data-placement="bottom" data-content="<?php if ($_smarty_tpl->getValue('module_data')['products_rating_count']) {?><i class='fa fa-heart-o'></i> <?php echo $_smarty_tpl->getValue('module_data')['products_rating_count'];?>
 <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_XT_REVIEWS_INFO), $_smarty_tpl);?>
<br /><i class='fa fa-star-o'></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('math')->handle(array('equation'=>'(x-1) / 100 * y','x'=>$_smarty_tpl->getValue('module_data')['review_stars_rating'],'y'=>5,'format'=>'%.1f'), $_smarty_tpl);?>
 <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PAGINATION_FROM), $_smarty_tpl);?>
 5<?php } else {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_XT_REVIEWS_NO_REVIEWS), $_smarty_tpl);
}?>">
                                            <div class="reviews_rating_light">
                                                <i></i><i></i><i></i><i></i><i></i>
                                                <div class="reviews_rating_dark" style="width:<?php echo $_smarty_tpl->getValue('module_data')['review_stars_rating'];?>
%">
                                                    <i></i><i></i><i></i><i></i><i></i>
                                                </div>
                                            </div>
                                        </div>
                                    <?php }?>
                                    <?php if ((defined('AQ_SHOW_ARTICEL_PRICE') ? constant('AQ_SHOW_ARTICEL_PRICE') : null) != '1' && $_smarty_tpl->getValue('module_data')['products_price']['plain'] != '0' || round((float) $_smarty_tpl->getValue('module_data')['products_quantity'], (int) 0, (int) 1) > '0') {?>
                                        <?php if ((defined('_CUST_STATUS_SHOW_PRICE') ? constant('_CUST_STATUS_SHOW_PRICE') : null) == '1' && $_smarty_tpl->getValue('module_data')['products_price']['formated'] != '') {?>
                                            <p class="product-price"><?php echo $_smarty_tpl->getValue('module_data')['products_price']['formated'];?>
</p>
                                            <?php if ($_smarty_tpl->getValue('module_data')['base_price']) {?>
                                                <p class="vpe"><?php echo $_smarty_tpl->getValue('module_data')['base_price']['price'];?>
 <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SHIPPING_BASE_PER), $_smarty_tpl);?>
 <?php echo $_smarty_tpl->getValue('module_data')['base_price']['vpe']['name'];?>
</p>
                                            <?php }?>
                                            <?php if ($_smarty_tpl->getValue('module_data')['products_shipping_link']) {?>
                                                <p class="product-tax-shipping"><?php echo $_smarty_tpl->getValue('module_data')['products_tax_info']['tax_desc'];?>
 <a href="<?php echo $_smarty_tpl->getValue('module_data')['products_shipping_link'];?>
" target="_blank" rel="nofollow"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_EXCL_SHIPPING), $_smarty_tpl);?>
</a></p>
                                            <?php } else { ?>
                                                <p class="product-tax-shipping"><?php echo $_smarty_tpl->getValue('module_data')['products_tax_info']['tax_desc'];?>
 <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_EXCL_SHIPPING), $_smarty_tpl);?>
</p>
                                            <?php }?>
                                        <?php }?>
                                    <?php }?>
                                    <?php if ($_smarty_tpl->getValue('module_data')['show_stock']) {?>
                                    <p class="product-stock"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_STOCK), $_smarty_tpl);?>
 <?php echo round((float) $_smarty_tpl->getValue('module_data')['products_quantity'], (int) 0, (int) 1);?>
 <?php if ($_smarty_tpl->getValue('module_data')['products_unit_name']) {
echo $_smarty_tpl->getValue('module_data')['products_unit_name'];
}?></p>
                                    <?php }?>
                                </div>
                                <footer class="section-footer">
                                    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'product_listing_slider_footer_top','module_data'=>$_smarty_tpl->getValue('module_data')), $_smarty_tpl);?>

                                    <?php if ($_smarty_tpl->getValue('module_data')['allow_add_cart'] == 'true') {?>
                                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'form','name'=>'product','action'=>'dynamic','link_params'=>'getParams','method'=>'post'), $_smarty_tpl);?>

                                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'hidden','name'=>'action','value'=>'add_product'), $_smarty_tpl);?>

                                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'hidden','name'=>'product','value'=>$_smarty_tpl->getValue('module_data')['products_id']), $_smarty_tpl);?>

                                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'hidden','name'=>'qty','value'=>1), $_smarty_tpl);?>

                                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'hidden','name'=>'gotoCart','value'=>(defined('_STORE_GOTO_CART_DIRECTLY') ? constant('_STORE_GOTO_CART_DIRECTLY') : null)), $_smarty_tpl);?>

                                    <?php }?>
                                    <div class="clearfix footer-buttons<?php if ($_smarty_tpl->getValue('module_data')['allow_add_cart'] != 'true') {?> b-0<?php }?>">
                                        <a href="<?php echo $_smarty_tpl->getValue('module_data')['products_link'];?>
" class="btn btn-sm btn-default pull-left" role="button">
                                            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_MORE), $_smarty_tpl);?>
...
                                        </a>
                                        <?php if ($_smarty_tpl->getValue('module_data')['allow_add_cart'] == 'true') {?>
                                            <button type="submit" class="btn btn-sm btn-cart pull-right" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>BUTTON_ADD_CART), $_smarty_tpl);?>
" data-toggle="tooltip">
                                                <i class="fa fa-shopping-basket"></i>
                                                <span class="sr-only"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>BUTTON_ADD_CART), $_smarty_tpl);?>
</span>
                                            </button>
                                        <?php }?>
                                    </div>
                                    <?php if ($_smarty_tpl->getValue('module_data')['allow_add_cart'] == 'true') {?>
                                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'formend'), $_smarty_tpl);?>

                                    <?php }?>
                                    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'product_listing_slider_footer_bottom','module_data'=>$_smarty_tpl->getValue('module_data')), $_smarty_tpl);?>

                                </footer>
                                <?php $_smarty_tpl->renderSubTemplate("file:includes/product_info_label.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('position'=>'slider','isSpecial'=>$_smarty_tpl->getValue('module_data')['flag_has_specials'],'dateAvailable'=>$_smarty_tpl->getValue('module_data')['date_available']), (int) 0, $_smarty_current_dir);
?>
                                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'product_listing_slider_section_panel_bottom','module_data'=>$_smarty_tpl->getValue('module_data')), $_smarty_tpl);?>

                            </div>
                        </div><!-- .col -->
                    <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>

                    <?php if ($_smarty_tpl->getValue('show_more') && $_smarty_tpl->getValue('code') && $_smarty_tpl->getValue('code') != 'xt_auto_cross_sell' && $_smarty_tpl->getValue('code') != 'xt_cross_selling') {?>
                        <div class="col col-<?php echo $_smarty_tpl->getValue('key')+1;
if (($_smarty_tpl->getValue('__smarty_foreach_listing')['first'] ?? null)) {?> col-first<?php } elseif (($_smarty_tpl->getValue('__smarty_foreach_listing')['last'] ?? null)) {?> col-last<?php }?>">
                            <div class="section panel panel-default">
                                <div class="section-body vertical-helper">
                                    <p class="image text-center text-big title">
                                        <a class="" href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>$_smarty_tpl->getValue('code'),'conn'=>'SSL'), $_smarty_tpl);?>
" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>'TEXT_MORE'), $_smarty_tpl);?>
">
                                            <i class="fa fa-plus-circle fa-5x"></i><br/><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>"TEXT_MORE"), $_smarty_tpl);?>

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
echo $_smarty_tpl->getValue('error_listing');?>

<?php }
}
