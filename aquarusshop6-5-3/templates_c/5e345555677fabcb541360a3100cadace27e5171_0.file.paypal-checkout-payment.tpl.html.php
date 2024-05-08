<?php
/* Smarty version 4.3.2, created on 2024-04-21 11:51:09
  from '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_paypal_checkout/templates/paypal-checkout-payment.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_6624e18d2307b4_69446084',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5e345555677fabcb541360a3100cadace27e5171' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_paypal_checkout/templates/paypal-checkout-payment.tpl.html',
      1 => 1710347938,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6624e18d2307b4_69446084 (Smarty_Internal_Template $_smarty_tpl) {
?>
<style>

    #checkout li[class*=list-group-item-xt_paypal_checkout],
    #checkout li[class*=list-group-item-xt_paypal_checkout].list-group-item.active:hover,
    #checkout li[class*=list-group-item-xt_paypal_checkout].list-group-item.active:focus
    {
        background-color: inherit;
        color: inherit;
    }
    #checkout li[class*=list-group-item-xt_paypal_checkout].list-group-item.active .price-tag
    {
        color: inherit;
    }

    [id^=xt_paypal_checkout] .desc
    {
        padding-left: 25px;
        padding-right: 25px;
    }

</style>


<?php echo '<script'; ?>
>

document.addEventListener('PayPalSdkLoaded', function ()
{
    console.log("paypal-checkout-payment.tpl.html");
    enableFoundingSources();
    //renderAllEligibleButtons();
});
<?php echo '</script'; ?>
><?php }
}
