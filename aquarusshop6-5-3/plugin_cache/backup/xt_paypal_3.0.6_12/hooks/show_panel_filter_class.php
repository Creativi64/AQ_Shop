<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if(get_class($this->class) == 'paypal_transactions'){
    if (file_exists(_SRV_WEBROOT . "plugins/xt_paypal/classes/class.paypalfilter.php")) {
        require_once(_SRV_WEBROOT . "plugins/xt_paypal/classes/class.paypalfilter.php");
        if (class_exists('PaypalFilter')) {
            $a = new PaypalFilter();
            $formFields = $a->formFields();
        }
    }
}
