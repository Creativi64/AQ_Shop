<?php
/* Smarty version 5.1.0, created on 2024-08-03 01:07:41
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/navigation/nav_pages.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_66ad66bd71a178_08700163',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '7cc20f2e96101a13f3fc0f1b97e7bb6bce471baf' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/navigation/nav_pages.html',
      1 => 1697144245,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_66ad66bd71a178_08700163 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/navigation';
?><div class="nav-pages btn-group">
    <?php if ($_smarty_tpl->getValue('pages') && $_smarty_tpl->getSmarty()->getModifierCallback('count')($_smarty_tpl->getValue('pages')) > 1) {?>
        <?php $_smarty_tpl->assign('padding', 2, false, NULL);?>

        <?php if ($_smarty_tpl->getValue('actual_page') == 1) {?>
            <button class="btn btn-default prev" disabled="disabled" type="button">
                <i class="fa fa-chevron-left"></i>
                <span class="sr-only">Prev</span>
            </button>
        <?php } else { ?>
            <a class="btn btn-default prev" role="button" href="<?php echo $_smarty_tpl->getValue('prev');?>
" rel="prev">
                <i class="fa fa-chevron-left"></i>
                <span class="sr-only">Prev</span>
            </a>
        <?php }?>

        <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('pages'), 'page_link', false, 'pager_number');
$foreach2DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('pager_number')->value => $_smarty_tpl->getVariable('page_link')->value) {
$foreach2DoElse = false;
?>        
            <?php if ($_smarty_tpl->getValue('pager_number') == 1) {?>
                            <a class="btn btn-default<?php if ($_smarty_tpl->getValue('pager_number') == $_smarty_tpl->getValue('actual_page')) {?> active<?php }?>" role="button" href="<?php echo $_smarty_tpl->getValue('page_link');?>
"<?php if ($_smarty_tpl->getValue('pager_number') == $_smarty_tpl->getValue('actual_page')-1) {?> rel="prev"<?php }?>><?php echo $_smarty_tpl->getValue('pager_number');?>
</a>
            <?php }?>           
            <?php if ($_smarty_tpl->getValue('actual_page')-$_smarty_tpl->getValue('padding') == $_smarty_tpl->getValue('pager_number') && $_smarty_tpl->getValue('actual_page')-$_smarty_tpl->getValue('padding') > 1 && $_smarty_tpl->getValue('actual_page')-$_smarty_tpl->getValue('padding')-1 > 1) {?>
                                        <button class="btn btn-default dots" disabled="disabled" type="button">...</button>
            <?php }?>
            <?php if ($_smarty_tpl->getValue('pager_number') != 1 && $_smarty_tpl->getValue('pager_number') != $_smarty_tpl->getValue('last_page') && $_smarty_tpl->getValue('pager_number') >= $_smarty_tpl->getValue('actual_page')-$_smarty_tpl->getValue('padding') && $_smarty_tpl->getValue('pager_number') <= $_smarty_tpl->getValue('actual_page')+$_smarty_tpl->getValue('padding')) {?>
                <a class="btn btn-default<?php if ($_smarty_tpl->getValue('pager_number') == $_smarty_tpl->getValue('actual_page')) {?> active<?php }?>" role="button" href="<?php echo $_smarty_tpl->getValue('page_link');?>
"<?php if ($_smarty_tpl->getValue('pager_number') == $_smarty_tpl->getValue('actual_page')-1) {?> rel="prev"<?php } elseif ($_smarty_tpl->getValue('pager_number') == $_smarty_tpl->getValue('actual_page')+1) {?> rel="next"<?php }?>><?php echo $_smarty_tpl->getValue('pager_number');?>
</a>
            <?php }?>         
            <?php if ($_smarty_tpl->getValue('actual_page')+$_smarty_tpl->getValue('padding') == $_smarty_tpl->getValue('pager_number') && $_smarty_tpl->getValue('actual_page')+$_smarty_tpl->getValue('padding') < $_smarty_tpl->getValue('last_page') && $_smarty_tpl->getValue('actual_page')+$_smarty_tpl->getValue('padding')+1 < $_smarty_tpl->getValue('last_page')) {?>
                            <button class="btn btn-default dots" disabled="disabled" type="button">...</button>
            <?php }?>        
            <?php if ($_smarty_tpl->getValue('pager_number') == $_smarty_tpl->getValue('last_page')) {?>
            <a class="btn btn-default<?php if ($_smarty_tpl->getValue('pager_number') == $_smarty_tpl->getValue('actual_page')) {?> active<?php }?>" role="button" href="<?php echo $_smarty_tpl->getValue('page_link');?>
"<?php if ($_smarty_tpl->getValue('pager_number') == $_smarty_tpl->getValue('actual_page')+1) {?> rel="next"<?php }?>><?php echo $_smarty_tpl->getValue('pager_number');?>
</a>
<?php }?>
        <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>

        <?php if ($_smarty_tpl->getValue('actual_page') == $_smarty_tpl->getValue('last_page')) {?>
            <button class="btn btn-default next" disabled="disabled" type="button">
                <i class="fa fa-chevron-right"></i>
                <span class="sr-only">Next</span>
            </button>
        <?php } else { ?>
            <a class="btn btn-default next" role="button" href="<?php echo $_smarty_tpl->getValue('next');?>
" rel="next">
                <i class="fa fa-chevron-right"></i>
                <span class="sr-only">Next</span>
            </a>
        <?php }?>
    <?php } else { ?>
        <button class="btn btn-default one-page" disabled="disabled" type="button"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PAGINATION_TITLE), $_smarty_tpl);?>
 <?php echo $_smarty_tpl->getValue('actual_page');?>
 <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PAGINATION_FROM), $_smarty_tpl);?>
 <?php echo $_smarty_tpl->getValue('last_page');?>
</button>
    <?php }?>
</div>
<?php }
}
