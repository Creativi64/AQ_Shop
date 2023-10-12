<!-- :START: plugins/tfm_amazon_payments_v2/templates/tfm_amazon_payments_v2_button_action_checkout.html.tpl -->
<script src="https://static-eu.payments-amazon.com/checkout.js"></script>
<div class="" id="{$element_id}_checkout"></div>
{literal}

<script type="text/javascript">
    var {/literal}{$element_id}{literal}_hasAmazonCheckoutSessionId = parseInt('{/literal}{$hasAmazonCheckoutSessionId}{literal}');
    var {/literal}{$element_id}{literal}_amazonPayButton = amazon.Pay.renderButton('#{/literal}{$element_id}{literal}_checkout', {
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
        {/literal}{$element_id}{literal}_amazonPayButton.onClick(function () {
            if ({/literal}{$element_id}{literal}_hasAmazonCheckoutSessionId === 1) {
                document.location = '{/literal}{link page='checkout' paction='shipping' conn=SSL}{literal}'
            } else {
                axios.post('{/literal}{link page='tfm_amazon_payments_v2_checkot_initiated_ajax' conn='SSL'}{literal}',
                    {
                        'type': 'cart'
                    })
                    .then(function (response) {
                        if (response.data === 1) {
                            {/literal}{$element_id}{literal}_amazonPayButton.initCheckout({
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
<!-- :ENDE: plugins/tfm_amazon_payments_v2/templates/tfm_amazon_payments_v2_button_action_checkout.html.tpl -->