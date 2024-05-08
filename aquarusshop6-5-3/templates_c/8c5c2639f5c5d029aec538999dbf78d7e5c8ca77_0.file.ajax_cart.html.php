<?php
/* Smarty version 4.3.2, created on 2024-05-08 17:56:12
  from '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_cart_popup/templates/ajax_cart.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_663ba09c366a92_14055769',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '8c5c2639f5c5d029aec538999dbf78d7e5c8ca77' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_cart_popup/templates/ajax_cart.html',
      1 => 1697144040,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_663ba09c366a92_14055769 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.link.php','function'=>'smarty_function_link',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),));
?>
<div id="xt_cart_popup" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="xt_cart_popup_label" data-ajax-url="<?php echo smarty_function_link(array('page'=>'cart','paction'=>'index'),$_smarty_tpl);?>
">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="xt_cart_popup_label"><?php echo smarty_function_txt(array('key'=>TEXT_CART),$_smarty_tpl);?>
</h4>
            </div>
            <div class="modal-footer clearfix">
                <button type="button" class="btn btn-primary pull-left" data-dismiss="modal"><?php echo smarty_function_txt(array('key'=>BUTTON_CONTINUE_SHOPPING),$_smarty_tpl);?>
</button>
                <?php if ($_smarty_tpl->tpl_vars['show_checkout_button']->value) {?><a type="button" class="btn btn-success" href="<?php echo smarty_function_link(array('page'=>'checkout','paction'=>'shipping','conn'=>'SSL'),$_smarty_tpl);?>
"><?php echo smarty_function_txt(array('key'=>BUTTON_CHECKOUT),$_smarty_tpl);?>
</a><?php }?>
                <a type="button" class="btn btn-success" href="<?php echo smarty_function_link(array('page'=>'cart','paction'=>'index','conn'=>'SSL'),$_smarty_tpl);?>
"><?php echo smarty_function_txt(array('key'=>TEXT_CART),$_smarty_tpl);?>
</a>
            </div>
            <div class="modal-body">
                Loading...
            </div>
            <?php if ($_smarty_tpl->tpl_vars['message']->value) {?>
            <div class="modal-footer clearfix">
                <?php echo $_smarty_tpl->tpl_vars['message']->value;?>

            </div>
            <?php }?>
            <!--div class="modal-footer clearfix">
                <button type="button" class="btn btn-primary pull-left" data-dismiss="modal"><?php echo smarty_function_txt(array('key'=>BUTTON_CONTINUE_SHOPPING),$_smarty_tpl);?>
</button>
                <?php if ($_smarty_tpl->tpl_vars['show_checkout_button']->value) {?><a type="button" class="btn btn-success" href="<?php echo smarty_function_link(array('page'=>'checkout','paction'=>'shipping','conn'=>'SSL'),$_smarty_tpl);?>
"><?php echo smarty_function_txt(array('key'=>BUTTON_CHECKOUT),$_smarty_tpl);?>
</a><?php }?>
                <a type="button" class="btn btn-success" href="<?php echo smarty_function_link(array('page'=>'cart','paction'=>'index','conn'=>'SSL'),$_smarty_tpl);?>
"><?php echo smarty_function_txt(array('key'=>TEXT_CART),$_smarty_tpl);?>
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
