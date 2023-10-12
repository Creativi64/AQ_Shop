<?php
/* Smarty version 4.3.0, created on 2023-06-19 13:53:20
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive/xtCore/boxes/box_footer_right.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.0',
  'unifunc' => 'content_649041b0287745_74675933',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5818bcf502747936f4adb0ce14aed9ea3c74d24f' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive/xtCore/boxes/box_footer_right.html',
      1 => 1687006095,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_649041b0287745_74675933 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.content.php','function'=>'smarty_function_content',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/vendor/smarty/smarty/libs/plugins/modifier.count.php','function'=>'smarty_modifier_count',),2=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),));
$_smarty_tpl->compiled->nocache_hash = '1744857043649041b027f0f9_48560100';
echo smarty_function_content(array('block_id'=>9,'levels'=>'nested'),$_smarty_tpl);?>

<?php if (smarty_modifier_count($_smarty_tpl->tpl_vars['_content_9']->value) > 0) {?>
    <div class="content">
        <p class="headline"><?php echo smarty_function_txt(array('key'=>TEXT_CONTENT),$_smarty_tpl);?>
</p>
        <ul>
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['_content_9']->value, 'box_data', false, NULL, 'aussen', array (
));
$_smarty_tpl->tpl_vars['box_data']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['box_data']->value) {
$_smarty_tpl->tpl_vars['box_data']->do_else = false;
?>
                <li class="level<?php echo $_smarty_tpl->tpl_vars['box_data']->value['level'];
if ($_smarty_tpl->tpl_vars['box_data']->value['active']) {?> active<?php }?>"><a href="<?php echo $_smarty_tpl->tpl_vars['box_data']->value['link'];?>
" <?php if ($_smarty_tpl->tpl_vars['page']->value != 'index' && $_smarty_tpl->tpl_vars['box_data']->value['content_hook'] > 0) {?> rel="nofollow"<?php }?>><?php echo $_smarty_tpl->tpl_vars['box_data']->value['title'];?>
</a></li>
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        </ul>
    </div>
<?php }
}
}
