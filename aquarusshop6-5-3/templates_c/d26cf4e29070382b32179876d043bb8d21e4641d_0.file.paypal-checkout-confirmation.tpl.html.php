<?php
/* Smarty version 4.3.2, created on 2024-04-21 11:51:18
  from '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_paypal_checkout/templates/paypal-checkout-confirmation.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_6624e196572228_26879577',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd26cf4e29070382b32179876d043bb8d21e4641d' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_paypal_checkout/templates/paypal-checkout-confirmation.tpl.html',
      1 => 1710347938,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:./paypal-checkout-wait-modal.tpl.html' => 1,
  ),
),false)) {
function content_6624e196572228_26879577 (Smarty_Internal_Template $_smarty_tpl) {
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
</style>


<?php echo '<script'; ?>
>

document.addEventListener('DOMContentLoaded', function ()
{
    try {
        try {
            ppcFix_button_changed_html(ppc_button_selector + '_<?php echo $_smarty_tpl->tpl_vars['requested_position']->value;?>
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
        enablePaypalButton('<?php echo $_smarty_tpl->tpl_vars['foundingSource']->value;?>
','<?php echo $_smarty_tpl->tpl_vars['requested_position']->value;?>
');
    }
    catch(e)
    {
        console.log(e);
    }
});
<?php echo '</script'; ?>
>

<?php $_smarty_tpl->_subTemplateRender("file:./paypal-checkout-wait-modal.tpl.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}