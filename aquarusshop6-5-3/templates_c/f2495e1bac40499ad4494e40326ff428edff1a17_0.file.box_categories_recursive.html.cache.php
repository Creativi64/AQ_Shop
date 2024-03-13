<?php
/* Smarty version 4.3.2, created on 2024-03-13 17:39:33
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive_AQ/xtCore/boxes/box_categories_recursive.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_65f1d6c5b07a47_13250909',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f2495e1bac40499ad4494e40326ff428edff1a17' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive_AQ/xtCore/boxes/box_categories_recursive.html',
      1 => 1697144118,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_65f1d6c5b07a47_13250909 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/vendor/smarty/smarty/libs/plugins/function.math.php','function'=>'smarty_function_math',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),));
$_smarty_tpl->compiled->nocache_hash = '69915711165f1d6c5a8d9a9_29180909';
if ($_smarty_tpl->tpl_vars['_categories']->value) {?>
    <?php if ($_smarty_tpl->tpl_vars['params']->value['position'] == 'navbar') {?>

        <ul class="nav navbar-nav">
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['_categories']->value, 'i', false, 'k', 'a', array (
  'first' => true,
  'last' => true,
  'index' => true,
  'iteration' => true,
  'total' => true,
));
$_smarty_tpl->tpl_vars['i']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['k']->value => $_smarty_tpl->tpl_vars['i']->value) {
$_smarty_tpl->tpl_vars['i']->do_else = false;
$_smarty_tpl->tpl_vars['__smarty_foreach_a']->value['iteration']++;
$_smarty_tpl->tpl_vars['__smarty_foreach_a']->value['index']++;
$_smarty_tpl->tpl_vars['__smarty_foreach_a']->value['first'] = !$_smarty_tpl->tpl_vars['__smarty_foreach_a']->value['index'];
$_smarty_tpl->tpl_vars['__smarty_foreach_a']->value['last'] = $_smarty_tpl->tpl_vars['__smarty_foreach_a']->value['iteration'] === $_smarty_tpl->tpl_vars['__smarty_foreach_a']->value['total'];
?>
                <li id="cid-<?php echo $_smarty_tpl->tpl_vars['i']->value['categories_id'];?>
" class="level-<?php echo $_smarty_tpl->tpl_vars['i']->value['level'];?>
 lang-<?php echo $_smarty_tpl->tpl_vars['language']->value;
if ($_smarty_tpl->tpl_vars['i']->value['active'] == 1) {?> active current<?php }
if ((isset($_smarty_tpl->tpl_vars['__smarty_foreach_a']->value['first']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_a']->value['first'] : null)) {?> first<?php }
if ((isset($_smarty_tpl->tpl_vars['__smarty_foreach_a']->value['last']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_a']->value['last'] : null)) {?> last<?php }
if ($_smarty_tpl->tpl_vars['i']->value['sub']) {?> dropdown<?php if ($_smarty_tpl->tpl_vars['params']->value['mega'] == 'true') {?> mega-dropdown<?php }
}?>">
                    <a class="dropdown-toggle" href="<?php echo $_smarty_tpl->tpl_vars['i']->value['categories_link'];?>
"<?php if ($_smarty_tpl->tpl_vars['i']->value['sub']) {?> data-toggle="dropdown" data-hover="dropdown"<?php }?>>
                        <?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['i']->value['categories_name'], ENT_QUOTES, 'UTF-8', true);?>

                        <?php if ($_smarty_tpl->tpl_vars['i']->value['sub']) {?><b class="caret"></b><?php }?>
                    </a>
                    <?php if ($_smarty_tpl->tpl_vars['i']->value['sub']) {?>
                        <ul class="dropdown-menu">
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['i']->value['sub'], 'i1', false, 'k1', 'a1', array (
));
$_smarty_tpl->tpl_vars['i1']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['k1']->value => $_smarty_tpl->tpl_vars['i1']->value) {
$_smarty_tpl->tpl_vars['i1']->do_else = false;
?>
                                <?php if ($_smarty_tpl->tpl_vars['params']->value['mega'] == 'true') {?>
                                    <li class="level-<?php echo $_smarty_tpl->tpl_vars['i1']->value['level'];?>
">
                                        <a href="<?php echo $_smarty_tpl->tpl_vars['i1']->value['categories_link'];?>
" class="title"><?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['i1']->value['categories_name'], ENT_QUOTES, 'UTF-8', true);?>
</a>
                                        <?php if ($_smarty_tpl->tpl_vars['i1']->value['sub']) {?>
                                            <ul class="<?php if ($_smarty_tpl->tpl_vars['i1']->value['level'] >= 2) {?>hidden-xs <?php }?>level-<?php echo smarty_function_math(array('equation'=>"x+1",'x'=>$_smarty_tpl->tpl_vars['i1']->value['level']),$_smarty_tpl);?>
">
                                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['i1']->value['sub'], 'i2', false, 'k2', 'a2', array (
));
$_smarty_tpl->tpl_vars['i2']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['k2']->value => $_smarty_tpl->tpl_vars['i2']->value) {
$_smarty_tpl->tpl_vars['i2']->do_else = false;
?>
                                                    <li class="level-<?php echo $_smarty_tpl->tpl_vars['i2']->value['level'];?>
">
                                                        <a href="<?php echo $_smarty_tpl->tpl_vars['i2']->value['categories_link'];?>
"><?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['i2']->value['categories_name'], ENT_QUOTES, 'UTF-8', true);?>
</a>
                                                    </li>
                                                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                            </ul>
                                        <?php }?>
                                    </li>
                                <?php } else { ?>
                                    <li>
                                        <a href="<?php echo $_smarty_tpl->tpl_vars['i1']->value['categories_link'];?>
"><?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['i1']->value['categories_name'], ENT_QUOTES, 'UTF-8', true);?>
</a>
                                    </li>
                                <?php }?>
                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                            <li class="static divider hidden-float-breakpoint"></li>
                            <li class="static">
                                <a class="dropdown-header" href="<?php echo $_smarty_tpl->tpl_vars['i']->value['categories_link'];?>
">
                                    <i class="fa fa-caret-right"></i>&nbsp;
                                    <?php echo smarty_function_txt(array('key'=>TEXT_MORE_DETAILS),$_smarty_tpl);?>
:&nbsp;
                                    <span class="text-uppercase text-primary"><?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['i']->value['categories_name'], ENT_QUOTES, 'UTF-8', true);?>
</span>
                                </a>
                            </li>
                        </ul>
                    <?php }?>
                </li>
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        </ul>

    <?php } else { ?>

        <div class="<?php echo $_smarty_tpl->tpl_vars['params']->value['name'];?>
 <?php echo $_smarty_tpl->tpl_vars['params']->value['position'];?>
 cid-<?php echo $_smarty_tpl->tpl_vars['current_parent_id']->value;?>
 box-categories panel panel-default text-word-wrap">
            <div class="panel-heading">
                <p class="panel-title text-uppercase">
                    <i class="fa fa-bars"></i>
                    <?php if ($_smarty_tpl->tpl_vars['current_parent_data']->value) {?>
                        <a href="<?php echo $_smarty_tpl->tpl_vars['current_parent_data']->value['categories_link'];?>
"><?php echo $_smarty_tpl->tpl_vars['current_parent_data']->value['categories_name'];?>
</a>
                    <?php } else { ?>
                        <?php echo smarty_function_txt(array('key'=>TEXT_BOX_TITLE_CATEGORIES),$_smarty_tpl);?>

                    <?php }?>
                </p>
            </div>
            <ul class="nav nav-pills nav-stacked">
                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['_categories']->value, 'module_data', false, NULL, 'aussen', array (
));
$_smarty_tpl->tpl_vars['module_data']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['module_data']->value) {
$_smarty_tpl->tpl_vars['module_data']->do_else = false;
?>
                    <li class="level-<?php if ($_smarty_tpl->tpl_vars['current_parent_id']->value) {
echo $_smarty_tpl->tpl_vars['module_data']->value['level']-1;
} else {
echo $_smarty_tpl->tpl_vars['module_data']->value['level'];
}?> cid-<?php echo $_smarty_tpl->tpl_vars['module_data']->value['categories_id'];
if ($_smarty_tpl->tpl_vars['module_data']->value['active']) {?> active<?php }?>">
                        <a href="<?php echo $_smarty_tpl->tpl_vars['module_data']->value['categories_link'];?>
"><span class="<?php if ($_smarty_tpl->tpl_vars['module_data']->value['level'] > 3) {?>icon<?php }?>"><?php echo $_smarty_tpl->tpl_vars['module_data']->value['categories_name'];?>
</span></a>
                    </li>
                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
            </ul>
        </div>

    <?php }
}
}
}
