<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if(!empty($cgID) && $cgID == 101)
{
    /**
     *
     **** KLARNA_CONFIG_KP_BASIC
    _KLARNA_CONFIG_KP_MID_TEST
    _KLARNA_CONFIG_KP_PWD_TEST
    _KLARNA_CONFIG_KP_TESTMODE
    _KLARNA_CONFIG_KP_MID
    _KLARNA_CONFIG_KP_PWD
    _KLARNA_CONFIG_KP_TESTMODE_CUSTOMER_GROUP
     *
     *
     **** KLARNA_CONFIG_ADVANCED
    _KLARNA_CONFIG_SEND_EXTENDED_PRODUCT_INFO
     *
     **** KLARNA_CONFIG_LAYOUT
    _KLARNA_CONFIG_LOGO_DESIGN
    _KLARNA_CONFIG_WIDGET_DESIGN
    _KLARNA_CONFIG_KP_RADIUS_BORDER
    _KLARNA_CONFIG_KP_COLOR_BORDER
    _KLARNA_CONFIG_KP_COLOR_BORDER_SELECTED
    _KLARNA_CONFIG_KP_COLOR_BUTTON
    _KLARNA_CONFIG_KP_COLOR_BUTTON_TEXT
    _KLARNA_CONFIG_KP_COLOR_CHECKBOX
    _KLARNA_CONFIG_KP_COLOR_CHECKBOX_CHECKMARK
    _KLARNA_CONFIG_KP_COLOR_DETAILS
    _KLARNA_CONFIG_KP_COLOR_HEADER
    _KLARNA_CONFIG_KP_COLOR_LINK
    _KLARNA_CONFIG_KP_COLOR_TEXT
    _KLARNA_CONFIG_KP_COLOR_TEXT_SECONDARY
     *
     **** KLARNA_CONFIG_STATUS_MAPPING
    _KLARNA_CONFIG_STATUS_ACCEPTED
    _KLARNA_CONFIG_STATUS_PENDING
    _KLARNA_CONFIG_STATUS_REJECTED
    _KLARNA_CONFIG_STATUS_CANCELED
     *
     **** KLARNA_CONFIG_TRIGGER
    _KLARNA_CONFIG_TRIGGER_FULL_CAPTURE
    _KLARNA_CONFIG_TRIGGER_FULL_CAPTURE_FORCE_STATUS_UPDATE
    _KLARNA_CONFIG_TRIGGER_CANCEL
    _KLARNA_CONFIG_TRIGGER_CANCEL_FORCE_STATUS_UPDATE
    _KLARNA_CONFIG_TRIGGER_ESCALATION_EMAIL
    _KLARNA_CONFIG_TRIGGER_ALLOWED_TRIGGER
     *
     *
     **** KLARNA_CONFIG_KP_B2B
    _KLARNA_CONFIG_KP_B2B_ENABLED
    _KLARNA_CONFIG_KP_B2B_GROUPS
     *
     *
     */

    $regexs = array('^#[a-fA-F0-9]{6}$' => array(
        "_KLARNA_CONFIG_KP_COLOR_BORDER",
        "_KLARNA_CONFIG_KP_COLOR_BORDER_SELECTED",
        "_KLARNA_CONFIG_KP_COLOR_BUTTON",
        "_KLARNA_CONFIG_KP_COLOR_BUTTON_TEXT",
        "_KLARNA_CONFIG_KP_COLOR_CHECKBOX",
        "_KLARNA_CONFIG_KP_COLOR_CHECKBOX_CHECKMARK",
        "_KLARNA_CONFIG_KP_COLOR_DETAILS",
        "_KLARNA_CONFIG_KP_COLOR_HEADER",
        "_KLARNA_CONFIG_KP_COLOR_LINK",
        "_KLARNA_CONFIG_KP_COLOR_TEXT",
        "_KLARNA_CONFIG_KP_COLOR_TEXT_SECONDARY",
    ));
    foreach($regexs as $regex => $itemsToCheck)
    {
        foreach($itemsToCheck as $item)
        {
            $header[$item]['regex'] = $regex;
        }
    }

    $csrf_param = '&sec='. $_SESSION['admin_user']['admin_key'];

    $header['_KLARNA_CONFIG_KP_B2B_GROUPS'] = array('type' => 'itemselect', 'readonly'=>false, 'required' => true,
        'height' => 250,
        'url' => 'DropdownData.php?get=klarna_kp_b2b_groups&store_id=' . $this->url_data['store_id'],
        'valueUrl' => 'adminHandler.php?plugin=xt_klarna_kp&load_section=xt_klarna_kp&pg=get_saved_klarna_kp_b2b_groups&store_id=' . $this->url_data['store_id'].$csrf_param);

    $header['_KLARNA_CONFIG_KP_PAYMENT_METHODS'] = array('type' => 'itemselect', 'readonly'=>false, 'required' => true,
        'height' => 150,
        'url' => 'DropdownData.php?get=klarna_kp_payment_methods&store_id=' . $this->url_data['store_id'],
        'valueUrl' => 'adminHandler.php?plugin=xt_klarna_kp&load_section=xt_klarna_kp&pg=get_saved_klarna_kp_payment_methods&store_id=' . $this->url_data['store_id'].$csrf_param);


    $header['_KLARNA_CONFIG_KP_B2B_INFO']['value'] = __define('TEXT_KLARNA_CONFIG_KP_B2B_INFO_TEXT');


    $add_keys = ['_KLARNA_CONFIG_TRIGGER_FULL_CAPTURE', '_KLARNA_CONFIG_TRIGGER_CANCEL'];

    foreach($add_keys as $key)
    {
        $header[$key] = array('type' => 'itemselect', 'readonly'=>false, 'required' => false,
            'height' => 250,
            'url' => 'DropdownData.php?get=klarna_kp_order_status&store_id=' . $this->url_data['store_id'],
            'valueUrl' => 'adminHandler.php?plugin=xt_klarna_kp&load_section=xt_klarna_kp&pg=get_saved_klarna_kp_triggers&store_id=' . $this->url_data['store_id'].'&key='.$key.$csrf_param);
    }

}
