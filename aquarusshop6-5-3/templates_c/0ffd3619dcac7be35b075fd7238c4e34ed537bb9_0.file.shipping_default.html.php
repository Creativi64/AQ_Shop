<?php
/* Smarty version 4.3.2, created on 2024-07-22 18:41:22
  from '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/shipping/shipping_default.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_669e8bb2c1cd16_56037392',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '0ffd3619dcac7be35b075fd7238c4e34ed537bb9' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/shipping/shipping_default.html',
      1 => 1697144246,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_669e8bb2c1cd16_56037392 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.img.php','function'=>'smarty_function_img',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.form.php','function'=>'smarty_function_form',),2=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.hook.php','function'=>'smarty_function_hook',),));
if (trim($_smarty_tpl->tpl_vars['shipping_name']->value) != '' && trim($_smarty_tpl->tpl_vars['shipping_code']->value) != '') {?>
    <div id="shipping_<?php echo $_smarty_tpl->tpl_vars['shipping_id']->value;?>
" class="item item-<?php echo $_smarty_tpl->tpl_vars['shipping_code']->value;
if ($_smarty_tpl->tpl_vars['shipping_code']->value == $_smarty_tpl->tpl_vars['shipping_selected']->value) {?> selected<?php }?>"<?php if (trim($_smarty_tpl->tpl_vars['shipping_desc']->value) != '') {?> data-toggle="collapse" data-target=".item-<?php echo $_smarty_tpl->tpl_vars['shipping_code']->value;?>
 .collapse"<?php }?>>
        <?php if (trim($_smarty_tpl->tpl_vars['shipping_icon']->value) != '') {?>
            <?php echo smarty_function_img(array('img'=>$_smarty_tpl->tpl_vars['shipping_icon']->value,'type'=>'w_media_shipping','alt'=>$_smarty_tpl->tpl_vars['shipping_name']->value,'class'=>"icon img-responsive img-thumbnail pull-right"),$_smarty_tpl);?>

        <?php }?>
        <header>
            <label class="cursor-pointer">
                <span class="check" style="display:inline-block;width: 25px;">
                    <?php if ($_smarty_tpl->tpl_vars['shipping_hidden']->value == true) {?>
                        <?php echo smarty_function_form(array('type'=>'hidden','name'=>'selected_shipping','value'=>$_smarty_tpl->tpl_vars['shipping_code']->value),$_smarty_tpl);?>

                    <?php } else { ?>
                        <?php if ($_smarty_tpl->tpl_vars['shipping_code']->value == $_smarty_tpl->tpl_vars['shipping_selected']->value) {?>
                            <?php echo smarty_function_form(array('type'=>'radio','name'=>'selected_shipping','value'=>$_smarty_tpl->tpl_vars['shipping_code']->value,'checked'=>true),$_smarty_tpl);?>

                        <?php } else { ?>
                            <?php echo smarty_function_form(array('type'=>'radio','name'=>'selected_shipping','value'=>$_smarty_tpl->tpl_vars['shipping_code']->value),$_smarty_tpl);?>

                        <?php }?>
                    <?php }?>
                </span>
                <span class="name"><?php echo $_smarty_tpl->tpl_vars['shipping_name']->value;?>
</span>
                <?php if ($_smarty_tpl->tpl_vars['shipping_price']->value['formated']) {?>
                    <small class="price">&nbsp;<?php echo $_smarty_tpl->tpl_vars['shipping_price']->value['formated'];?>
</small>
                <?php }?>
            </label>
        </header>
        <?php if (trim($_smarty_tpl->tpl_vars['shipping_desc']->value) != '') {?>
            <div class="desc collapse<?php if ($_smarty_tpl->tpl_vars['shipping_code']->value == $_smarty_tpl->tpl_vars['shipping_selected']->value) {?> in<?php }?>">
                <?php echo $_smarty_tpl->tpl_vars['shipping_desc']->value;?>

                <?php echo smarty_function_hook(array('key'=>'shipping_default_shipping_desc'),$_smarty_tpl);?>

            </div>
        <?php }?>
    </div>
<?php }
}
}
