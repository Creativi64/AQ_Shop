<?php
/* Smarty version 5.1.0, created on 2024-09-10 16:57:44
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/payment/payment_default.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_66e05e686ca054_13753183',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '6edd5aaa468806c4ea7bc021914411f4701005ba' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/payment/payment_default.html',
      1 => 1697144245,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_66e05e686ca054_13753183 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/payment';
if ($_smarty_tpl->getSmarty()->getModifierCallback('trim')($_smarty_tpl->getValue('payment_name')) != '' && $_smarty_tpl->getSmarty()->getModifierCallback('trim')($_smarty_tpl->getValue('payment_code')) != '') {?>
    <div class="item item-<?php echo $_smarty_tpl->getValue('payment_code');
if ($_smarty_tpl->getValue('payment_code') == $_smarty_tpl->getValue('payment_selected')) {?> selected<?php }?> payment-container">
        <?php if ($_smarty_tpl->getSmarty()->getModifierCallback('trim')($_smarty_tpl->getValue('payment_icon')) != '') {?>
            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('img')->handle(array('img'=>$_smarty_tpl->getValue('payment_icon'),'type'=>'w_media_payment','alt'=>$_smarty_tpl->getValue('payment_name'),'class'=>"icon img-responsive img-thumbnail pull-right",'style'=>"margin-top:-15px;"), $_smarty_tpl);?>

        <?php }?>
    <header data-toggle="collapse" data-target=".item-<?php echo $_smarty_tpl->getValue('payment_code');?>
 .collapse">
            <label class="cursor-pointer">
                <span class="check" style="display:inline-block;width: 25px;">
                    <?php if ($_smarty_tpl->getValue('payment_hidden') == true) {?>
                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'hidden','name'=>'selected_payment','value'=>$_smarty_tpl->getValue('payment_code')), $_smarty_tpl);?>

                    <?php } else { ?>
                        <?php if ($_smarty_tpl->getValue('payment_code') == $_smarty_tpl->getValue('payment_selected')) {?>
                            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'radio','name'=>'selected_payment','value'=>$_smarty_tpl->getValue('payment_code'),'checked'=>true,'style'=>"vertical-align: text-top;"), $_smarty_tpl);?>

                        <?php } else { ?>
                            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'radio','name'=>'selected_payment','value'=>$_smarty_tpl->getValue('payment_code'),'style'=>"vertical-align: text-top;"), $_smarty_tpl);?>

                        <?php }?>
                    <?php }?>
                </span>
                <span class="name payment-name"><?php echo $_smarty_tpl->getValue('payment_name');?>
</span>
                <?php if ($_smarty_tpl->getValue('payment_price')['formated']) {?>
                    <small class="price">&nbsp;<?php echo $_smarty_tpl->getValue('payment_price')['formated'];?>
</small>
                <?php }?>
            </label>
        </header>
        <?php if ($_smarty_tpl->getSmarty()->getModifierCallback('trim')($_smarty_tpl->getValue('payment_desc')) != '') {?>
            <div class="desc collapse<?php if ($_smarty_tpl->getValue('payment_code') == $_smarty_tpl->getValue('payment_selected')) {?> in<?php }?> payment-desc">
                <?php echo $_smarty_tpl->getValue('payment_desc');?>

            </div>
        <?php }?>
    </div>
<?php }
}
}
