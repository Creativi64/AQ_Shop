<?php
/* Smarty version 5.1.0, created on 2024-12-02 18:49:39
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes/box_categories_recursive.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_674df33325e554_02832991',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'b33968c4811222c190ba29541d1efb824bab610a' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes/box_categories_recursive.html',
      1 => 1697144118,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_674df33325e554_02832991 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes';
$_smarty_tpl->getCompiled()->nocache_hash = '207970446674df3332212f7_89809946';
if ($_smarty_tpl->getValue('_categories')) {?>
    <?php if ($_smarty_tpl->getValue('params')['position'] == 'navbar') {?>

        <ul class="nav navbar-nav">
            <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('_categories'), 'i', false, 'k', 'a', array (
  'first' => true,
  'last' => true,
  'index' => true,
  'iteration' => true,
  'total' => true,
));
$foreach5DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('k')->value => $_smarty_tpl->getVariable('i')->value) {
$foreach5DoElse = false;
$_smarty_tpl->tpl_vars['__smarty_foreach_a']->value['iteration']++;
$_smarty_tpl->tpl_vars['__smarty_foreach_a']->value['index']++;
$_smarty_tpl->tpl_vars['__smarty_foreach_a']->value['first'] = !$_smarty_tpl->tpl_vars['__smarty_foreach_a']->value['index'];
$_smarty_tpl->tpl_vars['__smarty_foreach_a']->value['last'] = $_smarty_tpl->tpl_vars['__smarty_foreach_a']->value['iteration'] === $_smarty_tpl->tpl_vars['__smarty_foreach_a']->value['total'];
?>
                <li id="cid-<?php echo $_smarty_tpl->getValue('i')['categories_id'];?>
" class="level-<?php echo $_smarty_tpl->getValue('i')['level'];?>
 lang-<?php echo $_smarty_tpl->getValue('language');
if ($_smarty_tpl->getValue('i')['active'] == 1) {?> active current<?php }
if (($_smarty_tpl->getValue('__smarty_foreach_a')['first'] ?? null)) {?> first<?php }
if (($_smarty_tpl->getValue('__smarty_foreach_a')['last'] ?? null)) {?> last<?php }
if ($_smarty_tpl->getValue('i')['sub']) {?> dropdown<?php if ($_smarty_tpl->getValue('params')['mega'] == 'true') {?> mega-dropdown<?php }
}?>">
                    <a class="dropdown-toggle" href="<?php echo $_smarty_tpl->getValue('i')['categories_link'];?>
"<?php if ($_smarty_tpl->getValue('i')['sub']) {?> data-toggle="dropdown" data-hover="dropdown"<?php }?>>
                        <?php echo htmlspecialchars((string)$_smarty_tpl->getValue('i')['categories_name'], ENT_QUOTES, 'UTF-8', true);?>

                        <?php if ($_smarty_tpl->getValue('i')['sub']) {?><b class="caret"></b><?php }?>
                    </a>
                    <?php if ($_smarty_tpl->getValue('i')['sub']) {?>
                        <ul class="dropdown-menu">
                            <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('i')['sub'], 'i1', false, 'k1', 'a1', array (
));
$foreach6DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('k1')->value => $_smarty_tpl->getVariable('i1')->value) {
$foreach6DoElse = false;
?>
                                <?php if ($_smarty_tpl->getValue('params')['mega'] == 'true') {?>
                                    <li class="level-<?php echo $_smarty_tpl->getValue('i1')['level'];?>
">
                                        <a href="<?php echo $_smarty_tpl->getValue('i1')['categories_link'];?>
" class="title"><?php echo htmlspecialchars((string)$_smarty_tpl->getValue('i1')['categories_name'], ENT_QUOTES, 'UTF-8', true);?>
</a>
                                        <?php if ($_smarty_tpl->getValue('i1')['sub']) {?>
                                            <ul class="<?php if ($_smarty_tpl->getValue('i1')['level'] >= 2) {?>hidden-xs <?php }?>level-<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('math')->handle(array('equation'=>"x+1",'x'=>$_smarty_tpl->getValue('i1')['level']), $_smarty_tpl);?>
">
                                                <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('i1')['sub'], 'i2', false, 'k2', 'a2', array (
));
$foreach7DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('k2')->value => $_smarty_tpl->getVariable('i2')->value) {
$foreach7DoElse = false;
?>
                                                    <li class="level-<?php echo $_smarty_tpl->getValue('i2')['level'];?>
">
                                                        <a href="<?php echo $_smarty_tpl->getValue('i2')['categories_link'];?>
"><?php echo htmlspecialchars((string)$_smarty_tpl->getValue('i2')['categories_name'], ENT_QUOTES, 'UTF-8', true);?>
</a>
                                                    </li>
                                                <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
                                            </ul>
                                        <?php }?>
                                    </li>
                                <?php } else { ?>
                                    <li>
                                        <a href="<?php echo $_smarty_tpl->getValue('i1')['categories_link'];?>
"><?php echo htmlspecialchars((string)$_smarty_tpl->getValue('i1')['categories_name'], ENT_QUOTES, 'UTF-8', true);?>
</a>
                                    </li>
                                <?php }?>
                            <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
                            <li class="static divider hidden-float-breakpoint"></li>
                            <li class="static">
                                <a class="dropdown-header" href="<?php echo $_smarty_tpl->getValue('i')['categories_link'];?>
">
                                    <i class="fa fa-caret-right"></i>&nbsp;
                                    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_MORE_DETAILS), $_smarty_tpl);?>
:&nbsp;
                                    <span class="text-uppercase text-primary"><?php echo htmlspecialchars((string)$_smarty_tpl->getValue('i')['categories_name'], ENT_QUOTES, 'UTF-8', true);?>
</span>
                                </a>
                            </li>
                        </ul>
                    <?php }?>
                </li>
            <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
        </ul>

    <?php } else { ?>

        <div class="<?php echo $_smarty_tpl->getValue('params')['name'];?>
 <?php echo $_smarty_tpl->getValue('params')['position'];?>
 cid-<?php echo $_smarty_tpl->getValue('current_parent_id');?>
 box-categories panel panel-default text-word-wrap">
            <div class="panel-heading">
                <p class="panel-title text-uppercase">
                    <i class="fa fa-bars"></i>
                    <?php if ($_smarty_tpl->getValue('current_parent_data')) {?>
                        <a href="<?php echo $_smarty_tpl->getValue('current_parent_data')['categories_link'];?>
"><?php echo $_smarty_tpl->getValue('current_parent_data')['categories_name'];?>
</a>
                    <?php } else { ?>
                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_BOX_TITLE_CATEGORIES), $_smarty_tpl);?>

                    <?php }?>
                </p>
            </div>
            <ul class="nav nav-pills nav-stacked">
                <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('_categories'), 'module_data', false, NULL, 'aussen', array (
));
$foreach8DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('module_data')->value) {
$foreach8DoElse = false;
?>
                    <li class="level-<?php if ($_smarty_tpl->getValue('current_parent_id')) {
echo $_smarty_tpl->getValue('module_data')['level']-1;
} else {
echo $_smarty_tpl->getValue('module_data')['level'];
}?> cid-<?php echo $_smarty_tpl->getValue('module_data')['categories_id'];
if ($_smarty_tpl->getValue('module_data')['active']) {?> active<?php }?>">
                        <a href="<?php echo $_smarty_tpl->getValue('module_data')['categories_link'];?>
"><span class="<?php if ($_smarty_tpl->getValue('module_data')['level'] > 3) {?>icon<?php }?>"><?php echo $_smarty_tpl->getValue('module_data')['categories_name'];?>
</span></a>
                    </li>
                <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
            </ul>
        </div>

    <?php }
}
}
}
