<?php
/* Smarty version 4.3.2, created on 2024-03-20 20:17:13
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/plugins/xt_rescission/templates/link_confirmation.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_65fb3639a50674_88964011',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '30e247ebfdd73995335f76d9ec4239f85459d89b' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/plugins/xt_rescission/templates/link_confirmation.html',
      1 => 1697144057,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_65fb3639a50674_88964011 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.content.php','function'=>'smarty_function_content',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.form.php','function'=>'smarty_function_form',),2=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),));
if ((defined('XT_RESCISSION_SHOW_CONFIRMATION') ? constant('XT_RESCISSION_SHOW_CONFIRMATION') : null) == 'true') {?>
    <?php echo smarty_function_content(array('cont_id'=>8,'is_id'=>'false'),$_smarty_tpl);?>

    <p class="checkbox">
        <label>
            <?php echo smarty_function_form(array('type'=>'checkbox','name'=>'rescission_accepted','class'=>"xt-form-required"),$_smarty_tpl);?>

            <?php echo smarty_function_txt(array('key'=>TEXT_RESCISSION_CONFIRMATION_1),$_smarty_tpl);?>
 <a href="<?php echo $_smarty_tpl->tpl_vars['_content_8']->value['content_link'];?>
" target="_blank"><?php echo smarty_function_txt(array('key'=>TEXT_RESCISSION_CONFIRMATION_2),$_smarty_tpl);?>
</a> <?php echo smarty_function_txt(array('key'=>TEXT_RESCISSION_CONFIRMATION_3),$_smarty_tpl);?>

        </label>
    </p>
<?php } else { ?>
    <?php echo smarty_function_content(array('cont_id'=>8,'is_id'=>'false'),$_smarty_tpl);?>

    <p><span class="glyphicon glyphicon-ok"></span> <strong><?php echo smarty_function_txt(array('key'=>TEXT_RESCISSION_CONFIRMATION_4),$_smarty_tpl);?>
 <a href="<?php echo $_smarty_tpl->tpl_vars['_content_8']->value['content_link'];?>
" target="_blank"><?php echo smarty_function_txt(array('key'=>TEXT_RESCISSION_CONFIRMATION_2),$_smarty_tpl);?>
</a><?php echo smarty_function_txt(array('key'=>TEXT_DOT),$_smarty_tpl);?>
</strong></p>
<?php }
}
}