<?php
/* Smarty version 4.3.0, created on 2023-10-08 01:15:26
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive_AQ/xtCore/pages/categorie_listing/categorie_listing.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.0',
  'unifunc' => 'content_6521e68ee5ce01_46352399',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '832a5e2d3413ddc4a7c4edcd8a088e53f5356ff9' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive_AQ/xtCore/pages/categorie_listing/categorie_listing.html',
      1 => 1691797603,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:includes/categorie_listing.html' => 1,
  ),
),false)) {
function content_6521e68ee5ce01_46352399 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.img.php','function'=>'smarty_function_img',),));
?>
<div id="categorie-listing" class="text-word-wrap">

    <?php if ($_smarty_tpl->tpl_vars['categories_image']->value) {?>
        <div class="full-width-image category-image img-thumbnail">
            <?php echo smarty_function_img(array('class'=>"img-responsive img-listingTop",'img'=>$_smarty_tpl->tpl_vars['categories_image']->value,'type'=>'m_listingTop','alt'=>htmlspecialchars((string)$_smarty_tpl->tpl_vars['categories_name']->value, ENT_QUOTES, 'UTF-8', true),'title'=>htmlspecialchars((string)$_smarty_tpl->tpl_vars['categories_name']->value, ENT_QUOTES, 'UTF-8', true)),$_smarty_tpl);?>

			<?php if ($_smarty_tpl->tpl_vars['categories_image_data']->value && $_smarty_tpl->tpl_vars['categories_image_data']->value[$_smarty_tpl->tpl_vars['categories_image_id']->value]['copyright_holder']) {?>
				&copy;&nbsp;<?php echo $_smarty_tpl->tpl_vars['categories_image_data']->value[$_smarty_tpl->tpl_vars['categories_image_id']->value]['copyright_holder'];?>

			<?php }?>
        </div>
    <?php }?>

	<?php if (trim(preg_replace('!<[^>]*?>!', ' ', $_smarty_tpl->tpl_vars['categories_heading_title']->value ?: '')) != '') {?>
	    <h1 class="text-primary"><?php echo $_smarty_tpl->tpl_vars['categories_heading_title']->value;?>
</h1>
	<?php } else { ?>
	    <h1 class="text-primary"><?php echo $_smarty_tpl->tpl_vars['categories_name']->value;?>
</h1>
	<?php }?>

	<?php if (trim($_smarty_tpl->tpl_vars['categories_description']->value) != '') {?>
	    <div id="categorie-description" class="textstyles"><?php echo $_smarty_tpl->tpl_vars['categories_description']->value;?>
</div>
	<?php }?>

	<?php $_smarty_tpl->_subTemplateRender("file:includes/categorie_listing.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('position'=>trim(htmlspecialchars(preg_replace('!<[^>]*?>!', ' ', $_GET['page'] ?: ''))),'categorie_listing'=>$_smarty_tpl->tpl_vars['categorie_listing']->value,'heading'=>(defined('TEXT_HEADING_MORE_CATEGORIES') ? constant('TEXT_HEADING_MORE_CATEGORIES') : null)), 0, false);
?>

</div><!-- #categorie-listing --><?php }
}
