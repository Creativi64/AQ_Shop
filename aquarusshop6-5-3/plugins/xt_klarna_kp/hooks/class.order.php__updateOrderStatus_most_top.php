<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_klarna_kp/classes/constants.php';

global $db;

$store_id = (int) $this->order_data["shop_id"];
if($store_id && !empty($trigger))
{
    $klarna_config = xt_klarna_kp::getKlarnaConfig($store_id);
    $allowed_triggers = $klarna_config['_KLARNA_CONFIG_TRIGGER_ALLOWED_TRIGGER'];
    $allowed_triggers = explode(',', $allowed_triggers);
    $allowed_triggers = array_filter($allowed_triggers);
    $payments = array_map('trim', $allowed_triggers);
    $allowed_triggers = array_unique($allowed_triggers);

    /**
     * die zZ möglichen trigger
     *
     *  der admin, wenn im Backend der Status geändert wird
     *  $_REQUEST["action"] == 'saveHistory' && $trigger == $_SESSION["admin_user"]["user_name"]
     *
     *  die SOAP-Schnittstelle
     *  $trigger == 'SOAP'
     *
     *  Ein in den Shopeinstellungen > Klarna festgelegter trigger
     *  in_array($trigger, $allowed_triggers)
    */
    if (($_REQUEST["action"] == 'saveHistory' && $trigger == $_SESSION["admin_user"]["user_name"])
        || $trigger == 'SOAP' || in_array($trigger, $allowed_triggers)
    )
        {

            global $klarna_trigger_warning;
            $klarna_trigger_warning = false;

            $cancel_triggers  = array_values(explode(',',$klarna_config['_KLARNA_CONFIG_TRIGGER_CANCEL']));
            $capture_triggers = array_values(explode(',',$klarna_config['_KLARNA_CONFIG_TRIGGER_FULL_CAPTURE']));

            /** überschneidungen in den triggern sind nicht erlaubt  */
            $no_conflict =  array_intersect($cancel_triggers, $capture_triggers);

            try
            {
                if (in_array($status, $cancel_triggers) && empty($no_conflict))
                {
                    // do cancel
                    $kp_order_arr = xt_klarna_kp::getKlarnaOrderFromXt($this->oID);
                    if (!empty($kp_order_arr['order_id']))
                    {
                        $kp_order_id = $kp_order_arr['order_id'];
                        $kp = klarna_kp::getInstance();
                        $kp->cancelOrder($kp_order_id);
                        $kp_order = $kp->getOrder($kp_order_id);
                        xt_klarna_kp::setKlarnaOrderInXt($kp_order, $this->oID);
                    }
                }
            else if (in_array($status, $capture_triggers) && empty($no_conflict))
            {
                // full capture
                $a = 0;
                $xt_kp = new xt_klarna_kp();
                $captureResult = $xt_kp->autoCapture($this->oID);
            }

        } catch (Exception $e)
            {
                $msg = explode('|', $e->getMessage());
                $msg[0] = $msg[0] . (!empty($msg[1]) ? '::' . $msg[1] : '');
                unset($msg[1]);
                array_unshift($msg, 'XT Bestellung/order ' . $this->oID);

            if ($_REQUEST["action"] == 'saveHistory' && $trigger == $_SESSION["admin_user"]["user_name"])
                {
                    // manual trigger from backend

                    $r = new stdClass();
                    $r->success = true;
                    $r->refresh = false;
                    $msg = implode("<br/>", $msg);
                    $r->msg = $r->message = $msg;

                    if (
                        (in_array($status, $cancel_triggers) && $klarna_config['_KLARNA_CONFIG_TRIGGER_CANCEL_FORCE_STATUS_UPDATE'] != 1)
                        ||
                        ($status == $klarna_config['_KLARNA_CONFIG_TRIGGER_FULL_CAPTURE'] && $klarna_config['_KLARNA_CONFIG_TRIGGER_FULL_CAPTURE_FORCE_STATUS_UPDATE'] != 1)
                    )
                    {
                        header("Content-Type: application/json");
                        echo json_encode($r);
                        die;
                    }
                    else
                    {
                        $klarna_trigger_warning = $r;
                    }
                }
                else
                {
                // SOAP / PHP (additional configured triggers in config  backend > shop > klarna)

                    $msg = implode(" | ", $msg);

                    if(!isset($cGroup)) $cGroup = -1;

                    if (!empty($klarna_config['_KLARNA_CONFIG_TRIGGER_ESCALATION_EMAIL']))
                    {
                        $mailer = new xtMailer('none', $lang, $cGroup, -1, $store_id);
                        $mailer->_addReceiver($klarna_config['_KLARNA_CONFIG_TRIGGER_ESCALATION_EMAIL'], $klarna_config['_KLARNA_CONFIG_TRIGGER_ESCALATION_EMAIL']);
                        $mailer->_setFrom(_CORE_DEBUG_MAIL_ADDRESS, _CORE_DEBUG_MAIL_ADDRESS);
                        $mailer->_setSubject('Klarna trigger warning');
                        $mailer->_setContent($msg, $msg);
                        $mailer->_sendMail(_CORE_DEBUG_MAIL_ADDRESS, _CORE_DEBUG_MAIL_ADDRESS);
                    }

                    if (
                        ($status == $klarna_config['_KLARNA_CONFIG_TRIGGER_CANCEL'] && $klarna_config['_KLARNA_CONFIG_TRIGGER_CANCEL_FORCE_STATUS_UPDATE'] != 1)
                        ||
                        ($status == $klarna_config['_KLARNA_CONFIG_TRIGGER_FULL_CAPTURE'] && $klarna_config['_KLARNA_CONFIG_TRIGGER_FULL_CAPTURE_FORCE_STATUS_UPDATE'] != 1)
                    )
                    {
                        $r['result'] = false;
                        $r['message'] = $msg;
                        $plugin_return_value = $r;
                    }

                }
            }
        }

}