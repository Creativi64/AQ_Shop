<?php
/* Smarty version 5.1.0, created on 2024-09-09 19:14:52
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes/box_footer_contact.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_66df2d0ca1c1e0_91240793',
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
))) {
function content_66df2d0ca1c1e0_91240793 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes';
$_smarty_tpl->getCompiled()->nocache_hash = '116439483266df2d0ca15aa8_81540756';
echo $_smarty_tpl->getSmarty()->getFunctionHandler('content')->handle(array('cont_id'=>5,'is_id'=>true), $_smarty_tpl);?>

<?php if ($_smarty_tpl->getSmarty()->getModifierCallback('count')($_smarty_tpl->getValue('_content_5')) != 0 && $_smarty_tpl->getValue('_content_5')['content_status'] == 1) {?>
    <div class="contact">
        <p class="headline"><?php echo $_smarty_tpl->getValue('_content_5')['title'];?>
</p>
        <?php echo $_smarty_tpl->getValue('_content_5')['content_body_short'];?>

        <?php if ($_smarty_tpl->getValue('_content_5')['content_link']) {?>
            <p><a href="<?php echo $_smarty_tpl->getValue('_content_5')['content_link'];?>
"><i class="fa fa-envelope-o"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_CONTACT_PAGE), $_smarty_tpl);?>
</a></p>
        <?php }?>
    </div>
<?php }
}
}
