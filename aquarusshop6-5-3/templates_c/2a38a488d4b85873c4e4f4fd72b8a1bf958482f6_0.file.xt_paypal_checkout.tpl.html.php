<?php
/* Smarty version 4.3.2, created on 2024-07-22 18:41:22
  from '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_paypal_checkout/templates/xt_paypal_checkout.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_669e8bb2d2eb38_35082785',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2a38a488d4b85873c4e4f4fd72b8a1bf958482f6' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_paypal_checkout/templates/xt_paypal_checkout.tpl.html',
      1 => 1721239475,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:./paypal-checkout-wait-modal-simple.tpl.html' => 1,
  ),
),false)) {
function content_669e8bb2d2eb38_35082785 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.img.php','function'=>'smarty_function_img',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.form.php','function'=>'smarty_function_form',),2=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),));
if (trim($_smarty_tpl->tpl_vars['payment_name']->value) != '' && trim($_smarty_tpl->tpl_vars['payment_code']->value) != '') {?>
    <div id="<?php echo $_smarty_tpl->tpl_vars['payment_code']->value;?>
" class="item item-<?php echo $_smarty_tpl->tpl_vars['payment_code']->value;?>
 <?php if ($_smarty_tpl->tpl_vars['payment_code']->value == $_smarty_tpl->tpl_vars['payment_selected']->value) {?> selected<?php }?> payment-container"
        style="<?php if ($_smarty_tpl->tpl_vars['payment_code']->value == 'applepay') {?>display:none<?php }?>">
        <?php if (trim($_smarty_tpl->tpl_vars['payment_icon']->value) != '') {?>
            <?php echo smarty_function_img(array('img'=>$_smarty_tpl->tpl_vars['payment_icon']->value,'type'=>'w_media_payment','alt'=>$_smarty_tpl->tpl_vars['payment_name']->value,'class'=>"icon img-responsive img-thumbnail___ pull-right",'style'=>"margin-top:-15px;"),$_smarty_tpl);?>

        <?php }?>
        <header data-toggle="collapse" data-target=".item-<?php echo $_smarty_tpl->tpl_vars['payment_code']->value;?>
 .collapse">
            <label class="cursor-pointer">
                <span class="check" style="display:inline-block;width: 25px;">
                    <?php if ($_smarty_tpl->tpl_vars['payment_hidden']->value == true) {?>
                        <?php echo smarty_function_form(array('type'=>'hidden','name'=>'selected_payment','value'=>((string)$_smarty_tpl->tpl_vars['payment_code']->value)),$_smarty_tpl);?>

                    <?php } else { ?>
                        <?php if ($_smarty_tpl->tpl_vars['payment_code']->value == $_smarty_tpl->tpl_vars['payment_selected']->value) {?>
                            <?php echo smarty_function_form(array('type'=>'radio','name'=>'selected_payment','value'=>((string)$_smarty_tpl->tpl_vars['payment_code']->value),'checked'=>true,'style'=>"vertical-align: text-top;"),$_smarty_tpl);?>

                        <?php } else { ?>
                            <?php echo smarty_function_form(array('type'=>'radio','name'=>'selected_payment','value'=>((string)$_smarty_tpl->tpl_vars['payment_code']->value),'style'=>"vertical-align: text-top;"),$_smarty_tpl);?>

                        <?php }?>
                    <?php }?>
                </span>
                <span class="name payment-name"><?php echo $_smarty_tpl->tpl_vars['payment_name']->value;?>
</span>&nbsp;<?php echo $_smarty_tpl->tpl_vars['payment_delete_link']->value;?>

                <?php if ($_smarty_tpl->tpl_vars['payment_price']->value['formated']) {?>
                    <small class="price">&nbsp;<?php echo $_smarty_tpl->tpl_vars['payment_price']->value['formated'];?>
</small>
                <?php }?>
            </label>
        </header>
        <?php if (trim($_smarty_tpl->tpl_vars['payment_desc']->value) != '') {?>
            <div class="desc collapse<?php if ($_smarty_tpl->tpl_vars['payment_code']->value == $_smarty_tpl->tpl_vars['payment_selected']->value) {?> in<?php }?> payment-desc">
                <?php echo $_smarty_tpl->tpl_vars['payment_desc']->value;?>

            </div>
        <?php }?>
    </div>
    <?php echo '<script'; ?>
>
        {
            <?php if ($_smarty_tpl->tpl_vars['payment_code']->value != "xt_paypal_checkout_pui" && !strpos($_smarty_tpl->tpl_vars['payment_code']->value,"_vaulted")) {?>

            let me = document.getElementById("<?php echo $_smarty_tpl->tpl_vars['payment_code']->value;?>
");
            if (me) {
                let parent = me.closest(".list-group-item");
                if (parent) {
                    parent.style.display = "none";
                    console.log('hiding <?php echo $_smarty_tpl->tpl_vars['payment_code']->value;?>
');
                }
            }

            <?php }?>
            <?php if ($_smarty_tpl->tpl_vars['payment_code']->value == "xt_paypal_checkout_googlepay") {?>
            document.addEventListener("GooglePaySdkLoaded", e => {
                try {
                    paypal.Googlepay().config()
                        .then(googlePayConfig => {
                            googlePaymentsClient = getGooglePaymentsClient();
                            if (googlePaymentsClient.isReadyToPay(googlePayConfig)) {
                                let me = document.getElementById("<?php echo $_smarty_tpl->tpl_vars['payment_code']->value;?>
");
                                if (me) {
                                    let parent = me.closest(".list-group-item");
                                    if (parent) {
                                        parent.style.display = "block";
                                    }
                                }
                            }
                        });
                } catch (e) {
                    console.log(e);
                }
            });
            <?php }?>
        }
    <?php echo '</script'; ?>
>
<?php }
if (!$_smarty_tpl->tpl_vars['ppcp_payments_page_script_rendered']->value) {?>
    <?php echo '<script'; ?>
>
        document.addEventListener('DOMContentLoaded', function ()
        {
            document.querySelectorAll('[id$="_vaulted"]').forEach(node => {
                let link = node.querySelector(".ppcp-delete-payment-token");
                if(link)
                {
                    link.addEventListener("click", ev => {

                        if(confirm("<?php echo smarty_function_txt(array('key'=>TEXT_XT_PAYPAL_CHECKOUT_SAVED_PAYMENT_DELETE),$_smarty_tpl);?>
")) {
                            try {
                                ppcWaitModal();
                                deleteSavedPaymentMethod(link.dataset.paymentToken).
                                then(function (response) {
                                    console.log(response);
                                    window.location.reload();
                                }).catch((error) => {
                                    console.log('deleteSavedPaymentMethod', error);
                                });
                            } catch (e) {
                                console.log(e);
                                ppcWaitModal('hide');
                            }
                        }
                    });
                }
            })
        });
    <?php echo '</script'; ?>
>
    <?php $_smarty_tpl->_subTemplateRender("file:./paypal-checkout-wait-modal-simple.tpl.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

    <?php $_smarty_tpl->_assignInScope('ppcp_payments_page_script_rendered', 1 ,false ,32);
}?>

<?php }
}
