<?php
/* Smarty version 4.3.0, created on 2023-10-08 01:15:17
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive_AQ/xtCore/boxes/box_teaser.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.0',
  'unifunc' => 'content_6521e68532e1e6_96288560',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'eae7e1b5d20148ba720d032d955aecefb20753ad' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive_AQ/xtCore/boxes/box_teaser.html',
      1 => 1691797588,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6521e68532e1e6_96288560 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.img.php','function'=>'smarty_function_img',),));
if ($_smarty_tpl->tpl_vars['data']->value) {?>
        <div class="carousel-outer-wrap">
            <div class="<?php echo $_smarty_tpl->tpl_vars['params']->value['name'];?>
 picture-slider owl clear">
                <div id="pictureCarousel-<?php echo $_smarty_tpl->tpl_vars['data']->value['slider_id'];?>
">
                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['data']->value['slides'], 'item', false, 'key');
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
?>
                        <?php if ($_smarty_tpl->tpl_vars['item']->value['slide_image']) {?>
                            <div class="imageDISABLE_SPINNER image-<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
 pull-left">
                                <?php if ($_smarty_tpl->tpl_vars['item']->value['slide_link']) {?><a href="<?php echo $_smarty_tpl->tpl_vars['item']->value['slide_link'];?>
"><?php }?>
									<?php if ($_smarty_tpl->tpl_vars['key']->value == 0) {?>
                                    <img alt="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['item']->value['slide_alt_text'], ENT_QUOTES, 'UTF-8', true);?>
" class="img-responsive" src="<?php echo smarty_function_img(array('img'=>$_smarty_tpl->tpl_vars['item']->value['slide_image'],'type'=>'m_org','path_only'=>true),$_smarty_tpl);?>
">
									<?php } else { ?>
									<img alt="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['item']->value['slide_alt_text'], ENT_QUOTES, 'UTF-8', true);?>
" class="img-responsive lazyOwl" data-src="<?php echo smarty_function_img(array('img'=>$_smarty_tpl->tpl_vars['item']->value['slide_image'],'type'=>'m_org','path_only'=>true),$_smarty_tpl);?>
">
									<?php }?>
                                <?php if ($_smarty_tpl->tpl_vars['item']->value['slide_link']) {?></a><?php }?>
                            </div><!-- .image -->
                        <?php }?>
                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                </div>
            </div>
        </div>
        <?php echo '<script'; ?>
 type="text/javascript">

document.addEventListener('DOMContentLoaded', function () {
	if(typeof jQuery.fn.owlCarousel === 'function')
	{
		const teaserElement_<?php echo $_smarty_tpl->tpl_vars['data']->value['slider_id'];?>
 = document.querySelector('#pictureCarousel-<?php echo $_smarty_tpl->tpl_vars['data']->value['slider_id'];?>
');
		if (teaserElement_<?php echo $_smarty_tpl->tpl_vars['data']->value['slider_id'];?>
.length != 0) {
			$(teaserElement_<?php echo $_smarty_tpl->tpl_vars['data']->value['slider_id'];?>
).owlCarousel({
				singleItem: true,
				navigation: true,
				pagination: false,
				slideSpeed: <?php echo $_smarty_tpl->tpl_vars['data']->value['slide_speed'];?>
,
				paginationSpeed: <?php echo $_smarty_tpl->tpl_vars['data']->value['pagination_speed'];?>
,
				scrollPerPage: true,
				autoPlay: <?php echo $_smarty_tpl->tpl_vars['data']->value['auto_play_speed'];?>
,
				stopOnHover: true,
				autoHeight: true,
				lazyLoad: true,
				addClassActive: true,
				navigationText: ['', '']
			});
		}
	}
});

<?php echo '</script'; ?>
>
<?php }
}
}
