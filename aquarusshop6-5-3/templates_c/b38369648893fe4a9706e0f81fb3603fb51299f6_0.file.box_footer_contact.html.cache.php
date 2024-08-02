<?php
/* Smarty version 4.3.2, created on 2024-08-02 22:56:57
  from '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes/box_footer_contact.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_66ad48197e5dc0_34556237',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'b38369648893fe4a9706e0f81fb3603fb51299f6' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes/box_footer_contact.html',
      1 => 1697144118,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_66ad48197e5dc0_34556237 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.content.php','function'=>'smarty_function_content',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/vendor/smarty/smarty/libs/plugins/modifier.count.php','function'=>'smarty_modifier_count',),2=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),));
$_smarty_tpl->compiled->nocache_hash = '85150400366ad48197e1179_44363637';
echo smarty_function_content(array('cont_id'=>5,'is_id'=>true),$_smarty_tpl);?>

<?php if (smarty_modifier_count($_smarty_tpl->tpl_vars['_content_5']->value) != 0 && $_smarty_tpl->tpl_vars['_content_5']->value['content_status'] == 1) {?>
    <div class="contact">
        <p class="headline"><?php echo $_smarty_tpl->tpl_vars['_content_5']->value['title'];?>
</p>
        <?php echo $_smarty_tpl->tpl_vars['_content_5']->value['content_body_short'];?>

        <?php if ($_smarty_tpl->tpl_vars['_content_5']->value['content_link']) {?>
            <p><a href="<?php echo $_smarty_tpl->tpl_vars['_content_5']->value['content_link'];?>
"><i class="fa fa-envelope-o"></i> <?php echo smarty_function_txt(array('key'=>TEXT_CONTACT_PAGE),$_smarty_tpl);?>
</a></p>
        <?php }?>
    </div>
<?php }
}
}
