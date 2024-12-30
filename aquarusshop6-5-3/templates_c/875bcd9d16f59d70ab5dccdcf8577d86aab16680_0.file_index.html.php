<?php
/* Smarty version 5.4.1, created on 2024-12-30 01:19:48
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/index.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.1',
  'unifunc' => 'content_6771e724cc4cb4_29614464',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '875bcd9d16f59d70ab5dccdcf8577d86aab16680' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/index.html',
      1 => 1725902082,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:xtCore/pages/navigation/breadcrumb.html' => 1,
  ),
))) {
function content_6771e724cc4cb4_29614464 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ';
if ($_smarty_tpl->getValue('page') == 'index' || $_smarty_tpl->getValue('page') == 'product' || $_smarty_tpl->getValue('page') == 'cart' || $_GET['page_action'] == 'login' || $_GET['page_action'] == 'order_info') {?>
    <?php $_smarty_tpl->assign('show_index_boxes', "false", false, NULL);
}
echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'index_top'), $_smarty_tpl);?>

<!-- Respond.js IE8 support of media queries -->
<!--[if lt IE 9]>
<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo (defined('_SRV_WEB_TEMPLATES') ? constant('_SRV_WEB_TEMPLATES') : null);
echo (defined('_STORE_TEMPLATE') ? constant('_STORE_TEMPLATE') : null);?>
/components/Respond/dest/respond.min.js"><?php echo '</script'; ?>
>
<![endif]-->
<meta name="google-site-verification" content="WAaUjV53OYsKXE-TgfamecXP_dG0LzlsJlJh1ykd_74" />

<div id="site-wrap" class="<?php echo $_smarty_tpl->getValue('page');?>
-wrap<?php if ($_smarty_tpl->getValue('page') != 'index') {?> subpage-wrap<?php }
if ($_GET['page_action']) {?> <?php echo $_GET['page_action'];?>
-action-wrap<?php }?>">
    <?php if (!(null !== ($_GET['viewport'] ?? null)) || $_GET['viewport'] != 'modal') {?>

        <header id="header">
            <div class="meta-navigation">
                <div class="container clearfix">
                    <ul class="meta list-inline pull-left hidden-xs pull-left">
                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'meta_nav_left_first'), $_smarty_tpl);?>

                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('box')->handle(array('name'=>'xt_country_preselect'), $_smarty_tpl);?>

                        <li><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('box')->handle(array('name'=>'language'), $_smarty_tpl);?>
</li>
                        <li><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('box')->handle(array('name'=>'currency'), $_smarty_tpl);?>
</li>
                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'meta_nav_left_last'), $_smarty_tpl);?>

                    </ul>
                    <ul class="user list-inline pull-right">
                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'meta_nav_right_first'), $_smarty_tpl);?>

                        <li><a href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'checkout','paction'=>'shipping','conn'=>'SSL'), $_smarty_tpl);?>
"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_CHECKOUT), $_smarty_tpl);?>
</a></li>
                        <?php if ($_smarty_tpl->getValue('account') == true) {?>
                            <li><a href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'customer','conn'=>'SSL'), $_smarty_tpl);?>
"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>'TEXT_ACCOUNT'), $_smarty_tpl);?>
</a></li>
                            <?php if ($_smarty_tpl->getValue('registered_customer')) {?>
                                <li><a href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'customer','paction'=>'logoff','conn'=>'SSL'), $_smarty_tpl);?>
"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_LOGOFF), $_smarty_tpl);?>
 <span class="customer-name hidden-xs">(<?php if ($_SESSION['customer']->customer_default_address['customers_title']) {
echo $_SESSION['customer']->customer_default_address['customers_title'];?>
 <?php }
echo $_SESSION['customer']->customer_default_address['customers_firstname'][0];?>
. <?php echo $_SESSION['customer']->customer_default_address['customers_lastname'];?>
)</span></a></li>
                            <?php } else { ?>
                                <li><a href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'customer','paction'=>'login','conn'=>'SSL'), $_smarty_tpl);?>
" data-toggle="modal" data-target="#loginModal" data-remote="false"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_LOGIN), $_smarty_tpl);?>
</a></li>
                            <?php }?>
                        <?php } elseif ($_smarty_tpl->getValue('guest_account') == true) {?>
                            <?php if ($_smarty_tpl->getValue('amazon_customer')) {?>
                             <li><a href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'customer','paction'=>'logoff','conn'=>'SSL'), $_smarty_tpl);?>
"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_LOGOFF), $_smarty_tpl);?>
</a></li>
                            <?php } else { ?>
                            <li><a href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'customer','paction'=>'logoff','conn'=>'SSL'), $_smarty_tpl);?>
"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_LOGOFF_GUEST), $_smarty_tpl);?>
</a></li>
                            <?php }?>
                        <?php }?>
                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'meta_nav_right_last'), $_smarty_tpl);?>

                    </ul>
                </div>
            </div><!-- .meta-navigation -->
            <div class="header-top">
                <div class="container">
                    <div class="row text-center-xs">
                        <div class="col col-sm-4 col-md-5 col-logo">
                            <div class="inner branding">
                                <p class="logo">
                                    <a href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'index'), $_smarty_tpl);?>
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
                                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('box')->handle(array('name'=>'search','htmlonly'=>1), $_smarty_tpl);?>

                            </div>
                        </div>
                        <div class="col col-sm-4 col-md-3 hidden-xs">
                            <div class="inner top-cart text-right">
                                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('box')->handle(array('name'=>'cart'), $_smarty_tpl);?>

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
                            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('box')->handle(array('name'=>'search','htmlonly'=>1), $_smarty_tpl);?>

                        </div>
                    </div>
                    <div class="navbar-collapse collapse">
                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('box_cache')->handle(array('name'=>'categories_recursive','position'=>'navbar','mega'=>true,'cache_id'=>'navbar'), $_smarty_tpl);?>

                    </div>
                </div>
            </div><!-- .main-navigation -->
        </header><!-- #header -->

        <?php if ($_smarty_tpl->getValue('page') != 'index' && $_smarty_tpl->getValue('page') != '404' && $_smarty_tpl->getValue('page') != 'checkout') {?>
            <div class="breadcrumb-container">
                <div class="container">
            <?php $_smarty_tpl->renderSubTemplate("file:xtCore/pages/navigation/breadcrumb.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('type'=>"tpl_include"), (int) 0, $_smarty_current_dir);
?>
                </div>
            </div>
        <?php }?>

        <noscript>
            <div class="container">
                <div class="alert alert-danger text-center">
                    <p><i class="fa fa-3x fa-exclamation-triangle"></i></p>
                    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_JS_DISABLED_WARNING), $_smarty_tpl);?>

                </div>
            </div>
        </noscript>

		<div id="navContainer" class="container">
			<ul class="navbar-mega hidden hidden-float-breakpoint with-backdrop-shadow"></ul>
		</div>

		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'pre_content_container'), $_smarty_tpl);?>


		<?php if (!(null !== ($_smarty_tpl->getValue('disable_content_container') ?? null))) {?>
        <div id="container" class="container">
            <?php echo $_SESSION['ppc_express_checkout'];?>

            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'container_top'), $_smarty_tpl);?>

            <div id="content" class="row">
                <div class="col primary<?php if ($_smarty_tpl->getValue('show_index_boxes') == 'true') {?> col-sm-8 col-sm-push-4 col-md-9 col-md-push-3<?php } else { ?> col-sm-12<?php }?>">
                    <?php echo $_smarty_tpl->getValue('content');?>

                </div>
                <?php if ($_smarty_tpl->getValue('show_index_boxes') == 'true') {?>
                    <div class="col secondary col-sm-4 col-sm-pull-8 col-md-3 col-md-pull-9">
                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'index_boxes_top'), $_smarty_tpl);?>

                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('box_cache')->handle(array('name'=>'categories_recursive','position'=>'sidebar','cache_id'=>'sidebar'), $_smarty_tpl);?>

                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('box')->handle(array('name'=>'xt_blog','type'=>'user','box_type'=>'categories'), $_smarty_tpl);?>

						<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('box')->handle(array('name'=>'xt_blog','box_type'=>'featured_messages','limit'=>5,'type'=>'user'), $_smarty_tpl);?>

                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'index_boxes_middle'), $_smarty_tpl);?>

                        <div class="sidebar-products">
                            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'sidebar_products_top'), $_smarty_tpl);?>

                            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('box')->handle(array('name'=>'xt_live_shopping','type'=>'user','limit'=>2), $_smarty_tpl);?>

                            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('box')->handle(array('name'=>'xt_last_viewed_products','type'=>'user'), $_smarty_tpl);?>

                            <?php if ($_smarty_tpl->getValue('page') != 'xt_bestseller_products') {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('box')->handle(array('name'=>'xt_bestseller_products','type'=>'user'), $_smarty_tpl);
}?>
                            <?php if ($_smarty_tpl->getValue('page') != 'xt_special_products') {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('box')->handle(array('name'=>'xt_special_products','type'=>'user','order_by'=>'p.products_id'), $_smarty_tpl);
}?>
                            <?php if ($_smarty_tpl->getValue('page') != 'xt_new_products') {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('box')->handle(array('name'=>'xt_new_products','type'=>'user','order_by'=>'p.last_modified DESC'), $_smarty_tpl);
}?>
                            <?php if ($_smarty_tpl->getValue('page') != 'xt_upcoming_products') {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('box')->handle(array('name'=>'xt_upcoming_products','type'=>'user','order_by'=>'p.date_available'), $_smarty_tpl);
}?>
                            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'sidebar_products_bottom'), $_smarty_tpl);?>

                        </div>
                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'index_boxes_bottom'), $_smarty_tpl);?>

                    </div>
                <?php }?>
            </div><!-- #content -->
            
                        
            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'container_bottom'), $_smarty_tpl);?>

        </div><!-- #container -->
		<?php }?>

        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'after_content_container'), $_smarty_tpl);?>


        <div class="clearfix"></div>

        <footer id="footer">
            <div class="container">
                <div id="footer-cols">
                    <div class="row">
                        <div class="col col-sm-4">
                            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('box_cache')->handle(array('name'=>'footer_contact','htmlonly'=>1), $_smarty_tpl);?>

                            
                            <a target="_blank" href="https://www.instagram.com/aquarus_stone?utm_source=qr&igsh=MXR5anV2MDJrbHJkbw==" >
                                <img width="50" height="50" alt="Instagramm Logo" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMzIuMDA0IiBoZWlnaHQ9IjEzMiIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiPgoJPGRlZnM+CgkJPGxpbmVhckdyYWRpZW50IGlkPSJiIj4KCQkJPHN0b3Agb2Zmc2V0PSIwIiBzdG9wLWNvbG9yPSIjMzc3MWM4Ii8+CgkJCTxzdG9wIHN0b3AtY29sb3I9IiMzNzcxYzgiIG9mZnNldD0iLjEyOCIvPgoJCQk8c3RvcCBvZmZzZXQ9IjEiIHN0b3AtY29sb3I9IiM2MGYiIHN0b3Atb3BhY2l0eT0iMCIvPgoJCTwvbGluZWFyR3JhZGllbnQ+CgkJPGxpbmVhckdyYWRpZW50IGlkPSJhIj4KCQkJPHN0b3Agb2Zmc2V0PSIwIiBzdG9wLWNvbG9yPSIjZmQ1Ii8+CgkJCTxzdG9wIG9mZnNldD0iLjEiIHN0b3AtY29sb3I9IiNmZDUiLz4KCQkJPHN0b3Agb2Zmc2V0PSIuNSIgc3RvcC1jb2xvcj0iI2ZmNTQzZSIvPgoJCQk8c3RvcCBvZmZzZXQ9IjEiIHN0b3AtY29sb3I9IiNjODM3YWIiLz4KCQk8L2xpbmVhckdyYWRpZW50PgoJCTxyYWRpYWxHcmFkaWVudCBpZD0iYyIgY3g9IjE1OC40MjkiIGN5PSI1NzguMDg4IiByPSI2NSIgeGxpbms6aHJlZj0iI2EiIGdyYWRpZW50VW5pdHM9InVzZXJTcGFjZU9uVXNlIiBncmFkaWVudFRyYW5zZm9ybT0ibWF0cml4KDAgLTEuOTgxOTggMS44NDM5IDAgLTEwMzEuNDAyIDQ1NC4wMDQpIiBmeD0iMTU4LjQyOSIgZnk9IjU3OC4wODgiLz4KCQk8cmFkaWFsR3JhZGllbnQgaWQ9ImQiIGN4PSIxNDcuNjk0IiBjeT0iNDczLjQ1NSIgcj0iNjUiIHhsaW5rOmhyZWY9IiNiIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgZ3JhZGllbnRUcmFuc2Zvcm09Im1hdHJpeCguMTczOTQgLjg2ODcyIC0zLjU4MTggLjcxNzE4IDE2NDguMzQ4IC00NTguNDkzKSIgZng9IjE0Ny42OTQiIGZ5PSI0NzMuNDU1Ii8+Cgk8L2RlZnM+Cgk8cGF0aCBmaWxsPSJ1cmwoI2MpIiBkPSJNNjUuMDMgMEMzNy44ODggMCAyOS45NS4wMjggMjguNDA3LjE1NmMtNS41Ny40NjMtOS4wMzYgMS4zNC0xMi44MTIgMy4yMi0yLjkxIDEuNDQ1LTUuMjA1IDMuMTItNy40NyA1LjQ2OEM0IDEzLjEyNiAxLjUgMTguMzk0LjU5NSAyNC42NTZjLS40NCAzLjA0LS41NjggMy42Ni0uNTk0IDE5LjE4OC0uMDEgNS4xNzYgMCAxMS45ODggMCAyMS4xMjUgMCAyNy4xMi4wMyAzNS4wNS4xNiAzNi41OS40NSA1LjQyIDEuMyA4LjgzIDMuMSAxMi41NiAzLjQ0IDcuMTQgMTAuMDEgMTIuNSAxNy43NSAxNC41IDIuNjguNjkgNS42NCAxLjA3IDkuNDQgMS4yNSAxLjYxLjA3IDE4LjAyLjEyIDM0LjQ0LjEyIDE2LjQyIDAgMzIuODQtLjAyIDM0LjQxLS4xIDQuNC0uMjA3IDYuOTU1LS41NSA5Ljc4LTEuMjggNy43OS0yLjAxIDE0LjI0LTcuMjkgMTcuNzUtMTQuNTMgMS43NjUtMy42NCAyLjY2LTcuMTggMy4wNjUtMTIuMzE3LjA4OC0xLjEyLjEyNS0xOC45NzcuMTI1LTM2LjgxIDAtMTcuODM2LS4wNC0zNS42Ni0uMTI4LTM2Ljc4LS40MS01LjIyLTEuMzA1LTguNzMtMy4xMjctMTIuNDQtMS40OTUtMy4wMzctMy4xNTUtNS4zMDUtNS41NjUtNy42MjRDMTE2LjkgNCAxMTEuNjQgMS41IDEwNS4zNzIuNTk2IDEwMi4zMzUuMTU3IDEwMS43My4wMjcgODYuMTkgMEg2NS4wM3oiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDEuMDA0IDEpIi8+Cgk8cGF0aCBmaWxsPSJ1cmwoI2QpIiBkPSJNNjUuMDMgMEMzNy44ODggMCAyOS45NS4wMjggMjguNDA3LjE1NmMtNS41Ny40NjMtOS4wMzYgMS4zNC0xMi44MTIgMy4yMi0yLjkxIDEuNDQ1LTUuMjA1IDMuMTItNy40NyA1LjQ2OEM0IDEzLjEyNiAxLjUgMTguMzk0LjU5NSAyNC42NTZjLS40NCAzLjA0LS41NjggMy42Ni0uNTk0IDE5LjE4OC0uMDEgNS4xNzYgMCAxMS45ODggMCAyMS4xMjUgMCAyNy4xMi4wMyAzNS4wNS4xNiAzNi41OS40NSA1LjQyIDEuMyA4LjgzIDMuMSAxMi41NiAzLjQ0IDcuMTQgMTAuMDEgMTIuNSAxNy43NSAxNC41IDIuNjguNjkgNS42NCAxLjA3IDkuNDQgMS4yNSAxLjYxLjA3IDE4LjAyLjEyIDM0LjQ0LjEyIDE2LjQyIDAgMzIuODQtLjAyIDM0LjQxLS4xIDQuNC0uMjA3IDYuOTU1LS41NSA5Ljc4LTEuMjggNy43OS0yLjAxIDE0LjI0LTcuMjkgMTcuNzUtMTQuNTMgMS43NjUtMy42NCAyLjY2LTcuMTggMy4wNjUtMTIuMzE3LjA4OC0xLjEyLjEyNS0xOC45NzcuMTI1LTM2LjgxIDAtMTcuODM2LS4wNC0zNS42Ni0uMTI4LTM2Ljc4LS40MS01LjIyLTEuMzA1LTguNzMtMy4xMjctMTIuNDQtMS40OTUtMy4wMzctMy4xNTUtNS4zMDUtNS41NjUtNy42MjRDMTE2LjkgNCAxMTEuNjQgMS41IDEwNS4zNzIuNTk2IDEwMi4zMzUuMTU3IDEwMS43My4wMjcgODYuMTkgMEg2NS4wM3oiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDEuMDA0IDEpIi8+Cgk8cGF0aCBmaWxsPSIjZmZmIiBkPSJNNjYuMDA0IDE4Yy0xMy4wMzYgMC0xNC42NzIuMDU3LTE5Ljc5Mi4yOS01LjExLjIzNC04LjU5OCAxLjA0My0xMS42NSAyLjIzLTMuMTU3IDEuMjI2LTUuODM1IDIuODY2LTguNTAzIDUuNTM1LTIuNjcgMi42NjgtNC4zMSA1LjM0Ni01LjU0IDguNTAyLTEuMTkgMy4wNTMtMiA2LjU0Mi0yLjIzIDExLjY1QzE4LjA2IDUxLjMyNyAxOCA1Mi45NjQgMTggNjZzLjA1OCAxNC42NjcuMjkgMTkuNzg3Yy4yMzUgNS4xMSAxLjA0NCA4LjU5OCAyLjIzIDExLjY1IDEuMjI3IDMuMTU3IDIuODY3IDUuODM1IDUuNTM2IDguNTAzIDIuNjY3IDIuNjcgNS4zNDUgNC4zMTQgOC41IDUuNTQgMy4wNTQgMS4xODcgNi41NDMgMS45OTYgMTEuNjUyIDIuMjMgNS4xMi4yMzMgNi43NTUuMjkgMTkuNzkuMjkgMTMuMDM3IDAgMTQuNjY4LS4wNTcgMTkuNzg4LS4yOSA1LjExLS4yMzQgOC42MDItMS4wNDMgMTEuNjU2LTIuMjMgMy4xNTYtMS4yMjYgNS44My0yLjg3IDguNDk3LTUuNTQgMi42Ny0yLjY2OCA0LjMxLTUuMzQ2IDUuNTQtOC41MDIgMS4xOC0zLjA1MyAxLjk5LTYuNTQyIDIuMjMtMTEuNjUuMjMtNS4xMi4yOS02Ljc1Mi4yOS0xOS43ODggMC0xMy4wMzYtLjA2LTE0LjY3Mi0uMjktMTkuNzkyLS4yNC01LjExLTEuMDUtOC41OTgtMi4yMy0xMS42NS0xLjIzLTMuMTU3LTIuODctNS44MzUtNS41NC04LjUwMy0yLjY3LTIuNjctNS4zNC00LjMxLTguNS01LjUzNS0zLjA2LTEuMTg3LTYuNTUtMS45OTYtMTEuNjYtMi4yMy01LjEyLS4yMzMtNi43NS0uMjktMTkuNzktLjI5em0tNC4zMDYgOC42NWMxLjI3OC0uMDAyIDIuNzA0IDAgNC4zMDYgMCAxMi44MTYgMCAxNC4zMzUuMDQ2IDE5LjM5Ni4yNzYgNC42OC4yMTQgNy4yMi45OTYgOC45MTIgMS42NTMgMi4yNC44NyAzLjgzNyAxLjkxIDUuNTE2IDMuNTkgMS42OCAxLjY4IDIuNzIgMy4yOCAzLjU5MiA1LjUyLjY1NyAxLjY5IDEuNDQgNC4yMyAxLjY1MyA4LjkxLjIzIDUuMDYuMjggNi41OC4yOCAxOS4zOXMtLjA1IDE0LjMzLS4yOCAxOS4zOWMtLjIxNCA0LjY4LS45OTYgNy4yMi0xLjY1MyA4LjkxLS44NyAyLjI0LTEuOTEyIDMuODM1LTMuNTkyIDUuNTE0LTEuNjggMS42OC0zLjI3NSAyLjcyLTUuNTE2IDMuNTktMS42OS42Ni00LjIzMiAxLjQ0LTguOTEyIDEuNjU0LTUuMDYuMjMtNi41OC4yOC0xOS4zOTYuMjgtMTIuODE3IDAtMTQuMzM2LS4wNS0xOS4zOTYtLjI4LTQuNjgtLjIxNi03LjIyLS45OTgtOC45MTMtMS42NTUtMi4yNC0uODctMy44NC0xLjkxLTUuNTItMy41OS0xLjY4LTEuNjgtMi43Mi0zLjI3Ni0zLjU5Mi01LjUxNy0uNjU3LTEuNjktMS40NC00LjIzLTEuNjUzLTguOTEtLjIzLTUuMDYtLjI3Ni02LjU4LS4yNzYtMTkuMzk4cy4wNDYtMTQuMzMuMjc2LTE5LjM5Yy4yMTQtNC42OC45OTYtNy4yMiAxLjY1My04LjkxMi44Ny0yLjI0IDEuOTEyLTMuODQgMy41OTItNS41MiAxLjY4LTEuNjggMy4yOC0yLjcyIDUuNTItMy41OTIgMS42OTItLjY2IDQuMjMzLTEuNDQgOC45MTMtMS42NTUgNC40MjgtLjIgNi4xNDQtLjI2IDE1LjA5LS4yN3ptMjkuOTI4IDcuOTdjLTMuMTggMC01Ljc2IDIuNTc3LTUuNzYgNS43NTggMCAzLjE4IDIuNTggNS43NiA1Ljc2IDUuNzYgMy4xOCAwIDUuNzYtMi41OCA1Ljc2LTUuNzYgMC0zLjE4LTIuNTgtNS43Ni01Ljc2LTUuNzZ6bS0yNS42MjIgNi43M2MtMTMuNjEzIDAtMjQuNjUgMTEuMDM3LTI0LjY1IDI0LjY1IDAgMTMuNjEzIDExLjAzNyAyNC42NDUgMjQuNjUgMjQuNjQ1Qzc5LjYxNyA5MC42NDUgOTAuNjUgNzkuNjEzIDkwLjY1IDY2Uzc5LjYxNiA0MS4zNSA2Ni4wMDMgNDEuMzV6bTAgOC42NWM4LjgzNiAwIDE2IDcuMTYzIDE2IDE2IDAgOC44MzYtNy4xNjQgMTYtMTYgMTYtOC44MzcgMC0xNi03LjE2NC0xNi0xNiAwLTguODM3IDcuMTYzLTE2IDE2LTE2eiIvPgo8L3N2Zz4=" />
                            </a>
                        </div>
                        <div class="col col-sm-4">
                            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('box_cache')->handle(array('name'=>'footer_left','htmlonly'=>1), $_smarty_tpl);?>

                        </div>
                        <div class="col col-sm-4">
                            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('box_cache')->handle(array('name'=>'footer_right','htmlonly'=>1), $_smarty_tpl);?>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-sm-4">
                            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('box_cache')->handle(array('name'=>'manufacturers','order_by'=>'m.manufacturers_name'), $_smarty_tpl);?>

                        </div>
                        <div class="col col-sm-4">
                            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('box')->handle(array('name'=>'language'), $_smarty_tpl);?>

                            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('box')->handle(array('name'=>'currency'), $_smarty_tpl);?>

                        </div>
                        <div class="col col-sm-4">
                            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('box_cache')->handle(array('name'=>'payment_logos'), $_smarty_tpl);?>

                            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('box')->handle(array('name'=>'trusted_shops_ratings'), $_smarty_tpl);?>

                            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('box')->handle(array('name'=>'trusted_shops_seal'), $_smarty_tpl);?>

                            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('box')->handle(array('name'=>'trusted_shops_video'), $_smarty_tpl);?>

                        </div>
                    </div>
                </div>
                <div id="footer-meta" class="text-center text-muted">
                    [<copyright>]
                    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'index_footer_tpl'), $_smarty_tpl);?>

                    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('box')->handle(array('name'=>'trusted_shops_rich_snippet'), $_smarty_tpl);?>

                    <!--img src="cronjob.php" width="1" height="1" alt="" /-->
                    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('cronjob')->handle(array('type'=>"ajax"), $_smarty_tpl);?>

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
                        <p class="h3 modal-title"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_LOGIN), $_smarty_tpl);?>
</p>
                    </div>
                    <div class="modal-body">
                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('box')->handle(array('name'=>'loginbox'), $_smarty_tpl);?>

                    </div>
                </div>
            </div>
        </div><!-- #loginModal -->
        
        <a id="back-to-top" class="hidden hidden-xs" href="<?php echo $_SERVER['REQUEST_URI'];?>
#top" rel="nofollow">
            <i class="fa fa-3x fa-arrow-circle-o-up"></i>
            <span class="sr-only">Back to Top</span>
        </a>

        <?php if ($_smarty_tpl->getValue('page') != 'cart' && $_smarty_tpl->getValue('page') != 'checkout') {?>
            <div class="bottom-cart">
                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('box')->handle(array('name'=>'cart'), $_smarty_tpl);?>

            </div>
        <?php }?>

    <?php } else { ?>
        <?php echo $_smarty_tpl->getValue('content');?>

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
