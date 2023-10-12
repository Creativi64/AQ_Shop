<?php
/* Smarty version 4.3.0, created on 2023-06-19 13:53:20
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive/xtCore/boxes/box_language.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.0',
  'unifunc' => 'content_649041b006ddb5_41319555',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '68197d7a7f34a5ed97e0eec3bf145840b3646e6c' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive/xtCore/boxes/box_language.html',
      1 => 1687006095,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_649041b006ddb5_41319555 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.form.php','function'=>'smarty_function_form',),));
?>
<div class="language">
    <p class="headline"><?php echo smarty_function_txt(array('key'=>TEXT_LANGUAGE),$_smarty_tpl);?>
</p>
    <?php echo smarty_function_form(array('type'=>'form','name'=>'language','method'=>'post'),$_smarty_tpl);?>
    <select name="new_lang" onchange="location.href=this.options[this.selectedIndex].value" class="show-tick form-control">
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['lang_data']->value, 'box_data', false, NULL, 'aussen', array (
));
$_smarty_tpl->tpl_vars['box_data']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['box_data']->value) {
$_smarty_tpl->tpl_vars['box_data']->do_else = false;
?>

            <?php $_smarty_tpl->_assignInScope('lang_switch_params', '?language=');?>
            <?php if (strstr($_smarty_tpl->tpl_vars['box_data']->value['link'],'?')) {?>
                <?php $_smarty_tpl->_assignInScope('lang_switch_params', '&language=');?>
            <?php }?>

            <?php if (!$_smarty_tpl->tpl_vars['selected_lang']->value) {?>                <option data-content="<span class='option-title'><?php echo smarty_function_txt(array('key'=>TEXT_LANGUAGE),$_smarty_tpl);?>
: </span><?php echo $_smarty_tpl->tpl_vars['box_data']->value['name'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['box_data']->value['link'];
echo $_smarty_tpl->tpl_vars['lang_switch_params']->value;
echo $_smarty_tpl->tpl_vars['box_data']->value['code'];?>
"<?php if ($_smarty_tpl->tpl_vars['language']->value == $_smarty_tpl->tpl_vars['box_data']->value['code']) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['box_data']->value['name'];?>
</option>
            <?php } else { ?>                <option data-content="<span class='option-title'><?php echo smarty_function_txt(array('key'=>TEXT_LANGUAGE),$_smarty_tpl);?>
: </span><?php echo $_smarty_tpl->tpl_vars['box_data']->value['name'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['box_data']->value['link'];
echo $_smarty_tpl->tpl_vars['lang_switch_params']->value;
echo $_smarty_tpl->tpl_vars['box_data']->value['code'];?>
"<?php if ($_smarty_tpl->tpl_vars['selected_lang']->value == $_smarty_tpl->tpl_vars['box_data']->value['code']) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['box_data']->value['name'];?>
</option>
            <?php }?>
        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    </select>
    <?php echo smarty_function_form(array('type'=>'formend'),$_smarty_tpl);?>

</div><?php }
}
