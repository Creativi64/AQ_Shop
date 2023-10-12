
document.addEventListener("DOMContentLoaded", function()
{
    if (klarna_kp_auth_required) {
        console.debug("KP activation form submit handler for [" + xt_payment_form_selector + "]");
        $(xt_payment_form_selector).off('submit'); // off xt's default submitt handler
        $(xt_payment_form_selector).bind("submit.klarna_kp", function (event) {

            try {
                var selectedPayment = '';
                var formValues = $(this).serializeArray();
                for (var i = 0; i < formValues.length; i++) {
                    if (formValues[i].name === 'selected_payment') {
                        selectedPayment = formValues[i].value;
                        break;
                    }
                }
                if (selectedPayment.startsWith('xt_klarna_kp:')) {

                    disableContainers.forEach(function (selector) {
                        $(xt_payment_form_selector + ' ' + selector).addClass('disabledContainer');
                    });

                    var split = selectedPayment.split(':');
                    if (split.length === 2) {
                        event.preventDefault();
                        kp_doAuthorize(split[1]);
                        return false;
                    }
                }
            }
            catch (e) {
                console.info('Error in xt-KP\'s payment submit handler ', e);
                return false;
            }
        });
    }

    if (klarna_kp_finalize_required || klarna_kp_selected) {
        console.debug("KP activation form submit handler for ["+xt_process_form_selector+"]");
        //$(xt_process_form_selector).off('submit'); // off xt's default submitt handler
        $(xt_process_form_selector).submit(function(e) {
            e.preventDefault();
        });
        $(xt_process_form_selector).bind("submit.klarna_kp", function (event) {
            try {
                var formChecked = true;
                if(typeof xtSimpleCheckForm === 'function'){
                    formChecked = xtSimpleCheckForm($(xt_process_form_selector)[0]);
                    if(formChecked === false) {
                        event.preventDefault();
                        return false;
                    }
                }
                if($(klarna_kp_input_name_auth_token).val() == ''){
                    kp_show_pay_now_info(1);
                    event.preventDefault();
                }
                else{
                    kp_show_pay_now_info(2);
                    klarna_kp_finalize_required = false;
                    $(document).keydown(function(e) {
                        console.debug(e.keyCode + ' esc');
                        if (e.keyCode == 27) return false;
                    });
                    $(xt_process_form_selector).unbind("submit.klarna_kp");
                    $(xt_process_form_selector).off('submit').submit();
                }
            }
            catch (e) {
                console.info('Error in xtKP\'s process submit handler ', e);
                return false;
            }
        });
    }

});

function kp_show_pay_now_info(step)
{
    step = step || 1;
    if(step == 1)
    {
        $("#"+kp_pay_now_info_selector_prefix+"_step1").modal();

        if(typeof Ladda !== 'undefined' && typeof Ladda.stopAll !== "undefined")
        {
            $("#"+kp_pay_now_info_selector_prefix+"_step1").on('hidden.bs.modal', function () {
                Ladda.stopAll();
            });
        }
    }
    else if(step == 2)
    {
        $("#"+kp_pay_now_info_selector_prefix+"_step1").modal('hide');
        $("#"+kp_pay_now_info_selector_prefix+"_step2").modal({backdrop:'static', keyboard:false});
    }
}

function kp_reset_pay_now_info()
{
    $("#"+kp_pay_now_info_selector_prefix).modal('hide');
}

window.klarnaAsyncCallback = function (){

    console.info('klarnaAsyncCallback');
    if (klarna_kp_client_token !== false)
    {
        try {
            console.info('KP initiating');
            Klarna.Payments.init({
                client_token: klarna_kp_client_token
            });

            if(klarna_kp_do_load === true)
            {
                klarna_kp_payment_method_categories.forEach(function(payment_category){
                    kp_doLoad(payment_category);
                });
            }

            if(klarna_kp_payment_method_categories_allowed.indexOf(klarna_kp_reauth_required_method) > -1)
            {
                kp_doReauthorize(klarna_kp_reauth_required_method);
            }
        }
        catch(e) {
            console.error('Error KP init', klarna_kp_client_token, e);
        }
    }
    else {
        console.info('Not initiating KP, client token not set');
    }
};

function kp_doLoad(payment_category)
{
    if(klarna_kp_payment_method_categories.indexOf(payment_category) >= 0) {
        try {
            Klarna.Payments.load({
                container: klarna_kp_container_selector_base + payment_category,
                payment_method_categories: [payment_category],
                instance_id: klarna_kp_instance_base + payment_category,
                preferred_payment_method: false
            }, function (res) {
                console.debug('Loaded KP ['+payment_category+']; instance ['+ klarna_kp_instance_base + payment_category + ']',  res);
                if(res.show_form){
                    $(xt_payment_form_selector + " " + xt_payment_method_selector_base + payment_category).css({display:'block'});
                    $("[id=spinner_xt_klarna_kp_" +payment_category+ "]").fadeOut();
                }
                else {
                    $(xt_payment_form_selector + " " + xt_payment_method_selector_base + payment_category).fadeOut();
                    //$(xt_payment_form_selector + " " + xt_payment_method_selector_base + payment_category).remove();
                }
            });
        }
        catch (e) {
            console.error('Error KP load ['+payment_category+']', e);
        }
    }
    else {
        console.warn('Not loading unknown KP category ['+payment_category+']. ');
    }
}

function kp_doReauthorize(payment_category)
{
    try {
        if (klarna_kp_payment_method_categories_allowed.indexOf(payment_category) < 0)
            throw "Payment method ["+payment_category+"] not allowed";

        Klarna.Payments.reauthorize({
                instance_id: 'klarna-payments-instance',
                payment_method_categories: [payment_category]
            }, {},
            function (res) {
                console.debug(res);

                console.debug('KP old auth token input', $(klarna_kp_input_name_auth_token));
                console.debug('KP old auth token', $(klarna_kp_input_name_auth_token).val());

                $(klarna_kp_input_name_auth_token).val('');
                $(klarna_kp_input_name_finalize_required).val('');

                if(res.approved) {
                    console.debug('ReAuthorized KP',  res);

                    $(klarna_kp_input_name_auth_token).val(res.authorization_token);
                    $(klarna_kp_input_name_finalize_required).val(res.finalize_required);

                    console.debug('KP new auth token from should be set now', $(klarna_kp_input_name_auth_token).val(), res.authorization_token);
                }
                else{
                    console.debug('Not reauthorized KP',  res);
                }
            });
    } catch (e) {
        console.error('Error KP reauthorize ', e);
    }
}


function kp_doAuthorize(payment_category)
{
    try {
        if (klarna_kp_payment_method_categories_allowed.indexOf(payment_category) < 0)
            throw "Payment method ["+payment_category+"] not allowed";

        Klarna.Payments.authorize({
                instance_id: klarna_kp_instance_base + payment_category,
                auto_finalize: false
            }, klarna_kp_auth_data,
            function (res) {
                console.debug(res);

                $(klarna_kp_input_name_auth_token).val('');
                $(klarna_kp_input_name_finalize_required).val('');

                if(res.approved) {
                    console.debug('Authorized KP ['+payment_category+'] ',  res);

                    $(klarna_kp_input_name_auth_token).val(res.authorization_token);
                    $(klarna_kp_input_name_finalize_required).val(res.finalize_required);

                    kp_xt_submitPaymentForm();
                }
                else{
                    console.debug('Not authorized KP ['+payment_category+'] ',  res);

                    $(xt_payment_form_selector + ' .disabledContainer').removeClass('disabledContainer');
                    if(res.show_form === false){
                        $(xt_payment_form_selector + " " + xt_payment_method_selector_base + payment_category).css({display:'none'});
                    }

                    if(typeof Ladda !== 'undefined' && typeof Ladda.stopAll !== "undefined")
                    {
                        Ladda.stopAll();
                    }
                }
            });
    } catch (e) {
        console.error('Error KP authorize ['+payment_category+']', e);
    }
}

function kp_doFinalize(payment_category)
{
    payment_category = payment_category || 'pay_now';

    try {
        if (klarna_kp_payment_method_categories_allowed.indexOf(payment_category) < 0)
            throw "Payment method ["+payment_category+"] not allowed";

        Klarna.Payments.finalize({
                payment_method_category: payment_category
            }, { },
            function (res) {
                console.debug(res);

                if(res.approved) {
                    console.debug('Finalized KP ['+payment_category+'] ',  res);

                    $(klarna_kp_input_name_auth_token).val(res.authorization_token);
                    if($(klarna_kp_input_name_auth_token).val() != res.authorization_token){
                        console.warn('Finalized KP could not set auth token input value ['+payment_category+'] ',  res);
                    }
                    else{
                        kp_show_pay_now_info(2);
                        klarna_kp_finalize_required = false;
                        $(xt_process_form_selector).unbind("submit.klarna_kp");
                        $(xt_process_form_selector).off('submit').submit();
                    }
                }
                else {
                    console.info('Finalized KP not authorized or aborted ['+payment_category+'] ',  res);
                    kp_reset_pay_now_info()

                    if(res.show_form == false){
                        // todo what
                    }
                }
            })
    } catch (e) {
        console.error('Error KP finalize ['+payment_category+']', e);
    }
}

function kp_xt_submitPaymentForm()
{
    $(xt_payment_form_selector).unbind("submit.klarna_kp");
    $(xt_payment_form_selector).off('submit').submit();
}

function kp_fetch_order(xt_order_id, url)
{
    var jqxhr = $.ajax({
        type: "GET",
        url: url,
        data: {'xt_order_id':xt_order_id, 'event':'fetch'},
        dataType: "json"
    })
        .done(function(jqxhr) {
            console.debug( "kp_fetch_order ok");
        })
        .fail(function(jqxhr, status, error) {
            console.warn( "kp_fetch_order ok error", jqxhr, status, error );
        })
        .always(function() {
        });
}