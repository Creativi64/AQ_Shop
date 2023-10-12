<?php
/* Smarty version 4.3.0, created on 2023-06-19 13:53:19
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive/includes/product_info_label.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.0',
  'unifunc' => 'content_649041aff187d1_11441052',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '070401adad4d534288b99ed7b77334ab8340d896' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive/includes/product_info_label.html',
      1 => 1687006066,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_649041aff187d1_11441052 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="product-info-label pos-<?php echo $_smarty_tpl->tpl_vars['position']->value;?>
">
    <?php if ($_smarty_tpl->tpl_vars['isSpecial']->value == 1) {?>
        <span class="icon special-product">%</span>
    <?php }?>
</div><?php }
}
