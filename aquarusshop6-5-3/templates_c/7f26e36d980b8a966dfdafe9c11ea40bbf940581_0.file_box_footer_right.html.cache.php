<?php
/* Smarty version 5.1.0, created on 2024-08-03 01:07:41
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes/box_footer_right.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_66ad66bdaa59f1_98055014',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '7f26e36d980b8a966dfdafe9c11ea40bbf940581' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes/box_footer_right.html',
      1 => 1697144118,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_66ad66bdaa59f1_98055014 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes';
$_smarty_tpl->getCompiled()->nocache_hash = '30347285966ad66bda9e143_66385120';
echo $_smarty_tpl->getSmarty()->getFunctionHandler('content')->handle(array('block_id'=>9,'levels'=>'nested'), $_smarty_tpl);?>

<?php if ($_smarty_tpl->getSmarty()->getModifierCallback('count')($_smarty_tpl->getValue('_content_9')) > 0) {?>
    <div class="content">
        <p class="headline"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_CONTENT), $_smarty_tpl);?>
</p>
        <ul>
            <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('_content_9'), 'box_data', false, NULL, 'aussen', array (
));
$foreach10DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('box_data')->value) {
$foreach10DoElse = false;
?>
                <li class="level<?php echo $_smarty_tpl->getValue('box_data')['level'];
if ($_smarty_tpl->getValue('box_data')['active']) {?> active<?php }?>"><a href="<?php echo $_smarty_tpl->getValue('box_data')['link'];?>
" <?php if ($_smarty_tpl->getValue('page') != 'index' && $_smarty_tpl->getValue('box_data')['content_hook'] > 0) {?> rel="nofollow"<?php }?>><?php echo $_smarty_tpl->getValue('box_data')['title'];?>
</a></li>
            <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
        </ul>
    </div>
<?php }
}
}
