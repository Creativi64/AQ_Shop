<!-- TOOL -->
<!-- :START: plugins/tfm_amazon_payments_v2/templates/tfm_amazon_payments_test.html.tpl -->
<script src="https://static-eu.payments-amazon.com/checkout.js"></script>
<div class="" id="AmazonPayButton"></div>
<div class="" id="log-out"><button id="logoffbutton">LOGOUT</button></div>
{literal}
<script type="text/javascript">
    //******************************************************************************************************
    amazon.Pay.renderButton('#AmazonPayButton', {
        merchantId: '{/literal}{$merchantId}{literal}',
        createCheckoutSession: {
            url: '{/literal}{link page='tfm_amazon_payments_v2_checkot_session_ajax' conn='SSL'}{literal}',
            method: 'POST',
        },
        ledgerCurrency: 'EUR',
        checkoutLanguage: 'de_DE',
        productType: 'PayAndShip',
        placement: 'Cart',
        sandbox: true,
        buttonColor: 'Gold'
    });

    document.getElementById('logoffbutton').onclick = function() {
        amazon.Pay.signout();
        window.location.reload();
    };
    //******************************************************************************************************
</script>
{/literal}
<!-- :ENDE: plugins/tfm_amazon_payments_v2/templates/tfm_amazon_payments_test.html.tpl -->