<?php
/* Smarty version 5.1.0, created on 2024-09-10 16:57:44
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_paypal_checkout/templates/./paypal-checkout-wait-modal-simple.tpl.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_66e05e6867e306_55077123',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '122676a0ae04f883a2a6ef8b87eb3de4eea3e092' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_paypal_checkout/templates/./paypal-checkout-wait-modal-simple.tpl.html',
      1 => 1722634751,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_66e05e6867e306_55077123 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_paypal_checkout/templates';
?>
<div class="ppcp-modal hidden" id="ppc_wait_overlay" tabindex="-1" role="dialog" aria-labelledby="ppc_wait_overlay" aria-hidden="true">
    <div class="ppcp-modal-dialog">
        <div class="ppcp-modal-content">
            <div class="ppcp-modal-body" id="ppc_wait_overlay_body">
                <div style="text-align: center; margin-bottom:10px">
                    <img class="img-responsive" src="media/logo/<?php echo (defined('_STORE_LOGO') ? constant('_STORE_LOGO') : null);?>
" alt="<?php echo (defined('_STORE_NAME') ? constant('_STORE_NAME') : null);?>
" style="display: unset;"/>
                </div>
                <div id="ppc_wait_overlay_step2" style="text-align: center; font-size:1.1em; padding:15px 0">
                    <i class="fa fa-refresh fa-2x" style="-webkit-animation:spin_ppcp_spinner 1.2s linear infinite;  -moz-animation:spin_ppcp_spinner 1.2s linear infinite;  animation:spin_ppcp_spinner 1.2s linear infinite;" ></i>
                    <p style="display: none" id="ppc_oops"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>"TEXT_PAYPAL_CHECKOUT_REDIRECT_INFO_OOPS"), $_smarty_tpl);?>
</p>
                </div>
            </div>
        </div>
    </div>
</div>


<?php }
}
