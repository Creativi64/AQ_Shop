<?php
/* Smarty version 5.1.0, created on 2024-09-10 02:14:56
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/content.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_66df8f801e0746_79836191',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '794a30292455eb70c59ae30e5c9748906f929b79' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/content.html',
      1 => 1697144119,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_66df8f801e0746_79836191 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages';
if (!(null !== ($_smarty_tpl->getValue('disable_content_container') ?? null))) {
if ($_smarty_tpl->getSmarty()->getModifierCallback('trim')($_smarty_tpl->getValue('data')['content_image']) != '') {?>
    <div class="full-width-image content-image img-thumbnail">
        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('img')->handle(array('img'=>$_smarty_tpl->getValue('data')['content_image'],'type'=>'m_org','class'=>"img-responsive",'alt'=>htmlspecialchars((string)$_smarty_tpl->getValue('data')['title'], ENT_QUOTES, 'UTF-8', true)), $_smarty_tpl);?>

    </div>
<?php }?>
<h1><?php echo $_smarty_tpl->getValue('data')['title'];?>
</h1>
<?php if ($_smarty_tpl->getValue('file')) {?>
    <?php echo $_smarty_tpl->getValue('file');?>

<?php } else { ?>
    <div class="textstyles">
        <?php echo $_smarty_tpl->getValue('data')['content_body'];?>

    </div>
<?php }?>
<div class="clearfix"></div>
<?php if ($_smarty_tpl->getValue('data')['subcontent']) {?>
    <hr />
    <p class="h4"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>HEADING_SUB_CONTENT), $_smarty_tpl);?>
</p>
    <ul class="list-inline">
        <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('data')['subcontent'], 'module_data', false, NULL, 'aussen', array (
));
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('module_data')->value) {
$foreach0DoElse = false;
?>
            <li>
                <a href="<?php echo $_smarty_tpl->getValue('module_data')['link'];?>
" class="btn btn-secondary">
                    <i class="fa fa-info-circle"></i>
                    <?php echo $_smarty_tpl->getSmarty()->getModifierCallback('trim')($_smarty_tpl->getValue('module_data')['title']);?>

                </a>
            </li>
        <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
    </ul>
    <br />
<?php }
}
}
}
