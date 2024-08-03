<?php
/* Smarty version 5.1.0, created on 2024-08-03 01:14:35
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/categorie_listing/categorie_listing.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_66ad685b596171_30849096',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd231b41af6793ba8e1c711fadc6240b2aaffa959' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/categorie_listing/categorie_listing.html',
      1 => 1697144245,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:includes/categorie_listing.html' => 1,
  ),
))) {
function content_66ad685b596171_30849096 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/categorie_listing';
?><div id="categorie-listing" class="text-word-wrap">

    <?php if ($_smarty_tpl->getValue('categories_image')) {?>
        <div class="full-width-image category-image img-thumbnail">
            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('img')->handle(array('class'=>"img-responsive img-listingTop",'img'=>$_smarty_tpl->getValue('categories_image'),'type'=>'m_listingTop','alt'=>htmlspecialchars((string)$_smarty_tpl->getValue('categories_name'), ENT_QUOTES, 'UTF-8', true),'title'=>htmlspecialchars((string)$_smarty_tpl->getValue('categories_name'), ENT_QUOTES, 'UTF-8', true)), $_smarty_tpl);?>

			<?php if ($_smarty_tpl->getValue('categories_image_data') && $_smarty_tpl->getValue('categories_image_data')[$_smarty_tpl->getValue('categories_image_id')]['copyright_holder']) {?>
				&copy;&nbsp;<?php echo $_smarty_tpl->getValue('categories_image_data')[$_smarty_tpl->getValue('categories_image_id')]['copyright_holder'];?>

			<?php }?>
        </div>
    <?php }?>

	<?php if ($_smarty_tpl->getSmarty()->getModifierCallback('trim')(preg_replace('!<[^>]*?>!', ' ', (string) $_smarty_tpl->getValue('categories_heading_title'))) != '') {?>
	    <h1 class="text-primary"><?php echo $_smarty_tpl->getValue('categories_heading_title');?>
</h1>
	<?php } else { ?>
	    <h1 class="text-primary"><?php echo $_smarty_tpl->getValue('categories_name');?>
</h1>
	<?php }?>

	<?php if ($_smarty_tpl->getSmarty()->getModifierCallback('trim')($_smarty_tpl->getValue('categories_description')) != '') {?>
	    <div id="categorie-description" class="textstyles"><?php echo $_smarty_tpl->getValue('categories_description');?>
</div>
	<?php }?>

	<?php $_smarty_tpl->renderSubTemplate("file:includes/categorie_listing.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('position'=>$_smarty_tpl->getSmarty()->getModifierCallback('trim')($_smarty_tpl->getSmarty()->getModifierCallback('htmlspecialchars')(preg_replace('!<[^>]*?>!', ' ', (string) $_GET['page']))),'categorie_listing'=>$_smarty_tpl->getValue('categorie_listing'),'heading'=>(defined('TEXT_HEADING_MORE_CATEGORIES') ? constant('TEXT_HEADING_MORE_CATEGORIES') : null)), (int) 0, $_smarty_current_dir);
?>

</div><!-- #categorie-listing --><?php }
}
