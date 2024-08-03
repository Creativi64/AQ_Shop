<?php
/* Smarty version 5.1.0, created on 2024-08-03 01:15:03
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes/box_cart.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_66ad68777e86f9_23981575',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '479e1d93e123a8b173cea0d3d23af13c9ecbb4e4' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes/box_cart.html',
      1 => 1697144118,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_66ad68777e86f9_23981575 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes';
if ((defined('_CUST_STATUS_SHOW_PRICE') ? constant('_CUST_STATUS_SHOW_PRICE') : null) == '1') {?>
    <div class="box-cart">
        <div class="inner btn-group" role="group" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_CART), $_smarty_tpl);?>
">
            <?php if ($_smarty_tpl->getValue('show_cart_content') && $_smarty_tpl->getValue('content_count')) {?>
                <div class="btn-group hidden-xs">
                    <button type="button" class="btn btn-default btn-left dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php echo $_smarty_tpl->getValue('content_count');?>
 <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_ARTICLE), $_smarty_tpl);?>
&nbsp;&nbsp;&nbsp;<span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'cart'), $_smarty_tpl);?>
">
                                <strong class="text-uppercase"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SUB_TOTAL), $_smarty_tpl);?>
:</strong><br />
                                <?php echo $_smarty_tpl->getSmarty()->getModifierCallback('trim')(preg_replace('!<[^>]*?>!', ' ', (string) $_smarty_tpl->getValue('cart_total')));?>

                            </a>
                        </li>
                    </ul>
                </div>
                <div class="btn-group dropup visible-xs">
                    <button type="button" class="btn btn-cart btn-left dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="text-uppercase"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_CART), $_smarty_tpl);?>
:</span>
                        <?php echo $_smarty_tpl->getValue('content_count');?>
 <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_ARTICLE), $_smarty_tpl);?>
 <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'cart'), $_smarty_tpl);?>
">
                                <strong class="text-uppercase"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SUB_TOTAL), $_smarty_tpl);?>
:</strong>
                                <?php echo $_smarty_tpl->getSmarty()->getModifierCallback('trim')(preg_replace('!<[^>]*?>!', ' ', (string) $_smarty_tpl->getValue('cart_total')));?>

                            </a>
                        </li>
                    </ul>
                </div>
            <?php } else { ?>
                <a href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'cart'), $_smarty_tpl);?>
" class="btn btn-default btn-left hidden-xs">
                    0 <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_ARTICLE), $_smarty_tpl);?>

                </a>
                <a href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'cart'), $_smarty_tpl);?>
" class="btn btn-cart btn-left visible-xs">
                    0 <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_ARTICLE), $_smarty_tpl);?>

                </a>
            <?php }?>
            <a href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'cart'), $_smarty_tpl);?>
" class="btn btn-cart btn-right" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_CART), $_smarty_tpl);?>
" data-toggle="tooltip" data-placement="auto">
                <i class="fa fa-shopping-basket"></i>
                <span class="sr-only"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_CART), $_smarty_tpl);?>
</span>
            </a>
        </div>
    </div>
<?php }
}
}
