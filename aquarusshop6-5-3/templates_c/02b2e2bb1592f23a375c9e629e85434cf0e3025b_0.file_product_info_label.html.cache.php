<?php
/* Smarty version 5.1.0, created on 2024-08-03 01:15:06
  from 'file:includes/product_info_label.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_66ad687a13a7f3_29877444',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '02b2e2bb1592f23a375c9e629e85434cf0e3025b' => 
    array (
      0 => 'includes/product_info_label.html',
      1 => 1697144063,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_66ad687a13a7f3_29877444 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/includes';
$_smarty_tpl->getCompiled()->nocache_hash = '139333764766ad687a138682_11844374';
?>
<div class="product-info-label pos-<?php echo $_smarty_tpl->getValue('position');?>
">
    <?php if ($_smarty_tpl->getValue('isSpecial') == 1) {?>
        <span class="icon special-product">%</span>
    <?php }?>
</div><?php }
}
