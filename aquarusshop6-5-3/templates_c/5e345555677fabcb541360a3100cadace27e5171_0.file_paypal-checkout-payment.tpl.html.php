<?php
/* Smarty version 5.4.1, created on 2024-12-03 17:04:11
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_paypal_checkout/templates/paypal-checkout-payment.tpl.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.1',
  'unifunc' => 'content_674f2bfbf246c8_81497023',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5e345555677fabcb541360a3100cadace27e5171' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_paypal_checkout/templates/paypal-checkout-payment.tpl.html',
      1 => 1733161754,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_674f2bfbf246c8_81497023 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_paypal_checkout/templates';
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
