<?php
/* Smarty version 5.1.0, created on 2024-09-10 16:58:09
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_dsgvo_shippingcheckbox/templates/dsgvo_checkbox.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_66e05e813f7b18_07504884',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '7753c0fdfd8b86cb2781d4bbc403fe4f7aaf9f65' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_dsgvo_shippingcheckbox/templates/dsgvo_checkbox.html',
      1 => 1722634734,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_66e05e813f7b18_07504884 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_dsgvo_shippingcheckbox/templates';
?><p class="checkbox">
    <label>
        <?php if ($_smarty_tpl->getValue('required') == '1') {?>
            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'checkbox','name'=>'dsgvo_shipping_optin','class'=>"xt-form-required"), $_smarty_tpl);?>

        <?php } else { ?>
            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'checkbox','name'=>'dsgvo_shipping_optin'), $_smarty_tpl);?>

        <?php }?>
        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_XT_DSGVO_SHIPPINGCHECKBOX), $_smarty_tpl);?>

    </label>
</p><?php }
}