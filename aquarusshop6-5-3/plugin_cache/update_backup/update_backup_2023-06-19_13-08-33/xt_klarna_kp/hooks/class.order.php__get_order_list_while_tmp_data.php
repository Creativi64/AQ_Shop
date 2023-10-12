<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

unset($status);
unset($capture);

$kp_order = json_decode($_data['order_data']['kp_order_data'], true);

$tmp_data['kp_order_status_overview'] = array();
if(is_array($kp_order))
{
    global $price;

    $test_mode = $_data['order_data']['kp_order_test_mode'] == '1';
    $txt_test_drive = $_data['order_data']['kp_order_test_mode'] == '1' ? __define('TEXT_KLARNA_CONFIG_KP_TESTMODE').': ' : '';

    $amount = $price->_StyleFormat($kp_order["original_order_amount"]/100);

    $status = array(
        'icon' => 'circle',
        'style' => 'solid',
        'color' => $test_mode ? '#9eeca5' : '#649268',
        'size' => 'sm',
        'cssClass' => '',
        'tooltip' => $txt_test_drive.constant('TEXT_KLARNA_KP_ACCEPTED') ?: $txt_test_drive.'Accepted '.$amount
    );

    $canceled = false;
    $captured = false;
    $refunded = false;

    // cancelled
    if ($_data['order_data']['kp_order_status'] == 'CANCELLED')
    {
        $canceled = array(
            'icon' => 'ban',
            'style' => 'solid',
            'size' => 'sm',
            'cssClass' => '',
            'tooltip' => $txt_test_drive.constant('TEXT_KLARNA_KP_CANCELED') ?: $txt_test_drive.'Canceled'
        );
    }

    // fraud status
    if ($_data['order_data']['kp_order_fraud_status'] != 'ACCEPTED')
    {
        if ($_data['order_data']['kp_order_fraud_status'] == 'PENDING')
        {
            $status['color'] = 'orange';
            $status['tooltip'] = $txt_test_drive.constant('TEXT_KLARNA_KP_PENDING') ?: $txt_test_drive.'Pending';
        }
        else if ($_data['order_data']['kp_order_fraud_status'] == 'REJECTED')
        {

            $status['color'] = 'red';
            $status['tooltip'] = $txt_test_drive.constant('TEXT_KLARNA_KP_REJECTED') ?: $txt_test_drive.'Rejected';
        }

    }
    else {
        // capture
        if($kp_order["captured_amount"] > 0)
        {
            $amount = $price->_StyleFormat($kp_order["captured_amount"]/100);
            $captured = array(
                'icon' => 'circle',
                'style' => 'solid',
                'size' => 'sm',
                'tooltip' => $txt_test_drive.constant('TEXT_KLARNA_KP_CAPTURED_FULL') ?: $txt_test_drive.'fully captured '.$amount
            );
            if ($kp_order["remaining_authorized_amount"] > 0)
            {
                $captured['style'] = 'regular';
                $captured['tooltip'] = $txt_test_drive.constant('TEXT_KLARNA_KP_CAPTURED_PART') ?: $txt_test_drive.'Partial captured '.$amount;
            }

            // refunded
            if($kp_order["refunded_amount"] > 0)
            {
                $amount = $price->_StyleFormat($kp_order["refunded_amount"]/100);
                $refunded = array(
                    'icon' => 'circle',
                    'style' => 'solid',
                    'size' => 'sm',
                    'tooltip' => $txt_test_drive.constant('TEXT_KLARNA_KP_REFUNDED_FULL') ?: $txt_test_drive.'fully refunded '.$amount
                );
                if ($kp_order["refunded_amount"] < $kp_order["captured_amount"])
                {
                    $refunded['style'] = 'regular';
                    $refunded['tooltip'] = $txt_test_drive.constant('TEXT_KLARNA_KP_REFUNDED_PART') ?: $txt_test_drive.'partial refunded '.$amount;
                }

                // refunded
            }

        }

    }



    $tmp_data['kp_order_status_overview'] = array(
        $status
    );
    if($canceled)
    {
        $tmp_data['kp_order_status_overview'][] = $canceled;
    }
    else if($captured)
    {
        $tmp_data['kp_order_status_overview'][] = $captured;

        if($refunded)
        {
            $tmp_data['kp_order_status_overview'][] = $refunded;
        }
    }

    if(($kp_order['status'] == 'EXPIRED')
        )
    {
        $not_captured_warning = array(
            'icon' => 'exclamation-circle',
            'style' => 'solid',
            'color' => '#ff3a37',
            'size' => 'lg',
            'class' => '',
            'tooltip' => $txt_test_drive.constant('TEXT_KP_EXPIRED') ?: 'Expired!'
        );
        $tmp_data['kp_order_status_overview'][] = $not_captured_warning;
    }
    else if(!in_array($kp_order['fraud_status'], ['PENDING','REJECTED'])
        && !in_array($kp_order['status'], ['CANCELLED'])
        && (!$captured || $kp_order["remaining_authorized_amount"] > 0)
        )
    {
        $not_captured_warning = array(
            'icon' => 'exclamation-circle',
            'style' => 'solid',
            'color' => '#ffa037',
            'size' => 'lg',
            'class' => 'blink blink20',
            'tooltip' => $txt_test_drive.constant('TEXT_KLARNA_DONT_FORGET_TO_CAPTURE') ?: 'Don\'t forget to capture!'
        );
        $tmp_data['kp_order_status_overview'][] = $not_captured_warning;
    }
}
