<?php
/* Smarty version 4.3.2, created on 2024-07-17 20:12:10
  from '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/default.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_6698097a6341c4_58992946',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '52a739a4be6eeef5dc2cf414ee73e9dfa4cc4eec' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/default.html',
      1 => 1697144119,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6698097a6341c4_58992946 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.box.php','function'=>'smarty_function_box',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.page.php','function'=>'smarty_function_page',),));
if ($_smarty_tpl->tpl_vars['content_status']->value == 1) {
echo smarty_function_box(array('name'=>'teaser','sliderid'=>1),$_smarty_tpl);?>

<?php echo smarty_function_box(array('name'=>'startpage_categories'),$_smarty_tpl);?>

<?php echo smarty_function_page(array('name'=>'xt_startpage_products','type'=>'user','tpl_type'=>'product_listing'),$_smarty_tpl);?>

    <div id="startpage-content" class="text-word-wrap">
        <div class="row">
            <?php if ($_smarty_tpl->tpl_vars['content_body']->value && trim($_smarty_tpl->tpl_vars['content_body']->value) != '') {?>
                <div class="col <?php if ($_smarty_tpl->tpl_vars['content_body_short']->value && trim($_smarty_tpl->tpl_vars['content_body_short']->value) != '') {?>col-sm-8<?php } else { ?>col-sm-12<?php }?>">
                    <?php if ($_smarty_tpl->tpl_vars['content_heading']->value && trim($_smarty_tpl->tpl_vars['content_heading']->value) != '') {?>
                        <h1 class="text-uppercase"><?php echo $_smarty_tpl->tpl_vars['content_heading']->value;?>
</h1>
                    <?php }?>
                    <div class="textstyles">
                        <?php echo $_smarty_tpl->tpl_vars['content_body']->value;?>

                        <div class="clearfix"></div>
                    </div>
                </div>
            <?php }?>
            <?php if ($_smarty_tpl->tpl_vars['content_body_short']->value && trim($_smarty_tpl->tpl_vars['content_body_short']->value) != '') {?>
                <div class="col <?php if ($_smarty_tpl->tpl_vars['content_body']->value && trim($_smarty_tpl->tpl_vars['content_body']->value) != '') {?>col-sm-4<?php } else { ?>col-sm-12<?php }?>">
                    <div class="textstyles">
                        <?php echo $_smarty_tpl->tpl_vars['content_body_short']->value;?>

                        <div class="clearfix"></div>
                    </div>
                </div>
            <?php }?>
        </div>
    </div>
<?php echo smarty_function_page(array('name'=>'xt_new_products','type'=>'user','limit'=>7,'tpl'=>"product_listing_slider.html"),$_smarty_tpl);?>

<?php echo smarty_function_page(array('name'=>'xt_live_shopping','type'=>'user','order_by'=>'rand','limit'=>8,'nopaging'=>true,'tpl'=>"product_listing_slider.html"),$_smarty_tpl);?>

<?php echo smarty_function_page(array('name'=>'xt_upcoming_products','type'=>'user','limit'=>7,'order_by'=>'p.date_available','tpl'=>"product_listing_slider.html"),$_smarty_tpl);?>

<?php echo smarty_function_page(array('name'=>'xt_bestseller_products','type'=>'user','limit'=>7,'tpl'=>"product_listing_slider.html"),$_smarty_tpl);?>

<?php echo smarty_function_page(array('name'=>'xt_special_products','type'=>'user','limit'=>7,'tpl'=>"product_listing_slider.html"),$_smarty_tpl);?>

<?php }
}
}
