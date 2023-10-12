<?php
/* Smarty version 4.3.0, created on 2023-06-19 13:53:20
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive/xtCore/boxes/box_cart.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.0',
  'unifunc' => 'content_649041b00c29f8_64815690',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '47c0cbc01abc2058a48291116e1ec68d3757a4a7' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive/xtCore/boxes/box_cart.html',
      1 => 1687006095,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_649041b00c29f8_64815690 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.link.php','function'=>'smarty_function_link',),));
if ((defined('_CUST_STATUS_SHOW_PRICE') ? constant('_CUST_STATUS_SHOW_PRICE') : null) == '1') {?>
    <div class="box-cart">
        <div class="inner btn-group" role="group" aria-label="<?php echo smarty_function_txt(array('key'=>TEXT_CART),$_smarty_tpl);?>
">
            <?php if ($_smarty_tpl->tpl_vars['show_cart_content']->value && $_smarty_tpl->tpl_vars['content_count']->value) {?>
                <div class="btn-group hidden-xs">
                    <button type="button" class="btn btn-default btn-left dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php echo $_smarty_tpl->tpl_vars['content_count']->value;?>
 <?php echo smarty_function_txt(array('key'=>TEXT_ARTICLE),$_smarty_tpl);?>
&nbsp;&nbsp;&nbsp;<span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="<?php echo smarty_function_link(array('page'=>'cart'),$_smarty_tpl);?>
">
                                <strong class="text-uppercase"><?php echo smarty_function_txt(array('key'=>TEXT_SUB_TOTAL),$_smarty_tpl);?>
:</strong><br />
                                <?php echo trim(preg_replace('!<[^>]*?>!', ' ', $_smarty_tpl->tpl_vars['cart_total']->value ?: ''));?>

                            </a>
                        </li>
                    </ul>
                </div>
                <div class="btn-group dropup visible-xs">
                    <button type="button" class="btn btn-cart btn-left dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="text-uppercase"><?php echo smarty_function_txt(array('key'=>TEXT_CART),$_smarty_tpl);?>
:</span>
                        <?php echo $_smarty_tpl->tpl_vars['content_count']->value;?>
 <?php echo smarty_function_txt(array('key'=>TEXT_ARTICLE),$_smarty_tpl);?>
 <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="<?php echo smarty_function_link(array('page'=>'cart'),$_smarty_tpl);?>
">
                                <strong class="text-uppercase"><?php echo smarty_function_txt(array('key'=>TEXT_SUB_TOTAL),$_smarty_tpl);?>
:</strong>
                                <?php echo trim(preg_replace('!<[^>]*?>!', ' ', $_smarty_tpl->tpl_vars['cart_total']->value ?: ''));?>

                            </a>
                        </li>
                    </ul>
                </div>
            <?php } else { ?>
                <a href="<?php echo smarty_function_link(array('page'=>'cart'),$_smarty_tpl);?>
" class="btn btn-default btn-left hidden-xs">
                    0 <?php echo smarty_function_txt(array('key'=>TEXT_ARTICLE),$_smarty_tpl);?>

                </a>
                <a href="<?php echo smarty_function_link(array('page'=>'cart'),$_smarty_tpl);?>
" class="btn btn-cart btn-left visible-xs">
                    0 <?php echo smarty_function_txt(array('key'=>TEXT_ARTICLE),$_smarty_tpl);?>

                </a>
            <?php }?>
            <a href="<?php echo smarty_function_link(array('page'=>'cart'),$_smarty_tpl);?>
" class="btn btn-cart btn-right" title="<?php echo smarty_function_txt(array('key'=>TEXT_CART),$_smarty_tpl);?>
" data-toggle="tooltip" data-placement="auto">
                <i class="fa fa-shopping-basket"></i>
                <span class="sr-only"><?php echo smarty_function_txt(array('key'=>TEXT_CART),$_smarty_tpl);?>
</span>
            </a>
        </div>
    </div>
<?php }
}
}
