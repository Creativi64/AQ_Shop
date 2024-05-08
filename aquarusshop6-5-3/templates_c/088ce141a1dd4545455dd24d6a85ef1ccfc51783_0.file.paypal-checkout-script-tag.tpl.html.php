<?php
/* Smarty version 4.3.2, created on 2024-05-08 17:56:07
  from '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_paypal_checkout/templates/paypal-checkout-script-tag.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_663ba097d99d88_00375050',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '088ce141a1dd4545455dd24d6a85ef1ccfc51783' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_paypal_checkout/templates/paypal-checkout-script-tag.tpl.html',
      1 => 1715183041,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_663ba097d99d88_00375050 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),));
if (!$_smarty_tpl->tpl_vars['ppcp_script_tag_rendered']->value) {?>

    <?php echo '<script'; ?>
 type="application/json" fncls="fnparams-dede7cc5-15fd-4c75-a9f4-36c430ee3a99">
        {
            "f":"<?php echo $_smarty_tpl->tpl_vars['fraudnet_session_identifier']->value;?>
",
            "s":"<?php echo $_smarty_tpl->tpl_vars['source_website_identifier']->value;?>
",
            "sandbox":<?php echo $_smarty_tpl->tpl_vars['sandbox']->value;?>

        }
    <?php echo '</script'; ?>
>
    <noscript>
        <img src="https://c.paypal.com/v1/r/d/b/ns?f=<?php echo $_smarty_tpl->tpl_vars['fraudnet_session_identifier']->value;?>
&s=<?php echo $_smarty_tpl->tpl_vars['source_website_identifier']->value;?>
&js=0&r=1" />
    </noscript>

    <?php echo '<script'; ?>
>

        console.log('paypal-checkout-script-tag setting ppcp constants');

        window.paypal_checkout_constant =
            {
                BUTTON_SIZE: <?php echo intval($_smarty_tpl->tpl_vars['button_size']->value);?>
,
                BUTTON_COLOR: "<?php echo $_smarty_tpl->tpl_vars['button_color']->value;?>
",
                BUTTON_SHAPE: "<?php echo $_smarty_tpl->tpl_vars['button_shape']->value;?>
",
                BUTTON_TYPE_AP: "<?php echo $_smarty_tpl->tpl_vars['button_type_ap']->value;?>
",
                version: "<?php echo $_smarty_tpl->tpl_vars['plugin_version']->value;?>
",
                language: "<?php echo $_smarty_tpl->tpl_vars['lng']->value;?>
",
                currency: "<?php echo $_smarty_tpl->tpl_vars['currency']->value;?>
",
                TEXT_ERROR_CONDITIONS_ACCEPTED: "<?php echo smarty_function_txt(array('key'=>ERROR_CONDITIONS_ACCEPTED),$_smarty_tpl);?>
",
                TEXT_SHIPPING_COSTS: "<?php echo smarty_function_txt(array('key'=>TEXT_SHIPPING_COSTS),$_smarty_tpl);?>
",
                WARNING_NO_SHIPPING_FOR_ZONE: "<?php echo smarty_function_txt(array('key'=>WARNING_NO_SHIPPING_FOR_ZONE),$_smarty_tpl);?>
",
                PPCP_SESSION_ID: "<?php echo $_smarty_tpl->tpl_vars['ppcp_session_id']->value;?>
"
            }

        let billingContact_ap = <?php echo $_smarty_tpl->tpl_vars['billingContact_ap']->value;?>
;
        let shippingContact_ap = <?php echo $_smarty_tpl->tpl_vars['shippingContact_ap']->value;?>
;
        let currentCountryCode_ap = "<?php echo $_smarty_tpl->tpl_vars['country']->value;?>
";
        let currentTotal_ap = 0;
        let totalLabel_ap = "<?php echo $_smarty_tpl->tpl_vars['totalLabel']->value;?>
";
        let subTotalLabel_ap = "<?php echo $_smarty_tpl->tpl_vars['subTotalLabel']->value;?>
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

                const paypal_script = "https://www.paypal.com/sdk/js?<?php echo $_smarty_tpl->tpl_vars['query_params']->value;?>
";
                let script = document.createElement("script");
                script.setAttribute("src", paypal_script);
                script.setAttribute("data-partner-attribution-id", "<?php echo $_smarty_tpl->tpl_vars['partner_attribution_id']->value;?>
");
                script.setAttribute("data-client-token", "<?php echo $_smarty_tpl->tpl_vars['client_token']->value;?>
");
                document.head.appendChild(script);

                // applepay laden
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

    <?php echo '</script'; ?>
>

    <?php $_smarty_tpl->_assignInScope('ppcp_script_tag_rendered', 1 ,false ,32);
}
}
}
