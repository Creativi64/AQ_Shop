<?php
/* Smarty version 5.1.0, created on 2024-08-03 01:07:41
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes/box_startpage_categories.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_66ad66bd6e90c6_96393716',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2a00f4718c5e9c370b044fa82f81937653175311' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes/box_startpage_categories.html',
      1 => 1697144118,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_66ad66bd6e90c6_96393716 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes';
if ($_smarty_tpl->getValue('_categories')) {?>
<div id="box-start-page-categories" class="listing categorie-listing hidden-xs" role="navigation">

	<div class="row">
		<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('_categories'), 'module_data');
$foreach1DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('module_data')->value) {
$foreach1DoElse = false;
?>
			<div class="col col-xs-6 col-md-4">
				<div class="section">
					<?php if ($_smarty_tpl->getValue('module_data')['categories_image']) {?>
						<p class="image">
							<a href="<?php echo $_smarty_tpl->getValue('module_data')['categories_link'];?>
" class="vertical-helper image-link"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('img')->handle(array('img'=>$_smarty_tpl->getValue('module_data')['categories_image'],'type'=>'m_category_startpage','class'=>"productImageBorder img-responsive",'alt'=>htmlspecialchars((string)$_smarty_tpl->getValue('module_data')['categories_name'], ENT_QUOTES, 'UTF-8', true)), $_smarty_tpl);?>
</a>
						</p>
					<?php }?>
					<p class="title text-word-wrap">
						<a class="text-uppercase bold"href="<?php echo $_smarty_tpl->getValue('module_data')['categories_link'];?>
"><?php echo $_smarty_tpl->getValue('module_data')['categories_name'];?>
</a>
						<a href="<?php echo $_smarty_tpl->getValue('module_data')['categories_link'];?>
"><?php echo $_smarty_tpl->getSmarty()->getModifierCallback('truncate')($_smarty_tpl->getSmarty()->getModifierCallback('html_entity_decode')(preg_replace('!<[^>]*?>!', ' ', (string) $_smarty_tpl->getValue('module_data')['categories_description']),2,"UTF-8"),77,'&nbsp;[...]',false);?>
</a>
					</p>
				</div>
			</div><!-- .col -->
		<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
	</div>

</div><!-- #box-start-page-categories -->
<?php }
}
}
