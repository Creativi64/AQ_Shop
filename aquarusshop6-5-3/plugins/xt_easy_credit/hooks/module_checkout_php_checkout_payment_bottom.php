<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $page, $price, $xtLink, $info, $db;

if ($_POST['selected_payment'] == 'xt_easy_credit' && get_class($payment_module_data) == 'xt_easy_credit')
{
    //$_SESSION['cart']->_deleteSubContent('payment');

    if(empty($_POST['zustimmung_accepted']) || empty($_POST['ec_customers_dob']))
    {
        if(empty($_POST['zustimmung_accepted']))
            $info->_addInfoSession(TEXT_EASY_CREDIT_ACCEPT, 'error');
        if(empty($_POST['ec_customers_dob']))
            $info->_addInfoSession(TEXT_EASY_CREDIT_DOB_REQUIRED, 'error');
        $xtLink->_redirect($xtLink->_link(array('page'=>'checkout', 'paction'=>'payment', 'conn'=>'SSL')));
    }

    // update address book
    try
    {
        $_SESSION['customer']->customer_payment_address['customers_dob'] =
        $_SESSION['customer']->customer_shipping_address['customers_dob'] = $_POST['ec_customers_dob'];

        $dob = DateTime::createFromFormat('d.m.Y', $_POST['ec_customers_dob']);
        $dob = $dob->format('Y-m-d');

        $sql = "UPDATE ".TABLE_CUSTOMERS_ADDRESSES." SET customers_dob=? WHERE address_book_id=?";
        $db->Execute($sql, array($dob, $_SESSION['customer']->customer_shipping_address['address_book_id']));
        $db->Execute($sql, array($dob, $_SESSION['customer']->customer_payment_address['address_book_id']));
    }
    catch(Exception $e)
    {
        error_log('xt_easy_credit - could not update dob with given value ['.$_POST['ec_customers_dob'].']');
        error_log($e->getMessage());
    }

    /*
    $amount = $_SESSION['cart']->content_total['plain'];
    if ($customers_status->customers_status_show_price_tax != '1' && $customers_status->customers_status_add_tax_ot=='1')
    {
        foreach($_SESSION['cart']->content_tax as $content_tax)
        {
            $amount += $content_tax['tax_value']['plain'];
        }
    }
    */

    $logistikDienstleister = $db->GetOne("SELECT sd.shipping_name FROM ".TABLE_SHIPPING_DESCRIPTION." sd
         left join ".TABLE_SHIPPING." s on s.shipping_id = sd.shipping_id
         where s.shipping_code = ? AND sd.language_code = ?", array($_SESSION['selected_shipping'], $language->content_language));

    $tmp_link_data = $payment_module_data->easy_credit_initVorgang(/*$amount,*/ $logistikDienstleister);
    if($tmp_link_data->error)
    {
        $tmp_link = $xtLink->_link(array('page'=>'checkout', 'paction'=>'payment', 'conn'=>'SSL'));
        if(count($tmp_link_data->errors))
        {
            foreach($tmp_link_data->errors as $err)
            {
                $info->_addInfoSession($err['msg'], 'error');
            }
        }
        else
        {
            $tmp_link .= '?error=ERROR_PAYMENT';
        }
    }
    else
    {
        $tmp_link = $tmp_link_data->url;
    }

}
