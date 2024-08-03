<?php
/* Smarty version 5.1.0, created on 2024-08-03 01:14:04
  from 'file:includes/product_info_label.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_66ad683c9210d5_65509063',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '8e8aa2edfd1d9a233b38daf7a1be948a311618e7' => 
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
function content_66ad683c9210d5_65509063 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/includes';
$_smarty_tpl->getCompiled()->nocache_hash = '47204762966ad683c91f0d7_75933978';
?>
<div class="product-info-label pos-<?php echo $_smarty_tpl->getValue('position');?>
">
    <?php if ($_smarty_tpl->getValue('isSpecial') == 1) {?>
        <span class="icon special-product">%</span>
    <?php }?>
</div><?php }
}
