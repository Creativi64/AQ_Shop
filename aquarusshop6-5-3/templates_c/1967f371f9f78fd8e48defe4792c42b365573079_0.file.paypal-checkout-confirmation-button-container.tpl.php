<?php
/* Smarty version 4.3.2, created on 2024-04-21 11:51:18
  from '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_paypal_checkout/templates/paypal-checkout-confirmation-button-container.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_6624e19629afb3_25228955',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '1967f371f9f78fd8e48defe4792c42b365573079' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_paypal_checkout/templates/paypal-checkout-confirmation-button-container.tpl',
      1 => 1710347938,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6624e19629afb3_25228955 (Smarty_Internal_Template $_smarty_tpl) {
?>
<div id="paypal_button_container_<?php echo $_smarty_tpl->tpl_vars['requested_position']->value;?>
" style="text-align: center; <?php if ($_SESSION['selected_payment'] == 'xt_paypal_checkout_card') {?>width:100%;<?php }?> " class="paypal_button_container pull-right paypal-button-container-confirmation"></div>
<?php }
}
