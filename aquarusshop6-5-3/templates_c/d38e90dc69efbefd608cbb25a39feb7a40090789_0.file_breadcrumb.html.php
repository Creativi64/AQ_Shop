<?php
/* Smarty version 5.4.1, created on 2024-12-02 19:35:55
  from 'file:xtCore/pages/navigation/breadcrumb.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.1',
  'unifunc' => 'content_674dfe0b878873_05074381',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd38e90dc69efbefd608cbb25a39feb7a40090789' => 
    array (
      0 => 'xtCore/pages/navigation/breadcrumb.html',
      1 => 1697144245,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_674dfe0b878873_05074381 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/navigation';
?><ul class="breadcrumb">
    <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('top_navigation'), 'breadcrumb', false, NULL, 'aussen', array (
  'first' => true,
  'last' => true,
  'index' => true,
  'iteration' => true,
  'total' => true,
));
$foreach7DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('breadcrumb')->value) {
$foreach7DoElse = false;
$_smarty_tpl->tpl_vars['__smarty_foreach_aussen']->value['iteration']++;
$_smarty_tpl->tpl_vars['__smarty_foreach_aussen']->value['index']++;
$_smarty_tpl->tpl_vars['__smarty_foreach_aussen']->value['first'] = !$_smarty_tpl->tpl_vars['__smarty_foreach_aussen']->value['index'];
$_smarty_tpl->tpl_vars['__smarty_foreach_aussen']->value['last'] = $_smarty_tpl->tpl_vars['__smarty_foreach_aussen']->value['iteration'] === $_smarty_tpl->tpl_vars['__smarty_foreach_aussen']->value['total'];
?>
        <?php if ($_smarty_tpl->getSmarty()->getModifierCallback('trim')(preg_replace('!<[^>]*?>!', ' ', (string) $_smarty_tpl->getValue('breadcrumb')['name'])) != '') {?>
            <?php if (($_smarty_tpl->getValue('__smarty_foreach_aussen')['first'] ?? null) == true) {?>
                <li class="home"><a href="<?php echo $_smarty_tpl->getValue('breadcrumb')['url'];?>
" title="<?php echo htmlspecialchars((string)$_smarty_tpl->getValue('breadcrumb')['name'], ENT_QUOTES, 'UTF-8', true);?>
" class="text-muted"><i class="fa fa-home"></i><span class="sr-only"><?php echo htmlspecialchars((string)$_smarty_tpl->getValue('breadcrumb')['name'], ENT_QUOTES, 'UTF-8', true);?>
</span></a></li>
            <?php } elseif (($_smarty_tpl->getValue('__smarty_foreach_aussen')['last'] ?? null) == false) {?>
                <li><span><a href="<?php echo $_smarty_tpl->getValue('breadcrumb')['url'];?>
"><span><?php echo $_smarty_tpl->getSmarty()->getModifierCallback('truncate')($_smarty_tpl->getSmarty()->getModifierCallback('trim')(htmlspecialchars((string)preg_replace('!<[^>]*?>!', ' ', (string) $_smarty_tpl->getValue('breadcrumb')['name']), ENT_QUOTES, 'UTF-8', true)),50," ...");?>
</span></a></span></li>
            <?php } else { ?>
                <li class="active" title="<?php echo htmlspecialchars((string)$_smarty_tpl->getValue('breadcrumb')['name'], ENT_QUOTES, 'UTF-8', true);?>
"><?php echo $_smarty_tpl->getSmarty()->getModifierCallback('truncate')($_smarty_tpl->getSmarty()->getModifierCallback('trim')(htmlspecialchars((string)preg_replace('!<[^>]*?>!', ' ', (string) $_smarty_tpl->getValue('breadcrumb')['name']), ENT_QUOTES, 'UTF-8', true)),50," ...");?>
</li>
            <?php }?>
        <?php }?>
    <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
</ul><?php }
}
