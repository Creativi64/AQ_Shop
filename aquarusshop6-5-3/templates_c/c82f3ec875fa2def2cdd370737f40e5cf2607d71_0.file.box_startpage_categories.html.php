<?php
/* Smarty version 4.3.2, created on 2024-05-08 20:28:39
  from '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes/box_startpage_categories.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_663bc457ece139_36735169',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c82f3ec875fa2def2cdd370737f40e5cf2607d71' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes/box_startpage_categories.html',
      1 => 1697144118,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_663bc457ece139_36735169 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.img.php','function'=>'smarty_function_img',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/vendor/smarty/smarty/libs/plugins/modifier.truncate.php','function'=>'smarty_modifier_truncate',),));
?>

<?php if ($_smarty_tpl->tpl_vars['_categories']->value) {?>
<div id="box-start-page-categories" class="listing categorie-listing hidden-xs" role="navigation">

	<div class="row">
		<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['_categories']->value, 'module_data');
$_smarty_tpl->tpl_vars['module_data']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['module_data']->value) {
$_smarty_tpl->tpl_vars['module_data']->do_else = false;
?>
			<div class="col col-xs-6 col-md-4">
				<div class="section">
					<?php if ($_smarty_tpl->tpl_vars['module_data']->value['categories_image']) {?>
						<p class="image">
							<a href="<?php echo $_smarty_tpl->tpl_vars['module_data']->value['categories_link'];?>
" class="vertical-helper image-link"><?php echo smarty_function_img(array('img'=>$_smarty_tpl->tpl_vars['module_data']->value['categories_image'],'type'=>'m_category_startpage','class'=>"productImageBorder img-responsive",'alt'=>htmlspecialchars((string)$_smarty_tpl->tpl_vars['module_data']->value['categories_name'], ENT_QUOTES, 'UTF-8', true)),$_smarty_tpl);?>
</a>
						</p>
					<?php }?>
					<p class="title text-word-wrap">
						<a class="text-uppercase bold"href="<?php echo $_smarty_tpl->tpl_vars['module_data']->value['categories_link'];?>
"><?php echo $_smarty_tpl->tpl_vars['module_data']->value['categories_name'];?>
</a>
						<a href="<?php echo $_smarty_tpl->tpl_vars['module_data']->value['categories_link'];?>
"><?php echo smarty_modifier_truncate(html_entity_decode(preg_replace('!<[^>]*?>!', ' ', $_smarty_tpl->tpl_vars['module_data']->value['categories_description'] ?: ''),2,"UTF-8"),77,'&nbsp;[...]',false);?>
</a>
					</p>
				</div>
			</div><!-- .col -->
		<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
	</div>

</div><!-- #box-start-page-categories -->
<?php }
}
}
