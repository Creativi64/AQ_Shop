<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if (constant('XT_PAYPAL_EXPRESS') == 1 && $_SESSION['paypalExpressCheckout'] == true) {
    $link_array = array('page' => $page->page_name, 'paction' => '', 'conn' => 'SSL');
}

?>