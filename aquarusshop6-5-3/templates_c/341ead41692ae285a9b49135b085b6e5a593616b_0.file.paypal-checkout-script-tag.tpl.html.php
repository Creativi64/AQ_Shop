<?php
/* Smarty version 4.3.0, created on 2023-10-08 01:15:17
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/plugins/xt_paypal_checkout/templates/paypal-checkout-script-tag.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.0',
  'unifunc' => 'content_6521e685b25182_11345450',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '341ead41692ae285a9b49135b085b6e5a593616b' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/plugins/xt_paypal_checkout/templates/paypal-checkout-script-tag.tpl.html',
      1 => 1696720402,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6521e685b25182_11345450 (Smarty_Internal_Template $_smarty_tpl) {
if (!$_smarty_tpl->tpl_vars['ppcp_script_tag_rendered']->value) {?>
    <?php echo '<script'; ?>
>

        window.paypal_checkout_constant =
            {
                BUTTON_SIZE: <?php echo intval($_smarty_tpl->tpl_vars['button_size']->value);?>
,
                BUTTON_COLOR: "<?php echo $_smarty_tpl->tpl_vars['button_color']->value;?>
",
                BUTTON_SHAPE: "<?php echo $_smarty_tpl->tpl_vars['button_shape']->value;?>
"
            }

    <?php echo '</script'; ?>
>

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

        document.addEventListener('DOMContentLoaded', function ()
        {
            try {
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

                // fraunet nachladen
                const fraudnet_script = "https://c.paypal.com/da/r/fb.js";
                script = document.createElement("script");
                script.setAttribute("src", fraudnet_script);
                document.head.appendChild(script);

                const ppcpSetTimeout = 50;
                const ppcpMaxTimeout = 10000;
                let ppcpWaited = 0;
                checkPaypalAvailable();

                function checkPaypalAvailable()
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
                        setTimeout(checkPaypalAvailable, ppcpSetTimeout);
                    }
                }
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
