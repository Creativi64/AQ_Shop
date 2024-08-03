<!-- :START: plugins/tfm_amazon_payments_v2/templates/amazon_sign_in_button_popover.html.tpl -->
<script src="https://static-eu.payments-amazon.com/checkout.js"></script>
<div class="custom_tfm_amazon_payments_v2_button" id="{$element_id}_login"></div>
{literal}
<script type="text/javascript">
    amazon.Pay.renderButton('#{/literal}{$element_id}{literal}_login', {
        merchantId: '{/literal}{$merchantId}{literal}',
        signInConfig: {
            payloadJSON: '{/literal}{$payload}{literal}',
            signature: '{/literal}{$signature}{literal}',
            publicKeyId: '{/literal}{$publicKey}{literal}'
        },
        ledgerCurrency: '{/literal}{$ledgerCurrency}{literal}',
        checkoutLanguage: '{/literal}{$checkoutLanguage}{literal}',
        productType: 'SignIn',
        placement: 'Other',
        sandbox: {/literal}{$sandbox}{literal},
        buttonColor: '{/literal}{$buttonTheme}{literal}'
    });
</script>
{/literal}
<!-- :ENDE: plugins/tfm_amazon_payments_v2/templates/amazon_sign_in_button_popover.html.tpl -->