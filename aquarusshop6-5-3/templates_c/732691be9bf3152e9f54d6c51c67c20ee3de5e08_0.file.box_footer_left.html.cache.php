<?php
/* Smarty version 4.3.2, created on 2024-05-08 20:07:28
  from '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes/box_footer_left.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_663bbf60e72562_76357098',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '732691be9bf3152e9f54d6c51c67c20ee3de5e08' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes/box_footer_left.html',
      1 => 1697144118,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_663bbf60e72562_76357098 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.content.php','function'=>'smarty_function_content',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/vendor/smarty/smarty/libs/plugins/modifier.count.php','function'=>'smarty_modifier_count',),2=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),));
$_smarty_tpl->compiled->nocache_hash = '1794619806663bbf60e6c687_20852963';
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
