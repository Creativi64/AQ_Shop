<?php
/* Smarty version 5.4.1, created on 2024-12-03 17:05:14
  from 'file:xtCore/pages/checkout/subpage_success.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.1',
  'unifunc' => 'content_674f2c3a858ad3_17272709',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '8caa66fcd9c7a8d3ba7499af0d3f08d7b96699c3' => 
    array (
      0 => 'xtCore/pages/checkout/subpage_success.html',
      1 => 1697144245,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_674f2c3a858ad3_17272709 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/checkout';
?><div id="checkout-success">
    <h1>
        <i class="fa fa-check text-success"></i>
        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PAGE_TITLE_CHECKOUT_SUCCESS), $_smarty_tpl);?>

    </h1>
    <?php if ($_SESSION['kco_order_id']) {?>
    <div id="klarna-order-success-details" style="width:50%; max-width:640px; margin:auto; border:solid 1px #444"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'kco_checkout_success'), $_smarty_tpl);?>
</div>
    <?php }?>
    <br />
    <p><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_CHECKOUT_SUCCESS_DESC), $_smarty_tpl);?>
</p>
    <?php if ($_smarty_tpl->getValue('show_next_button') == 'true') {?>
    <p><a href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'index','conn'=>'NOSSL'), $_smarty_tpl);?>
" class="btn btn-secondary"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>BUTTON_NEXT), $_smarty_tpl);?>
</a></p>
    <?php }?>
    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'checkout_success_tpl'), $_smarty_tpl);?>

</div><!-- #checkout-shipping .row --><?php }
}
