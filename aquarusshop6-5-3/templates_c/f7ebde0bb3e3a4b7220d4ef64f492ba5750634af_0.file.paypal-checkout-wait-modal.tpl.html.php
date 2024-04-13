<?php
/* Smarty version 4.3.2, created on 2024-03-20 20:27:52
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/plugins/xt_paypal_checkout/templates/paypal-checkout-wait-modal.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_65fb38b82910f7_02520873',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f7ebde0bb3e3a4b7220d4ef64f492ba5750634af' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/plugins/xt_paypal_checkout/templates/paypal-checkout-wait-modal.tpl.html',
      1 => 1710347938,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_65fb38b82910f7_02520873 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),));
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
                    <p><?php echo smarty_function_txt(array('key'=>"TEXT_PAYPAL_CHECKOUT_REDIRECT_INFO"),$_smarty_tpl);?>
</p>
                    <i class="fa fa-refresh fa-2x" style="-webkit-animation:spin_ppcp_spinner 1.2s linear infinite;  -moz-animation:spin_ppcp_spinner 1.2s linear infinite;  animation:spin_ppcp_spinner 1.2s linear infinite;" ></i>
                    <p style="display: none" id="ppc_oops"><?php echo smarty_function_txt(array('key'=>"TEXT_PAYPAL_CHECKOUT_REDIRECT_INFO_OOPS"),$_smarty_tpl);?>
</p>
                </div>
            </div>
        </div>
    </div>
</div>


<?php }
}
