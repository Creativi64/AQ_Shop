<?php
/* Smarty version 4.3.2, created on 2023-12-20 20:51:52
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive_AQ/xtCore/boxes/box_footer_contact.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_658345d8d8ebd9_32153011',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '8dc84a7b7210b051ee32653cf5a549164e0e9d16' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive_AQ/xtCore/boxes/box_footer_contact.html',
      1 => 1697144118,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_658345d8d8ebd9_32153011 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.content.php','function'=>'smarty_function_content',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/vendor/smarty/smarty/libs/plugins/modifier.count.php','function'=>'smarty_modifier_count',),2=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),));
$_smarty_tpl->compiled->nocache_hash = '20590750658345d8d89a11_97774043';
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
