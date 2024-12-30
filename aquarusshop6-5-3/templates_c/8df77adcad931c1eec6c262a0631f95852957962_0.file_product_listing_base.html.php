<?php
/* Smarty version 5.4.1, created on 2024-12-30 01:21:05
  from 'file:includes/product_listing_base.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.1',
  'unifunc' => 'content_6771e77190bee9_24657374',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '8df77adcad931c1eec6c262a0631f95852957962' => 
    array (
      0 => 'includes/product_listing_base.html',
      1 => 1697144063,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:xtCore/pages/search.html' => 1,
    'file:includes/product_info_label.html' => 1,
  ),
))) {
function content_6771e77190bee9_24657374 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/includes';
?><div class="product-listing product-listing-<?php echo $_smarty_tpl->getValue('position');
if ($_smarty_tpl->getValue('listingSwitch') == 1) {?> product-listing-switch<?php }?>">
    <?php echo $_smarty_tpl->getValue('categories');?>

    <?php if ($_smarty_tpl->getValue('product_listing')) {?>

        <?php if ($_smarty_tpl->getValue('manufacturer')['MANUFACTURER']['manufacturers_image']) {?><div class="pull-right manufacturers-image"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('img')->handle(array('img'=>$_smarty_tpl->getValue('manufacturer')['MANUFACTURER']['manufacturers_image'],'type'=>'m_org','class'=>"img-responsive img-thumbnail"), $_smarty_tpl);?>
</div><?php }?>

        <?php if ($_smarty_tpl->getValue('heading_text')) {?>
            <?php if (!$_GET['page'] || $_GET['page'] == 'index') {?>
                <h2 class="breaking-headline index-heading"><?php echo $_smarty_tpl->getValue('heading_text');?>
</h2>
            <?php } else { ?>
                <?php if ($_GET['page'] == 'cart') {?>
                    <h2 class="breaking-headline <?php echo $_GET['page'];?>
-heading"><?php echo $_smarty_tpl->getValue('heading_text');?>
</h2>
                <?php } else { ?>
                    <h1 class="breaking-headline <?php echo $_GET['page'];?>
-heading"><?php echo $_smarty_tpl->getValue('heading_text');?>
</h1>
                <?php }?>
            <?php }?>
        <?php }?>
        <?php if ($_smarty_tpl->getValue('manufacturer')['MANUFACTURER']['manufacturers_url']) {?><a class="small" href="<?php echo $_smarty_tpl->getValue('manufacturer')['MANUFACTURER']['manufacturers_url'];?>
" target="_blank"><?php echo $_smarty_tpl->getValue('manufacturer')['MANUFACTURER']['manufacturers_url'];?>
</a><?php }?>

        <?php if ($_GET['page'] == 'search') {?>
            <?php $_smarty_tpl->renderSubTemplate("file:xtCore/pages/search.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('included'=>true,'type'=>"tpl_include"), (int) 0, $_smarty_current_dir);
?>
        <?php }?>

        <?php if ($_smarty_tpl->getValue('PRODUCTS_DROPDOWN')) {
echo $_smarty_tpl->getValue('PRODUCTS_DROPDOWN');
}?>
        <?php if ($_smarty_tpl->getValue('manufacturer') && $_smarty_tpl->getSmarty()->getModifierCallback('trim')($_smarty_tpl->getValue('manufacturer')['MANUFACTURER']['manufacturers_description']) != '') {?><div class="manufacturers-description text-word-wrap"><?php echo $_smarty_tpl->getValue('manufacturer')['MANUFACTURER']['manufacturers_description'];?>
</div><?php }?>

        <div class="clearfix"></div>

        <div class="row products-sort-pages top<?php if (!$_GET['page'] || $_GET['page'] == 'index') {?> pull-right<?php }?>">
            <?php if ($_smarty_tpl->getValue('sort_dropdown')['options']) {?>
                <div class="col col-md-4 products-sort">
                    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'form','name'=>'sort_dropdown','action'=>'dynamic','method'=>'get'), $_smarty_tpl);?>

                        <input type="hidden" name=page value="<?php echo $_smarty_tpl->getValue('page');?>
">
                        <?php if ((null !== ($_smarty_tpl->getValue('current_category_id') ?? null))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'hidden','name'=>'cat','value'=>$_smarty_tpl->getValue('current_category_id')), $_smarty_tpl);
}?>
                        <?php if ((null !== ($_smarty_tpl->getValue('current_manufacturer_id') ?? null))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'hidden','name'=>'mnf','value'=>$_smarty_tpl->getValue('current_manufacturer_id')), $_smarty_tpl);
}?>
                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('class'=>"form-control",'type'=>'select','name'=>'sorting','value'=>$_smarty_tpl->getValue('sort_dropdown')['options'],'default'=>$_smarty_tpl->getValue('sort_default'),'onchange'=>'this.form.submit();'), $_smarty_tpl);?>

                        
                        <?php if ($_smarty_tpl->getValue('MANUFACTURER_DROPDOWN') && $_smarty_tpl->getValue('MANUFACTURER_DROPDOWN')['options']) {?>
                            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('class'=>"form-control",'type'=>'select','name'=>'filter_id','value'=>$_smarty_tpl->getValue('MANUFACTURER_DROPDOWN')['options'],'default'=>$_smarty_tpl->getValue('filter_default'),'onchange'=>'this.form.submit();'), $_smarty_tpl);?>

                        <?php }?>

                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'product_listing_base_sort_form_bottom'), $_smarty_tpl);?>

                    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'formend'), $_smarty_tpl);?>

                </div><!-- .products-sort -->
                <div class="col col-md-8 navigation-pages">
                    <div class="btn-toolbar pull-right" role="toolbar">
                        <?php if ($_smarty_tpl->getValue('listingSwitch') == 1) {?>
                            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('box')->handle(array('name'=>'listing_switch','htmlonly'=>1), $_smarty_tpl);?>

                        <?php }?>
                        <?php echo $_smarty_tpl->getValue('NAVIGATION_PAGES');?>

                    </div>
                </div><!-- .navigation-pages -->
            <?php } else { ?>
                <div class="col col-md-12 navigation-pages">
                    <div class="btn-toolbar pull-right" role="toolbar">
                        <?php if ($_smarty_tpl->getValue('listingSwitch') == 1) {?>
                            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('box')->handle(array('name'=>'listing_switch','htmlonly'=>1), $_smarty_tpl);?>

                        <?php }?>
                        <?php echo $_smarty_tpl->getValue('NAVIGATION_PAGES');?>

                    </div>
                </div><!-- .navigation-pages -->
            <?php }?>
        </div><!-- .products-sort-pages -->
        <div class="clearfix"></div>

        <div class="listing row products">
            <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('product_listing'), 'module_data', false, 'key', 'listing', array (
  'first' => true,
  'last' => true,
  'index' => true,
  'iteration' => true,
  'total' => true,
));
$foreach1DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('key')->value => $_smarty_tpl->getVariable('module_data')->value) {
$foreach1DoElse = false;
$_smarty_tpl->tpl_vars['__smarty_foreach_listing']->value['iteration']++;
$_smarty_tpl->tpl_vars['__smarty_foreach_listing']->value['index']++;
$_smarty_tpl->tpl_vars['__smarty_foreach_listing']->value['first'] = !$_smarty_tpl->tpl_vars['__smarty_foreach_listing']->value['index'];
$_smarty_tpl->tpl_vars['__smarty_foreach_listing']->value['last'] = $_smarty_tpl->tpl_vars['__smarty_foreach_listing']->value['iteration'] === $_smarty_tpl->tpl_vars['__smarty_foreach_listing']->value['total'];
?>
                <div class="col col-md-4 col-sm-6 col-<?php echo $_smarty_tpl->getValue('key')+1;
if (($_smarty_tpl->getValue('__smarty_foreach_listing')['first'] ?? null)) {?> col-first<?php } elseif (($_smarty_tpl->getValue('__smarty_foreach_listing')['last'] ?? null)) {?> col-last<?php }?>">

                    <div class="section panel panel-default<?php if ($_smarty_tpl->getValue('module_data')['flag_has_specials'] == 1) {?> special-price<?php }
if ($_smarty_tpl->getValue('module_data')['date_available'] != '') {?> available-soon<?php }?>">
                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'product_listing_base_section_panel_top','module_data'=>$_smarty_tpl->getValue('module_data')), $_smarty_tpl);?>

                        <div class="section-body panel-body">
                            <div class="row">
                                <div class="col col-sm-4">
                                    <p class="image text-center">
                                        <?php if (!$_smarty_tpl->getValue('module_data')['products_image'] || $_smarty_tpl->getValue('module_data')['products_image'] == 'product:noimage.gif') {?>
                                            <a href="<?php echo $_smarty_tpl->getValue('module_data')['products_link'];?>
" class="vertical-helper image-link no-image">
                                                <i class="no-image-icon"></i>
                                            </a>
                                        <?php } else { ?>
                                            <a href="<?php echo $_smarty_tpl->getValue('module_data')['products_link'];?>
" class="vertical-helper image-link"><?php ob_start();
if ($_smarty_tpl->getValue('module_data')['products_image_data']['media_name']) {
echo htmlspecialchars((string)$_smarty_tpl->getValue('module_data')['products_image_data']['media_name'], ENT_QUOTES, 'UTF-8', true);
} else {
echo htmlspecialchars((string)$_smarty_tpl->getValue('module_data')['products_name'], ENT_QUOTES, 'UTF-8', true);
}
$_prefixVariable1=ob_get_clean();
ob_start();
if ($_smarty_tpl->getValue('module_data')['products_image_data']['copyright_holder']) {
echo " &copy; ";
echo (string)$_smarty_tpl->getValue('module_data')['products_image_data']['copyright_holder'];
}
$_prefixVariable2=ob_get_clean();
ob_start();
if ($_smarty_tpl->getValue('module_data')['products_image_data']['media_name']) {
echo htmlspecialchars((string)$_smarty_tpl->getValue('module_data')['products_image_data']['media_name'], ENT_QUOTES, 'UTF-8', true);
} else {
echo htmlspecialchars((string)$_smarty_tpl->getValue('module_data')['products_name'], ENT_QUOTES, 'UTF-8', true);
}
$_prefixVariable3=ob_get_clean();
ob_start();
if ($_smarty_tpl->getValue('module_data')['products_image_data']['copyright_holder']) {
echo " &copy; ";
echo (string)$_smarty_tpl->getValue('module_data')['products_image_data']['copyright_holder'];
}
$_prefixVariable4=ob_get_clean();
echo $_smarty_tpl->getSmarty()->getFunctionHandler('img')->handle(array('img'=>$_smarty_tpl->getValue('module_data')['products_image'],'type'=>'m_info','class'=>"productImageBorder img-responsive",'alt'=>$_prefixVariable1.$_prefixVariable2,'title'=>$_prefixVariable3.$_prefixVariable4), $_smarty_tpl);?>
</a>
                                        <?php }?>
                                    </p>
                                </div>
                                <div class="col col-sm-8">
                                    <div class="title">
                                        <p class="h4"><a href="<?php echo $_smarty_tpl->getValue('module_data')['products_link'];?>
"><?php echo $_smarty_tpl->getValue('module_data')['products_name'];?>
</a></p>
                                        <?php if ($_smarty_tpl->getSmarty()->getModifierCallback('trim')(preg_replace('!<[^>]*?>!', ' ', (string) $_smarty_tpl->getValue('module_data')['products_short_description'])) != '') {?>
                                            <p class="desc desc-short text-middle hidden-xs"><?php echo $_smarty_tpl->getSmarty()->getModifierCallback('truncate')(preg_replace('!<[^>]*?>!', ' ', (string) $_smarty_tpl->getValue('module_data')['products_short_description']),75,'...');?>
</p>
                                        <?php } elseif ($_smarty_tpl->getSmarty()->getModifierCallback('trim')(preg_replace('!<[^>]*?>!', ' ', (string) $_smarty_tpl->getValue('module_data')['products_description'])) != '') {?>
                                            <p class="desc desc-long text-middle hidden-xs"><?php echo $_smarty_tpl->getSmarty()->getModifierCallback('truncate')(preg_replace('!<[^>]*?>!', ' ', (string) $_smarty_tpl->getValue('module_data')['products_description']),75,'...');?>
</p>
                                        <?php }?>
                                    </div>
                                    <?php if ($_smarty_tpl->getValue('module_data')['review_stars_rating']) {?>
                                        <div class="product-reviews cursor-pointer" onclick="document.location.href='<?php echo $_smarty_tpl->getValue('module_data')['link_reviews_list'];?>
'" data-toggle="popover" data-placement="bottom" data-content="<?php if ($_smarty_tpl->getValue('module_data')['products_rating_count']) {?><i class='fa fa-heart-o'></i> <?php echo $_smarty_tpl->getValue('module_data')['products_rating_count'];?>
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
                                    <?php if ($_smarty_tpl->getValue('module_data')['date_available'] != '') {?>
                                        <p class="box info visible-v2 hidden-xs"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PRODUCT_AVAILABLE), $_smarty_tpl);?>
 <?php echo $_smarty_tpl->getSmarty()->getModifierCallback('date_format_intl')($_smarty_tpl->getValue('module_data')['date_available_object'],"EEEE, dd.MM.Y");?>
</p>
                                    <?php }?>
                                    <?php if ((defined('AQ_SHOW_ARTICEL_PRICE') ? constant('AQ_SHOW_ARTICEL_PRICE') : null) != '1' && $_smarty_tpl->getValue('module_data')['products_price']['plain'] != '0' || round((float) $_smarty_tpl->getValue('module_data')['products_quantity'], (int) 0, (int) 1) > '0') {?>
                                        <?php if ((defined('_CUST_STATUS_SHOW_PRICE') ? constant('_CUST_STATUS_SHOW_PRICE') : null) == '1' && $_smarty_tpl->getValue('module_data')['products_price']['formated'] != '') {?>
                                            <p class="product-price"><?php echo $_smarty_tpl->getValue('module_data')['products_price']['formated'];?>
</p>
                                            <?php if ($_smarty_tpl->getValue('module_data')['base_price']) {?>
                                                <p class="vpe"><?php echo $_smarty_tpl->getSmarty()->getModifierCallback('number_format_prec')($_smarty_tpl->getValue('module_data')['products_vpe_value']);?>
 <?php echo $_smarty_tpl->getValue('module_data')['base_price']['vpe']['name'];?>
 / <?php echo $_smarty_tpl->getValue('module_data')['base_price']['price'];?>
 <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SHIPPING_BASE_PER), $_smarty_tpl);?>
 <?php echo $_smarty_tpl->getValue('module_data')['base_price']['vpe']['name'];?>
</p>
                                            <?php }?>

                                            <?php if ($_smarty_tpl->getValue('module_data')['products_shipping_link']) {?>
                                                <p class="product-tax-shipping"><?php echo $_smarty_tpl->getValue('module_data')['products_tax_info']['tax_desc'];?>
 <?php if (!$_smarty_tpl->getValue('module_data')['products_digital']) {?><a href="<?php echo $_smarty_tpl->getValue('module_data')['products_shipping_link'];?>
" target="_blank" rel="nofollow"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_EXCL_SHIPPING), $_smarty_tpl);?>
</a><?php }?></p>
                                            <?php } else { ?>
                                                <p class="product-tax-shipping"><?php echo $_smarty_tpl->getValue('module_data')['products_tax_info']['tax_desc'];?>
 <?php if (!$_smarty_tpl->getValue('module_data')['products_digital']) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_EXCL_SHIPPING), $_smarty_tpl);
}?></p>
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
                            </div>
                        </div>
                        <footer class="section-footer">
                            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'product_listing_base_footer_top','module_data'=>$_smarty_tpl->getValue('module_data')), $_smarty_tpl);?>

                            <?php if ($_smarty_tpl->getValue('module_data')['allow_add_cart'] == 'true') {?>
                                <?php ob_start();
echo "product_listing_add_cart_".((string)$_smarty_tpl->getValue('module_data')['products_id']);
$_prefixVariable5 = ob_get_clean();
echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'form','name'=>'product','id'=>$_prefixVariable5,'action'=>'dynamic','link_params'=>'getParams','method'=>'post'), $_smarty_tpl);?>

                                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'hidden','name'=>'action','value'=>'add_product'), $_smarty_tpl);?>

                                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'hidden','name'=>'product','value'=>$_smarty_tpl->getValue('module_data')['products_id']), $_smarty_tpl);?>

                                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'hidden','name'=>'qty','value'=>1), $_smarty_tpl);?>

                                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'hidden','name'=>'gotoCart','value'=>(defined('_STORE_GOTO_CART_DIRECTLY') ? constant('_STORE_GOTO_CART_DIRECTLY') : null)), $_smarty_tpl);?>

                            <?php }?>
                            <div class="row">
                                <div class="col col-sm-4"></div>
                                <div class="col col-sm-8">
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
                                                <span class="visible-v2"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>BUTTON_ADD_CART), $_smarty_tpl);?>
</span>
                                            </button>
                                        <?php }?>
                                        <?php if ($_smarty_tpl->getSmarty()->getModifierCallback('in_array')('xt_notify_on_restock',$_smarty_tpl->getValue('activeModules')) && $_smarty_tpl->getValue('module_data')['products_quantity'] <= 0) {?>
                                        <button type="button" class="btn btn-sm btn-cart pull-right notify nor_popup_trigger" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>'NOR_REQUEST'), $_smarty_tpl);?>
" data-toggle="tooltip" onclick="javascript:nor_popup(<?php echo $_smarty_tpl->getValue('module_data')['products_id'];?>
);">
                                            <i class="fa fa-envelope"></i><span class="visible-xs"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>'NOR_REQUEST'), $_smarty_tpl);?>
</span>
                                        </button>
                                        <?php }?>
                                    </div>
                                </div>
                            </div>
                            <?php if ($_smarty_tpl->getValue('module_data')['allow_add_cart'] == 'true') {?>
                                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'formend'), $_smarty_tpl);?>

                            <?php }?>
                            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'product_listing_base_footer_bottom','module_data'=>$_smarty_tpl->getValue('module_data')), $_smarty_tpl);?>

                        </footer>
                        <?php $_smarty_tpl->renderSubTemplate("file:includes/product_info_label.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('position'=>$_smarty_tpl->getValue('position'),'isSpecial'=>$_smarty_tpl->getValue('module_data')['flag_has_specials'],'dateAvailable'=>$_smarty_tpl->getValue('module_data')['date_available']), (int) 0, $_smarty_current_dir);
?>
                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'product_listing_base_section_panel_bottom','module_data'=>$_smarty_tpl->getValue('module_data')), $_smarty_tpl);?>

                    </div>
                </div><!-- .col -->
            <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
        </div><!-- .listing -->

        <?php if ($_smarty_tpl->getValue('NAVIGATION_PAGES')) {?>
            <div class="row products-sort-pages bottom">
                <div class="col-md-12 navigation-pages text-right">
                    <?php echo $_smarty_tpl->getValue('NAVIGATION_PAGES');?>

                </div><!-- .navigation-pages -->
            </div><!-- .products-sort-pages -->
        <?php }?>
    <?php }?>

    <?php echo $_smarty_tpl->getValue('error_listing');?>


    <?php if ($_smarty_tpl->getValue('category_data')['categories_description_bottom']) {?>
        <div id="categorie-description-bottom" class="textstyles text-word-wrap"><?php echo $_smarty_tpl->getValue('category_data')['categories_description_bottom'];?>
</div>
    <?php }?>
</div>
<?php }
}
