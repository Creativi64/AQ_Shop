<?php
/* Smarty version 5.1.0, created on 2024-09-09 19:14:52
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes/box_teaser.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_66df2d0c5ac432_42539809',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c25ae0b98e41bd04272865b0f3142ae77fa29a83' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes/box_teaser.html',
      1 => 1697144118,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_66df2d0c5ac432_42539809 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes';
if ($_smarty_tpl->getValue('data')) {?>
        <div class="carousel-outer-wrap">
            <div class="<?php echo $_smarty_tpl->getValue('params')['name'];?>
 picture-slider owl clear">
                <div id="pictureCarousel-<?php echo $_smarty_tpl->getValue('data')['slider_id'];?>
">
                    <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('data')['slides'], 'item', false, 'key');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('key')->value => $_smarty_tpl->getVariable('item')->value) {
$foreach0DoElse = false;
?>
                        <?php if ($_smarty_tpl->getValue('item')['slide_image']) {?>
                            <div class="imageDISABLE_SPINNER image-<?php echo $_smarty_tpl->getValue('key');?>
 pull-left">
                                <?php if ($_smarty_tpl->getValue('item')['slide_link']) {?><a href="<?php echo $_smarty_tpl->getValue('item')['slide_link'];?>
"><?php }?>
									<?php if ($_smarty_tpl->getValue('key') == 0) {?>
                                    <img alt="<?php echo htmlspecialchars((string)$_smarty_tpl->getValue('item')['slide_alt_text'], ENT_QUOTES, 'UTF-8', true);?>
" class="img-responsive" src="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('img')->handle(array('img'=>$_smarty_tpl->getValue('item')['slide_image'],'type'=>'m_org','path_only'=>true), $_smarty_tpl);?>
">
									<?php } else { ?>
									<img alt="<?php echo htmlspecialchars((string)$_smarty_tpl->getValue('item')['slide_alt_text'], ENT_QUOTES, 'UTF-8', true);?>
" class="img-responsive lazyOwl" data-src="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('img')->handle(array('img'=>$_smarty_tpl->getValue('item')['slide_image'],'type'=>'m_org','path_only'=>true), $_smarty_tpl);?>
">
									<?php }?>
                                <?php if ($_smarty_tpl->getValue('item')['slide_link']) {?></a><?php }?>
                            </div><!-- .image -->
                        <?php }?>
                    <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
                </div>
            </div>
        </div>
        <?php echo '<script'; ?>
 type="text/javascript">

document.addEventListener('DOMContentLoaded', function () {
	if(typeof jQuery.fn.owlCarousel === 'function')
	{
		const teaserElement_<?php echo $_smarty_tpl->getValue('data')['slider_id'];?>
 = document.querySelector('#pictureCarousel-<?php echo $_smarty_tpl->getValue('data')['slider_id'];?>
');
		if (teaserElement_<?php echo $_smarty_tpl->getValue('data')['slider_id'];?>
.length != 0) {
			$(teaserElement_<?php echo $_smarty_tpl->getValue('data')['slider_id'];?>
).owlCarousel({
				singleItem: true,
				navigation: true,
				pagination: false,
				slideSpeed: <?php echo $_smarty_tpl->getValue('data')['slide_speed'];?>
,
				paginationSpeed: <?php echo $_smarty_tpl->getValue('data')['pagination_speed'];?>
,
				scrollPerPage: true,
				autoPlay: <?php echo $_smarty_tpl->getValue('data')['auto_play_speed'];?>
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
