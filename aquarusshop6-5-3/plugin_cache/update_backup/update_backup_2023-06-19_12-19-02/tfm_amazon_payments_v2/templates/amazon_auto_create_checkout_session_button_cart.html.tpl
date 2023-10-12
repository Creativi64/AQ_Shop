<!-- :START: plugins/tfm_amazon_payments_v2/templates/amazon_auto_create_checkout_session_button_cart.html.tpl -->
<!-- Kunde hat am Produkt auf "zahlen mit AmazonPayments" geklickt. Automatisches erstellen der CheckoutSession und Weiterleitung zu Amazon -->
<script src="https://static-eu.payments-amazon.com/checkout.js"></script>
<div class="width:100%;min-height:300px">

    <h3 class="text-center" style="margin-bottom:80px">Bitte warten.</h3>
    <p class="text-center" style="font-size:60px"><i class="fa fa-refresh fa-spin"></i></p>

</div>
<div class="" id="AmazonPayButton" style="display: none"></div>
{literal}
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script type="text/javascript">
    var hasAmazonCheckoutSessionId = parseInt('{/literal}{$hasAmazonCheckoutSessionId}{literal}');
    var amazonPayButton = amazon.Pay.renderButton('#AmazonPayButton', {
        merchantId: '{/literal}{$merchantId}{literal}',
        ledgerCurrency: '{/literal}{$ledgerCurrency}{literal}',
        checkoutLanguage: '{/literal}{$checkoutLanguage}{literal}',
        productType: '{/literal}{$productType}{literal}',
        estimatedOrderAmount: {
            'amount': '{/literal}{$estimatedOrderAmount}{literal}',
            'currencyCode': '{/literal}{$currency}{literal}'
        },
        placement: 'Product',
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

    function dReady(fn) {
        if (document.readyState != 'loading') {
            fn();
        } else {
            document.addEventListener('DOMContentLoaded', fn);
        }
    }

    dReady(function () {
        window.setTimeout(function () {
            if (hasAmazonCheckoutSessionId === 1) {
                document.location = '{/literal}{link page='checkout' paction='shipping' conn=SSL}{literal}'
            } else {
                axios.post('{/literal}{link page='tfm_amazon_payments_v2_checkot_initiated_ajax' conn='SSL'}{literal}',
                    {
                        'type': 'product'
                    })
                    .then(function (response) {
                        if (response.data === 1) {
                            amazonPayButton.initCheckout({
                                createCheckoutSession: {
                                    url: '{/literal}{link page='tfm_amazon_payments_v2_checkot_session_ajax' conn='SSL'}{literal}',
                                    method: 'POST',
                                }
                            });
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            }
        }, 1000);
    });
</script>
{/literal}
<!-- :ENDE: plugins/tfm_amazon_payments_v2/templates/amazon_auto_create_checkout_session_button_cart.html.tpl -->
