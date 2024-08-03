
var klarna_kp_existing_method_categories = ["card", "direct_bank_transfer", "direct_debit", "pay_later", "pay_now", "pay_over_time"];

// set some defaults; they will display no kp methods; some values changed in hook 'index_footer_tpl'
var klarna_kp_payment_method_categories_allowed = ["pay_now", "pay_later", "pay_over_time"];
var klarna_kp_payment_method_categories = [];
var klarna_kp_do_load = false;
var klarna_kp_client_token = false;
var klarna_kp_auth_data = {};
var klarna_kp_auth_required = false;
var klarna_kp_reauth_required_method = false;
var klarna_kp_finalize_required = false;
var klarna_kp_selected = false;

// xt template selectors, defaulting to xt_responsive
var xt_payment_form_selector = "form[name^=payment]";
var xt_process_form_selector = "form[id=checkout-form]";
var xt_payment_method_selector_base = ".list-group-item-xt_klarna_kp_";
// input selected_payment is immutable
var disableContainers = ['.list-group','.well.form-counter','textarea','button'];

// these selectors names come from xt_klarna_kp itself, so they are 'immutable' too
var klarna_kp_container_selector_base = "#klarna_kp_container_";
var klarna_kp_instance_base = "kp_instance_";
var klarna_kp_input_name_auth_token = "input[name=kp_authorization_token]";
var klarna_kp_input_name_finalize_required = "input[name=kp_finalize_required]";

// pay_now modal
var kp_pay_now_info_selector_prefix = "kp_pay_now_info";
