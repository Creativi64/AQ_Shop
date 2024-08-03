<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_klarna_kp/classes/constants.php';

global $db;

try
{
    $kp_order_arr = xt_klarna_kp::getKlarnaOrderFromXt($data['orders_id']);
    if(!empty($kp_order_arr['order_id']))
    {
        $addr = $db->GetArray('SELECT * FROM '.TABLE_CUSTOMERS_ADDRESSES. " WHERE address_book_id=?", array($data['address_book_id']));

        if(is_array($data))
        {
            $addr = $data;

            $email = $db->GetOne('SELECT customers_email_address FROM '.TABLE_ORDERS. " WHERE orders_id=?", array($data['orders_id']));

            $salut = '';
            switch ($addr['customers_gender'])
            {
                case 'f':
                    $salut = TEXT_FEMALE;
                    break;
                case 'm':
                    $salut = TEXT_MALE;
                    break;
                case 'c':
                    $salut = TEXT_COMPANY_GENDER;
                    break;
                default:
                    continue;
            }
            $addr_data = array(
                'title' => $salut,
                'given_name' => $addr['customers_firstname'],
                'family_name' => $addr['customers_lastname'],
                'email' => $email,
                'street_address' => $addr['customers_street_address'],
                'postal_code' => $addr['customers_postcode'],
                'city' => $addr['customers_city'],
                //'region' => $addr['customers_federal_state_code'],
                'phone' => !empty($addr['customers_mobile_phone']) ? $addr['customers_mobile_phone'] : $addr['customers_phone'],
                'country' => strtolower($addr['customers_country_code'])
            );

            if(!empty($addr['customers_federal_state_code']))
            {
                $addr_data['region'] = $addr['customers_federal_state_code'];
            }
            else {
                $addr_data['region'] = null;
            }


            $kp_order_id = $kp_order_arr['order_id'];
            $kp = klarna_kp::getInstance();

            if($data['address_class'] == 'shipping')
                $kp->updateCustomerDetails($kp_order_id, $addr_data);
            else
                $kp->updateCustomerDetails($kp_order_id, false, $addr_data);

            $kp_order = $kp->getOrder($kp_order_id);
            xt_klarna_kp::setKlarnaOrderInXt($kp_order, $data['orders_id']);
        }
    }
}
catch (Exception $e)
{
    $r = new stdClass();
    $r->success = false;
    $r->msg =  TEXT_KLARNA_CANT_CHANGE_ADDRESS.'<br />'.$e->getMessage();

    $plugin_return_value = $r;
}