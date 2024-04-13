<?php
/* Smarty version 4.3.2, created on 2024-03-20 20:27:52
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/plugins/xt_paypal_checkout/templates/paypal-checkout-confirmation-button-container.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_65fb38b816b490_13705825',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '34c79a45067fb4610fcdf3af88ce8100c87fb041' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/plugins/xt_paypal_checkout/templates/paypal-checkout-confirmation-button-container.tpl',
      1 => 1710347938,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_65fb38b816b490_13705825 (Smarty_Internal_Template $_smarty_tpl) {
?>
<div id="paypal_button_container_<?php echo $_smarty_tpl->tpl_vars['requested_position']->value;?>
" style="text-align: center; <?php if ($_SESSION['selected_payment'] == 'xt_paypal_checkout_card') {?>width:100%;<?php }?> " class="paypal_button_container pull-right paypal-button-container-confirmation"></div>
<?php }
}
