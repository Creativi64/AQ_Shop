<?php
/* Smarty version 4.3.2, created on 2024-03-20 20:20:51
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/plugins/xt_paypal_checkout/templates/xt_paypal_checkout.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_65fb37136e88b5_80470803',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'fb2475fe7b585fe532973ac6bff308bd35758303' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/plugins/xt_paypal_checkout/templates/xt_paypal_checkout.tpl.html',
      1 => 1710347938,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_65fb37136e88b5_80470803 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.img.php','function'=>'smarty_function_img',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.form.php','function'=>'smarty_function_form',),));
if (trim($_smarty_tpl->tpl_vars['payment_name']->value) != '' && trim($_smarty_tpl->tpl_vars['payment_code']->value) != '') {?>
    <div id="<?php echo $_smarty_tpl->tpl_vars['payment_code']->value;?>
" class="item item-<?php echo $_smarty_tpl->tpl_vars['payment_code']->value;?>
 <?php if ($_smarty_tpl->tpl_vars['payment_code']->value == $_smarty_tpl->tpl_vars['payment_selected']->value) {?> selected<?php }?> payment-container"
        style="<?php if ($_smarty_tpl->tpl_vars['payment_code']->value == 'applepay') {?>display:none<?php }?>">
        <?php if (trim($_smarty_tpl->tpl_vars['payment_icon']->value) != '') {?>
            <?php echo smarty_function_img(array('img'=>$_smarty_tpl->tpl_vars['payment_icon']->value,'type'=>'w_media_payment','alt'=>$_smarty_tpl->tpl_vars['payment_name']->value,'class'=>"icon img-responsive img-thumbnail___ pull-right",'style'=>"margin-top:-15px;"),$_smarty_tpl);?>

        <?php }?>
        <header data-toggle="collapse" data-target=".item-<?php echo $_smarty_tpl->tpl_vars['payment_code']->value;?>
 .collapse">
            <label class="cursor-pointer">
                <span class="check" style="display:inline-block;width: 25px;">
                    <?php if ($_smarty_tpl->tpl_vars['payment_hidden']->value == true) {?>
                        <?php echo smarty_function_form(array('type'=>'hidden','name'=>'selected_payment','value'=>((string)$_smarty_tpl->tpl_vars['payment_code']->value)),$_smarty_tpl);?>

                    <?php } else { ?>
                        <?php if ($_smarty_tpl->tpl_vars['payment_code']->value == $_smarty_tpl->tpl_vars['payment_selected']->value) {?>
                            <?php echo smarty_function_form(array('type'=>'radio','name'=>'selected_payment','value'=>((string)$_smarty_tpl->tpl_vars['payment_code']->value),'checked'=>true,'style'=>"vertical-align: text-top;"),$_smarty_tpl);?>

                        <?php } else { ?>
                            <?php echo smarty_function_form(array('type'=>'radio','name'=>'selected_payment','value'=>((string)$_smarty_tpl->tpl_vars['payment_code']->value),'style'=>"vertical-align: text-top;"),$_smarty_tpl);?>

                        <?php }?>
                    <?php }?>
                </span>
                <span class="name payment-name"><?php echo $_smarty_tpl->tpl_vars['payment_name']->value;?>
</span>
                <?php if ($_smarty_tpl->tpl_vars['payment_price']->value['formated']) {?>
                    <small class="price">&nbsp;<?php echo $_smarty_tpl->tpl_vars['payment_price']->value['formated'];?>
</small>
                <?php }?>
            </label>
        </header>
        <?php if (trim($_smarty_tpl->tpl_vars['payment_desc']->value) != '') {?>
            <div class="desc collapse<?php if ($_smarty_tpl->tpl_vars['payment_code']->value == $_smarty_tpl->tpl_vars['payment_selected']->value) {?> in<?php }?> payment-desc">
                <?php echo $_smarty_tpl->tpl_vars['payment_desc']->value;?>

            </div>
        <?php }?>
    </div>
    <?php echo '<script'; ?>
>
        {
            if("<?php echo $_smarty_tpl->tpl_vars['payment_code']->value;?>
" != "xt_paypal_checkout_pui")
            {
                let me = document.getElementById("<?php echo $_smarty_tpl->tpl_vars['payment_code']->value;?>
");
                if (me) {
                    let parent = me.closest(".list-group-item");
                    if (parent) {
                        parent.style.display = "none";
                    }
                }
            }
        }
    <?php echo '</script'; ?>
>
<?php }?>

<?php }
}
