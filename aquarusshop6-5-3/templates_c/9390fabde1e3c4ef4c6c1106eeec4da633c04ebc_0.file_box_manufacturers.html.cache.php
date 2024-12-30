<?php
/* Smarty version 5.4.1, created on 2024-12-30 01:19:49
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes/box_manufacturers.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.1',
  'unifunc' => 'content_6771e72513b653_17498476',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '9390fabde1e3c4ef4c6c1106eeec4da633c04ebc' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes/box_manufacturers.html',
      1 => 1697144118,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6771e72513b653_17498476 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes';
$_smarty_tpl->getCompiled()->nocache_hash = '10735424786771e725103659_25338217';
?>
<div class="manufacturers">
	<p class="headline"><?php if ($_smarty_tpl->getValue('link')) {?><a href="$link"><?php }
echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_BOX_TITLE_MANUFACTURERS), $_smarty_tpl);
if ($_smarty_tpl->getValue('link')) {?></a><?php }?></p>
	<select id="manufacturers" class="form-control" onchange="location.href=this.options[this.selectedIndex].value">
		<option value=""><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SELECT_MANUFACTURERS), $_smarty_tpl);?>
</option>
		<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('_manufacturers'), 'module_data', false, NULL, 'aussen', array (
));
$foreach9DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('module_data')->value) {
$foreach9DoElse = false;
?>
		<option value="<?php echo $_smarty_tpl->getValue('module_data')['link'];?>
"><?php echo $_smarty_tpl->getValue('module_data')['manufacturers_name'];?>
</option>
		<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
	</select>
</div><?php }
}
