<?php
/* Smarty version 5.1.0, created on 2024-08-03 01:07:41
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes/box_footer_left.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_66ad66bda835b6_91514634',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '80675ad2e343f0d418d5da3d35ca19be735ba026' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes/box_footer_left.html',
      1 => 1697144118,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_66ad66bda835b6_91514634 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes';
$_smarty_tpl->getCompiled()->nocache_hash = '182645545766ad66bda7bdb5_32414218';
echo $_smarty_tpl->getSmarty()->getFunctionHandler('content')->handle(array('block_id'=>6,'levels'=>'nested'), $_smarty_tpl);?>

<?php if ($_smarty_tpl->getSmarty()->getModifierCallback('count')($_smarty_tpl->getValue('_content_6')) > 0) {?>
    <div class="info">
        <p class="headline"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_INFO), $_smarty_tpl);?>
</p>
        <ul>
            <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('_content_6'), 'box_data', false, NULL, 'aussen', array (
));
$foreach9DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('box_data')->value) {
$foreach9DoElse = false;
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
