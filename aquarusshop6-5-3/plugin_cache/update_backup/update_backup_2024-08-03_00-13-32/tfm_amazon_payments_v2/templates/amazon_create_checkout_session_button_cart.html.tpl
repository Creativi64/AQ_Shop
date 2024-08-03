<!-- :START: plugins/tfm_amazon_payments_v2/templates/amazon_create_checkout_session_button_cart.html.tpl -->
<script src="https://static-eu.payments-amazon.com/checkout.js"></script>
<p class="pull-right">&nbsp;&nbsp;</p>
<div class="pull-right">
    <div class="" id="AmazonPayButton"></div>
</div>
{literal}

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
        placement: 'Cart',
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
        amazonPayButton.onClick(function () {
            if (hasAmazonCheckoutSessionId === 1) {
                document.location = '{/literal}{link page='checkout' paction='shipping' conn=SSL}{literal}'
            } else {
                axios.post('{/literal}{link page='tfm_amazon_payments_v2_checkot_initiated_ajax' conn='SSL'}{literal}',
                    {
                        'type': 'cart'
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
        });
    });
</script>
{/literal}
<!-- :ENDE: plugins/tfm_amazon_payments_v2/templates/amazon_create_checkout_session_button_cart.html.tpl -->