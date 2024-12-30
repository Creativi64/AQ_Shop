<?php
/* Smarty version 5.4.1, created on 2024-12-30 01:21:05
  from 'file:includes/categorie_listing.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.1',
  'unifunc' => 'content_6771e77178fff9_82341729',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '089731394a1f3e7949ace1e569219a8b49d0be95' => 
    array (
      0 => 'includes/categorie_listing.html',
      1 => 1697144063,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6771e77178fff9_82341729 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/includes';
if ($_smarty_tpl->getValue('categorie_listing')) {?>
    <div class="listing categorie-listing">
        <?php if ($_smarty_tpl->getValue('position') == 'index') {?>
            <p class="h2 index-heading"><?php echo $_smarty_tpl->getValue('heading');?>
</p>
        <?php } else { ?>
            <p class="h3"><?php echo $_smarty_tpl->getValue('heading');?>
</p>
        <?php }?>
        <div class="row">
            <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('categorie_listing'), 'module_data');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('module_data')->value) {
$foreach0DoElse = false;
?>
                <div class="col col-xs-6 col-sm-6 col-md-4 col-lg-4">
                    <div class="section text-center">
                        <?php if ($_smarty_tpl->getValue('module_data')['categories_image']) {?>
                            <p class="image">
                                <a href="<?php echo $_smarty_tpl->getValue('module_data')['categories_link'];?>
" class="vertical-helper image-link"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('img')->handle(array('img'=>$_smarty_tpl->getValue('module_data')['categories_image'],'type'=>'m_category_thumb','class'=>"productImageBorder img-responsive",'alt'=>htmlspecialchars((string)$_smarty_tpl->getValue('module_data')['categories_name'], ENT_QUOTES, 'UTF-8', true)), $_smarty_tpl);?>
</a>
                            </p>
                        <?php }?>
                        <p class="title text-word-wrap">
                            <a href="<?php echo $_smarty_tpl->getValue('module_data')['categories_link'];?>
"><?php echo $_smarty_tpl->getValue('module_data')['categories_name'];?>
</a>
                        </p>
                    </div>
                </div><!-- .col -->
            <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
        </div>
    </div>
<?php }
}
}
