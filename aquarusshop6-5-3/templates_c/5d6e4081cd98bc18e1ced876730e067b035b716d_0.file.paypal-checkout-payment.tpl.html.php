<?php
/* Smarty version 4.3.2, created on 2024-03-20 20:16:35
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/plugins/xt_paypal_checkout/templates/paypal-checkout-payment.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_65fb3613be8102_16012113',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5d6e4081cd98bc18e1ced876730e067b035b716d' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/plugins/xt_paypal_checkout/templates/paypal-checkout-payment.tpl.html',
      1 => 1710347938,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_65fb3613be8102_16012113 (Smarty_Internal_Template $_smarty_tpl) {
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
