<div id="paypal_button_container_{$requested_position}_wrapper">
    <div id="paypal_button_container_{$requested_position}" style="/*text-align: center;*/ {if $smarty.session.selected_payment == 'xt_paypal_checkout_card'}width:100%;{/if} " class="paypal_button_container pull-right paypal-button-container-confirmation"></div>

</div>
<div class="clearfix"></div>
{if $smarty.session.selected_payment == 'xt_paypal_checkout_paypal'}
<div class="pull-right">
        <input type="checkbox" id="ppcp_agree_save_payment_method" name="ppcp_agree_save_payment_method">
        <label for="ppcp_agree_save_payment_method">{txt key="TEXT_PPCP_AGREE_SAVE_PAYMENT_METHOD"}&nbsp;&nbsp;</label><i class="fa fa-question-circle" aria-hidden="true" onclick="ppcSavePaymentMethodInfoModal()"></i>
</div>
{/if}

{if $smarty.session.selected_payment != 'xt_paypal_checkout_card_vaulted'}
{include file="./paypal-checkout-save-payment-method-info.tpl.html"}
{/if}
