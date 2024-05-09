<?php
/* Smarty version 4.3.2, created on 2024-05-08 20:07:28
  from '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/includes/box_sidebar_products.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_663bbf60d1bf77_94139581',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '78eae9edb241ef87b7e855c3068dd1ccefc7c931' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/includes/box_sidebar_products.html',
      1 => 1697144063,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:includes/product_info_label.html' => 1,
  ),
),false)) {
function content_663bbf60d1bf77_94139581 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.link.php','function'=>'smarty_function_link',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.hook.php','function'=>'smarty_function_hook',),2=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.img.php','function'=>'smarty_function_img',),3=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),4=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/vendor/smarty/smarty/libs/plugins/function.math.php','function'=>'smarty_function_math',),5=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/vendor/smarty/smarty/libs/plugins/modifier.count.php','function'=>'smarty_modifier_count',),));
$_smarty_tpl->_assignInScope('visible_items', "1");?>

<div id="box_<?php echo $_smarty_tpl->tpl_vars['code']->value;?>
" data-visible-items="<?php echo $_smarty_tpl->tpl_vars['visible_items']->value;?>
" class="products-box listing no-image-height-helper equalize-nothing panel panel-<?php if ($_smarty_tpl->tpl_vars['code']->value == 'xt_special_products') {?>secondary<?php } else { ?>default<?php }?> switch-area<?php if ($_smarty_tpl->tpl_vars['classes']->value) {?> <?php echo $_smarty_tpl->tpl_vars['classes']->value;
}?>">

    <div class="panel-heading">
        <p class="panel-title text-uppercase">
            <?php if (($_smarty_tpl->tpl_vars['_show_more_link']->value == 'true' && $_smarty_tpl->tpl_vars['_show_page_link']->value == 'true') || ($_smarty_tpl->tpl_vars['_show_more_link']->value == '1' && $_smarty_tpl->tpl_vars['_show_page_link']->value == '1')) {?>
                <a href="<?php echo smarty_function_link(array('page'=>$_smarty_tpl->tpl_vars['code']->value,'conn'=>'SSL'),$_smarty_tpl);?>
">
            <?php }?>
            <?php echo $_smarty_tpl->tpl_vars['heading_text']->value;?>

            <?php if (($_smarty_tpl->tpl_vars['_show_more_link']->value == 'true' && $_smarty_tpl->tpl_vars['_show_page_link']->value == 'true') || ($_smarty_tpl->tpl_vars['_show_more_link']->value == '1' && $_smarty_tpl->tpl_vars['_show_page_link']->value == '1')) {?>
                </a>
            <?php }?>
        </p>
    </div>

    <div class="panel-body switch-items text-center product-listing">
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['product_listing']->value, 'module_data', false, 'nr', 'aussen', array (
  'first' => true,
  'last' => true,
  'index' => true,
  'iteration' => true,
  'total' => true,
));
$_smarty_tpl->tpl_vars['module_data']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['nr']->value => $_smarty_tpl->tpl_vars['module_data']->value) {
$_smarty_tpl->tpl_vars['module_data']->do_else = false;
$_smarty_tpl->tpl_vars['__smarty_foreach_aussen']->value['iteration']++;
$_smarty_tpl->tpl_vars['__smarty_foreach_aussen']->value['index']++;
$_smarty_tpl->tpl_vars['__smarty_foreach_aussen']->value['first'] = !$_smarty_tpl->tpl_vars['__smarty_foreach_aussen']->value['index'];
$_smarty_tpl->tpl_vars['__smarty_foreach_aussen']->value['last'] = $_smarty_tpl->tpl_vars['__smarty_foreach_aussen']->value['iteration'] === $_smarty_tpl->tpl_vars['__smarty_foreach_aussen']->value['total'];
?>
                <?php if (!(isset($_smarty_tpl->tpl_vars['__smarty_foreach_aussen']->value['first']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_aussen']->value['first'] : null)) {?>
                    <hr class="seperator" />
                <?php }?>
        <?php echo smarty_function_hook(array('key'=>'box_sidebar_products_product_top','module_data'=>$_smarty_tpl->tpl_vars['module_data']->value),$_smarty_tpl);?>

        <div class="section">
            <div class="product product-<?php echo $_smarty_tpl->tpl_vars['nr']->value;
if ((isset($_smarty_tpl->tpl_vars['__smarty_foreach_aussen']->value['first']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_aussen']->value['first'] : null)) {?> first-product<?php }
if ((isset($_smarty_tpl->tpl_vars['__smarty_foreach_aussen']->value['last']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_aussen']->value['last'] : null)) {?> last-product<?php }
if ($_smarty_tpl->tpl_vars['module_data']->value['flag_has_specials'] == 1) {?> special-price<?php }
if ($_smarty_tpl->tpl_vars['module_data']->value['date_available'] != '') {?> available-soon<?php }?>">

                <div class="product-image">
                    <p class="image">
                        <?php if (!$_smarty_tpl->tpl_vars['module_data']->value['products_image'] || $_smarty_tpl->tpl_vars['module_data']->value['products_image'] == 'product:noimage.gif') {?>
                            <a href="<?php echo $_smarty_tpl->tpl_vars['module_data']->value['products_link'];?>
" class="vertical-helper image-link no-image">
                                <i class="no-image-icon"></i>
                            </a>
                        <?php } else { ?>
                            <a href="<?php echo $_smarty_tpl->tpl_vars['module_data']->value['products_link'];?>
" class="vertical-helper image-link">
                                <?php ob_start();
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
ob_start();
if ($_smarty_tpl->tpl_vars['module_data']->value['products_image_data']['media_name']) {
echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['module_data']->value['products_image_data']['media_name'], ENT_QUOTES, 'UTF-8', true);
} else {
echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['module_data']->value['products_name'], ENT_QUOTES, 'UTF-8', true);
}
$_prefixVariable5=ob_get_clean();
ob_start();
if ($_smarty_tpl->tpl_vars['module_data']->value['products_image_data']['copyright_holder']) {
echo " &copy; ";
echo (string)$_smarty_tpl->tpl_vars['module_data']->value['products_image_data']['copyright_holder'];
}
$_prefixVariable6=ob_get_clean();
echo smarty_function_img(array('img'=>$_smarty_tpl->tpl_vars['module_data']->value['products_image'],'type'=>'m_info','class'=>"productImageBorder img-responsive",'alt'=>$_prefixVariable3.$_prefixVariable4,'title'=>$_prefixVariable5.$_prefixVariable6),$_smarty_tpl);?>

                            </a>
                        <?php }?>
                    </p>
                </div>
                <p class="product-name h4 title"><a href="<?php echo $_smarty_tpl->tpl_vars['module_data']->value['products_link'];?>
"><?php echo $_smarty_tpl->tpl_vars['module_data']->value['products_name'];?>
</a></p>
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

                <?php $_smarty_tpl->_subTemplateRender("file:includes/product_info_label.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('position'=>$_smarty_tpl->tpl_vars['position']->value,'isSpecial'=>$_smarty_tpl->tpl_vars['module_data']->value['flag_has_specials'],'dateAvailable'=>$_smarty_tpl->tpl_vars['module_data']->value['date_available']), 0, true);
?>
            	<?php echo smarty_function_hook(array('key'=>'box_sidebar_products_product_bottom','module_data'=>$_smarty_tpl->tpl_vars['module_data']->value),$_smarty_tpl);?>

            </div>

        </div>
        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    </div>

    <?php if (smarty_modifier_count($_smarty_tpl->tpl_vars['product_listing']->value) >= ($_smarty_tpl->tpl_vars['visible_items']->value+1)) {?>
        <button class="btn btn-block btn-default switch-button panel-footer" type="button">
            <span class="more">
                <strong>+<?php echo smarty_function_math(array('equation'=>"x - y",'x'=>smarty_modifier_count($_smarty_tpl->tpl_vars['product_listing']->value),'y'=>$_smarty_tpl->tpl_vars['visible_items']->value),$_smarty_tpl);?>
</strong> <?php echo smarty_function_txt(array('key'=>BUTTON_SHOW),$_smarty_tpl);?>

            </span>
            <span class="less">
                <strong>-<?php echo smarty_function_math(array('equation'=>"x - y",'x'=>smarty_modifier_count($_smarty_tpl->tpl_vars['product_listing']->value),'y'=>$_smarty_tpl->tpl_vars['visible_items']->value),$_smarty_tpl);?>
</strong> <?php echo smarty_function_txt(array('key'=>BUTTON_SHOW),$_smarty_tpl);?>

            </span>
        </button>
    <?php }?>

</div><!-- .products-box --><?php }
}
