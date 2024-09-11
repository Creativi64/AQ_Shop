<?php
/* Smarty version 5.1.0, created on 2024-09-10 16:57:44
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/checkout.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_66e05e6886ab61_13798744',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f39bc5941a71a0012039c50fa34e15649299e16c' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/checkout.html',
      1 => 1697144119,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:xtCore/pages/checkout/subpage_shipping.html' => 1,
    'file:xtCore/pages/checkout/subpage_payment.html' => 1,
    'file:xtCore/pages/checkout/subpage_confirmation.html' => 1,
    'file:xtCore/pages/checkout/subpage_pay.html' => 1,
    'file:xtCore/pages/checkout/subpage_success.html' => 1,
  ),
))) {
function content_66e05e6886ab61_13798744 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages';
?><div id="checkout">
    <div class="progress checkout-progress">
        <div class="<?php if ($_smarty_tpl->getValue('page_action') == 'payment' || $_smarty_tpl->getValue('page_action') == 'confirmation') {?>cursor-pointer<?php } elseif ($_smarty_tpl->getValue('page_action') != 'success') {?>cursor-help<?php }?> progress-bar <?php if ($_smarty_tpl->getValue('page_action') == 'shipping') {?>progress-bar-success progress-bar-striped active<?php }?> <?php if ($_smarty_tpl->getValue('page_action') == 'payment' || $_smarty_tpl->getValue('page_action') == 'confirmation' || $_smarty_tpl->getValue('page_action') == 'success') {?>progress-bar-success done<?php }?>"
             style="width: 25%"
             title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SELECT_SHIPPING_DESC), $_smarty_tpl);?>
"
             onclick="<?php if ($_smarty_tpl->getValue('page_action') == 'payment' || $_smarty_tpl->getValue('page_action') == 'confirmation') {?>document.location.href='<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'checkout','paction'=>'shipping','conn'=>'SSL'), $_smarty_tpl);?>
'<?php }?>">
            <span class="hidden-xs"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SELECT_SHIPPING), $_smarty_tpl);
if ($_smarty_tpl->getValue('page_action') == 'success') {?>&nbsp;<i class="fa fa-check" style="font-size: 12px;"></i><?php }?></span>
            <span class="visible-xs"><i class="fa fa-truck"></i><?php if ($_smarty_tpl->getValue('page_action') == 'success') {?>&nbsp;<i class="fa fa-check" style="font-size: 12px;"></i><?php }?></span>
        </div>
        <div class="<?php if ($_smarty_tpl->getValue('page_action') == 'confirmation') {?>cursor-pointer<?php } elseif ($_smarty_tpl->getValue('page_action') != 'success') {?>cursor-help<?php }?> progress-bar <?php if ($_smarty_tpl->getValue('page_action') == 'payment') {?>progress-bar-success progress-bar-striped active<?php } else { ?>progress-bar-default<?php }?> <?php if ($_smarty_tpl->getValue('page_action') == 'confirmation' || $_smarty_tpl->getValue('page_action') == 'success') {?>progress-bar-success done<?php }?>"
             style="width: 25%"
             title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SELECT_PAYMENT_DESC), $_smarty_tpl);?>
"
             onclick="<?php if ($_smarty_tpl->getValue('page_action') == 'confirmation') {?>document.location.href='<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'checkout','paction'=>'payment','conn'=>'SSL'), $_smarty_tpl);?>
'<?php }?>">
            <span class="hidden-xs"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SELECT_PAYMENT), $_smarty_tpl);
if ($_smarty_tpl->getValue('page_action') == 'success') {?>&nbsp;<i class="fa fa-check" style="font-size: 12px;"></i><?php }?></span>
            <span class="visible-xs"><i class="fa fa-credit-card"></i><?php if ($_smarty_tpl->getValue('page_action') == 'success') {?>&nbsp;<i class="fa fa-check" style="font-size: 12px;"></i><?php }?></span>
        </div>
        <div class="<?php if ($_smarty_tpl->getValue('page_action') != 'success') {?>cursor-help<?php }?> progress-bar <?php if ($_smarty_tpl->getValue('page_action') == 'confirmation') {?>progress-bar-success progress-bar-striped active<?php } else { ?>progress-bar-default<?php }?> <?php if ($_smarty_tpl->getValue('page_action') == 'success') {?>progress-bar-success done<?php }?>"
             style="width: 25%"
             title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_CONFIRMATION_DESC), $_smarty_tpl);?>
">
            <span class="hidden-xs"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_CONFIRMATION), $_smarty_tpl);
if ($_smarty_tpl->getValue('page_action') == 'success') {?>&nbsp;<i class="fa fa-check" style="font-size: 12px;"></i><?php }?></span>
            <span class="visible-xs"><i class="fa fa-handshake-o" aria-hidden="true"></i>&nbsp;<?php if ($_smarty_tpl->getValue('page_action') == 'success') {?><i class="fa fa-check" style="font-size: 12px;"></i><?php }?></span>
        </div>
        <div class="progress-bar <?php if ($_smarty_tpl->getValue('page_action') == 'success') {?>progress-bar-success active<?php } else { ?>progress-bar-default<?php }?> <?php if ($_smarty_tpl->getValue('page_action') == 'success') {?>progress-bar-success done<?php }?>"
             style="width: 25%"
             title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SUCCESS_DESC), $_smarty_tpl);?>
">
            <span <?php if ($_smarty_tpl->getValue('page_action') == 'success') {?>class="bold"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SUCCESS), $_smarty_tpl);?>
</span>

        </div>
    </div>

    <?php echo $_smarty_tpl->getValue('message');?>


    <?php if ($_smarty_tpl->getValue('page_action') == 'shipping' || $_smarty_tpl->getValue('page_action') == '') {?>
    <?php $_smarty_tpl->renderSubTemplate("file:xtCore/pages/checkout/subpage_shipping.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('type'=>"tpl_include"), (int) 0, $_smarty_current_dir);
?>
    <?php }?>

    <?php if ($_smarty_tpl->getValue('page_action') == 'payment') {?>
    <?php $_smarty_tpl->renderSubTemplate("file:xtCore/pages/checkout/subpage_payment.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('type'=>"tpl_include"), (int) 0, $_smarty_current_dir);
?>
    <?php }?>

    <?php if ($_smarty_tpl->getValue('page_action') == 'confirmation') {?>
    <?php $_smarty_tpl->renderSubTemplate("file:xtCore/pages/checkout/subpage_confirmation.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('type'=>"tpl_include"), (int) 0, $_smarty_current_dir);
?>
    <?php }?>

    <?php if ($_smarty_tpl->getValue('page_action') == 'pay') {?>
    <?php $_smarty_tpl->renderSubTemplate("file:xtCore/pages/checkout/subpage_pay.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('type'=>"tpl_include"), (int) 0, $_smarty_current_dir);
?>
    <?php }?>

    <?php if ($_smarty_tpl->getValue('page_action') == 'success') {?>
    <?php $_smarty_tpl->renderSubTemplate("file:xtCore/pages/checkout/subpage_success.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('type'=>"tpl_include"), (int) 0, $_smarty_current_dir);
?>
    <?php }?>
</div>
<?php }
}
