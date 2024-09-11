<?php
/* Smarty version 5.1.0, created on 2024-09-10 16:58:09
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_paypal_checkout/templates/./paypal-checkout-save-payment-method-info.tpl.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_66e05e811b2e83_23000639',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '8bced8d8f1fb437948dfaf1eb33895359fff5b56' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_paypal_checkout/templates/./paypal-checkout-save-payment-method-info.tpl.html',
      1 => 1722634751,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_66e05e811b2e83_23000639 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_paypal_checkout/templates';
?>
<div class="ppcp-modal hidden" id="ppc_save_payment_method_info_overlay" tabindex="-1" role="dialog" aria-labelledby="ppc_save_payment_method_info_overlay" aria-hidden="true">
    <div class="ppcp-modal-dialog">
        <div class="ppcp-modal-content">
            <div class="ppcp-modal-body" id="ppc_save_payment_method_info_overlay_body">
                    <p><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>"TEXT_PPCP_SAVE_PAYMENT_METHOD_INFO"), $_smarty_tpl);?>
</p>
                    <p>
                        <input type="button" class="pull-right" value="OK" style="width:100px" onclick="ppcSavePaymentMethodInfoModal(true)"/>
                    </p>
                    <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>



<?php }
}
