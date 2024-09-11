<?php
/* Smarty version 5.1.0, created on 2024-09-10 16:57:44
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_paypal_checkout/templates/xt_paypal_checkout.tpl.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_66e05e685c5146_71688103',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2a38a488d4b85873c4e4f4fd72b8a1bf958482f6' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_paypal_checkout/templates/xt_paypal_checkout.tpl.html',
      1 => 1722634751,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:./paypal-checkout-wait-modal-simple.tpl.html' => 1,
  ),
))) {
function content_66e05e685c5146_71688103 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_paypal_checkout/templates';
if ($_smarty_tpl->getSmarty()->getModifierCallback('trim')($_smarty_tpl->getValue('payment_name')) != '' && $_smarty_tpl->getSmarty()->getModifierCallback('trim')($_smarty_tpl->getValue('payment_code')) != '') {?>
    <div id="<?php echo $_smarty_tpl->getValue('payment_code');?>
" class="item item-<?php echo $_smarty_tpl->getValue('payment_code');?>
 <?php if ($_smarty_tpl->getValue('payment_code') == $_smarty_tpl->getValue('payment_selected')) {?> selected<?php }?> payment-container"
        style="<?php if ($_smarty_tpl->getValue('payment_code') == 'applepay') {?>display:none<?php }?>">
        <?php if ($_smarty_tpl->getSmarty()->getModifierCallback('trim')($_smarty_tpl->getValue('payment_icon')) != '') {?>
            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('img')->handle(array('img'=>$_smarty_tpl->getValue('payment_icon'),'type'=>'w_media_payment','alt'=>$_smarty_tpl->getValue('payment_name'),'class'=>"icon img-responsive img-thumbnail___ pull-right",'style'=>"margin-top:-15px;"), $_smarty_tpl);?>

        <?php }?>
        <header data-toggle="collapse" data-target=".item-<?php echo $_smarty_tpl->getValue('payment_code');?>
 .collapse">
            <label class="cursor-pointer">
                <span class="check" style="display:inline-block;width: 25px;">
                    <?php if ($_smarty_tpl->getValue('payment_hidden') == true) {?>
                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'hidden','name'=>'selected_payment','value'=>((string)$_smarty_tpl->getValue('payment_code'))), $_smarty_tpl);?>

                    <?php } else { ?>
                        <?php if ($_smarty_tpl->getValue('payment_code') == $_smarty_tpl->getValue('payment_selected')) {?>
                            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'radio','name'=>'selected_payment','value'=>((string)$_smarty_tpl->getValue('payment_code')),'checked'=>true,'style'=>"vertical-align: text-top;"), $_smarty_tpl);?>

                        <?php } else { ?>
                            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'radio','name'=>'selected_payment','value'=>((string)$_smarty_tpl->getValue('payment_code')),'style'=>"vertical-align: text-top;"), $_smarty_tpl);?>

                        <?php }?>
                    <?php }?>
                </span>
                <span class="name payment-name"><?php echo $_smarty_tpl->getValue('payment_name');?>
</span>&nbsp;<?php echo $_smarty_tpl->getValue('payment_delete_link');?>

                <?php if ($_smarty_tpl->getValue('payment_price')['formated']) {?>
                    <small class="price">&nbsp;<?php echo $_smarty_tpl->getValue('payment_price')['formated'];?>
</small>
                <?php }?>
            </label>
        </header>
        <?php if ($_smarty_tpl->getSmarty()->getModifierCallback('trim')($_smarty_tpl->getValue('payment_desc')) != '') {?>
            <div class="desc collapse<?php if ($_smarty_tpl->getValue('payment_code') == $_smarty_tpl->getValue('payment_selected')) {?> in<?php }?> payment-desc">
                <?php echo $_smarty_tpl->getValue('payment_desc');?>

            </div>
        <?php }?>
    </div>
    <?php echo '<script'; ?>
>
        {
            <?php if ($_smarty_tpl->getValue('payment_code') != "xt_paypal_checkout_pui" && !$_smarty_tpl->getSmarty()->getModifierCallback('strpos')($_smarty_tpl->getValue('payment_code'),"_vaulted")) {?>

            let me = document.getElementById("<?php echo $_smarty_tpl->getValue('payment_code');?>
");
            if (me) {
                let parent = me.closest(".list-group-item");
                if (parent) {
                    parent.style.display = "none";
                    console.log('hiding <?php echo $_smarty_tpl->getValue('payment_code');?>
');
                }
            }

            <?php }?>
            <?php if ($_smarty_tpl->getValue('payment_code') == "xt_paypal_checkout_googlepay") {?>
            document.addEventListener("GooglePaySdkLoaded", e => {
                try {
                    paypal.Googlepay().config()
                        .then(googlePayConfig => {
                            googlePaymentsClient = getGooglePaymentsClient();
                            if (googlePaymentsClient.isReadyToPay(googlePayConfig)) {
                                let me = document.getElementById("<?php echo $_smarty_tpl->getValue('payment_code');?>
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
if (!$_smarty_tpl->getValue('ppcp_payments_page_script_rendered')) {?>
    <?php echo '<script'; ?>
>
        document.addEventListener('DOMContentLoaded', function ()
        {
            document.querySelectorAll('[id$="_vaulted"]').forEach(node => {
                let link = node.querySelector(".ppcp-delete-payment-token");
                if(link)
                {
                    link.addEventListener("click", ev => {

                        if(confirm("<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_XT_PAYPAL_CHECKOUT_SAVED_PAYMENT_DELETE), $_smarty_tpl);?>
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
    <?php $_smarty_tpl->renderSubTemplate("file:./paypal-checkout-wait-modal-simple.tpl.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>

    <?php $_smarty_tpl->assign('ppcp_payments_page_script_rendered', 1, false, 32);
}?>

<?php }
}
