<?php
/* Smarty version 5.4.1, created on 2024-12-03 17:04:25
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_paypal_checkout/templates/paypal-checkout-confirmation.tpl.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.1',
  'unifunc' => 'content_674f2c09697280_15894508',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd26cf4e29070382b32179876d043bb8d21e4641d' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_paypal_checkout/templates/paypal-checkout-confirmation.tpl.html',
      1 => 1733161754,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:./paypal-checkout-wait-modal.tpl.html' => 1,
  ),
))) {
function content_674f2c09697280_15894508 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_paypal_checkout/templates';
?><style>
    @media screen and (min-width: 300px) and (max-width: 439px) {
        .paypal_button_container {
            width: 240px;
        }
    }
    @media screen and (min-width: 440px) {
        .paypal_button_container {
            width: 235px;
        }
    }
    @media screen and (min-width: 767px) {
        .paypal_button_container {
            width: 300px;
        }
    }
    @media screen and (min-width: 1000px) {
        .paypal_button_container {
            width: 400px;
        }
    }
    @media screen and (min-width: 1200px) {
        .paypal_button_container {
            width: 600px;
        }
    }

    apple-pay-button {
        --apple-pay-button-width: 100%;
        --apple-pay-button-height: 36px;
        #--apple-pay-button-border-radius: 5px;
        #--apple-pay-button-padding: 5px 0px;
    }

    form#checkout_form .xt-form-required.xt-form-error::before,
    form#checkout-form .xt-form-required.xt-form-error::before
    {
        content: "*";
        color: darkred;
        margin-left: -10px;
        font-size: 1.2em;
        display: inline-block;
        line-height: 1px;
    }



</style>


<?php echo '<script'; ?>
>

document.addEventListener('DOMContentLoaded', function ()
{
    try {
        try {
            ppcFix_button_changed_html(ppc_button_selector + '_<?php echo $_smarty_tpl->getValue('requested_position');?>
');
        }
        catch(e){
            console.log('ppcFix_button_changed_html failed', e);
        }
        ppcRemoveSubmitButton();
    }
    catch(e)
    {
        console.log(e);
    }
});
document.addEventListener('PayPalSdkLoaded', function ()
{
    try {
        if('<?php echo $_smarty_tpl->getValue('foundingSource');?>
' === 'applepay') setupApplepay();
        if('<?php echo $_smarty_tpl->getValue('foundingSource');?>
' === 'googlepay') setupGooglepay();
        else enablePaypalButton('<?php echo $_smarty_tpl->getValue('foundingSource');?>
','<?php echo $_smarty_tpl->getValue('requested_position');?>
');
    }
    catch(e)
    {
        console.log(e);
    }
});

const google_transaction_info = <?php echo $_smarty_tpl->getValue('google_transaction_info');?>
;
<?php echo '</script'; ?>
>

<?php $_smarty_tpl->renderSubTemplate("file:./paypal-checkout-wait-modal.tpl.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
}
}
