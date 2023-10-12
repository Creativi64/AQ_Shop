<?php
/* Smarty version 4.3.0, created on 2023-06-19 13:53:20
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive/xtCore/boxes/box_footer_contact.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.0',
  'unifunc' => 'content_649041b01ceca5_27397371',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '894372d265e553817d6813a133308feaf2f2d3ee' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive/xtCore/boxes/box_footer_contact.html',
      1 => 1687006095,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_649041b01ceca5_27397371 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.content.php','function'=>'smarty_function_content',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/vendor/smarty/smarty/libs/plugins/modifier.count.php','function'=>'smarty_modifier_count',),2=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),));
$_smarty_tpl->compiled->nocache_hash = '1459685526649041b01ba626_21931165';
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
