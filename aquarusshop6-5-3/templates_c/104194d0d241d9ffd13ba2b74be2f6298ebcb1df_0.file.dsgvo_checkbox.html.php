<?php
/* Smarty version 4.3.2, created on 2024-03-20 20:17:13
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/plugins/xt_dsgvo_shippingcheckbox/templates/dsgvo_checkbox.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_65fb3639a64ae5_67833762',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '104194d0d241d9ffd13ba2b74be2f6298ebcb1df' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/plugins/xt_dsgvo_shippingcheckbox/templates/dsgvo_checkbox.html',
      1 => 1697144044,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_65fb3639a64ae5_67833762 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.form.php','function'=>'smarty_function_form',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),));
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
