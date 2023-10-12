<!-- :START: plugins/tfm_amazon_payments_v2/templates/amazon_create_checkout_session_button_product.html.tpl -->
<!-- Das Amazon CreateCheckoutSession wird unterbrochen, der Artikel wird in den Warenkorb gelegt und anschließend folgt das automatische erstellen der CheckoutSession -->
<script src="https://static-eu.payments-amazon.com/checkout.js"></script>
<div class="" style="width:200px;margin-top:10px">
    <div class="" id="AmazonPayButton" data-source="true"></div>
</div>
{literal}
<script type="text/javascript">
    var amazonPayButton = amazon.Pay.renderButton('#AmazonPayButton', {
        merchantId: '{/literal}{$merchantId}{literal}',
        ledgerCurrency: '{/literal}{$ledgerCurrency}{literal}',
        checkoutLanguage: '{/literal}{$checkoutLanguage}{literal}',
        productType: '{/literal}{$productType}{literal}',
        estimatedOrderAmount: { 'amount': '{/literal}{$estimatedOrderAmount}{literal}', 'currencyCode': '{/literal}{$currency}{literal}'},
        placement: 'Product',
        sandbox: {/literal}{$sandbox}{literal},
        buttonColor: '{/literal}{$buttonTheme}{literal}'
    });

    // jQuery $(document).ready replacement
    function dReady(fn) {
        if (document.readyState != 'loading'){
            fn();
        } else {
            document.addEventListener('DOMContentLoaded', fn);
        }
    }
    dReady(function () {
        let cButton = document.querySelector({/literal}{$selector|stripslashes}{literal});
        if (cButton) {
            amazonPayButton.onClick(function () {
                var _new=document.createElement('span');
                _new.innerHTML="<input type='hidden' name='tfm_amazon_payments' value='1'>";
                cButton.parentNode.insertBefore(_new,cButton);
                cButton.dispatchEvent(new MouseEvent('click'));
            });
        }
    });
</script>
{/literal}
<!-- :ENDE: plugins/tfm_amazon_payments_v2/templates/amazon_create_checkout_session_button_product.html.tpl -->