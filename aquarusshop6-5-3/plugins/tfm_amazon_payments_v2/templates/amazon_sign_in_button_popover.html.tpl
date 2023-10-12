<!-- :START: plugins/tfm_amazon_payments_v2/templates/amazon_sign_in_button_popover.html.tpl -->
<script src="https://static-eu.payments-amazon.com/checkout.js"></script>
<div class="pull-right" style="width:100%;height:40px"><div class="pull-right" id="AmazonSignInButtonPopOver"></div></div>
{literal}
<script type="text/javascript">
    amazon.Pay.renderButton('#AmazonSignInButtonPopOver', {
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