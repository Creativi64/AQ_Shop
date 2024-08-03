<?php
/* Smarty version 5.1.0, created on 2024-08-03 01:15:14
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/default.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_66ad68825ea381_53392493',
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
))) {
function content_66ad68825ea381_53392493 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages';
if ($_smarty_tpl->getValue('content_status') == 1) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('box')->handle(array('name'=>'teaser','sliderid'=>1), $_smarty_tpl);?>

<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('box')->handle(array('name'=>'startpage_categories'), $_smarty_tpl);?>

<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('page')->handle(array('name'=>'xt_startpage_products','type'=>'user','tpl_type'=>'product_listing'), $_smarty_tpl);?>

    <div id="startpage-content" class="text-word-wrap">
        <div class="row">
            <?php if ($_smarty_tpl->getValue('content_body') && $_smarty_tpl->getSmarty()->getModifierCallback('trim')($_smarty_tpl->getValue('content_body')) != '') {?>
                <div class="col <?php if ($_smarty_tpl->getValue('content_body_short') && $_smarty_tpl->getSmarty()->getModifierCallback('trim')($_smarty_tpl->getValue('content_body_short')) != '') {?>col-sm-8<?php } else { ?>col-sm-12<?php }?>">
                    <?php if ($_smarty_tpl->getValue('content_heading') && $_smarty_tpl->getSmarty()->getModifierCallback('trim')($_smarty_tpl->getValue('content_heading')) != '') {?>
                        <h1 class="text-uppercase"><?php echo $_smarty_tpl->getValue('content_heading');?>
</h1>
                    <?php }?>
                    <div class="textstyles">
                        <?php echo $_smarty_tpl->getValue('content_body');?>

                        <div class="clearfix"></div>
                    </div>
                </div>
            <?php }?>
            <?php if ($_smarty_tpl->getValue('content_body_short') && $_smarty_tpl->getSmarty()->getModifierCallback('trim')($_smarty_tpl->getValue('content_body_short')) != '') {?>
                <div class="col <?php if ($_smarty_tpl->getValue('content_body') && $_smarty_tpl->getSmarty()->getModifierCallback('trim')($_smarty_tpl->getValue('content_body')) != '') {?>col-sm-4<?php } else { ?>col-sm-12<?php }?>">
                    <div class="textstyles">
                        <?php echo $_smarty_tpl->getValue('content_body_short');?>

                        <div class="clearfix"></div>
                    </div>
                </div>
            <?php }?>
        </div>
    </div>
<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('page')->handle(array('name'=>'xt_new_products','type'=>'user','limit'=>7,'tpl'=>"product_listing_slider.html"), $_smarty_tpl);?>

<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('page')->handle(array('name'=>'xt_live_shopping','type'=>'user','order_by'=>'rand','limit'=>8,'nopaging'=>true,'tpl'=>"product_listing_slider.html"), $_smarty_tpl);?>

<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('page')->handle(array('name'=>'xt_upcoming_products','type'=>'user','limit'=>7,'order_by'=>'p.date_available','tpl'=>"product_listing_slider.html"), $_smarty_tpl);?>

<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('page')->handle(array('name'=>'xt_bestseller_products','type'=>'user','limit'=>7,'tpl'=>"product_listing_slider.html"), $_smarty_tpl);?>

<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('page')->handle(array('name'=>'xt_special_products','type'=>'user','limit'=>7,'tpl'=>"product_listing_slider.html"), $_smarty_tpl);?>

<?php }
}
}
