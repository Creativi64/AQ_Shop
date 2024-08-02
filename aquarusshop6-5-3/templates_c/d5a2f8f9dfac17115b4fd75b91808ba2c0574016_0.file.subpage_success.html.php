<?php
/* Smarty version 4.3.2, created on 2024-07-22 18:48:16
  from '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/checkout/subpage_success.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_669e8d505c6ca2_77123423',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd5a2f8f9dfac17115b4fd75b91808ba2c0574016' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/checkout/subpage_success.html',
      1 => 1697144245,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_669e8d505c6ca2_77123423 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.hook.php','function'=>'smarty_function_hook',),2=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.link.php','function'=>'smarty_function_link',),));
?>
<div id="checkout-success">
    <h1>
        <i class="fa fa-check text-success"></i>
        <?php echo smarty_function_txt(array('key'=>TEXT_PAGE_TITLE_CHECKOUT_SUCCESS),$_smarty_tpl);?>

    </h1>
    <?php if ($_SESSION['kco_order_id']) {?>
    <div id="klarna-order-success-details" style="width:50%; max-width:640px; margin:auto; border:solid 1px #444"><?php echo smarty_function_hook(array('key'=>'kco_checkout_success'),$_smarty_tpl);?>
</div>
    <?php }?>
    <br />
    <p><?php echo smarty_function_txt(array('key'=>TEXT_CHECKOUT_SUCCESS_DESC),$_smarty_tpl);?>
</p>
    <?php if ($_smarty_tpl->tpl_vars['show_next_button']->value == 'true') {?>
    <p><a href="<?php echo smarty_function_link(array('page'=>'index','conn'=>'NOSSL'),$_smarty_tpl);?>
" class="btn btn-secondary"><?php echo smarty_function_txt(array('key'=>BUTTON_NEXT),$_smarty_tpl);?>
</a></p>
    <?php }?>
    <?php echo smarty_function_hook(array('key'=>'checkout_success_tpl'),$_smarty_tpl);?>

</div><!-- #checkout-shipping .row --><?php }
}
