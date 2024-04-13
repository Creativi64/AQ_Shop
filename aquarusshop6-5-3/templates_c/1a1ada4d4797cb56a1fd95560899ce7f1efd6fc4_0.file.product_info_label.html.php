<?php
/* Smarty version 4.3.2, created on 2024-03-20 20:16:43
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive_AQ/includes/product_info_label.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_65fb361bf3f736_05145239',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '1a1ada4d4797cb56a1fd95560899ce7f1efd6fc4' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive_AQ/includes/product_info_label.html',
      1 => 1697144063,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_65fb361bf3f736_05145239 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="product-info-label pos-<?php echo $_smarty_tpl->tpl_vars['position']->value;?>
">
    <?php if ($_smarty_tpl->tpl_vars['isSpecial']->value == 1) {?>
        <span class="icon special-product">%</span>
    <?php }?>
</div><?php }
}
