<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

/**
 * Class skrill_api  Automated Payment Interface
 */
class skrill_api
{
    private static $_api_url_url = 'https://www.skrill.com/app/refund.pl';
    private $_email_id;
    private $_pwd;
    private $_trn_id;

    private $_def_data;

    public function __construct($email_id, $pwd, $trn_id)
    {
        $this->_email_id = $email_id;
        $this->_pwd = $pwd;
        $this->_trn_id = $trn_id;

        $this->_def_data = array(
            'email' => $this->_email_id,
            'password' => strtolower(md5($this->_pwd)),
            'transaction_id' => $this->_trn_id.''
        );
    }

    public function prepareRefund($notify_url, $refunds_id, $amount = false, $note = false)
    {
        $data = array(
            'refund_status_url' => $notify_url,
            'refunds_id' => $refunds_id,
            'merchant_fields' => 'msg_type,refund_type,refunds_id',
            'msg_type' => 'refund_notification',
            'refund_type' => $amount ? 'partial' : 'full');
        if($amount>0)
        {
            $data['amount'] = $amount;
        }
        if($note && $note != '')
        {
            $data['refund_note'] = $note;
        }
        $xml = $this->action('prepare', $data);

        if(!isset($xml->sid))
        {
            throw new Exception("SID not found in responses to prepare refund");
        }
        return (string) $xml->sid;
    }

    public function refund($sid)
    {
        $data = array('sid' => $sid);
        $r = $this->action('refund', $data);
        $r = (array) $r;
        return $r;
    }

    private function action($action, $data = array())
    {
        $ch = curl_init();

        $data = array_merge($this->_def_data, $data, array('action' => $action));

        //error_log('------ skrill api data ');
        //error_log(print_r($data,true));

        curl_setopt($ch, CURLOPT_POST, TRUE);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_URL, self::$_api_url_url.'?action='.$action);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);

        // value should be an integer for the following values of the option parameter: CURLOPT_SSLVERSION
        // CURL_SSLVERSION_TLSv1_2 (6)
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
        // to ensure curls verify functions working in dev:
        // download http://curl.haxx.se/ca/cacert.pem
        // point _CURL_CURLOPT_CAINFO to cacert.pem, eg in config.php
        if (_CURL_CURLOPT_CAINFO !== '_CURL_CURLOPT_CAINFO')
        {
            curl_setopt($ch, CURLOPT_CAINFO, _CURL_CURLOPT_CAINFO);
        }

        $result = curl_exec($ch);

        if (curl_errno($ch))
        {
            $log_data = array();
            $log_data['class'] = 'error_skrill_'.$action;
            $log_data['transaction_id'] = $this->transaction_id;
            $log_data['error_data'] = curl_errno($ch). '-' .curl_error($ch);
            $msg = "xt_skrill  API action [$action] curl_error: ".curl_errno($ch). '-' .curl_error($ch);
            $log_data['error_msg'] = $msg;
            $this->_addCallbackLog($log_data);
            curl_close($ch);
            throw new Exception($msg);
        }

        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE );
        if($code != 200)
        {
            $log_data = array();
            $log_data['class'] = 'error_skrill_'.$action;
            $log_data['transaction_id'] = $this->_trn_id;
            $log_data['error_data'] = $result;
            $msg = "xt_skrill API action [$action] unexpected response: [$result]" ;
            $log_data['error_msg'] = $msg;
            $this->_addCallbackLog($log_data);
            curl_close($ch);
            throw new Exception($msg);
        }


        curl_close($ch);

        $xml = simplexml_load_string($result);
        if(isset($xml->error))
        {
            $msg = isset($xml->error->error_msg) ? (string) $xml->error->error_msg : (string) $xml->error;
            throw new Exception($msg);
        }
        return $xml;
    }


}