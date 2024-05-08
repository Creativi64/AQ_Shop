<?php
/* Smarty version 4.3.2, created on 2024-05-08 17:56:07
  from '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/index.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_663ba097b582f8_55923226',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '875bcd9d16f59d70ab5dccdcf8577d86aab16680' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/index.html',
      1 => 1713607199,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:xtCore/pages/navigation/breadcrumb.html' => 1,
  ),
),false)) {
function content_663ba097b582f8_55923226 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.hook.php','function'=>'smarty_function_hook',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.box.php','function'=>'smarty_function_box',),2=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.link.php','function'=>'smarty_function_link',),3=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),4=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.box_cache.php','function'=>'smarty_function_box_cache',),5=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.cronjob.php','function'=>'smarty_function_cronjob',),));
if ($_smarty_tpl->tpl_vars['page']->value == 'index' || $_smarty_tpl->tpl_vars['page']->value == 'product' || $_smarty_tpl->tpl_vars['page']->value == 'cart' || $_GET['page_action'] == 'login' || $_GET['page_action'] == 'order_info') {?>
    <?php $_smarty_tpl->_assignInScope('show_index_boxes', "false");
}
echo smarty_function_hook(array('key'=>'index_top'),$_smarty_tpl);?>

<!-- Respond.js IE8 support of media queries -->
<!--[if lt IE 9]>
<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo (defined('_SRV_WEB_TEMPLATES') ? constant('_SRV_WEB_TEMPLATES') : null);
echo (defined('_STORE_TEMPLATE') ? constant('_STORE_TEMPLATE') : null);?>
/components/Respond/dest/respond.min.js"><?php echo '</script'; ?>
>
<![endif]-->
<meta name="google-site-verification" content="WAaUjV53OYsKXE-TgfamecXP_dG0LzlsJlJh1ykd_74" />

<div id="site-wrap" class="<?php echo $_smarty_tpl->tpl_vars['page']->value;?>
-wrap<?php if ($_smarty_tpl->tpl_vars['page']->value != 'index') {?> subpage-wrap<?php }
if ($_GET['page_action']) {?> <?php echo $_GET['page_action'];?>
-action-wrap<?php }?>">
    <?php if (!(isset($_GET['viewport'])) || $_GET['viewport'] != 'modal') {?>

        <header id="header">
            <div class="meta-navigation">
                <div class="container clearfix">
                    <ul class="meta list-inline pull-left hidden-xs pull-left">
                        <?php echo smarty_function_hook(array('key'=>'meta_nav_left_first'),$_smarty_tpl);?>

                        <?php echo smarty_function_box(array('name'=>'xt_country_preselect'),$_smarty_tpl);?>

                        <li><?php echo smarty_function_box(array('name'=>'language'),$_smarty_tpl);?>
</li>
                        <li><?php echo smarty_function_box(array('name'=>'currency'),$_smarty_tpl);?>
</li>
                        <?php echo smarty_function_hook(array('key'=>'meta_nav_left_last'),$_smarty_tpl);?>

                    </ul>
                    <ul class="user list-inline pull-right">
                        <?php echo smarty_function_hook(array('key'=>'meta_nav_right_first'),$_smarty_tpl);?>

                        <li><a href="<?php echo smarty_function_link(array('page'=>'checkout','paction'=>'shipping','conn'=>'SSL'),$_smarty_tpl);?>
"><?php echo smarty_function_txt(array('key'=>TEXT_CHECKOUT),$_smarty_tpl);?>
</a></li>
                        <?php if ($_smarty_tpl->tpl_vars['account']->value == true) {?>
                            <li><a href="<?php echo smarty_function_link(array('page'=>'customer','conn'=>'SSL'),$_smarty_tpl);?>
"><?php echo smarty_function_txt(array('key'=>'TEXT_ACCOUNT'),$_smarty_tpl);?>
</a></li>
                            <?php if ($_smarty_tpl->tpl_vars['registered_customer']->value) {?>
                                <li><a href="<?php echo smarty_function_link(array('page'=>'customer','paction'=>'logoff','conn'=>'SSL'),$_smarty_tpl);?>
"><?php echo smarty_function_txt(array('key'=>TEXT_LOGOFF),$_smarty_tpl);?>
 <span class="customer-name hidden-xs">(<?php if ($_SESSION['customer']->customer_default_address['customers_title']) {
echo $_SESSION['customer']->customer_default_address['customers_title'];?>
 <?php }
echo $_SESSION['customer']->customer_default_address['customers_firstname'][0];?>
. <?php echo $_SESSION['customer']->customer_default_address['customers_lastname'];?>
)</span></a></li>
                            <?php } else { ?>
                                <li><a href="<?php echo smarty_function_link(array('page'=>'customer','paction'=>'login','conn'=>'SSL'),$_smarty_tpl);?>
" data-toggle="modal" data-target="#loginModal" data-remote="false"><?php echo smarty_function_txt(array('key'=>TEXT_LOGIN),$_smarty_tpl);?>
</a></li>
                            <?php }?>
                        <?php } elseif ($_smarty_tpl->tpl_vars['guest_account']->value == true) {?>
                            <?php if ($_smarty_tpl->tpl_vars['amazon_customer']->value) {?>
                             <li><a href="<?php echo smarty_function_link(array('page'=>'customer','paction'=>'logoff','conn'=>'SSL'),$_smarty_tpl);?>
"><?php echo smarty_function_txt(array('key'=>TEXT_LOGOFF),$_smarty_tpl);?>
</a></li>
                            <?php } else { ?>
                            <li><a href="<?php echo smarty_function_link(array('page'=>'customer','paction'=>'logoff','conn'=>'SSL'),$_smarty_tpl);?>
"><?php echo smarty_function_txt(array('key'=>TEXT_LOGOFF_GUEST),$_smarty_tpl);?>
</a></li>
                            <?php }?>
                        <?php }?>
                        <?php echo smarty_function_hook(array('key'=>'meta_nav_right_last'),$_smarty_tpl);?>

                    </ul>
                </div>
            </div><!-- .meta-navigation -->
            <div class="header-top">
                <div class="container">
                    <div class="row text-center-xs">
                        <div class="col col-sm-4 col-md-5 col-logo">
                            <div class="inner branding">
                                <p class="logo">
                                    <a href="<?php echo smarty_function_link(array('page'=>'index'),$_smarty_tpl);?>
" class="text-muted" title="<?php echo (defined('_STORE_NAME') ? constant('_STORE_NAME') : null);?>
">
                                        <img class="img-responsive" src="media/logo/<?php echo (defined('_STORE_LOGO') ? constant('_STORE_LOGO') : null);?>
" alt="<?php echo (defined('_STORE_NAME') ? constant('_STORE_NAME') : null);?>
" />
                                    </a>
                                </p>
                                                            </div>
                        </div>
                        <div class="col col-sm-4 hidden-xs">
                            <div class="inner top-search hidden-sm">
                                <?php echo smarty_function_box(array('name'=>'search','htmlonly'=>1),$_smarty_tpl);?>

                            </div>
                        </div>
                        <div class="col col-sm-4 col-md-3 hidden-xs">
                            <div class="inner top-cart text-right">
                                <?php echo smarty_function_box(array('name'=>'cart'),$_smarty_tpl);?>

                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- .header-top -->
            <div class="main-navigation navbar navbar-default" role="navigation">
                <div class="container">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#header .main-navigation .navbar-collapse">
                            <span class="sr-only">Navigation</span>
                            <span class="burger pull-left">
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </span>
                            <span class="caret pull-left"></span>
                        </button>
                        <div class="navbar-search visible-float-breakpoint">
                            <?php echo smarty_function_box(array('name'=>'search','htmlonly'=>1),$_smarty_tpl);?>

                        </div>
                    </div>
                    <div class="navbar-collapse collapse">
                        <?php echo smarty_function_box_cache(array('name'=>'categories_recursive','position'=>'navbar','mega'=>true,'cache_id'=>'navbar'),$_smarty_tpl);?>

                    </div>
                </div>
            </div><!-- .main-navigation -->
        </header><!-- #header -->

        <?php if ($_smarty_tpl->tpl_vars['page']->value != 'index' && $_smarty_tpl->tpl_vars['page']->value != '404' && $_smarty_tpl->tpl_vars['page']->value != 'checkout') {?>
            <div class="breadcrumb-container">
                <div class="container">
            <?php $_smarty_tpl->_subTemplateRender("file:xtCore/pages/navigation/breadcrumb.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('type'=>"tpl_include"), 0, false);
?>
                </div>
            </div>
        <?php }?>

        <noscript>
            <div class="container">
                <div class="alert alert-danger text-center">
                    <p><i class="fa fa-3x fa-exclamation-triangle"></i></p>
                    <?php echo smarty_function_txt(array('key'=>TEXT_JS_DISABLED_WARNING),$_smarty_tpl);?>

                </div>
            </div>
        </noscript>

		<div id="navContainer" class="container">
			<ul class="navbar-mega hidden hidden-float-breakpoint with-backdrop-shadow"></ul>
		</div>

		<?php echo smarty_function_hook(array('key'=>'pre_content_container'),$_smarty_tpl);?>


		<?php if (!(isset($_smarty_tpl->tpl_vars['disable_content_container']->value))) {?>
        <div id="container" class="container">
            <?php echo $_SESSION['ppc_express_checkout'];?>

            <?php echo smarty_function_hook(array('key'=>'container_top'),$_smarty_tpl);?>

            <div id="content" class="row">
                <div class="col primary<?php if ($_smarty_tpl->tpl_vars['show_index_boxes']->value == 'true') {?> col-sm-8 col-sm-push-4 col-md-9 col-md-push-3<?php } else { ?> col-sm-12<?php }?>">
                    <?php echo $_smarty_tpl->tpl_vars['content']->value;?>

                </div>
                <?php if ($_smarty_tpl->tpl_vars['show_index_boxes']->value == 'true') {?>
                    <div class="col secondary col-sm-4 col-sm-pull-8 col-md-3 col-md-pull-9">
                        <?php echo smarty_function_hook(array('key'=>'index_boxes_top'),$_smarty_tpl);?>

                        <?php echo smarty_function_box_cache(array('name'=>'categories_recursive','position'=>'sidebar','cache_id'=>'sidebar'),$_smarty_tpl);?>

                        <?php echo smarty_function_box(array('name'=>'xt_blog','type'=>'user','box_type'=>'categories'),$_smarty_tpl);?>

						<?php echo smarty_function_box(array('name'=>'xt_blog','box_type'=>'featured_messages','limit'=>5,'type'=>'user'),$_smarty_tpl);?>

                        <?php echo smarty_function_hook(array('key'=>'index_boxes_middle'),$_smarty_tpl);?>

                        <div class="sidebar-products">
                            <?php echo smarty_function_hook(array('key'=>'sidebar_products_top'),$_smarty_tpl);?>

                            <?php echo smarty_function_box(array('name'=>'xt_live_shopping','type'=>'user','limit'=>2),$_smarty_tpl);?>

                            <?php echo smarty_function_box(array('name'=>'xt_last_viewed_products','type'=>'user'),$_smarty_tpl);?>

                            <?php if ($_smarty_tpl->tpl_vars['page']->value != 'xt_bestseller_products') {
echo smarty_function_box(array('name'=>'xt_bestseller_products','type'=>'user'),$_smarty_tpl);
}?>
                            <?php if ($_smarty_tpl->tpl_vars['page']->value != 'xt_special_products') {
echo smarty_function_box(array('name'=>'xt_special_products','type'=>'user','order_by'=>'p.products_id'),$_smarty_tpl);
}?>
                            <?php if ($_smarty_tpl->tpl_vars['page']->value != 'xt_new_products') {
echo smarty_function_box(array('name'=>'xt_new_products','type'=>'user','order_by'=>'p.last_modified DESC'),$_smarty_tpl);
}?>
                            <?php if ($_smarty_tpl->tpl_vars['page']->value != 'xt_upcoming_products') {
echo smarty_function_box(array('name'=>'xt_upcoming_products','type'=>'user','order_by'=>'p.date_available'),$_smarty_tpl);
}?>
                            <?php echo smarty_function_hook(array('key'=>'sidebar_products_bottom'),$_smarty_tpl);?>

                        </div>
                        <?php echo smarty_function_hook(array('key'=>'index_boxes_bottom'),$_smarty_tpl);?>

                    </div>
                <?php }?>
            </div><!-- #content -->
            
                        
            <?php echo smarty_function_hook(array('key'=>'container_bottom'),$_smarty_tpl);?>

        </div><!-- #container -->
		<?php }?>

        <?php echo smarty_function_hook(array('key'=>'after_content_container'),$_smarty_tpl);?>


        <div class="clearfix"></div>

        <footer id="footer">
            <div class="container">
                <div id="footer-cols">
                    <div class="row">
                        <div class="col col-sm-4">
                            <?php echo smarty_function_box_cache(array('name'=>'footer_contact','htmlonly'=>1),$_smarty_tpl);?>

                        </div>
                        <div class="col col-sm-4">
                            <?php echo smarty_function_box_cache(array('name'=>'footer_left','htmlonly'=>1),$_smarty_tpl);?>

                        </div>
                        <div class="col col-sm-4">
                            <?php echo smarty_function_box_cache(array('name'=>'footer_right','htmlonly'=>1),$_smarty_tpl);?>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-sm-4">
                            <?php echo smarty_function_box_cache(array('name'=>'manufacturers','order_by'=>'m.manufacturers_name'),$_smarty_tpl);?>

                        </div>
                        <div class="col col-sm-4">
                            <?php echo smarty_function_box(array('name'=>'language'),$_smarty_tpl);?>

                            <?php echo smarty_function_box(array('name'=>'currency'),$_smarty_tpl);?>

                        </div>
                        <div class="col col-sm-4">
                            <?php echo smarty_function_box_cache(array('name'=>'payment_logos'),$_smarty_tpl);?>

                            <?php echo smarty_function_box(array('name'=>'trusted_shops_ratings'),$_smarty_tpl);?>

                            <?php echo smarty_function_box(array('name'=>'trusted_shops_seal'),$_smarty_tpl);?>

                            <?php echo smarty_function_box(array('name'=>'trusted_shops_video'),$_smarty_tpl);?>

                        </div>
                    </div>
                </div>
                <div id="footer-meta" class="text-center text-muted">
                    [<copyright>]
                    <?php echo smarty_function_hook(array('key'=>'index_footer_tpl'),$_smarty_tpl);?>

                    <?php echo smarty_function_box(array('name'=>'trusted_shops_rich_snippet'),$_smarty_tpl);?>

                    <!--img src="cronjob.php" width="1" height="1" alt="" /-->
                    <?php echo smarty_function_cronjob(array('type'=>"ajax"),$_smarty_tpl);?>

                </div>
            </div>
        </footer><!-- #footer -->

        <div id="loginModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                            <span class="sr-only">Close</span>
                        </button>
                        <p class="h3 modal-title"><?php echo smarty_function_txt(array('key'=>TEXT_LOGIN),$_smarty_tpl);?>
</p>
                    </div>
                    <div class="modal-body">
                        <?php echo smarty_function_box(array('name'=>'loginbox'),$_smarty_tpl);?>

                    </div>
                </div>
            </div>
        </div><!-- #loginModal -->
        
        <a id="back-to-top" class="hidden hidden-xs" href="<?php echo $_SERVER['REQUEST_URI'];?>
#top" rel="nofollow">
            <i class="fa fa-3x fa-arrow-circle-o-up"></i>
            <span class="sr-only">Back to Top</span>
        </a>

        <?php if ($_smarty_tpl->tpl_vars['page']->value != 'cart' && $_smarty_tpl->tpl_vars['page']->value != 'checkout') {?>
            <div class="bottom-cart">
                <?php echo smarty_function_box(array('name'=>'cart'),$_smarty_tpl);?>

            </div>
        <?php }?>

    <?php } else { ?>
        <?php echo $_smarty_tpl->tpl_vars['content']->value;?>

    <?php }?>
</div><!-- #site-wrap -->
<div class="clearfix"></div>
<?php if ($_SESSION['cartChanged']) {?>

<?php echo '<script'; ?>
 type="application/javascript">
    document.addEventListener("DOMContentLoaded",function(event){
        showCartChanged();
    });
<?php echo '</script'; ?>
>

<?php }?>

<?php }
}
