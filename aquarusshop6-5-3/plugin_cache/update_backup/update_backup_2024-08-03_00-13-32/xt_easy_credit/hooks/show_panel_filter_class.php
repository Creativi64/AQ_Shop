<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if(get_class($this->class) == 'easy_credit_transactions'){
    if (file_exists(_SRV_WEBROOT . "plugins/xt_easy_credit/classes/class.easy_creditfilter_installments.php")) {
        require_once(_SRV_WEBROOT . "plugins/xt_easy_credit/classes/class.easy_creditfilter_installments.php");
        if (class_exists('Easy_creditFilter_installments')) {
            $a = new Easy_creditFilter_installments();
            $formFields = $a->formFields();
        }
    }
}
