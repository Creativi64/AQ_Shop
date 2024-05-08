<?php
/* Smarty version 4.3.2, created on 2024-04-21 11:51:18
  from '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_dsgvo_shippingcheckbox/templates/dsgvo_checkbox.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_6624e196470a55_02350445',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '7753c0fdfd8b86cb2781d4bbc403fe4f7aaf9f65' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_dsgvo_shippingcheckbox/templates/dsgvo_checkbox.html',
      1 => 1697144044,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6624e196470a55_02350445 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.form.php','function'=>'smarty_function_form',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),));
?>
<p class="checkbox">
    <label>
        <?php if ($_smarty_tpl->tpl_vars['required']->value == '1') {?>
            <?php echo smarty_function_form(array('type'=>'checkbox','name'=>'dsgvo_shipping_optin','class'=>"xt-form-required"),$_smarty_tpl);?>

        <?php } else { ?>
            <?php echo smarty_function_form(array('type'=>'checkbox','name'=>'dsgvo_shipping_optin'),$_smarty_tpl);?>

        <?php }?>
        <?php echo smarty_function_txt(array('key'=>TEXT_XT_DSGVO_SHIPPINGCHECKBOX),$_smarty_tpl);?>

    </label>
</p><?php }
}
