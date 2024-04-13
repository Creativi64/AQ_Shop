<?php
/* Smarty version 4.3.2, created on 2024-03-20 20:16:45
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive_AQ/xtCore/pages/navigation/breadcrumb.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_65fb361de9c241_26375411',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '4a612fad320bb220f16305c899e16f6e91269e57' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive_AQ/xtCore/pages/navigation/breadcrumb.html',
      1 => 1697144245,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_65fb361de9c241_26375411 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/vendor/smarty/smarty/libs/plugins/modifier.truncate.php','function'=>'smarty_modifier_truncate',),));
?>
<ul class="breadcrumb">
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['top_navigation']->value, 'breadcrumb', false, NULL, 'aussen', array (
  'first' => true,
  'last' => true,
  'index' => true,
  'iteration' => true,
  'total' => true,
));
$_smarty_tpl->tpl_vars['breadcrumb']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['breadcrumb']->value) {
$_smarty_tpl->tpl_vars['breadcrumb']->do_else = false;
$_smarty_tpl->tpl_vars['__smarty_foreach_aussen']->value['iteration']++;
$_smarty_tpl->tpl_vars['__smarty_foreach_aussen']->value['index']++;
$_smarty_tpl->tpl_vars['__smarty_foreach_aussen']->value['first'] = !$_smarty_tpl->tpl_vars['__smarty_foreach_aussen']->value['index'];
$_smarty_tpl->tpl_vars['__smarty_foreach_aussen']->value['last'] = $_smarty_tpl->tpl_vars['__smarty_foreach_aussen']->value['iteration'] === $_smarty_tpl->tpl_vars['__smarty_foreach_aussen']->value['total'];
?>
        <?php if (trim(preg_replace('!<[^>]*?>!', ' ', $_smarty_tpl->tpl_vars['breadcrumb']->value['name'] ?: '')) != '') {?>
            <?php if ((isset($_smarty_tpl->tpl_vars['__smarty_foreach_aussen']->value['first']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_aussen']->value['first'] : null) == true) {?>
                <li class="home"><a href="<?php echo $_smarty_tpl->tpl_vars['breadcrumb']->value['url'];?>
" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['breadcrumb']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
" class="text-muted"><i class="fa fa-home"></i><span class="sr-only"><?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['breadcrumb']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
</span></a></li>
            <?php } elseif ((isset($_smarty_tpl->tpl_vars['__smarty_foreach_aussen']->value['last']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_aussen']->value['last'] : null) == false) {?>
                <li><span><a href="<?php echo $_smarty_tpl->tpl_vars['breadcrumb']->value['url'];?>
"><span><?php echo smarty_modifier_truncate(trim(htmlspecialchars((string)preg_replace('!<[^>]*?>!', ' ', $_smarty_tpl->tpl_vars['breadcrumb']->value['name'] ?: ''), ENT_QUOTES, 'UTF-8', true)),50," ...");?>
</span></a></span></li>
            <?php } else { ?>
                <li class="active" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['breadcrumb']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
"><?php echo smarty_modifier_truncate(trim(htmlspecialchars((string)preg_replace('!<[^>]*?>!', ' ', $_smarty_tpl->tpl_vars['breadcrumb']->value['name'] ?: ''), ENT_QUOTES, 'UTF-8', true)),50," ...");?>
</li>
            <?php }?>
        <?php }?>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
</ul><?php }
}
