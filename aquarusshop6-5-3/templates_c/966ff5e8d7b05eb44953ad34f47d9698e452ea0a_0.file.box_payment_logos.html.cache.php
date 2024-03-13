<?php
/* Smarty version 4.3.2, created on 2024-03-13 17:39:33
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive_AQ/xtCore/boxes/box_payment_logos.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_65f1d6c5cde7c6_90806067',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '966ff5e8d7b05eb44953ad34f47d9698e452ea0a' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive_AQ/xtCore/boxes/box_payment_logos.html',
      1 => 1697144118,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_65f1d6c5cde7c6_90806067 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),));
$_smarty_tpl->compiled->nocache_hash = '61079424665f1d6c5c85bb7_62247143';
?>


    <div class="payment-logos">
        <p class="headline"><?php echo smarty_function_txt(array('key'=>TEXT_PAYMENTS_FEE),$_smarty_tpl);?>
</p>
        <div class="logos">
            <?php if ((!$_smarty_tpl->tpl_vars['is_pro_version']->value || (defined('KLARNA_LOGO_PRO') && (defined('KLARNA_LOGO_PRO') ? constant('KLARNA_LOGO_PRO') : null) === true)) && (defined('KLARNA_LOGO_URL') ? constant('KLARNA_LOGO_URL') : null)) {?><img class="img-responsive" src="<?php echo (defined('KLARNA_LOGO_URL') ? constant('KLARNA_LOGO_URL') : null);?>
" alt="<?php echo smarty_function_txt(array('key'=>'TEXT_KLARNA_SLOGAN'),$_smarty_tpl);?>
" title="<?php echo smarty_function_txt(array('key'=>'TEXT_KLARNA_SLOGAN'),$_smarty_tpl);?>
" data-toggle="tooltip" /><?php }?>
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['_payment_logos']->value, 'module_data', false, NULL, 'aussen', array (
));
$_smarty_tpl->tpl_vars['module_data']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['module_data']->value) {
$_smarty_tpl->tpl_vars['module_data']->do_else = false;
?>
                <?php $_smarty_tpl->_assignInScope('image', ((string)$_smarty_tpl->tpl_vars['tpl_path']->value)."/img/payments/".((string)$_smarty_tpl->tpl_vars['language']->value)."/".((string)$_smarty_tpl->tpl_vars['module_data']->value['payment_code']).".png");?>
                <?php $_smarty_tpl->_assignInScope('image_system', ((string)$_smarty_tpl->tpl_vars['tpl_path_system']->value)."/img/payments/".((string)$_smarty_tpl->tpl_vars['language']->value)."/".((string)$_smarty_tpl->tpl_vars['module_data']->value['payment_code']).".png");?>
                <?php if (file_exists($_smarty_tpl->tpl_vars['image']->value)) {?>
                    <img src="<?php echo $_smarty_tpl->tpl_vars['tpl_url_path']->value;?>
img/payments/<?php echo $_smarty_tpl->tpl_vars['language']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['module_data']->value['payment_code'];?>
.png" alt="<?php echo $_smarty_tpl->tpl_vars['module_data']->value['payment_name'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['module_data']->value['payment_name'];?>
" data-toggle="tooltip" />
                <?php } elseif (file_exists($_smarty_tpl->tpl_vars['image_system']->value)) {?>
                    <img src="<?php echo $_smarty_tpl->tpl_vars['tpl_url_path_system']->value;?>
img/payments/<?php echo $_smarty_tpl->tpl_vars['language']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['module_data']->value['payment_code'];?>
.png" alt="<?php echo $_smarty_tpl->tpl_vars['module_data']->value['payment_name'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['module_data']->value['payment_name'];?>
" data-toggle="tooltip" />
                <?php }?>
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        </div>
    </div>
<?php }
}
