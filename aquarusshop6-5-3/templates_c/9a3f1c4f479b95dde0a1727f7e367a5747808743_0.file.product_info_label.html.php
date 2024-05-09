<?php
/* Smarty version 4.3.2, created on 2024-05-08 20:07:28
  from '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/includes/product_info_label.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_663bbf60d31272_01630901',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '9a3f1c4f479b95dde0a1727f7e367a5747808743' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/includes/product_info_label.html',
      1 => 1697144063,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_663bbf60d31272_01630901 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="product-info-label pos-<?php echo $_smarty_tpl->tpl_vars['position']->value;?>
">
    <?php if ($_smarty_tpl->tpl_vars['isSpecial']->value == 1) {?>
        <span class="icon special-product">%</span>
    <?php }?>
</div><?php }
}
