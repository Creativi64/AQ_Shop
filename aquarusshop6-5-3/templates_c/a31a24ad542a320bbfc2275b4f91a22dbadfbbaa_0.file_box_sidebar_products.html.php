<?php
/* Smarty version 5.1.0, created on 2024-08-03 01:15:03
  from 'file:includes/box_sidebar_products.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_66ad6877913b62_53798914',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'a31a24ad542a320bbfc2275b4f91a22dbadfbbaa' => 
    array (
      0 => 'includes/box_sidebar_products.html',
      1 => 1697144063,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:includes/product_info_label.html' => 1,
  ),
))) {
function content_66ad6877913b62_53798914 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/includes';
$_smarty_tpl->assign('visible_items', "1", false, NULL);?>

<div id="box_<?php echo $_smarty_tpl->getValue('code');?>
" data-visible-items="<?php echo $_smarty_tpl->getValue('visible_items');?>
" class="products-box listing no-image-height-helper equalize-nothing panel panel-<?php if ($_smarty_tpl->getValue('code') == 'xt_special_products') {?>secondary<?php } else { ?>default<?php }?> switch-area<?php if ($_smarty_tpl->getValue('classes')) {?> <?php echo $_smarty_tpl->getValue('classes');
}?>">

    <div class="panel-heading">
        <p class="panel-title text-uppercase">
            <?php if (($_smarty_tpl->getValue('_show_more_link') == 'true' && $_smarty_tpl->getValue('_show_page_link') == 'true') || ($_smarty_tpl->getValue('_show_more_link') == '1' && $_smarty_tpl->getValue('_show_page_link') == '1')) {?>
                <a href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>$_smarty_tpl->getValue('code'),'conn'=>'SSL'), $_smarty_tpl);?>
">
            <?php }?>
            <?php echo $_smarty_tpl->getValue('heading_text');?>

            <?php if (($_smarty_tpl->getValue('_show_more_link') == 'true' && $_smarty_tpl->getValue('_show_page_link') == 'true') || ($_smarty_tpl->getValue('_show_more_link') == '1' && $_smarty_tpl->getValue('_show_page_link') == '1')) {?>
                </a>
            <?php }?>
        </p>
    </div>

    <div class="panel-body switch-items text-center product-listing">
        <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('product_listing'), 'module_data', false, 'nr', 'aussen', array (
  'first' => true,
  'last' => true,
  'index' => true,
  'iteration' => true,
  'total' => true,
));
$foreach9DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('nr')->value => $_smarty_tpl->getVariable('module_data')->value) {
$foreach9DoElse = false;
$_smarty_tpl->tpl_vars['__smarty_foreach_aussen']->value['iteration']++;
$_smarty_tpl->tpl_vars['__smarty_foreach_aussen']->value['index']++;
$_smarty_tpl->tpl_vars['__smarty_foreach_aussen']->value['first'] = !$_smarty_tpl->tpl_vars['__smarty_foreach_aussen']->value['index'];
$_smarty_tpl->tpl_vars['__smarty_foreach_aussen']->value['last'] = $_smarty_tpl->tpl_vars['__smarty_foreach_aussen']->value['iteration'] === $_smarty_tpl->tpl_vars['__smarty_foreach_aussen']->value['total'];
?>
                <?php if (!($_smarty_tpl->getValue('__smarty_foreach_aussen')['first'] ?? null)) {?>
                    <hr class="seperator" />
                <?php }?>
        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'box_sidebar_products_product_top','module_data'=>$_smarty_tpl->getValue('module_data')), $_smarty_tpl);?>

        <div class="section">
            <div class="product product-<?php echo $_smarty_tpl->getValue('nr');
if (($_smarty_tpl->getValue('__smarty_foreach_aussen')['first'] ?? null)) {?> first-product<?php }
if (($_smarty_tpl->getValue('__smarty_foreach_aussen')['last'] ?? null)) {?> last-product<?php }
if ($_smarty_tpl->getValue('module_data')['flag_has_specials'] == 1) {?> special-price<?php }
if ($_smarty_tpl->getValue('module_data')['date_available'] != '') {?> available-soon<?php }?>">

                <div class="product-image">
                    <p class="image">
                        <?php if (!$_smarty_tpl->getValue('module_data')['products_image'] || $_smarty_tpl->getValue('module_data')['products_image'] == 'product:noimage.gif') {?>
                            <a href="<?php echo $_smarty_tpl->getValue('module_data')['products_link'];?>
" class="vertical-helper image-link no-image">
                                <i class="no-image-icon"></i>
                            </a>
                        <?php } else { ?>
                            <a href="<?php echo $_smarty_tpl->getValue('module_data')['products_link'];?>
" class="vertical-helper image-link">
                                <?php ob_start();
if ($_smarty_tpl->getValue('module_data')['products_image_data']['media_name']) {
echo htmlspecialchars((string)$_smarty_tpl->getValue('module_data')['products_image_data']['media_name'], ENT_QUOTES, 'UTF-8', true);
} else {
echo htmlspecialchars((string)$_smarty_tpl->getValue('module_data')['products_name'], ENT_QUOTES, 'UTF-8', true);
}
$_prefixVariable8=ob_get_clean();
ob_start();
if ($_smarty_tpl->getValue('module_data')['products_image_data']['copyright_holder']) {
echo " &copy; ";
echo (string)$_smarty_tpl->getValue('module_data')['products_image_data']['copyright_holder'];
}
$_prefixVariable9=ob_get_clean();
ob_start();
if ($_smarty_tpl->getValue('module_data')['products_image_data']['media_name']) {
echo htmlspecialchars((string)$_smarty_tpl->getValue('module_data')['products_image_data']['media_name'], ENT_QUOTES, 'UTF-8', true);
} else {
echo htmlspecialchars((string)$_smarty_tpl->getValue('module_data')['products_name'], ENT_QUOTES, 'UTF-8', true);
}
$_prefixVariable10=ob_get_clean();
ob_start();
if ($_smarty_tpl->getValue('module_data')['products_image_data']['copyright_holder']) {
echo " &copy; ";
echo (string)$_smarty_tpl->getValue('module_data')['products_image_data']['copyright_holder'];
}
$_prefixVariable11=ob_get_clean();
echo $_smarty_tpl->getSmarty()->getFunctionHandler('img')->handle(array('img'=>$_smarty_tpl->getValue('module_data')['products_image'],'type'=>'m_info','class'=>"productImageBorder img-responsive",'alt'=>$_prefixVariable8.$_prefixVariable9,'title'=>$_prefixVariable10.$_prefixVariable11), $_smarty_tpl);?>

                            </a>
                        <?php }?>
                    </p>
                </div>
                <p class="product-name h4 title"><a href="<?php echo $_smarty_tpl->getValue('module_data')['products_link'];?>
"><?php echo $_smarty_tpl->getValue('module_data')['products_name'];?>
</a></p>
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

                <?php $_smarty_tpl->renderSubTemplate("file:includes/product_info_label.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('position'=>$_smarty_tpl->getValue('position'),'isSpecial'=>$_smarty_tpl->getValue('module_data')['flag_has_specials'],'dateAvailable'=>$_smarty_tpl->getValue('module_data')['date_available']), (int) 0, $_smarty_current_dir);
?>
            	<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'box_sidebar_products_product_bottom','module_data'=>$_smarty_tpl->getValue('module_data')), $_smarty_tpl);?>

            </div>

        </div>
        <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
    </div>

    <?php if ($_smarty_tpl->getSmarty()->getModifierCallback('count')($_smarty_tpl->getValue('product_listing')) >= ($_smarty_tpl->getValue('visible_items')+1)) {?>
        <button class="btn btn-block btn-default switch-button panel-footer" type="button">
            <span class="more">
                <strong>+<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('math')->handle(array('equation'=>"x - y",'x'=>$_smarty_tpl->getSmarty()->getModifierCallback('count')($_smarty_tpl->getValue('product_listing')),'y'=>$_smarty_tpl->getValue('visible_items')), $_smarty_tpl);?>
</strong> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>BUTTON_SHOW), $_smarty_tpl);?>

            </span>
            <span class="less">
                <strong>-<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('math')->handle(array('equation'=>"x - y",'x'=>$_smarty_tpl->getSmarty()->getModifierCallback('count')($_smarty_tpl->getValue('product_listing')),'y'=>$_smarty_tpl->getValue('visible_items')), $_smarty_tpl);?>
</strong> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>BUTTON_SHOW), $_smarty_tpl);?>

            </span>
        </button>
    <?php }?>

</div><!-- .products-box --><?php }
}
