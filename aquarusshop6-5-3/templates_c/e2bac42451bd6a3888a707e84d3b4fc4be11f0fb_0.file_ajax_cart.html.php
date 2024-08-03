<?php
/* Smarty version 5.1.0, created on 2024-08-03 01:14:37
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_cart_popup/templates/ajax_cart.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_66ad685dd80461_50554937',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'e2bac42451bd6a3888a707e84d3b4fc4be11f0fb' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_cart_popup/templates/ajax_cart.html',
      1 => 1697144040,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_66ad685dd80461_50554937 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_cart_popup/templates';
?><div id="xt_cart_popup" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="xt_cart_popup_label" data-ajax-url="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'cart','paction'=>'index'), $_smarty_tpl);?>
">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="xt_cart_popup_label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_CART), $_smarty_tpl);?>
</h4>
            </div>
            <div class="modal-footer clearfix">
                <button type="button" class="btn btn-primary pull-left" data-dismiss="modal"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>BUTTON_CONTINUE_SHOPPING), $_smarty_tpl);?>
</button>
                <?php if ($_smarty_tpl->getValue('show_checkout_button')) {?><a type="button" class="btn btn-success" href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'checkout','paction'=>'shipping','conn'=>'SSL'), $_smarty_tpl);?>
"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>BUTTON_CHECKOUT), $_smarty_tpl);?>
</a><?php }?>
                <a type="button" class="btn btn-success" href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'cart','paction'=>'index','conn'=>'SSL'), $_smarty_tpl);?>
"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_CART), $_smarty_tpl);?>
</a>
            </div>
            <div class="modal-body">
                Loading...
            </div>
            <?php if ($_smarty_tpl->getValue('message')) {?>
            <div class="modal-footer clearfix">
                <?php echo $_smarty_tpl->getValue('message');?>

            </div>
            <?php }?>
            <!--div class="modal-footer clearfix">
                <button type="button" class="btn btn-primary pull-left" data-dismiss="modal"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>BUTTON_CONTINUE_SHOPPING), $_smarty_tpl);?>
</button>
                <?php if ($_smarty_tpl->getValue('show_checkout_button')) {?><a type="button" class="btn btn-success" href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'checkout','paction'=>'shipping','conn'=>'SSL'), $_smarty_tpl);?>
"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>BUTTON_CHECKOUT), $_smarty_tpl);?>
</a><?php }?>
                <a type="button" class="btn btn-success" href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'cart','paction'=>'index','conn'=>'SSL'), $_smarty_tpl);?>
"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_CART), $_smarty_tpl);?>
</a>
            </div-->
        </div>
    </div>
</div>


<?php echo '<script'; ?>
>
    // get cart content with jquery ajax load
    document.addEventListener("DOMContentLoaded",function(event){
        $('#xt_cart_popup').on('show.bs.modal', function () {
            var m = $(this)
            var ajaxUrl = m.data('ajax-url') + ' #cart-table';

            m.find('.modal-body').load(ajaxUrl, function() {
                $(this).find('button, .btn, .btn-group').hide()
                $(this).find('input').prop('disabled', true);
                $(this).find('input[type=number]').removeProp('type');
            });
        });
    });
<?php echo '</script'; ?>
>

<?php }
}
