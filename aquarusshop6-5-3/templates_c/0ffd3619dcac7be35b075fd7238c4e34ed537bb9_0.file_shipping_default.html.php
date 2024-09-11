<?php
/* Smarty version 5.1.0, created on 2024-09-10 16:57:44
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/shipping/shipping_default.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_66e05e6835f763_33133297',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '0ffd3619dcac7be35b075fd7238c4e34ed537bb9' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/shipping/shipping_default.html',
      1 => 1697144246,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_66e05e6835f763_33133297 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/shipping';
if ($_smarty_tpl->getSmarty()->getModifierCallback('trim')($_smarty_tpl->getValue('shipping_name')) != '' && $_smarty_tpl->getSmarty()->getModifierCallback('trim')($_smarty_tpl->getValue('shipping_code')) != '') {?>
    <div id="shipping_<?php echo $_smarty_tpl->getValue('shipping_id');?>
" class="item item-<?php echo $_smarty_tpl->getValue('shipping_code');
if ($_smarty_tpl->getValue('shipping_code') == $_smarty_tpl->getValue('shipping_selected')) {?> selected<?php }?>"<?php if ($_smarty_tpl->getSmarty()->getModifierCallback('trim')($_smarty_tpl->getValue('shipping_desc')) != '') {?> data-toggle="collapse" data-target=".item-<?php echo $_smarty_tpl->getValue('shipping_code');?>
 .collapse"<?php }?>>
        <?php if ($_smarty_tpl->getSmarty()->getModifierCallback('trim')($_smarty_tpl->getValue('shipping_icon')) != '') {?>
            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('img')->handle(array('img'=>$_smarty_tpl->getValue('shipping_icon'),'type'=>'w_media_shipping','alt'=>$_smarty_tpl->getValue('shipping_name'),'class'=>"icon img-responsive img-thumbnail pull-right"), $_smarty_tpl);?>

        <?php }?>
        <header>
            <label class="cursor-pointer">
                <span class="check" style="display:inline-block;width: 25px;">
                    <?php if ($_smarty_tpl->getValue('shipping_hidden') == true) {?>
                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'hidden','name'=>'selected_shipping','value'=>$_smarty_tpl->getValue('shipping_code')), $_smarty_tpl);?>

                    <?php } else { ?>
                        <?php if ($_smarty_tpl->getValue('shipping_code') == $_smarty_tpl->getValue('shipping_selected')) {?>
                            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'radio','name'=>'selected_shipping','value'=>$_smarty_tpl->getValue('shipping_code'),'checked'=>true), $_smarty_tpl);?>

                        <?php } else { ?>
                            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'radio','name'=>'selected_shipping','value'=>$_smarty_tpl->getValue('shipping_code')), $_smarty_tpl);?>

                        <?php }?>
                    <?php }?>
                </span>
                <span class="name"><?php echo $_smarty_tpl->getValue('shipping_name');?>
</span>
                <?php if ($_smarty_tpl->getValue('shipping_price')['formated']) {?>
                    <small class="price">&nbsp;<?php echo $_smarty_tpl->getValue('shipping_price')['formated'];?>
</small>
                <?php }?>
            </label>
        </header>
        <?php if ($_smarty_tpl->getSmarty()->getModifierCallback('trim')($_smarty_tpl->getValue('shipping_desc')) != '') {?>
            <div class="desc collapse<?php if ($_smarty_tpl->getValue('shipping_code') == $_smarty_tpl->getValue('shipping_selected')) {?> in<?php }?>">
                <?php echo $_smarty_tpl->getValue('shipping_desc');?>

                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'shipping_default_shipping_desc'), $_smarty_tpl);?>

            </div>
        <?php }?>
    </div>
<?php }
}
}
