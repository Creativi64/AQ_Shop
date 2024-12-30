<?php
/* Smarty version 5.4.1, created on 2024-12-30 01:19:49
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_paypal_checkout/templates/paypal-checkout-script-tag.tpl.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.1',
  'unifunc' => 'content_6771e72571ac17_05111114',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '088ce141a1dd4545455dd24d6a85ef1ccfc51783' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_paypal_checkout/templates/paypal-checkout-script-tag.tpl.html',
      1 => 1733161754,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6771e72571ac17_05111114 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_paypal_checkout/templates';
if (!$_smarty_tpl->getValue('ppcp_script_tag_rendered')) {?>

    <?php echo '<script'; ?>
 type="application/json" fncls="fnparams-dede7cc5-15fd-4c75-a9f4-36c430ee3a99">
        {
            "f":"<?php echo $_smarty_tpl->getValue('fraudnet_session_identifier');?>
",
            "s":"<?php echo $_smarty_tpl->getValue('source_website_identifier');?>
",
            "sandbox":<?php echo $_smarty_tpl->getValue('sandbox');?>

        }
    <?php echo '</script'; ?>
>
    <noscript>
        <img src="https://c.paypal.com/v1/r/d/b/ns?f=<?php echo $_smarty_tpl->getValue('fraudnet_session_identifier');?>
&s=<?php echo $_smarty_tpl->getValue('source_website_identifier');?>
&js=0&r=1" />
    </noscript>

    <?php echo '<script'; ?>
>

        console.log('paypal-checkout-script-tag setting ppcp constants');

        window.paypal_checkout_constant =
            {
                BUTTON_SIZE: <?php echo $_smarty_tpl->getSmarty()->getModifierCallback('intval')($_smarty_tpl->getValue('button_size'));?>
,
                BUTTON_COLOR: "<?php echo $_smarty_tpl->getValue('button_color');?>
",
                BUTTON_SHAPE: "<?php echo $_smarty_tpl->getValue('button_shape');?>
",
                BUTTON_TYPE_AP: "<?php echo $_smarty_tpl->getValue('button_type_ap');?>
",
                version: "<?php echo $_smarty_tpl->getValue('plugin_version');?>
",
                language: "<?php echo $_smarty_tpl->getValue('lng');?>
",
                language_short: "<?php echo $_smarty_tpl->getValue('lng_short');?>
",
                currency: "<?php echo $_smarty_tpl->getValue('currency');?>
",
                TEXT_ERROR_CONDITIONS_ACCEPTED: "<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>ERROR_CONDITIONS_ACCEPTED), $_smarty_tpl);?>
",
                TEXT_SHIPPING_COSTS: "<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SHIPPING_COSTS), $_smarty_tpl);?>
",
                WARNING_NO_SHIPPING_FOR_ZONE: "<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>WARNING_NO_SHIPPING_FOR_ZONE), $_smarty_tpl);?>
",
                PPCP_SESSION_ID: "<?php echo $_smarty_tpl->getValue('ppcp_session_id');?>
",
                googlePaymentsEnvironment: "<?php echo $_smarty_tpl->getValue('googlePaymentsEnvironment');?>
"
            }

        let billingContact_ap = <?php echo $_smarty_tpl->getValue('billingContact_ap');?>
;
        let shippingContact_ap = <?php echo $_smarty_tpl->getValue('shippingContact_ap');?>
;
        let currentCountryCode_ap = "<?php echo $_smarty_tpl->getValue('country');?>
";
        let currentTotal_ap = 0;
        let totalLabel_ap = "<?php echo $_smarty_tpl->getValue('totalLabel');?>
";
        let subTotalLabel_ap = "<?php echo $_smarty_tpl->getValue('subTotalLabel');?>
";

        const ppcpSetTimeout = 50;
        const ppcpMaxTimeout = 10000;
        let ppcpWaited = 0;
        async function checkPaypalScriptLoaded()
        {
            ppcpWaited += ppcpSetTimeout;
            if(ppcpWaited >= ppcpMaxTimeout)
            {
                console.warn("waited for paypal sdk " + ppcpWaited + " ms so far. ABORTING");
                return;
            }
            if (typeof window.paypal === "object")
            {
                console.info("paypal sdk loaded after " + ppcpWaited + " ms. emitting event PayPalSdkLoaded");
                const event = new Event("PayPalSdkLoaded");
                document.dispatchEvent(event);
            }
            else {
                console.debug("waiting for paypal sdk " + ppcpWaited + " ms so far");
                setTimeout(checkPaypalScriptLoaded, ppcpSetTimeout);
            }
        }

        console.log("script tag. adding DOMContentLoaded listener");
        document.addEventListener('DOMContentLoaded', function ()
        {
            console.log('paypal-checkout-script-tag  DOMContentLoaded')
            try {
                const terms_cb = document.querySelector('input[type=checkbox][name=conditions_accepted]');
                if(terms_cb)
                {
                    let div = document.createElement('div');
                    div.innerText = window.paypal_checkout_constant.TEXT_ERROR_CONDITIONS_ACCEPTED;
                    div.id = 'TEXT_ERROR_CONDITIONS_ACCEPTED';
                    div.classList.add('alert', 'alert-danger');
                    div.style.display = 'none';
                    terms_cb.closest("div").prepend(div);

                    terms_cb.addEventListener('change', (e) => {
                        if (e.currentTarget.checked) {
                            document.getElementById('TEXT_ERROR_CONDITIONS_ACCEPTED').style.display = 'none';
                        } else {
                            document.getElementById('TEXT_ERROR_CONDITIONS_ACCEPTED').style.display = 'block';
                        }
                    })
                }
                console.log("ppcp display.php DOMContentLoaded. injecting sdk script");

                const paypal_script = "https://www.paypal.com/sdk/js?<?php echo $_smarty_tpl->getValue('query_params');?>
";
                let script = document.createElement("script");
                script.setAttribute("src", paypal_script);
                script.setAttribute("data-partner-attribution-id", "<?php echo $_smarty_tpl->getValue('partner_attribution_id');?>
");
                script.setAttribute("data-client-token", "<?php echo $_smarty_tpl->getValue('client_token');?>
");
                script.setAttribute("data-user-id-token","<?php echo $_smarty_tpl->getValue('user_token');?>
");
                document.head.appendChild(script);

                // apple pay laden
                const apple_script = "https://applepay.cdn-apple.com/jsapi/v1/apple-pay-sdk.js";
                script = document.createElement("script");
                script.setAttribute("src", apple_script);
                document.head.appendChild(script);

                // fraudnet nachladen
                const fraudnet_script = "https://c.paypal.com/da/r/fb.js";
                script = document.createElement("script");
                script.setAttribute("src", fraudnet_script);
                document.head.appendChild(script);

                checkPaypalScriptLoaded();
            }
            catch(e)
            {
                console.log(e);
            }
        });

        document.addEventListener('PayPalSdkLoaded', function ()
        {
            // google pay laden
            const google_script = "https://pay.google.com/gp/p/js/pay.js";
            script = document.createElement("script");
            script.setAttribute("src", google_script);
            script.setAttribute("async", "");
            script.setAttribute("onload", "let gpslevent = new Event('GooglePaySdkLoaded'); document.dispatchEvent(gpslevent);");
            document.head.appendChild(script);
        });

    <?php echo '</script'; ?>
>

    <?php $_smarty_tpl->assign('ppcp_script_tag_rendered', 1, false, 32);
}
}
}
