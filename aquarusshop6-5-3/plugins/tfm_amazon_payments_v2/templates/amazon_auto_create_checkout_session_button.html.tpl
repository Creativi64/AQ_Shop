<!-- :START: plugins/tfm_amazon_payments_v2/templates/amazon_auto_create_checkout_session_button.html.tpl -->
<!-- Kunde kommt von .../checkout/payment und hat AmazonPayments gewählt. Automatisches erstellen der CheckoutSession und Weiterleitung zu Amazon -->
<script src="https://static-eu.payments-amazon.com/checkout.js"></script>
<div class="width:100%;min-height:300px">

    <h3 class="text-center" style="margin-bottom:80px">Bitte warten.</h3>
    <p class="text-center" style="font-size:60px"><i class="fa fa-refresh fa-spin"></i></p>
    <pre>{$message}</pre>

</div>
<div class="" id="AmazonPayButton" style="display: none"></div>
{if $error==0}
{literal}
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script type="text/javascript">
    var amazonPayButton = amazon.Pay.renderButton('#AmazonPayButton', {
        merchantId: '{/literal}{$merchantId}{literal}',
        ledgerCurrency: '{/literal}{$ledgerCurrency}{literal}',
        checkoutLanguage: '{/literal}{$checkoutLanguage}{literal}',
        productType: '{/literal}{$productType}{literal}',
        placement: 'Checkout',
        sandbox: {/literal}{$sandbox}{literal},
        buttonColor: '{/literal}{$buttonTheme}{literal}'
    });

    // jQuery $(document).ready replacement
    function dReady(fn) {
        if (document.readyState != 'loading') {
            fn();
        } else {
            document.addEventListener('DOMContentLoaded', fn);
        }
    }

    dReady(function () {
        amazonPayButton.initCheckout({
            createCheckoutSessionConfig: {
                payloadJSON: {/literal}{$payload}{literal},
                signature: '{/literal}{$signature}{literal}',
                publicKeyId: '{/literal}{$publicKeyId}{literal}',
            }
        });
    });
</script>
{/literal}
{/if}
<!-- :ENDE: plugins/tfm_amazon_payments_v2/templates/amazon_auto_create_checkout_session_button.html.tpl -->