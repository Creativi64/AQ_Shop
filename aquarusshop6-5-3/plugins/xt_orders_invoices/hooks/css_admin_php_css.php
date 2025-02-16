<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_orders_invoices/classes/constants.php';

echo '
    .xt_orders_invoices-overdue {
        color: #fa8072;
        font-weight: bold !important;
    }
    
    .xt_orders_invoices_edit::before {
    font-family: "Font Awesome 5 Free";
    font-weight: 600;
    content: "\f044";
    font-style: normal;
    }
    
    .xt_orders_invoices_view::before {
    font-family: "Font Awesome 5 Free";
    font-weight: 600;
    content: "\f06e";
    font-style: normal;
    }
    
    .xt_orders_invoices_cancel::before {
    font-family: "Font Awesome 5 Free";
    font-weight: 600;
    content: "\f05e";
    font-style: normal;
    }
    
    .xt_orders_invoices_email::before {
    font-family: "Font Awesome 5 Free";
    font-weight: 600;
    content: "\f0e0";
    font-style: normal;
    }
		
	.xt_orders_invoices_print_dummy::before {
    font-family: "Font Awesome 5 Free";
    font-weight: 600;
    content: "\f06e";
    font-style: normal;
    }

    .xt_electronic_invoice_view::before {
    font-family: "Font Awesome 5 Free";
    font-weight: 600;
    content: "\f1c9";
    font-style: normal;
    }
';



