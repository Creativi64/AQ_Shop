<?php
/* Smarty version 5.4.1, created on 2024-12-03 17:04:25
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_paypal_checkout/templates/paypal-checkout-confirmation-button-container.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.1',
  'unifunc' => 'content_674f2c091b8911_27435559',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '1967f371f9f78fd8e48defe4792c42b365573079' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_paypal_checkout/templates/paypal-checkout-confirmation-button-container.tpl',
      1 => 1733161754,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:./paypal-checkout-save-payment-method-info.tpl.html' => 1,
  ),
))) {
function content_674f2c091b8911_27435559 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_paypal_checkout/templates';
?><div id="paypal_button_container_<?php echo $_smarty_tpl->getValue('requested_position');?>
_wrapper">
    <div id="paypal_button_container_<?php echo $_smarty_tpl->getValue('requested_position');?>
" style="/*text-align: center;*/ <?php if ($_SESSION['selected_payment'] == 'xt_paypal_checkout_card') {?>width:100%;<?php }?> " class="paypal_button_container pull-right paypal-button-container-confirmation"></div>

</div>
<div class="clearfix"></div>
<?php if ($_SESSION['selected_payment'] == 'xt_paypal_checkout_paypal' && $_SESSION['PAYPAL_WALLET_VAULTING_ADVANCED_available']) {?>
<div class="pull-right">
        <input type="checkbox" id="ppcp_agree_save_payment_method" name="ppcp_agree_save_payment_method">
        <label for="ppcp_agree_save_payment_method"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>"TEXT_PPCP_AGREE_SAVE_PAYMENT_METHOD"), $_smarty_tpl);?>
&nbsp;&nbsp;</label><i class="fa fa-question-circle" aria-hidden="true" onclick="ppcSavePaymentMethodInfoModal()"></i>
</div>
<?php }?>

<?php if ($_SESSION['selected_payment'] != 'xt_paypal_checkout_card_vaulted') {
$_smarty_tpl->renderSubTemplate("file:./paypal-checkout-save-payment-method-info.tpl.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
}
}
}
