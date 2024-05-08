console.log('xt_paypal_checkout.js');

let previousCountryCode = "";
let operation = "add";
let count = 0;

const ppc_button_selector = '#paypal_button_container';

function enableFoundingSources()
{
    if (window.paypal && window.paypal.Buttons)
    {
        try {
            paypal.getFundingSources().forEach(function (fundingSource) {
                console.log(fundingSource, paypal.isFundingEligible(fundingSource));

                if (paypal.isFundingEligible(fundingSource))
                {
                    let isApplepay = false;
                    let isApplepayAvail = false;
                    if(fundingSource === 'applepay')
                    {
                        isApplepay = true;
                        if (!window.ApplePaySession) {
                            console.log("This device does not support Apple Pay");
                        } else if (!ApplePaySession.canMakePayments()) {
                            console.log("This device, although an Apple device, is not capable of making Apple Pay payments");
                        }
                        else
                            isApplepayAvail = true;

                    }
                    let me = document.getElementById("xt_paypal_checkout_" + fundingSource);
                    if (me && (!isApplepay || isApplepayAvail)) {
                        let parent = me.closest(".list-group-item");
                        if (parent) {
                            parent.style.display = "block";
                        }
                    }
                }
            });
        }
        catch(e)
        {
            console.warn('enableFoundingSources: error');
            console.log(e)
        }
    }
    else
    {
        console.warn("paypal checkout no available. could not resolve eligible fundingSource's")
    }
}


function enablePaypalCardForm()
{
    try {
        console.log("paypal.HostedFields.isEligible = " + paypal.HostedFields.isEligible());

        const cardFields = paypal.HostedFields.render({

            styles: {
                'input': {
                    'font-size': '12pt',
                    'color': '#3A3A3A',

                },
                '.number': {
                    'font-family': 'monospace'
                },
                '.valid': {
                    'color': 'green'
                },
                '.invalid': {
                    'color': 'red'
                }
            },

            fields: {
                number: {
                    selector: '#card_number',
                    placeholder: "4111 1111 1111 1111"
                },
                cvv: {
                    selector: '#cvv',
                    placeholder: '•••'
                },

                expirationDate: {
                    selector: '#expiration_date',
                    placeholder: "MM/YY"
                }
            },

            createOrder: (data, actions) => ppcCreateOrder('card_processing', actions),
            onApprove: (data, actions) => ppcOnApprove(data, actions)
        }).then((cardFields) => {

            document.getElementById("card_form").addEventListener("submit", (event) => {
                event.preventDefault();
                console.log('submitting card form');

                let cardFormValid = true;
                Object.keys(cardFields._state.fields).forEach(cardField => {
                    console.log(`key=${cardField}  value=${cardFields._state.fields[cardField]}`);
                    if(!cardFields._state.fields[cardField].isValid)
                    {
                        cardFormValid = false;
                        cardFields._fields[cardField].containerElement.style.backgroundColor = '#eebaba';
                        setTimeout(function(div)
                        {
                            div.style.backgroundColor = 'unset';
                        } ,2000, cardFields._fields[cardField].containerElement);
                    }
                });

                let xt_form = ppcGetCheckoutForm();

                if(cardFormValid && xtSimpleCheckForm_ppc(xt_form)) {

                    const errContainer = document.getElementById('card_container_error');
                    errContainer.innerHTML = '';
                    errContainer.style.display = 'none';

                    ppcWaitModal();
                    cardFields
                        .submit({
                            cardholderName: document.getElementById("card_holder_name").value,
                        })
                        .then((data) => {
                            console.log(data);
                            ppcWaitModal();
                            xt_form.submit();
                        })
                        .catch((err) => {
                            ppcWaitModal('hide');
                            console.log("Payment could not be processed! " + JSON.stringify(err));

                            let errorMsg = '';
                            err.details.forEach(errDetail => {
                                errorMsg += errDetail.description + '. ';
                            });
                            errContainer.innerHTML += errorMsg;
                            errContainer.style.display = 'block';
                        });
                }
            });
        });
    }
    catch(e)
    {
        console.warn('enablePaypalCardForm: error');
        console.log(e);
    }
}


function enablePaypalButton(fundingSource,position)
{
    if (window.paypal && window.paypal.Buttons)
    {
        console.log('creating button for fundingSource ' + fundingSource, paypal_checkout_constant);

        let selector = ppc_button_selector+'_'+position;

        try {
            ppcFix_button_changed_html(selector);
        }
        catch(e){
            console.log('ppcFix_button_changed_html failed', e);
        }

        try {
            let button = paypal.Buttons({

                fundingSource: fundingSource,
                style: {
                    label: 'pay',
                    height: paypal_checkout_constant.BUTTON_SIZE,    // medium | large | responsive
                    shape: paypal_checkout_constant.BUTTON_SHAPE,      // pill | rect
                    color: paypal_checkout_constant.BUTTON_COLOR,       // gold | blue | silver | white | bla
                },
                createOrder: (data, actions) => ppcCreateOrder(data, actions),
                onApprove: (data, actions) => ppcOnApprove(data, actions),
                onError: function (err) {
                    console.log(err);
                    ppcWaitModal('hide');
                    //window.location.href = window.XT.baseUrl + '?page=checkout&page_action=payment&error=ERROR_PAYMENT'
                },
                onCancel: function (data) {
                    console.log(data);
                    ppcWaitModal('hide');
                },
                onInit: function (data, actions) {
                    console.log('ppcp onInit');

                    let form = ppcGetCheckoutForm();

                    if (form) {
                        const valid = xtSimpleCheckForm_ppc(form, false);
                        if (!valid) {
                            actions.disable();
                        }
                        let cbox = form.querySelectorAll("input[type=checkbox]");
                        cbox.forEach(box => {
                            box.addEventListener('change', function (event) {
                                const valid = xtSimpleCheckForm_ppc(form, false);
                                if (valid) {
                                    actions.enable();
                                    box.classList.remove("xt-form-error");
                                } else {
                                    actions.disable();
                                }

                            });
                        });
                    }

                },
                onClick: function (data, actions) {
                    console.log('ppcp onClick');

                    let form = ppcGetCheckoutForm();

                    if (form) {
                        const valid = xtSimpleCheckForm_ppc(form, true, true);
                        if (!valid) {
                            actions.reject();
                        }
                        let cbox = form.querySelectorAll("input[type=checkbox]");
                        cbox.forEach(box => {
                            box.addEventListener('change', function (event) {
                                const valid = xtSimpleCheckForm_ppc(form, false);
                                if (valid) {
                                    actions.resolve();
                                    box.classList.remove("xt-form-error");
                                } else {
                                    actions.reject();
                                }

                            });
                        });
                    }

                }
            }).render(selector);
        }
        catch (e)
        {
            console.warn('enablePaypalButton: error creating paypal button for selector ['+ppc_button_selector + '_' + position+']');
            console.log(e)
        }
    }
    else
    {
        console.warn('enablePaypalButton: paypal checkout no available. could not create button for fundingSource ' + fundingSource)
    }
}


function renderAllEligibleButtons()
{
    if (window.paypal && window.paypal.Buttons)
    {

        paypal.getFundingSources().forEach(function (fundingSource) {

            console.log(fundingSource, paypal.isFundingEligible(fundingSource));

            var button = paypal.Buttons({

                fundingSource: fundingSource,
                createOrder: (data, actions) => ppcCreateOrder(data, actions),
                onApprove: (data, actions) => ppcOnApprove(data, actions),
                onError: function (err) {
                    console.log(err);
                    ppcWaitModal('hide');
                    window.location.href = window.XT.baseUrl + '?page=checkout&page_action=payment&error=ERROR_PAYMENT'
                },
                onCancel: function (data) {
                    console.log(data);
                    ppcWaitModal('hide');
                }
            });

        });

    }
    else
    {
        console.warn('renderAllEligibleButtons: paypal checkout no available');
    }
}

function renderAllEligibleButtonsCart(position)
{
    console.log("renderAllEligibleButtonsCart position:" + position);
    if (window.paypal && window.paypal.Buttons)
    {
        try {
            var button = paypal.Buttons({

                //fundingSource: fundingSource,
                style: {
                    layout: 'horizontal',  // horizontal | vertical
                    label: 'checkout',
                    height: paypal_checkout_constant.BUTTON_SIZE,    // medium | large | responsive
                    shape: paypal_checkout_constant.BUTTON_SHAPE,      // pill | rect
                    color: paypal_checkout_constant.BUTTON_COLOR,       // gold | blue | silver | white | black
                    tagline: 'true'

                },
                createOrder: (data, actions, container) => ppcCreateOrder(data, actions, ppc_button_selector + '_' + position),
                onApprove: (data, actions) => ppcOnApproveCart(data, actions),
                onCancel: function (data) {
                    console.log(data);
                    ppcWaitModal('hide');
                },
                onShippingChange:  (data, actions) => ppcGetShippingOptions(data, actions),
            }).render(ppc_button_selector + '_' + position);
        }
        catch(e)
        {
            console.warn('renderAllEligibleButtonsCart: error creating paypal button for selector ['+ppc_button_selector + '_' + position+']');
            console.log(e)
        }
    }
    else
    {
        console.warn('window.paypal not available');
    }
}

function ppcGetShippingOptions(data, actions)
{
    console.log(data, actions);
    console.log("previous country " + previousCountryCode, "current country " + data.shipping_address.country_code);

    if(data.shipping_address.country_code != previousCountryCode || previousCountryCode == "")
    {

        let url = baseUrl + '?page=PAYPAL_CHECKOUT_GET_SHIPPING_OPTIONS';

        if (count > 0){
            operation = "replace"; // zur zeit wird eh immer ge add'ed in shippingOptions.php
        }
        count++;

        return fetch(
            url,
            {
                method: 'POST',
                redirect: "error",
                body: JSON.stringify({oderID:data.orderID, shipping_address:data.shipping_address, operation: operation})
            }
        ).then((response) => {
            console.log('fast zurück von PAYPAL_CHECKOUT_GET_SHIPPING_OPTIONS',  response);
            let json = response.json();
            return json;
        }).then((response) => {
            console.log('zurück von PAYPAL_CHECKOUT_GET_SHIPPING_OPTIONS',  response);
            if(response && response.error)
            {
                // response.error wird imo zZ nicht verwendet
                // wäre eine ander mgl zb für class.cart.php:_getContent_bottom und änderungen im cart
                // fangen wir zZ über redirect-error
                actions.reject();
                window.location.href = window.XT.baseUrl + '?page=cart';
            }
            previousCountryCode = data.shipping_address.country_code;
            return actions.resolve();
        }).catch(reason => {
            console.error('error in PAYPAL_CHECKOUT_GET_SHIPPING_OPTIONS', reason);
            actions.reject();
            // shippingOptions nur bei express
            // wenn der reason ein redirect ist gehen wir in den cart
            // bestimmt hat sich irgend etwas im cart geändert
            // siehe class.cart.php:_getContent_bottom
            window.location.href = window.XT.baseUrl + '?page=cart';
        });
    }
    return actions.resolve();
}


function ppcCreateOrder(data, actions, button_container_selector)
{
    console.log({'fnc': 'ppcCreateOrder', 'data': data, 'actions': actions, 'container': button_container_selector});

    previousCountryCode = "";
    operation = "add";
    count = 0;

    ppcWaitModal();

    const form =  ppcGetCheckoutForm();
    let valid = true;
    if(form) valid = xtSimpleCheckForm_ppc(form, true, true);

    let isExpress = false;
    let url = baseUrl + '?page=PAYPAL_CHECKOUT_ORDER_CREATE';

    const page = window.XT.page.page_name;

    if(valid) {
        const formData = new URLSearchParams();
        if(form) {
            for (const pair of new FormData(form)) {
                formData.append(pair[0], pair[1]);
            }
        }
        if(data === 'card_processing')
        {
            formData.append('card_processing', "1");
        }
        else if(page !== 'checkout') {
            // ar we in cart / product  => express checkout ?
            const page = window.XT.page.page_name;

            isExpress = true;
            formData.append('ppcp_express_checkout', 'true');// apple_pay_checkout  ppcp_express_checkout

            // check if cart or product
            const form = document.getElementById('main_product_form');
            if(!form)
            {
                // fallback versuch für artikel seite
                document.querySelector('form[name=product]');
            }

            if(form) { // products page, append quantity and products_id to request

                // now check we are not in eg coe_cart_popup. then there no need to add the product to cart
                // check the pp-container has the product form as parent
                let add_ADD_PRODUCT_param = true; // weil alles andere Ausnahme sein sollte.... ):
                const button_container = document.querySelector(button_container_selector);
                if(button_container)
                {
                    // das sollte wohl klappen bis hier
                    // jetzt schauen wir nach dont_add_product_when_paypal_button_is_child_of_one_of_this_containers
                    // definiert in paypal-checkout-cart-and-product_ignored_containers.tpl.html (tpl-überschreibbar)
                    if(typeof dont_add_product_when_paypal_button_is_child_of_one_of_this_containers != 'undefined')
                    {
                        dont_add_product_when_paypal_button_is_child_of_one_of_this_containers.forEach(function (container_selector, index)
                        {
                            const container = document.querySelector(container_selector);
                            if (container)
                                add_ADD_PRODUCT_param = (add_ADD_PRODUCT_param && !container.contains(button_container));
                        });
                    }

                }

                console.log("add_ADD_PRODUCT_param => " + add_ADD_PRODUCT_param);

                if(add_ADD_PRODUCT_param) {
                    for (const pair of new FormData(form)) {
                        formData.append(pair[0], pair[1]);
                    }
                }
            }
            formData.delete('action');
        }

        if(data && data.shippingContact_ap)
        {
            formData.append('shippingContact_ap', JSON.stringify(data.shippingContact_ap));
        }

        if(data && data.billingContact_ap)
        {
            formData.append('billingContact_ap', JSON.stringify(data.billingContact_ap));
        }


        return fetch(
            url,
            {
                method: 'POST',
                body: formData,
                redirect: "error"
            }
        ).then(function (response) {
            console.log(response);
            ppcWaitModal('hide');
            return response.json();
        }).then(function (resJson) {
            console.log(resJson);
            return resJson.data.id;
        }).catch((error) => {
            console.log(error, isExpress);
            if(!isExpress) window.location.href = window.XT.baseUrl + '?page=cart';
            else if (page !='product') window.location.href = window.XT.baseUrl + '?page=cart';
            else if (page =='product') location.reload();
        });
    }
}

function ppcCaptureOrder(ppcp_order_id)
{
    console.log({'fnc': 'ppcCaptureOrder', 'ppcp_order_id': ppcp_order_id});

    ppcWaitModal();

    let url = baseUrl + '?page=PAYPAL_CHECKOUT_ORDER_CAPTURE';

    return fetch(
        url,
        {
            method: 'POST',
            body: { ppcp_order_id: ppcp_order_id},
            redirect: "error"
        }
    ).then(function (response) {
        console.log(response);
        ppcWaitModal('hide');
        return response.json();
    }).then(function (resJson) {
        console.log(resJson);
        return resJson.id;
    }).catch((error) => {
        console.log('ppcCaptureOrder error', error);
    });

}

function ppcOnApprove(data, actions)
{
    console.log({'fnc': 'ppcOnApprove', 'data': data, 'actions': actions});

    ppcWaitModal();

    const form = ppcGetCheckoutForm();

    let url = baseUrl + '?page=checkout&page_action=process';
    let formData = new URLSearchParams();
    if(form) {
        for (const pair of new FormData(form)) {
            formData.append(pair[0], pair[1]);
        }
    }

    return fetch(
        url,
        {
            method: 'POST',
            body: formData,
            redirect: "error"
        }
    ).then(function(response) {
        console.log(response);
        if (!response.ok) {
            throw Error(response.statusText);
        }
        return response.json();
    }).then(function(resJson) {
        console.log(resJson);

        ppcWaitModal();
        setTimeout(function () {
            document.getElementById("ppc_oops").style.display = "block"
        }, 3000);

        window.location.href = baseUrl + '?page=checkout&page_action=success'
    }).catch((error) => {
        ppcWaitModal(close);
        console.log(error);
        window.location.href = window.XT.baseUrl + '?page=cart'
    });
}
function ppcOnApproveCart(data, actions)
{
    console.log({'fnc': 'ppcOnApproveCart', 'data': data, 'actions': actions});
    ppcWaitModal();
    window.location.href = baseUrl + '?page=checkout&ppcp_express_checkout=true';
}

function ppcSetupPuiForm()
{
    let pui_div = document.querySelector("div[id=xt_paypal_checkout_pui]");
    if(pui_div)
    {
        const regexPrefix = /^[0-9]{0,3}?$/;
        const regexPhone  = /^[0-9]{0,14}?$/;
        const regexDate  = /^[1-9]{2}\.[0-9]{2}\.[0-9]{4}$/;

        let inputPrefix = document.getElementById("paypal_checkout_selection_pui_phoneNumberPrefix");
        if(inputPrefix)
        {
            setInputFilter(inputPrefix, function(value) {
                return regexPrefix.test(value);
            }, "0-9 / min2 / max 3");
        }

        let inputPhone = document.getElementById("paypal_checkout_selection_pui_phoneNumber");
        if(inputPhone)
        {
            setInputFilter(inputPhone, function(value) {
                return regexPhone.test(value);
            }, "0-9 / min3 / max 14");
        }

        let inputDate = document.getElementById("paypal_checkout_selection_pui_birthdate");
        if(inputDate && false)
        {
            setInputFilter(inputDate, function(value) {
                return regexDate.test(value);
            }, "Format 31.01.1980 / 1980-01-31");
        }

        let form = pui_div.closest("form");
        if (form) {
            form.addEventListener('submit', function(event)
            {
                let paypalSelected = form.querySelector("input[value^=xt_paypal_checkout_]:checked");
                if(paypalSelected) {
                    event.preventDefault();
                    let puiSelected = form.querySelector("input[value=xt_paypal_checkout_pui]:checked");
                    if (puiSelected) {
                        // regex anpassen
                        const regexPrefixSubmit = /^[0-9]{2,3}?$/;
                        const regexPhoneSubmit = /^[0-9]{3,14}?$/;
                        const regexDateSubmit = /^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/;

                        let validPrefix = regexPrefixSubmit.test(inputPrefix.value);
                        let validPhone = regexPhoneSubmit.test(inputPhone.value);
                        let validDate = regexDateSubmit.test(inputDate.value);

                        if (validPrefix && validPhone && validDate)
                            form.submit();
                        else {
                            if (!validPrefix) {
                                inputPrefix.classList.add("input-error", "ppcp-has-error", "animated", "shake");
                                inputPrefix.setCustomValidity('min 2');
                                inputPrefix.reportValidity();

                                setTimeout(function () {
                                    inputPrefix.classList.remove("input-error", "ppcp-has-error", "animated", "shake");
                                }, 1000);
                            } else if (!validPhone) {
                                inputPhone.classList.add("input-error", "ppcp-has-error", "animated", "shake");
                                inputPhone.setCustomValidity('min 3');
                                inputPhone.reportValidity();

                                setTimeout(function () {
                                    inputPhone.classList.remove("input-error", "ppcp-has-error", "animated", "shake");
                                }, 1000);
                            } else if (!validDate) {
                                inputDate.classList.add("input-error", "ppcp-has-error", "animated", "shake");
                                setTimeout(function () {
                                    inputDate.classList.remove("input-error", "ppcp-has-error", "animated", "shake");
                                }, 1000);
                            }

                            // ew_evelations
                            setTimeout(function () {

                                form.querySelectorAll("button[type=submit], input[type=submit]").forEach(function (btn, index) {
                                    btn.removeAttribute("disabled");
                                    btn.removeAttribute("data-loading");
                                });
                            }, 100);
                        }
                    } else {
                            form.submit();
                    }
                }
            });
        }
    }
}

function ppcRemoveBackButton()
{
    try {
        let form = ppcGetCheckoutForm();
        if(form)
        {
            form.querySelectorAll('a[href*="checkout/payment"]').forEach(
                element => element.remove()
            );
        }
    }
    catch(e)
    {
        console.log(e);
    }
}

function ppcRemoveSubmitButton()
{
    try {
        let form = ppcGetCheckoutForm();
        if(form)
        {
            form.querySelectorAll("button:not(.paypal-card-button)").forEach(
                element => element.remove()
            );
        }
    }
    catch(e)
    {
        console.log(e);
    }
}

function ppcWaitModal(close)
{
    try {
        const modal = document.getElementById('ppc_wait_overlay');
        if(modal) {
            if (typeof close === 'undefined')
                modal.classList.remove("hidden");
            else
                modal.classList.add("hidden");
        }
        else console.warn('ES WURDE KEIN ppc_wait_overlay GEFUNDEN');
    }
    catch(e)
    {
        console.info(e);
    }
}


// Restricts input for the given textbox to the given inputFilter function.
function setInputFilter(textbox, inputFilter, errMsg) {
    ["input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop", "focusout"].forEach(function(event) {
        textbox.addEventListener(event, function(e) {
            if (inputFilter(this.value)) {
                // Accepted value
                if (["keydown","mousedown","focusout"].indexOf(e.type) >= 0){
                    this.classList.remove("input-error");
                    this.setCustomValidity("");
                }
                this.oldValue = this.value;
                this.oldSelectionStart = this.selectionStart;
                this.oldSelectionEnd = this.selectionEnd;
            } else if (this.hasOwnProperty("oldValue")) {
                // Rejected value - restore the previous one
                this.classList.add("input-error");
                this.setCustomValidity(errMsg);
                this.reportValidity();
                this.value = this.oldValue;
                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
            } else {
                // Rejected value - nothing to restore
                this.value = "";
            }
        });
    });
}

function ppcFix_button_changed_html(selector) {
    try {
        // in templates vor xt 6.5 ist auf der confirmation das $button_changed_html an der falsche stelle eingebunden
        let submitButton = document.querySelector("form[id=checkout-form] button[type=submit]");
        if (!submitButton) {
            const form = ppcGetCheckoutForm();
            if (form)
                submitButton = form.querySelector("button[type=submit]");
        }
        if (submitButton) {
            const btnContainer = submitButton.querySelector(selector);
            console.log(btnContainer);
            if (btnContainer) {
                submitButton.parentElement.insertBefore(btnContainer, submitButton);
                submitButton.parentElement.removeChild(submitButton);
            }
        }
    }
    catch(e){
        console.log(e);
    }
}

if(typeof xtSimpleCheckForm_ppc != 'function'){
    window.xtSimpleCheckForm_ppc = function(form, scrollTo, showTermsWarning)
    {
        var errorElements = [];

        if(scrollTo !== true && scrollTo !== false) scrollTo = true;
        if(showTermsWarning !== true && showTermsWarning !== false) showTermsWarning = false;


        form.querySelectorAll('input[type=checkbox].xt-form-required').forEach(function(el){
            if( el.checked !== true) {
                errorElements.push(el);
            }
        });

        form.querySelectorAll('input[type=radio].xt-form-required').forEach(function(el){
            //
        });


        form.querySelectorAll('input[type=text].xt-form-required').forEach(function(el){
            //
        });

        // ....

        if(errorElements.length)
        {
            errorElements.forEach(function(el){
                el.classList.add('xt-form-error');
                if(el.name == 'conditions_accepted' && showTermsWarning)
                {
                    let div = document.getElementById('TEXT_ERROR_CONDITIONS_ACCEPTED');
                    if(div)
                        div.style.display = 'block';
                }
            });
            if(scrollTo && !ppcIsInViewport(errorElements[0])) {
                errorElements[0].closest('form').scrollIntoView({
                    behavior: 'smooth'
                });
            }
            // evelations button reaktivieren
            setTimeout(function() {
                form.querySelectorAll("[data-loading]").forEach((el) => {
                    el.removeAttribute('data-loading');
                    el.removeAttribute('disabled');
                });
            }, 1000);
            return false;
        }

        return true;
    };
}

function ppcGetCheckoutForm()
{
    let xt_form = document.getElementById("checkout-form");
    if(!xt_form) xt_form = document.getElementById("checkout_form");
    if(!xt_form) xt_form = document.getElementById("formCard"); // danke dafür ador, hauptsache eigene id's
    if(!xt_form) {
        let inp = document.querySelector('input[type=hidden][name=action][value=process]');
        if(inp)
            xt_form = inp.parentElement;
    }
    if(!xt_form) console.warn('ES WURDE KEIN FORMULAR GEFUNDEN. form[id=checkout_form] bzw form[id=checkout-form] oder input[type=hidden][name=action][value=process]');
    return xt_form;
}

function ppcIsInViewport(element) {
    const rect = element.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

async function ppcp_log_js(fnc, data)
{
    let url = baseUrl + '?page=PAYPAL_CHECKOUT_LOG';

    return fetch(
        url,
        {
            method: 'POST',
            redirect: "error",
            body: JSON.stringify({ ppcp_session_id: paypal_checkout_constant.PPCP_SESSION_ID, fnc: fnc, data: data })
        }
    ).then((response) => { })
        .catch(error => {
        console.error('PAYPAL_CHECKOUT_LOG error.', error);
    });
}

/****
 *
 *      APPLE PAY
 * */
let getShippingOptions_ap = countryCode =>
{
    console.log('getShippingOptions_ap', "current country ap " + currentCountryCode_ap, "new country ap " + countryCode);

    let url = baseUrl + '?page=PAYPAL_CHECKOUT_GET_SHIPPING_OPTIONS_AP';

    return fetch(
        url,
        {
            method: 'POST',
            redirect: "error",
            body: JSON.stringify({ country_code:countryCode })
        }
    ).then((response) => {
        console.log('fast zurück von PAYPAL_CHECKOUT_GET_SHIPPING_OPTIONS_AP',  response);
        return response.json();
    }).then(function (resJson) {
        console.log(resJson);
        return resJson;
    }).catch(error => {
        console.error('PAYPAL_CHECKOUT_GET_SHIPPING_OPTIONS_AP error. returning empty array', error);
        ppcp_log_js('error getShippingOptions_ap', error);
        return [];
    });
}

let setShippingMethod_ap = (countryCode, shippingCode) =>
{
    console.log('setShippingMethod_ap', "shippingCode " + shippingCode + ". countryCode " +countryCode);

    let url = baseUrl + '?page=PAYPAL_CHECKOUT_SET_SHIPPING_METHOD_AP';

    return fetch(
        url,
        {
            method: 'POST',
            redirect: "error",
            body: JSON.stringify({
                country_code: countryCode,
                shipping_code: shippingCode
            })
        }
    ).then((response) => {
        console.log('fast zurück von PAYPAL_CHECKOUT_SET_SHIPPING_METHOD_AP',  response);
        return response.json();
    }).then(function (resJson) {
        console.log(resJson);
        return resJson;
    }).catch(error => {
        console.error('PAYPAL_CHECKOUT_SET_SHIPPING_METHOD_AP error. returning empty array', error);
        return [];
    });
}

let check_applepay = async () => {
    return new Promise((resolve, reject) => {
        let error_message = "";
        if (!window.ApplePaySession) {
            error_message = "This device does not support Apple Pay";
        } else
        if (!ApplePaySession.canMakePayments()) {
            error_message = "This device, although an Apple device, is not capable of making Apple Pay payments";
        }
        if (error_message !== "") {
            reject(error_message);
        } else {
            resolve();
        }
    });
};

async function setupApplepay(appendToContainer)
{
    let container = appendToContainer || "paypal_button_container_checkout";
    check_applepay()
        .then(() => {
            const applepay = paypal.Applepay();

            applepay.config()
                .then(async applePayConfig => {
                    console.log(applePayConfig);
                    if (applePayConfig.isEligible) {

                        let appleButton = document.createElement('apple-pay-button');
                        appleButton.setAttribute('id', 'btn-apple-pay');
                        appleButton.setAttribute('buttonstyle', paypal_checkout_constant.BUTTON_TYPE_AP); // TODO  payment config
                        appleButton.setAttribute('type', 'buy');
                        appleButton.setAttribute('locale', paypal_checkout_constant.language);

                        document.getElementById(container).appendChild(appleButton);

                        let shippingResult = await getShippingOptions_ap(currentCountryCode_ap);
                        currentTotal_ap = shippingResult.newTotal;
                        console.log('applepay shippings', shippingResult);

                        appleButton.addEventListener('click', e => {

                            let xt_form = ppcGetCheckoutForm();

                            if(xtSimpleCheckForm_ppc(xt_form, true, true)) {


                                const paymentRequest = {
                                    countryCode: applePayConfig.countryCode,
                                    merchantCapabilities: applePayConfig.merchantCapabilities,
                                    supportedNetworks: applePayConfig.supportedNetworks,
                                    currencyCode: applePayConfig.currencyCode,

                                    billingContact: billingContact_ap,
                                    shippingContact: shippingContact_ap,
                                    shippingContactEditingMode: 'available', //storePickup

                                    requiredShippingContactFields: ["name", "phone", "postalAddress"],
                                    requiredBillingContactFields: ["name", /*"phone",*/ "postalAddress", "email"],

                                    lineItems: [
                                        {
                                            label: subTotalLabel_ap,
                                            type: "final",
                                            amount: shippingResult.newSubtotal
                                        },
                                        {
                                            label: paypal_checkout_constant.TEXT_SHIPPING_COSTS + " " + shippingResult.shipping_label,
                                            type: "final",
                                            amount: shippingResult.shipping_price
                                        }
                                    ],


                                    total: {
                                        label: totalLabel_ap,
                                        type: "final",
                                        amount: shippingResult.newTotal
                                    },
                                    shippingMethods: shippingResult.shippings
                                };


                                const session = new ApplePaySession(4, paymentRequest);


                                console.log('apple pay session created', session);


                                session.oncouponcodechanged = (event) => {
                                    console.log('on coupon code changed', event);
                                }


                                session.onpaymentmethodselected_DISABLED = (event) => {
                                    console.log('on payment method selected. zZ machen wir hier nichts', event);

                                    // Define ApplePayPaymentMethodUpdate based on the selected payment method.
                                    // No updates or errors are needed, pass an empty object.
                                    // empty object funktioniert leider nicht
                                    const update = {};
                                    session.completePaymentMethodSelection(update);
                                }

                                session.onshippingcontactselected = (event) => {

                                    console.log('applepay on shipping contact selected', event);

                                    // Define ApplePayShippingContactUpdate based on the selected shipping contact.
                                    if (event.shippingContact.countryCode !== currentCountryCode_ap) {
                                        // würde uns eigentlich reichen
                                        // aber die event handler müssen immer ein update object haben
                                        // und dieses mus ein newTotal lineItem haben
                                    }

                                    getShippingOptions_ap(event.shippingContact.countryCode)
                                        .then(shippingResult => {

                                            let type = 'final';
                                            let errors = [];
                                            if (shippingResult.shippings.length === 0) {
                                                type = 'pending';
                                                errors = [new ApplePayError("shippingContactInvalid", "country", paypal_checkout_constant.WARNING_NO_SHIPPING_FOR_ZONE)];
                                            }

                                            if (shippingResult.success) {
                                                const update = {
                                                    newTotal: {
                                                        label: totalLabel_ap,
                                                        type: type,
                                                        amount: shippingResult.newTotal,
                                                    },
                                                    newShippingMethods: shippingResult.shippings,

                                                    errors: errors

                                                };
                                                session.completeShippingContactSelection(update);
                                                currentCountryCode_ap = event.shippingContact.countryCode;
                                                currentTotal_ap = shippingResult.newTotal;
                                            } else {
                                                throw new Error(shippingResult.msg)
                                            }
                                        })
                                        .catch(error => {
                                            console.log(' error in onshippingcontactselected while retrieving shipping options', error);
                                            ppcp_log_js('error onshippingcontactselected > getShippingOptions_ap', error);
                                        });
                                }

                                session.onshippingmethodselected = (event) => {
                                    console.log('applepay on shipping method selected', event);
                                    // todo hier ansetzen für onepage

                                    setShippingMethod_ap(currentCountryCode_ap, event.shippingMethod.identifier)
                                        .then(shippingResult => {

                                            if (shippingResult.success) {
                                                const update = {
                                                    newTotal: {
                                                        label: totalLabel_ap,
                                                        type: "final",
                                                        amount: shippingResult.newTotal,
                                                    }
                                                    //newShippingMethods: shippingResult.shippings

                                                };

                                                session.completeShippingMethodSelection(update);
                                                currentTotal_ap = shippingResult.newTotal;
                                            } else {
                                                throw new Error(shippingResult.msg)
                                            }
                                        })
                                        .catch(error => {
                                            console.log(' error in onshippingmethodselected while retrieving shipping options', error);
                                            ppcp_log_js('error onshippingmethodselected > setShippingMethod_ap', error);
                                        });
                                }

                                session.onvalidatemerchant = (event) => {
                                    console.log('on validate merchant', event);
                                    applepay.validateMerchant({
                                        validationUrl: event.validationURL,
                                        displayName: "My Store"
                                    })
                                        .then(validateResult => {

                                            console.log('on validate merchant validateResult', validateResult);
                                            session.completeMerchantValidation(validateResult.merchantSession);
                                        })
                                        .catch(validateError => {
                                            console.error('validateError', validateError);
                                            ppcp_log_js('error onvalidatemerchant', error);
                                            session.abort();
                                        });
                                };

                                session.onpaymentauthorized = (event) => {
                                    console.log('on apple payment authorized', event);
                                    console.log('Your billing address is:', event.payment.billingContact);
                                    console.log('Your shipping address is:', event.payment.shippingContact);
                                    // The `billingContact` and `shippingContact` object use the `ApplePayPaymentContact` format.
                                    // Find more details about this object at this URL:
                                    // https://developer.apple.com/documentation/apple_pay_on_the_web/applepaypaymentcontact
                                    ppcCreateOrder({
                                        billingContact_ap: event.payment.billingContact,
                                        shippingContact_ap: event.payment.shippingContact
                                    })
                                        .then((ppOrderId) => {
                                            console.log('ppc order created. ppOrderId', ppOrderId);
                                            applepay.confirmOrder({
                                                orderId: ppOrderId,
                                                token: event.payment.token,
                                                billingContact: event.payment.billingContact,
                                                shippingContact: event.payment.shippingContact
                                            })
                                                .then(confirmResult => {
                                                    console.log('confirmResult', confirmResult)

                                                    // Submit approval to the server and authorize or capture the order.
                                                    ppcCaptureOrder(ppOrderId)
                                                        .then(captureResult => {
                                                            console.log('ppc capture result applepay. setting ApplePaySession.STATUS_SUCCESS', captureResult);

                                                            session.completePayment(ApplePaySession.STATUS_SUCCESS);

                                                            ppcWaitModal();
                                                            window.location.href = baseUrl + '?page=checkout&page_action=payment_process'
                                                        })
                                                        .catch(captureError => {
                                                            console.error('ppc error when capture applepay', captureError);
                                                            session.completePayment(ApplePaySession.STATUS_FAILURE);
                                                            ppcp_log_js('error when capture applepay. onpaymentauthorized > ppcCreateOrder > confirmOrder > ppcCaptureOrder', captureError);
                                                        });

                                                })
                                                .catch(confirmError => {
                                                    if (confirmError) {
                                                        console.error('Error confirming order with applepay token', confirmError);
                                                        session.completePayment(ApplePaySession.STATUS_FAILURE);
                                                        ppcp_log_js('Error confirming order with applepay token. onpaymentauthorized > ppcCreateOrder > confirmOrder',
                                                            {
                                                                error: confirmError, input: {
                                                                    orderId: ppOrderId,
                                                                    token: event && event.payment ? event.payment.token : "n/a",
                                                                    billingContact: event && event.payment ? event.payment.billingContact : "n/a",
                                                                    shippingContact: event && event.payment ? event.payment.shippingContact : "n/a"
                                                                }
                                                            }
                                                        );
                                                    }
                                                });

                                        })
                                        .catch((error) => {
                                            console.log('error onpaymentauthorized > ppcCreateOrder', error);
                                            session.completePayment(ApplePaySession.STATUS_FAILURE);
                                            ppcp_log_js('error onpaymentauthorized > ppcCreateOrder', error);
                                        });

                                };

                                session.oncancel = event => {
                                    console.log('apple pay session canceled', event);

                                    let url = baseUrl + '?page=PAYPAL_CHECKOUT_RESET_ADDRESSES_AP';

                                    return fetch(
                                        url,
                                        {
                                            method: 'POST',
                                            //redirect: "error",
                                            body: JSON.stringify({})
                                        }
                                    ).then((response) => {
                                        console.log('fast zurück von PAYPAL_CHECKOUT_RESET_ADDRESSES_AP', response);
                                        return response.json();
                                    }).then(function (resJson) {
                                        console.log(resJson);
                                        return resJson;
                                    }).catch(error => {
                                        console.error('PAYPAL_CHECKOUT_RESET_ADDRESSES_AP error', error);
                                        return [];
                                    });
                                };

                                session.begin();
                            } // form validated
                        });
                    }
                })
                .catch(applePayConfigError => {
                    console.error('Error while fetching Apple Pay configuration.', applePayConfigError);
                });
        })
        .catch(errorMessage => {
            console.log(errorMessage)
        })
}
