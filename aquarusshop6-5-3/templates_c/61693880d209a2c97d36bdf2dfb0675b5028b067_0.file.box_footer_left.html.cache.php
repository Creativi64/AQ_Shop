<?php
/* Smarty version 4.3.2, created on 2023-12-20 20:51:52
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive_AQ/xtCore/boxes/box_footer_left.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_658345d8db4935_34635254',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '61693880d209a2c97d36bdf2dfb0675b5028b067' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive_AQ/xtCore/boxes/box_footer_left.html',
      1 => 1697144118,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_658345d8db4935_34635254 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.content.php','function'=>'smarty_function_content',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/vendor/smarty/smarty/libs/plugins/modifier.count.php','function'=>'smarty_modifier_count',),2=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),));
$_smarty_tpl->compiled->nocache_hash = '1984729183658345d8da8e67_85309719';
echo smarty_function_content(array('block_id'=>6,'levels'=>'nested'),$_smarty_tpl);?>

<?php if (smarty_modifier_count($_smarty_tpl->tpl_vars['_content_6']->value) > 0) {?>
    <div class="info">
        <p class="headline"><?php echo smarty_function_txt(array('key'=>TEXT_INFO),$_smarty_tpl);?>
</p>
        <ul>
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['_content_6']->value, 'box_data', false, NULL, 'aussen', array (
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
