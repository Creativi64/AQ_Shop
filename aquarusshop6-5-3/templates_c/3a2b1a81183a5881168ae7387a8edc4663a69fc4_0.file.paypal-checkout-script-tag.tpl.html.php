<?php
/* Smarty version 4.3.0, created on 2023-06-19 13:53:20
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/plugins/xt_paypal_checkout/templates/paypal-checkout-script-tag.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.0',
  'unifunc' => 'content_649041b03ee2f2_98759837',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '3a2b1a81183a5881168ae7387a8edc4663a69fc4' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/plugins/xt_paypal_checkout/templates/paypal-checkout-script-tag.tpl.html',
      1 => 1687006057,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_649041b03ee2f2_98759837 (Smarty_Internal_Template $_smarty_tpl) {
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
 src="https://www.paypal.com/sdk/js?<?php echo $_smarty_tpl->tpl_vars['query_params']->value;?>
" <?php echo $_smarty_tpl->tpl_vars['script_params']->value;?>
 defer><?php echo '</script'; ?>
>
    <?php $_smarty_tpl->_assignInScope('ppcp_script_tag_rendered', 1 ,false ,32);
}
}
}
