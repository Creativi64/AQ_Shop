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

                if (paypal.isFundingEligible(fundingSource)) {
                    let me = document.getElementById("xt_paypal_checkout_" + fundingSource);
                    if (me) {
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
                                } else {
                                    actions.disable();
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
    console.log("previous country " + previousCountryCode, "current country" + data.shipping_address.country_code);

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
                body: JSON.stringify({oderID:data.orderID, shipping_address:data.shipping_address, operation: operation})
            }
        ).then((response) => {
            console.log('fast zurück von shippingOptions.php',  response);
            let json = response.json();
            return json;
        }).then((response) => {
            console.log('zurück von shippingOptions.php',  response);
            if(response && response.error)
                return actions.reject();
            previousCountryCode = data.shipping_address.country_code;
            return actions.resolve();
        }).catch(reason => {
            console.error(reason);
            actions.reject();
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
    if(form) valid = xtSimpleCheckForm_ppc(form);

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
            formData.append('ppcp_express_checkout', 'true');

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
            if(!isExpress) window.location.href = window.XT.baseUrl + '?page=checkout&page_action=payment&error=ERROR_PAYMENT';
            else if (page !='product') window.location.href = window.XT.baseUrl + '?page=cart&error=ERROR_PAYMENT';
            else if (page =='product') location.reload();
        });
    }
}


function ppcOnApprove(data, actions)
{
    console.log({'fnc': 'ppcOnApprove', 'data': data, 'actions': actions});

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
            body: formData
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
        window.location.href = window.XT.baseUrl + '?page=checkout&page_action=payment&error=ERROR_PAYMENT'
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
    window.xtSimpleCheckForm_ppc = function(form, scrollTo)
    {
        var errorElements = [];

        if(scrollTo !== true && scrollTo !== false) scrollTo = true;


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
            });
            if(scrollTo && !ppcIsInViewport(errorElements[0])) {
                errorElements[0].closest('form').scrollIntoView({
                    behavior: 'smooth'
                });
            }
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
    if(!xt_form) console.warn('ES WURDE KEINE form[id=checkout_form] bzw form[id=checkout-form]');
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
