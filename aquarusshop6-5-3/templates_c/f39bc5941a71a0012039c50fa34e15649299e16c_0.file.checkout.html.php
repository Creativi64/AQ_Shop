<?php
/* Smarty version 4.3.2, created on 2024-07-22 18:41:22
  from '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/checkout.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_669e8bb2f29306_19902625',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f39bc5941a71a0012039c50fa34e15649299e16c' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/checkout.html',
      1 => 1697144119,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:xtCore/pages/checkout/subpage_shipping.html' => 1,
    'file:xtCore/pages/checkout/subpage_payment.html' => 1,
    'file:xtCore/pages/checkout/subpage_confirmation.html' => 1,
    'file:xtCore/pages/checkout/subpage_pay.html' => 1,
    'file:xtCore/pages/checkout/subpage_success.html' => 1,
  ),
),false)) {
function content_669e8bb2f29306_19902625 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.link.php','function'=>'smarty_function_link',),));
?>
<div id="checkout">
    <div class="progress checkout-progress">
        <div class="<?php if ($_smarty_tpl->tpl_vars['page_action']->value == 'payment' || $_smarty_tpl->tpl_vars['page_action']->value == 'confirmation') {?>cursor-pointer<?php } elseif ($_smarty_tpl->tpl_vars['page_action']->value != 'success') {?>cursor-help<?php }?> progress-bar <?php if ($_smarty_tpl->tpl_vars['page_action']->value == 'shipping') {?>progress-bar-success progress-bar-striped active<?php }?> <?php if ($_smarty_tpl->tpl_vars['page_action']->value == 'payment' || $_smarty_tpl->tpl_vars['page_action']->value == 'confirmation' || $_smarty_tpl->tpl_vars['page_action']->value == 'success') {?>progress-bar-success done<?php }?>"
             style="width: 25%"
             title="<?php echo smarty_function_txt(array('key'=>TEXT_SELECT_SHIPPING_DESC),$_smarty_tpl);?>
"
             onclick="<?php if ($_smarty_tpl->tpl_vars['page_action']->value == 'payment' || $_smarty_tpl->tpl_vars['page_action']->value == 'confirmation') {?>document.location.href='<?php echo smarty_function_link(array('page'=>'checkout','paction'=>'shipping','conn'=>'SSL'),$_smarty_tpl);?>
'<?php }?>">
            <span class="hidden-xs"><?php echo smarty_function_txt(array('key'=>TEXT_SELECT_SHIPPING),$_smarty_tpl);
if ($_smarty_tpl->tpl_vars['page_action']->value == 'success') {?>&nbsp;<i class="fa fa-check" style="font-size: 12px;"></i><?php }?></span>
            <span class="visible-xs"><i class="fa fa-truck"></i><?php if ($_smarty_tpl->tpl_vars['page_action']->value == 'success') {?>&nbsp;<i class="fa fa-check" style="font-size: 12px;"></i><?php }?></span>
        </div>
        <div class="<?php if ($_smarty_tpl->tpl_vars['page_action']->value == 'confirmation') {?>cursor-pointer<?php } elseif ($_smarty_tpl->tpl_vars['page_action']->value != 'success') {?>cursor-help<?php }?> progress-bar <?php if ($_smarty_tpl->tpl_vars['page_action']->value == 'payment') {?>progress-bar-success progress-bar-striped active<?php } else { ?>progress-bar-default<?php }?> <?php if ($_smarty_tpl->tpl_vars['page_action']->value == 'confirmation' || $_smarty_tpl->tpl_vars['page_action']->value == 'success') {?>progress-bar-success done<?php }?>"
             style="width: 25%"
             title="<?php echo smarty_function_txt(array('key'=>TEXT_SELECT_PAYMENT_DESC),$_smarty_tpl);?>
"
             onclick="<?php if ($_smarty_tpl->tpl_vars['page_action']->value == 'confirmation') {?>document.location.href='<?php echo smarty_function_link(array('page'=>'checkout','paction'=>'payment','conn'=>'SSL'),$_smarty_tpl);?>
'<?php }?>">
            <span class="hidden-xs"><?php echo smarty_function_txt(array('key'=>TEXT_SELECT_PAYMENT),$_smarty_tpl);
if ($_smarty_tpl->tpl_vars['page_action']->value == 'success') {?>&nbsp;<i class="fa fa-check" style="font-size: 12px;"></i><?php }?></span>
            <span class="visible-xs"><i class="fa fa-credit-card"></i><?php if ($_smarty_tpl->tpl_vars['page_action']->value == 'success') {?>&nbsp;<i class="fa fa-check" style="font-size: 12px;"></i><?php }?></span>
        </div>
        <div class="<?php if ($_smarty_tpl->tpl_vars['page_action']->value != 'success') {?>cursor-help<?php }?> progress-bar <?php if ($_smarty_tpl->tpl_vars['page_action']->value == 'confirmation') {?>progress-bar-success progress-bar-striped active<?php } else { ?>progress-bar-default<?php }?> <?php if ($_smarty_tpl->tpl_vars['page_action']->value == 'success') {?>progress-bar-success done<?php }?>"
             style="width: 25%"
             title="<?php echo smarty_function_txt(array('key'=>TEXT_CONFIRMATION_DESC),$_smarty_tpl);?>
">
            <span class="hidden-xs"><?php echo smarty_function_txt(array('key'=>TEXT_CONFIRMATION),$_smarty_tpl);
if ($_smarty_tpl->tpl_vars['page_action']->value == 'success') {?>&nbsp;<i class="fa fa-check" style="font-size: 12px;"></i><?php }?></span>
            <span class="visible-xs"><i class="fa fa-handshake-o" aria-hidden="true"></i>&nbsp;<?php if ($_smarty_tpl->tpl_vars['page_action']->value == 'success') {?><i class="fa fa-check" style="font-size: 12px;"></i><?php }?></span>
        </div>
        <div class="progress-bar <?php if ($_smarty_tpl->tpl_vars['page_action']->value == 'success') {?>progress-bar-success active<?php } else { ?>progress-bar-default<?php }?> <?php if ($_smarty_tpl->tpl_vars['page_action']->value == 'success') {?>progress-bar-success done<?php }?>"
             style="width: 25%"
             title="<?php echo smarty_function_txt(array('key'=>TEXT_SUCCESS_DESC),$_smarty_tpl);?>
">
            <span <?php if ($_smarty_tpl->tpl_vars['page_action']->value == 'success') {?>class="bold"<?php }?>><?php echo smarty_function_txt(array('key'=>TEXT_SUCCESS),$_smarty_tpl);?>
</span>

        </div>
    </div>

    <?php echo $_smarty_tpl->tpl_vars['message']->value;?>


    <?php if ($_smarty_tpl->tpl_vars['page_action']->value == 'shipping' || $_smarty_tpl->tpl_vars['page_action']->value == '') {?>
    <?php $_smarty_tpl->_subTemplateRender("file:xtCore/pages/checkout/subpage_shipping.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('type'=>"tpl_include"), 0, false);
?>
    <?php }?>

    <?php if ($_smarty_tpl->tpl_vars['page_action']->value == 'payment') {?>
    <?php $_smarty_tpl->_subTemplateRender("file:xtCore/pages/checkout/subpage_payment.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('type'=>"tpl_include"), 0, false);
?>
    <?php }?>

    <?php if ($_smarty_tpl->tpl_vars['page_action']->value == 'confirmation') {?>
    <?php $_smarty_tpl->_subTemplateRender("file:xtCore/pages/checkout/subpage_confirmation.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('type'=>"tpl_include"), 0, false);
?>
    <?php }?>

    <?php if ($_smarty_tpl->tpl_vars['page_action']->value == 'pay') {?>
    <?php $_smarty_tpl->_subTemplateRender("file:xtCore/pages/checkout/subpage_pay.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('type'=>"tpl_include"), 0, false);
?>
    <?php }?>

    <?php if ($_smarty_tpl->tpl_vars['page_action']->value == 'success') {?>
    <?php $_smarty_tpl->_subTemplateRender("file:xtCore/pages/checkout/subpage_success.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('type'=>"tpl_include"), 0, false);
?>
    <?php }?>
</div>
<?php }
}
