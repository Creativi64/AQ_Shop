<?php
/* Smarty version 5.1.0, created on 2024-10-14 17:40:11
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes/box_payment_logos.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_670d3b5b308593_57038724',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f7c716f1bdeea0b074029b5821b99cd327247822' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes/box_payment_logos.html',
      1 => 1697144118,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_670d3b5b308593_57038724 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes';
$_smarty_tpl->getCompiled()->nocache_hash = '1365181654670d3b5b2fd8a3_40579921';
?>


    <div class="payment-logos">
        <p class="headline"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PAYMENTS_FEE), $_smarty_tpl);?>
</p>
        <div class="logos">
            <?php if ((!$_smarty_tpl->getValue('is_pro_version') || ($_smarty_tpl->getSmarty()->getModifierCallback('defined')('KLARNA_LOGO_PRO') && (defined('KLARNA_LOGO_PRO') ? constant('KLARNA_LOGO_PRO') : null) === true)) && (defined('KLARNA_LOGO_URL') ? constant('KLARNA_LOGO_URL') : null)) {?><img class="img-responsive" src="<?php echo (defined('KLARNA_LOGO_URL') ? constant('KLARNA_LOGO_URL') : null);?>
" alt="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>'TEXT_KLARNA_SLOGAN'), $_smarty_tpl);?>
" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>'TEXT_KLARNA_SLOGAN'), $_smarty_tpl);?>
" data-toggle="tooltip" /><?php }?>
            <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('_payment_logos'), 'module_data', false, NULL, 'aussen', array (
));
$foreach9DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('module_data')->value) {
$foreach9DoElse = false;
?>
                <?php $_smarty_tpl->assign('image', ((string)$_smarty_tpl->getValue('tpl_path'))."/img/payments/".((string)$_smarty_tpl->getValue('language'))."/".((string)$_smarty_tpl->getValue('module_data')['payment_code']).".png", false, NULL);?>
                <?php $_smarty_tpl->assign('image_system', ((string)$_smarty_tpl->getValue('tpl_path_system'))."/img/payments/".((string)$_smarty_tpl->getValue('language'))."/".((string)$_smarty_tpl->getValue('module_data')['payment_code']).".png", false, NULL);?>
                <?php if ($_smarty_tpl->getSmarty()->getModifierCallback('file_exists')($_smarty_tpl->getValue('image'))) {?>
                    <img src="<?php echo $_smarty_tpl->getValue('tpl_url_path');?>
img/payments/<?php echo $_smarty_tpl->getValue('language');?>
/<?php echo $_smarty_tpl->getValue('module_data')['payment_code'];?>
.png" alt="<?php echo $_smarty_tpl->getValue('module_data')['payment_name'];?>
" title="<?php echo $_smarty_tpl->getValue('module_data')['payment_name'];?>
" data-toggle="tooltip" />
                <?php } elseif ($_smarty_tpl->getSmarty()->getModifierCallback('file_exists')($_smarty_tpl->getValue('image_system'))) {?>
                    <img src="<?php echo $_smarty_tpl->getValue('tpl_url_path_system');?>
img/payments/<?php echo $_smarty_tpl->getValue('language');?>
/<?php echo $_smarty_tpl->getValue('module_data')['payment_code'];?>
.png" alt="<?php echo $_smarty_tpl->getValue('module_data')['payment_name'];?>
" title="<?php echo $_smarty_tpl->getValue('module_data')['payment_name'];?>
" data-toggle="tooltip" />
                <?php }?>
            <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
        </div>
    </div>
<?php }
}
