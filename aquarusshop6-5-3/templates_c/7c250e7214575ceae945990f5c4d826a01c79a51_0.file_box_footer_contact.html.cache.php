<?php
/* Smarty version 5.1.0, created on 2024-08-03 01:07:41
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes/box_footer_contact.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_66ad66bda64b22_94969764',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '7c250e7214575ceae945990f5c4d826a01c79a51' => 
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
function content_66ad66bda64b22_94969764 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes';
$_smarty_tpl->getCompiled()->nocache_hash = '102070516866ad66bda5f595_60887114';
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
