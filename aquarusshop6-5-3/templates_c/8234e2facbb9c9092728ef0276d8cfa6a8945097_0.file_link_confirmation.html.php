<?php
/* Smarty version 5.4.1, created on 2024-12-03 17:04:25
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_rescission/templates/link_confirmation.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.1',
  'unifunc' => 'content_674f2c09356ad6_16659033',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '8234e2facbb9c9092728ef0276d8cfa6a8945097' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_rescission/templates/link_confirmation.html',
      1 => 1722634754,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_674f2c09356ad6_16659033 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_rescission/templates';
if ((defined('XT_RESCISSION_SHOW_CONFIRMATION') ? constant('XT_RESCISSION_SHOW_CONFIRMATION') : null) == 'true') {?>
    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('content')->handle(array('cont_id'=>8,'is_id'=>'false'), $_smarty_tpl);?>

    <p class="checkbox">
        <label>
            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'checkbox','name'=>'rescission_accepted','class'=>"xt-form-required"), $_smarty_tpl);?>

            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_RESCISSION_CONFIRMATION_1), $_smarty_tpl);?>
 <a href="<?php echo $_smarty_tpl->getValue('_content_8')['content_link'];?>
" target="_blank"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_RESCISSION_CONFIRMATION_2), $_smarty_tpl);?>
</a> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_RESCISSION_CONFIRMATION_3), $_smarty_tpl);?>

        </label>
    </p>
<?php } else { ?>
    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('content')->handle(array('cont_id'=>8,'is_id'=>'false'), $_smarty_tpl);?>

    <p><span class="glyphicon glyphicon-ok"></span> <strong><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_RESCISSION_CONFIRMATION_4), $_smarty_tpl);?>
 <a href="<?php echo $_smarty_tpl->getValue('_content_8')['content_link'];?>
" target="_blank"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_RESCISSION_CONFIRMATION_2), $_smarty_tpl);?>
</a><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_DOT), $_smarty_tpl);?>
</strong></p>
<?php }
}
}
