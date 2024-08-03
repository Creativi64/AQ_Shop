<!-- :START: plugins/tfm_amazon_payments_v2/templates/amazon_sign_in_button_login.html.tpl -->
<div id="amazon_loginbox" class="panel panel-default">
    <p class="panel-heading">{txt key=TEXT_LOGIN_PAGE_HEADLINE}</p>
    <div class="panel-body">
        <div class="col-xs-12 col-sm-8 col-md-9">

            <p class="info-text">{txt key=TEXT_LOGIN_PAGE_DESC}</p>
        </div>
        <div class="col-xs-12 col-sm-4 col-md-3">
            <div class="AmazonPaymentBox">
                <script src="https://static-eu.payments-amazon.com/checkout.js"></script>
                <div class="pull-right" style="width:100%;height:40px">
                    <div class="pull-right" id="AmazonSignInButtonLogin"></div>
                </div>
            </div>
        </div>
    </div>
</div>
{literal}
<script type="text/javascript">
    amazon.Pay.renderButton('#AmazonSignInButtonLogin', {
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
<!-- :ENDE: plugins/tfm_amazon_payments_v2/templates/amazon_sign_in_button_login.html.tpl -->