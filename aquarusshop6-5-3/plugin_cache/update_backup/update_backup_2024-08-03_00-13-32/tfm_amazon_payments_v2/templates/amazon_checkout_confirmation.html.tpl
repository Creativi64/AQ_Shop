<!-- :START: plugins/tfm_amazon_payments_v2/templates/amazon_checkout_confirmation.html.tpl -->
<div id="checkout">
    {$message}
    <div id="checkout-confirmation" class="row">
        <div class="col col-sm-4 col-md-3">
            {if $is_virtual == 0}
                <div class="well shipping-address address">
                    <p class="headline-underline clearfix">
                        {txt key=TEXT_SHIPPING_ADDRESS}
                        <a title="{txt key=TEXT_EDIT}" class="btn btn-xs btn-primary pull-right"
                           id="amazon-shipping-address-change-btn{$amazon_checkout_session_id}">
                            <span class="glyphicon glyphicon-pencil"></span>
                            <span class="sr-only">{txt key=TEXT_EDIT}</span>
                        </a>
                    </p>
                    {if $shipping_address.customers_company}<p>{$shipping_address.customers_company}</p>{/if}
                    {if $shipping_address.customers_company_2}<p>{$shipping_address.customers_company_2}</p>{/if}
                    {if $shipping_address.customers_company_3}<p>{$shipping_address.customers_company_3}</p>{/if}
                    <p>{if $shipping_address.customers_title}{$shipping_address.customers_title} {/if}{$shipping_address.customers_firstname} {$shipping_address.customers_lastname}</p>
                    <p>{$shipping_address.customers_street_address}</p>
                    {if $shipping_address.customers_address_addition}</p>{$shipping_address.customers_address_addition}</p>{/if}
                    {if $shipping_address.customers_suburb}<p>{$shipping_address.customers_suburb}</p>{/if}
                    <p>{$shipping_address.customers_postcode} {$shipping_address.customers_city}</p>
                    <p>{$shipping_address.customers_country}</p>
                </div>
            {/if}
            <div class="well">
                <p class="headline-underline clearfix">
                    {txt key=TEXT_PAYMENT_METHOD}
                    <a title="{txt key=TEXT_EDIT}" class="btn btn-xs btn-primary pull-right"
                       id="amazon-payment-change-btn{$amazon_checkout_session_id}">
                        <span class="glyphicon glyphicon-pencil"></span>
                        <span class="sr-only">{txt key=TEXT_EDIT}</span>
                    </a>
                </p>
                {if $payment_info.payment_name}<p class="bold">{$payment_info.payment_name}</p>{/if}
                {if $payment_info.payment_desc}<p>{$payment_info.payment_desc}</p>{/if}
                {if $payment_info.payment_info}<p>{$payment_info.payment_info}</p>{/if}
            </div>
            {hook key=tpl_checkout_confirmation_info_boxes}
        </div>
        <div class="col col-sm-8 col-md-9">
            <h1>{txt key=TEXT_CONFIRMATION_DESC}</h1>
            {if $is_packstation_address == 0}
            {if $shipping_data|count eq 0 && $smarty.session.cart->type != 'virtual'}
                <p>
                <h4>{txt key=TEXT_TFM_AMAZON_PAYMENTS_V2_ERROR_MESSAGE_UNSUPPORTED_SHIPPING_COUNTRY}</h4>
                <a title="{txt key=TEXT_EDIT}" class="btn btn-xs btn-primary"
                   id="amazon-shipping-address-error-change-btn{$amazon_checkout_session_id}">
                    <span class="glyphicon glyphicon-pencil"></span>
                    <span class="sr-only">{txt key=TEXT_EDIT}</span>
                </a>
                </p>
            {else}


            {if $shipping_info.shipping_name}

                {hook key=checkout_tpl_info}
{literal}
                <script type="application/javascript">
                    // .closest replacement (polifill)
                    if (!Element.prototype.matches) {
                        Element.prototype.matches =
                            Element.prototype.msMatchesSelector ||
                            Element.prototype.webkitMatchesSelector;
                    }

                    if (!Element.prototype.closest) {
                        Element.prototype.closest = function (s) {
                            var el = this;

                            do {
                                if (Element.prototype.matches.call(el, s)) return el;
                                el = el.parentElement || el.parentNode;
                            } while (el !== null && el.nodeType === 1);
                            return null;
                        };
                    }
                    function updateShipping(event, el) {
                        event.preventDefault();
                        const selected_input_element=el.querySelector("input");
                        let elementContainer = document.querySelectorAll("[data-confirm-order-button='true']");
                        for (let i = 0; i < elementContainer.length; i++) {
                            elementContainer[i].style.display = "none";
                        }
                        const form = el.closest("form");
                        form.querySelectorAll("input[name='selected_shipping']").forEach(function (mel) {
                            mel.checked = false; // thanks IE
                        });
                        // form.querySelector("input[name='selected_shipping']").setAttribute('checked',false); // thanks IE
                        // form.querySelector("input[name='selected_shipping']").setAttribute('checked',true); // thanks IE
                        // el.setAttribute('checked',true);
                        selected_input_element.checked = true; 
                        //selected_input_element.setAttribute('checked',true); 
                        
                        form.submit();
                    }
                </script>
            {/literal}
            {if $smarty.session.cart->type != 'virtual'}
                <div class="box">
                    <h1>{txt key=TEXT_SELECT_SHIPPING}:</h1>
                    {form type=form name=shipping action='checkout' method=post conn=SSL}
                    {form type=hidden name=action value=shipping}
                    <ul class="list-group">
                        {foreach name=aussen item=sdata from=$shipping_data}
                            <li class="list-group-item clearfix"
                                onclick="updateShipping(event, this);">{$sdata.shipping}</li>
                        {/foreach}
                    </ul>
                    {form type=formend}
                </div>
            {/if}

            {/if}
                {form type=form name=process action='checkout' method=post conn=SSL id='checkout-form'}
                {form type=hidden name=action value=process}
                {hook key=after_checkout_tpl_form}
                <div class="alert alert-warning">
                    {if $smarty.const._STORE_TERMSCOND_CHECK eq 'true'}
                        {content cont_id=3 is_id='false'}
                        <p class="checkbox">
                            <label>
                                {form type=checkbox name=conditions_accepted class="xt-form-required"}
                                {txt key=TEXT_TERMSANDCOND_CONFIRMATION_1} <a href="{$_content_3.content_link}"
                                                                              target="_blank">{txt key=TEXT_TERMSANDCOND_CONFIRMATION_2}</a> {txt key=TEXT_TERMSANDCOND_CONFIRMATION_3}
                            </label>
                        </p>
                    {else}
                        {content cont_id=3 is_id='false'}
                        <p><span class="glyphicon glyphicon-ok"></span>
                            <strong>{txt key=TEXT_TERMSANDCOND_CONFIRMATION_1} <a href="{$_content_3.content_link}"
                                                                                  target="_blank">{txt key=TEXT_TERMSANDCOND_CONFIRMATION_2}</a> {txt key=TEXT_TERMSANDCOND_CONFIRMATION_3}
                            </strong></p>
                    {/if}
                    {if $show_digital_checkbox eq 'true'}
                        <p class="checkbox">
                            <label>
                                {form type=checkbox name=withdrawal_reject_accepted class="xt-form-required"}
                                {txt key=TEXT_DIGITALCOND_CHECK}
                            </label>
                        </p>
                    {/if}
                    {hook key=checkout_tpl_form}
                </div>
                <div class="div-table table-hover table-bordered">
                    <div class="row th">
                        <div class="col col-md-8">
                            {txt key=TEXT_ARTICLE}
                        </div>
                        <div class="col col-md-2 hidden-xs hidden-sm text-right">
                            {txt key=TEXT_SINGLE_PRICE}
                        </div>
                        <div class="col col-md-2 hidden-xs hidden-sm text-right">
                            {txt key=TEXT_TOTAL_PRICE}
                        </div>
                    </div>
                    {foreach key=key item=product from=$data}
                        {form type=hidden name='products_key[]' value=$product.products_key}
                        <div class="row tr">
                            <div class="col col-xs-5 col-md-2">
                                <p class="image">
                                    {if !$product.products_image || $product.products_image == 'product:noimage.gif'}
                                        <a href="{$product.products_link}"
                                           class="vertical-helper image-link no-image img-thumbnail">
                                            <i class="no-image-icon"></i>
                                        </a>
                                    {else}
                                        <a href="{$product.products_link}"
                                           class="vertical-helper image-link img-thumbnail"
                                           target="_blank">{img img=$product.products_image type=m_thumb class="productImageBorder img-responsive" alt=$product.products_name|escape:"html"}</a>
                                    {/if}
                                </p>
                            </div>
                            <div class="col col-xs-7 col-md-6">
                                <p class="product-name break-word bold"><a href="{$product.products_link}"
                                                                           target="_blank">{$product.products_name}</a>
                                </p>
                                {hook key=cart_xt_options pid=$product.products_id}
                                {if $product.products_short_description}
                                    <p class="product-description text-muted hidden-xs">{$product.products_short_description|strip_tags|truncate:100:'...'}
                                        (<a href="{$product.products_link}" target="_blank">{txt key=TEXT_MORE}</a>)</p>
                                {elseif $product.products_description}
                                    <p class="product-description text-muted hidden-xs">{$product.products_description|strip_tags|truncate:100:'...'}
                                        (<a href="{$product.products_link}" target="_blank">{txt key=TEXT_MORE}</a>)</p>
                                {/if}
                                <ul class="label-list fixed-padding">
                                    {if $product.products_information}
                                        <li><span
                                                class="badge">{$product.products_information|replace:'<tr class="contentrow1">':''|replace:'<tr class="contentrow2">':''|replace:'<td>':''|replace:'<td class="left" colspan="4">':''|replace:'</td>':''|replace:'</tr>':''}</span>
                                        </li>{/if}
                                    {if $product.products_model!=''}
                                        <li><span
                                                class="badge">{txt key=TEXT_PRODUCTS_MODEL} {$product.products_model}</span>
                                        </li>{/if}
                                    {if $product.products_weight > 0}
                                        <li><span
                                                class="badge">{txt key=TEXT_PRODUCTS_WEIGHT} {$product.products_weight|weight_format:"kg"}</span>
                                        </li>{/if}
                                    {if $product.shipping_status_data.name}
                                        <li><span
                                                class="badge">{txt key=TEXT_SHIPPING_STATUS} {$product.shipping_status_data.name}</span>
                                        </li>{/if}
                                    {if $product.base_price}
                                        <li><span
                                                class="badge">{$product.products_vpe_value|number_format:2:",":"."} {$product.base_price.vpe.name} / {$product.base_price.price} {txt key=TEXT_SHIPPING_BASE_PER} {$product.base_price.vpe.name}</span>
                                        </li>{/if}
                                </ul>

                                {*<!-- price info visible-xs visible-sm part --- start -->*}
                                <div class="hidden-md hidden-lg text-left-xs">
                                    <span class="text-middle">{txt key=TEXT_SINGLE_PRICE}:</span><br/>
                                    <p class="product-price">
                                        {$product.products_price.formated}
                                        {if $product._cart_discount}&nbsp;
                                            <span class="price-old">{$product._original_products_price.formated}</span>
                                            &nbsp;
                                            <span class="small">(-{$product._cart_discount} %)</span>
                                        {/if}
                                    </p>
                                    <span class="text-middle">{txt key=TEXT_TOTAL_PRICE}:</span><br/>
                                    <p class="product-price final-price">{$product.products_final_price.formated}</p>
                                </div>
                                {*<!-- / price info visible-xs visible-sm part --- end -->*}

                                <div class="form-inline">
                                    <div class="form-group form-group-sm">
                                        <div class="input-group">

                                            <input type="text"
                                                   class="form-control disabled"
                                                   disabled="disabled"
                                                   id="form-qty-{$product.products_id}"
                                                   value="{$product.products_quantity}"
                                                   style="width: 6em"/>
                                            <label for="form-qty-{$product.products_id}"
                                                   class="input-group-addon text-regular">{sys_status id=$product.products_unit}</label>
                                        </div>

                                    </div>
                                    <a href="{link page='cart'}" class="btn btn-sm btn-primary">
                                        <span class="glyphicon glyphicon-pencil"></span>
                                        {txt key=TEXT_EDIT}
                                    </a>
                                </div>
                            </div>
                            <div class="col col-md-2 hidden-xs hidden-sm text-right">
                                <p class="product-price">
                                    {$product.products_price.formated}
                                    {if $product._cart_discount}
                                        <br/>
                                        <span class="price-old">{$product._original_products_price.formated}</span>
                                        <br/>
                                        <span class="small">(-{$product._cart_discount} %)</span>
                                    {/if}
                                </p>
                            </div>
                            <div class="col col-md-2 hidden-xs hidden-sm text-right">
                                <p class="product-price final-price">{$product.products_final_price.formated}</p>
                            </div>
                        </div>
                    {/foreach}
                    <div class="row tfoot text-right">
                        <div class="col col-md-offset-5 col-xs-5 col-md-5">
                            {hook key=checkout_tpl_form_total_lines_top}
                            {hook key=checkout_tpl_form_total_lines}
                            {txt key=TEXT_SUB_TOTAL}
                        </div>
                        <div class="col col-xs-7 col-md-2">
                            {$sub_total}
                        </div>
                    </div>
                    {foreach item=total from=$sub_data}
                        <div class="row tfoot text-right">
                            <div class="col col-md-offset-5 col-xs-5 col-md-5">
                                {$total.products_name}
                            </div>
                            <div class="col col-xs-7 col-md-2">
                                {$total.products_price.formated}
                            </div>
                        </div>
                    {/foreach}
                    <div class="row tfoot bold text-right">
                        <div class="col col-md-offset-5 col-xs-5 col-md-5">
                            {txt key=TEXT_TOTAL}
                        </div>
                        <div class="col col-xs-7 col-md-2">
                            <span id="grand-total-value">{$total}</span>
                        </div>
                    </div>
                    {foreach item=tax_data from=$tax}
                        <div class="row tfoot text-right">
                            <div class="col col-md-offset-5 col-xs-5 col-md-5">
                                {txt key=TEXT_TAX} {$tax_data.tax_key}%
                            </div>
                            <div class="col col-xs-7 col-md-2">
                                {$tax_data.tax_value.formated|replace:"*":""}
                            </div>
                        </div>
                    {/foreach}
                    {if $discount}
                        <div class="row tfoot text-right">
                            <div class="col col-md-offset-5 col-xs-5 col-md-5">
                                <strong>{txt key=TEXT_DISCOUNT_MADE}</strong>
                            </div>
                            <div class="col col-xs-7 col-md-2">
                                {$discount.formated}
                            </div>
                        </div>
                    {/if}

                    {hook key=checkout_tpl_form_total_lines_bottom}
                </div>
                <!-- .div-table -->

            <br/>

            {if $payment_info.payment_cost_info eq '1' and $language eq 'de'}
                <p class="alert alert-info">{txt key=TEXT_ORDER_CONFIRMATION_BUTTON_LAW}</p>
            {/if}
            {if $post_form eq '1'}
                <p class="alert alert-info">{txt key=TEXT_INFO_PAY_NEXT_STEP}</p>
            {/if}
                <div class="form-group"><label>{txt key=TEXT_COMMENTS}</label>
                    <div class="well"><textarea class="form-control" rows="3" cols="50" name="comments"
                                                data-order-comments="true"
                                                placeholder="{txt key=TEXT_COMMENTS}">{if $smarty.session.order_comments != ''}{$smarty.session.order_comments|nl2br}{/if}</textarea>
                    </div>
                </div>
                <div class="clearfix">
                    <div class="hidden-lg hidden-md hidden-sm">
                        <button id="{$page_action}-submit" type="submit" class="btn btn-success btn-block btn-lg"
                                data-confirm-order-button="true">
                            <span class="glyphicon glyphicon-flag"></span>
                            {txt key=BUTTON_CONFIRM_ORDER}
                        </button>
                        <br/>
                    </div>
                    <button id="{$page_action}-submit" type="submit" class="btn btn-success pull-right hidden-xs"
                            data-confirm-order-button="true">
                        <span class="glyphicon glyphicon-flag"></span>
                        {txt key=BUTTON_CONFIRM_ORDER}
                    </button>
                    <a href="javascript:history.back();" class="btn btn-default pull-left">
                        <i class="fa fa-chevron-left"></i>
                        {txt key=BUTTON_BACK}
                    </a>
                </div>
            <br/>

                {hook key=checkout_tpl_confiramtion}{* deprecated *}
                {hook key=checkout_tpl_confirmation}
                {form type=formend}
                {hook key=checkout_tpl_bottom}
            {/if}
            {else}
                <p>
                <h4>{txt key=TEXT_TFM_AMAZON_PAYMENTS_V2_ERROR_MESSAGE_PACKSTATION_SHIPPINGADDRESS}</h4>
                <a title="{txt key=TEXT_EDIT}" class="btn btn-xs btn-primary"
                   id="amazon-shipping-address-error-change-btn{$amazon_checkout_session_id}">
                    <span class="glyphicon glyphicon-pencil"></span>
                    <span class="sr-only">{txt key=TEXT_EDIT}</span>
                </a>
                </p>
            {/if}
        </div><!-- #checkout-confirmation .row -->

    </div>
</div>
{literal}
    <script src="https://static-eu.payments-amazon.com/checkout.js"></script>
<script type="text/javascript">
    const checkoutSessID = "{/literal}{$amazon_checkout_session_id}{literal}";
    let _element = document.querySelector('#amazon-shipping-address-change-btn' + checkoutSessID);
    if (_element) {
        amazon.Pay.bindChangeAction('#amazon-shipping-address-change-btn' + checkoutSessID, {
            amazonCheckoutSessionId: checkoutSessID,
            changeAction: 'changeAddress'
        });
    }

    // Button in Packstation error Message
    _element = document.querySelector('#amazon-shipping-address-error-change-btn' + checkoutSessID);
    if (_element) {
        amazon.Pay.bindChangeAction('#amazon-shipping-address-error-change-btn' + checkoutSessID, {
            amazonCheckoutSessionId: checkoutSessID,
            changeAction: 'changeAddress'
        });
    }
    // edit payment instrument
    _element = document.querySelector('#amazon-payment-change-btn' + checkoutSessID);
    if (_element) {
        amazon.Pay.bindChangeAction('#amazon-payment-change-btn' + checkoutSessID, {
            amazonCheckoutSessionId: checkoutSessID,
            changeAction: 'changePayment'
        });
    }
</script>
{/literal}
<!-- :ENDE: plugins/tfm_amazon_payments_v2/templates/amazon_checkout_confirmation.html.tpl -->