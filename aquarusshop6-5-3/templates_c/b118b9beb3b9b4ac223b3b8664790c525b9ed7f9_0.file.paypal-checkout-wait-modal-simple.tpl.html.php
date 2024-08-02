<?php
/* Smarty version 4.3.2, created on 2024-07-22 18:41:22
  from '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_paypal_checkout/templates/paypal-checkout-wait-modal-simple.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_669e8bb2df99b5_94108014',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'b118b9beb3b9b4ac223b3b8664790c525b9ed7f9' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_paypal_checkout/templates/paypal-checkout-wait-modal-simple.tpl.html',
      1 => 1721239475,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_669e8bb2df99b5_94108014 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),));
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
                    <p style="display: none" id="ppc_oops"><?php echo smarty_function_txt(array('key'=>"TEXT_PAYPAL_CHECKOUT_REDIRECT_INFO_OOPS"),$_smarty_tpl);?>
</p>
                </div>
            </div>
        </div>
    </div>
</div>


<?php }
}
