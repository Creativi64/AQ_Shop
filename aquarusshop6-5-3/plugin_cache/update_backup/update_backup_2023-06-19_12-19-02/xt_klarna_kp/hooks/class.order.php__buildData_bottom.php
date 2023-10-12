<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_klarna_kp/classes/constants.php';

global $kp_order; // created in class.order.php__buildData_top.php

if(is_array($kp_order) &&
    $_REQUEST['gridHandle'] == 'ordergridForm' &&
    strpos($_REQUEST['parentNode'], 'node_order') === 0 &&
    $_REQUEST['pg'] == 'overview')
{
    global $price;

    global $db;

    $klarna_backend_url ='';
    $mid = false; // xt_klarna_kp::getMerchantIdForOrder($oID);
    if($mid)
    {
        $merchant_id_parts = explode('_', $mid);
        $klarna_backend_url = klarna_kp::getBackendBaseUrl(/* TODO  force_test */).'/merchants/'.$merchant_id_parts[0].'/orders/'.$kp_order['order_id'];
    }

    $text = '<span class="bold" style="font-weight:bold">' . TEXT_KP_DETAILS;
    if(!empty($klarna_backend_url))
    {
        $text .= ' <a href="'.$klarna_backend_url.'" target="_blank'.$kp_order['klarna_reference'].'">' . $kp_order['klarna_reference'] . '</a>';
    }
    $text .= '</span><span style="font-size: 0.8em"> Ref&nbsp;'.$kp_order['klarna_reference'].'</span> ';
    $order_data['order_data']['order_info_options'][] = array('text' => $text, 'value' => '', 'topic' => true);

    $keys = array('status', 'fraud_status', 'original_order_amount', 'captured_amount', 'refunded_amount', 'billed_amount', 'not_billed_amount', 'expires_at');
    foreach ($keys as $key)
    {
        switch ($key)
        {
            case 'fraud_status':
                if ($kp_order['fraud_status'] == 'PENDING')
                {
                    $value = '<span style="background: #F0B800; font-weight: bold; display: inline-block; padding:1px 5px">!&nbsp;'.$kp_order['fraud_status'].'&nbsp;!</span>';
                }
                else if ($kp_order['fraud_status'] == 'REJECTED')
                {
                    $value = '<span style="background: #eb6f93; font-weight: bold; display: inline-block; padding:1px 5px">!&nbsp;'.$kp_order['fraud_status'].'&nbsp;!</span>';
                }
                else {
                    $value = $kp_order[$key];
                }
                break;
            case 'captured_amount':
            case 'order_amount':
            case 'refunded_amount':
            case 'original_order_amount':
                $value = $price->_StyleFormat(((int)$kp_order[$key] / 100));
                break;
            case 'billed_amount':
                $value = $price->_StyleFormat(((int)$kp_order['captured_amount']-(int)$kp_order['refunded_amount']) / 100 );
                break;
            case 'not_billed_amount':
                $value = $price->_StyleFormat(((int)$kp_order['remaining_authorized_amount']) / 100 );

                $js_do_capture = '';
                if ($kp_order['remaining_authorized_amount'] > 0 && $kp_order['fraud_status'] == 'ACCEPTED' && $kp_order['status']!='CANCELLED')
                {
                    $code = 'doCapture_kp';
                    $panelData = xt_klarna_kp::getCaptureWindowPanel_kp($oID, $kp_order['remaining_authorized_amount'] / 100, $kp_order);

                    $remoteWindow = ExtAdminHandler::_RemoteModalWindow3(XT_KLARNA_KP . $infoText, $code, $panelData['panel'],
                        500, 170, 'window',
                        $panelData['buttons']);
                    $remoteWindow->setId('doCapture_kp_wnd' . $oID);

                    $js_do_capture = $remoteWindow->getJavascript(false, "new_window") . ' new_window.show();';
                }


                //$value .= '<a href="javascript:void(0)" onclick="javascript:showKlarnaWindow66(\'doCapture_kp_wnd\',87)">sfdvdvf</a><script>function showKlarnaWindow66(){'.$js_do_capture.'}</script>';
                break;
            case'expires_at':
                try
                {
                    $dt = new DateTime($kp_order[$key]);
                    $value = '<span class="'.$kp_order['klarna_reference'].'_expires_at">'.$dt->format('Y-m-d H:i').'</span>';
                } catch (Exception $e)
                {
                    $value = $kp_order[$key];
                }
                break;
            default:
                $value = $kp_order[$key];
        }
        $order_data['order_data']['order_info_options'][] = array('text' => '<span class="" style="">&nbsp;' . constant('TEXT_KP_' . strtoupper($key)) . '</span>', 'value' => $value);
    }

    if(!in_array($kp_order['fraud_status'], ['PENDING','REJECTED'])
        && !in_array($kp_order['status'], ['CANCELLED'])
        && ($kp_order['captured_amount'] == 0 || $kp_order["remaining_authorized_amount"] > 0))
    {
        $order_data['order_data']['order_info_options'][] = array('text' => TEXT_KLARNA_DONT_FORGET_TO_CAPTURE, 'value' => '', 'msgCls' => 'warning');
    }

    if($order_data['order_data']['kp_order_test_mode'] == "1")
    {
        $order_data['order_data']['order_info_options'][] = array('text' => __define('TEXT_KLARNA_CONFIG_KP_TESTMODE'), 'value' => '', 'msgCls' => 'info-box-red');
    }

    $order_data['order_data']['payment_name'] = 'Klarna';
}
