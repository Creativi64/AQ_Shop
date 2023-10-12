<?php
/* Smarty version 4.3.0, created on 2023-06-19 13:53:19
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive/xtCore/boxes/box_startpage_categories.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.0',
  'unifunc' => 'content_649041afc95847_27335501',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c374f8a9cc973b2d6a4362e8a84c0495675d9d4d' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive/xtCore/boxes/box_startpage_categories.html',
      1 => 1687006095,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_649041afc95847_27335501 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.img.php','function'=>'smarty_function_img',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/vendor/smarty/smarty/libs/plugins/modifier.truncate.php','function'=>'smarty_modifier_truncate',),));
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
