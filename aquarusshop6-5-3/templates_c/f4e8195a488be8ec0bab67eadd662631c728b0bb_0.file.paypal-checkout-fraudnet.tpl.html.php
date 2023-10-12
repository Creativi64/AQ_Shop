<?php
/* Smarty version 4.3.0, created on 2023-06-19 13:53:20
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/plugins/xt_paypal_checkout/templates/paypal-checkout-fraudnet.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.0',
  'unifunc' => 'content_649041b03fe1e1_27911160',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f4e8195a488be8ec0bab67eadd662631c728b0bb' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/plugins/xt_paypal_checkout/templates/paypal-checkout-fraudnet.tpl.html',
      1 => 1687006057,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_649041b03fe1e1_27911160 (Smarty_Internal_Template $_smarty_tpl) {
echo '<script'; ?>
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
 type="text/javascript" src="https://c.paypal.com/da/r/fb.js"><?php echo '</script'; ?>
>

<?php }
}
