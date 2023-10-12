<?php
/*
 #########################################################################
 #                       xt:Commerce Shopsoftware
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # Copyright 2007-2018 xt:Commerce International Ltd. All Rights Reserved.
 # This file may not be redistributed in whole or significant part.
 # Content of this file is Protected By International Copyright Laws.
 #
 # ~~~~~~ xt:Commerce Shopsoftware IS NOT FREE SOFTWARE ~~~~~~~
 #
 # http://www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # @copyright xt:Commerce International Ltd., www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # xt:Commerce International Ltd., Kafkasou 9, Aglantzia, CY-2112 Nicosia
 #
 # office@xt-commerce.com
 #
 #########################################################################
 */

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_skrill/classes/skrill_mqi.php';
require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_skrill/classes/skrill_api.php';
require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_skrill/callback/class.callback.php';


class xt_skrill
{

    var $data = array();
    var $external = true;
    var $version = '1.0';
    var $subpayments = true;
    var $iframe = false;
    private $orders_id = 0;
    private $transaction_id;

    public static $payments = array(
        'ALI' => array( 'name'=> 'Alipay',              'icon' => 'alipay.png','allowed_countries'=>['CN']),
        'AMX' => array( 'name'=> 'American Express',    'icon' => 'amex.gif'),
        'ADB' => array( 'name'=> 'Astropay',            'icon' => 'astropay.png','allowed_countries'=>['AR','BR']),
        'CSI' => array( 'name'=> 'Cartasi',             'icon' => 'cartasi.png','allowed_countries'=>['IT']),
        'BCB' => array( 'name'=> 'Carte Bleue',         'icon' => 'cartebleue.png','allowed_countries'=>['FR']),
        'ACC' => array( 'name'=> 'VISA, Mastercard, Amex', 'icon' => 'multiple-options.gif'),
        'DNK' => array( 'name'=> 'Dankort',             'icon' => 'dankort.png','allowed_countries'=>['DK']),
        'DIN' => array( 'name'=> 'Diners',              'icon' => 'dinersclub.gif'),
        'EPY' => array( 'name'=> 'Epay.Bg',             'icon' => 'epay.gif','allowed_countries'=>['BG']),
        'NPY' => array( 'name'=> 'EPS (Netpay)',        'icon' => 'eps.png','allowed_countries'=>['AT']),
        'GIR' => array( 'name'=> 'Giropay',             'icon' => 'giropay.png','allowed_countries'=>['DE']),
        'IDL' => array( 'name'=> 'Ideal',               'icon' => 'ideal.gif','allowed_countries'=>['NL']),
        /*'JCB' => array( 'name'=> 'JCB',                 'icon' => 'jcb.gif'),*/
        'MAE' => array( 'name'=> 'Maestro',             'icon' => 'maestro.png','allowed_countries'=>['GB','ES','IE','AT']),
        'MSC' => array( 'name'=> 'Mastercard',          'icon' => 'mc.png'),
        'NTL' => array( 'name'=> 'Neteller',            'icon' => 'neteller.png'),
        'EBT' => array( 'name'=> 'Nordea Solo',         'icon' => 'nordea.png'),
        'PCS' => array( 'name'=> 'Paysafecard',         'icon' => 'paysafecard.gif'),
        'PCH' => array( 'name'=> 'Paysafecash',         'icon' => 'paysafecash.png','allowed_countries'=>['AT','HR','HU','IT','MT','PT','RO','SI','ES']),
        'PLI' => array( 'name'=> 'Poli',                'icon' => 'poli.png','allowed_countries'=>['AU']),
        'PSP' => array( 'name'=> 'Postepay',            'icon' => 'postepay.png','allowed_countries'=>['IL']),
        'PWY' => array( 'name'=> 'Przelewy24',          'icon' => 'przelewy24.gif','allowed_countries'=>['PL']),
        'DID' => array( 'name'=> 'SEPA Direct Debit',   'icon' => 'sepa.png','allowed_countries'=>['DE']),
        'OBT' => array( 'name'=> 'Rapid Transfer',      'icon' => 'rapid-transfer.png','allowed_countries'=>['AT','DK','FL','FR','DE','HU','IT','PT','NO','PL','PT','ES','SE','GB']),
        'WLT' => array( 'name'=> 'Skrill Wallet',       'icon' => 'skrill-wallet.png'),
        'SFT' => array( 'name'=> 'Sofortueberweisung',  'icon' => 'sofort.png','allowed_countries'=>['DE','AT','BE','NL','IT','FR','PL','HU','SK','CZ','GB']),
        'GLU' => array( 'name'=> 'Trustly',             'icon' => 'trustly.png','allowed_countries'=>['DE','AT','BE','NL','IT','FR','PL','HU','SK','CZ','GB','BG','EE','FI','LT','LV','RO','IE']),
        'VSA' => array( 'name'=> 'VISA',                'icon' => 'visa.gif'),
        'VSE' => array( 'name'=> 'VISA Electron',       'icon' => 'visa-electron.png'),
        'BTC' => array( 'name'=> 'Bitcoin',         'icon' => 'bitcoin.png'),
        );

        public static $status = array(
        '2'  => 'Processed',
        '0'  => 'Pending',
        '-1' => 'Cancelled',
        '-2' => 'Failed',
        '-3' => 'Chargeback',
    );

    public static $errors = array(
        '1' => 'Referred by card issuer',
        '2' => 'Invalid Merchant',
        '3' => 'Stolen card',
        '4' => 'Declined by customer\'s Card Issuer',
        '5' => 'Insufficient funds',
        '8' => 'PIN tries exceed - card blocked',
        '9' => 'Invalid Transaction',
        '10' => 'Transaction frequency limit exceeded',
        '12' => 'Invalid credit card or bank account',
        '15' => 'Duplicate transaction',
        '19' => 'Unknown failure reason. Try again',
        '24' => 'Card expired',
        '28' => 'Lost/Stolen card',
        '32' => 'Card Security Code check failed',
        '37' => 'Card restricted by card issuer',
        '38' => 'Security violation',
        '42' => 'Card blocked by card issuer',
        '44' => 'Customer\'s issuing bank not available',
        '51' => 'Processing system error',
        '63' => 'Transaction not permitted to cardholder ',
        '70' => 'Customer failed to complete 3DS',
        '71' => 'Customer failed SMS verification',
        '80' => 'Fraud engine declined',
        '98' => 'Error in communication with provider',
        '99' => 'Failure reason not specified',
    );

    public $position;

    public function setPosition($pos)
    {
        $this->position = $pos;
    }

    public function _getParams()
    {
        return false;
    }


    function email_check ()
    {
    }

    function secret_word_check ()
    {
    }

    function __construct ()
    {
        global $xtLink;
        $this->RETURN_URL = $xtLink->_link(array('page' => 'checkout', 'paction' => 'payment_process'));
        $this->ERROR_URL = $xtLink->_link(array('page' => 'checkout', 'paction' => 'payment', 'params' => 'error=ERROR_PAYMENT'));
        $this->CANCEL_URL = $xtLink->_link(array('page' => 'checkout', 'paction' => 'payment'));
        $this->NOTIFY_URL = $xtLink->_link(array('page' => 'callback', 'paction' => 'xt_skrill'));
        $this->TARGET_URL = 'https://pay.skrill.com';

        $this->EMAIL_CHECK_URL = 'https://www.skrill.com/app/email_check.pl';
        $this->SECRET_WORD_CHECK_URL = 'https://www.skrill.com/app/secret_word_check.pl';

        $this->allowed_subpayments = array_unique(array_filter(explode(',', XT_SKRILL_ACTIVATED_PAYMENTS)));
    }

    function build_payment_info ($data)
    {

    }

    function prepareRedirect($data, $endpoint='https://pay.skrill.com/' )
    {
        // debug
        /*
        $data = array(
            "pay_to_email" => $data['pay_to_email'],
            "amount" => $data['amount'],
            "currency" => $data['currency'] ,
            "language" => $data['language'] ,
            "prepare_only" => 1
        );
        */

        $data['prepare_only'] = 1;

        $ch = curl_init();

        // debug
        //curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        //curl_setopt($ch, CURLOPT_VERBOSE, 1);
        //curl_setopt($ch, CURLOPT_HEADER, 1);

        curl_setopt($ch, CURLOPT_POST, TRUE);
        $data = http_build_query($data);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_URL, $endpoint.'?ts=' . time());
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

        $json = json_decode($result);
        if(is_object($json) || empty($result))
        {
            $log_data = array();
            $log_data['class'] = 'error_skrill';
            $log_data['transaction_id'] = $this->transaction_id;
            $log_data['error_data'] = $result;
            $log_data['error_msg'] = 'xt_skrill prepareRedirect unexpected response' ;
            $this->_addCallbackLog($log_data);

            global $xtLink;
            $xtLink->_redirect($this->ERROR_URL);
        }

        // debug
        // $headerSent = curl_getinfo($ch, CURLINFO_HEADER_OUT );
        //$a = str_replace("\r",'',$headerSent);
        // $a = str_replace("\n",'<br>',$a);
        //echo $a;
        //die();

        if (curl_errno($ch))
        {
            $log_data = array();
            $log_data['class'] = 'error_skrill';
            $log_data['transaction_id'] = $this->transaction_id;
            $log_data['error_msg'] = 'xt_skrill prepareRedirect curl_error: ' . curl_errno($ch). '-' .curl_error($ch) ;
            $this->_addCallbackLog($log_data);

            return false;
        }
        curl_close($ch);
        return $result;
    }

    function pspRedirect ($processed_data = array())
    {
        global $xtLink, $filter, $db, $countries, $language;

        $orders_id = (int)$_SESSION['last_order_id'];

        $rs = $db->Execute("SELECT customers_id FROM " . TABLE_ORDERS . " WHERE orders_id=?", array($orders_id));

        $order = new order($orders_id, $rs->fields['customers_id']);

        $this->transaction_id = $this->generate_trid();
        $subpayment = $_SESSION['selected_payment_sub'];

        $data = array();
        $data['pay_to_email'] = XT_SKRILL_EMAILID;
        $data['transaction_id'] = $this->transaction_id;

        $data['return_url'] = $this->RETURN_URL;
        $data['cancel_url'] = $this->CANCEL_URL;
        $data['status_url'] = $this->NOTIFY_URL;
        $data['language'] = $language->code;

        $data['status_url2'] = XT_SKRILL_NOTIFICATION_EMAIL;
        $data['logo_url'] = XT_SKRILL_LOGO_URL;

        $data['pay_from_email'] = $order->order_data['customers_email_address'];
        $data['amount'] = round($order->order_total['total']['plain'], 2);
        $data['currency'] = $order->order_data['currency_code'];
        $data['detail1_description'] = '';
        $data['detail1_text'] = TEXT_ORDER_NUMBER . ' ' . $orders_id;
        $data['hide_login'] = '1';
        $data['detail2_description'] = TEXT_ORDER_DATE;
        $data['detail2_text'] = $db->BindDate(time());
        $data['amount2_description'] = TEXT_TOTAL . ':';
        $data['amount2'] = round($order->order_total['total']['plain'], 2);

        if (!in_array($subpayment, $this->allowed_subpayments)) $subpayment = 'VSA,MSC';

        $data['payment_methods'] = $subpayment;
        $data['merchant_fields'] = 'Field1,platform';
        $data['Field1'] = md5(XT_SKRILL_EMAILID);
        $data['platform'] = '21477279';

        $data['firstname'] = $order->order_data['delivery_firstname'];
        $data['lastname'] = $order->order_data['delivery_lastname'];
        $data['address'] = $order->order_data['delivery_street_address'];
        $data['postal_code'] = $order->order_data['delivery_postcode'];
        $data['city'] = $order->order_data['delivery_city'];
        $data['state'] = $order->order_data['delivery_state'];

        if (!is_object($countries)) {
            $countries = new countries('true');
        }

        $data['country'] = $order->order_data['billing_country_code'];
        $data['confirmation_note'] = '';

        $data['wpf_redirect'] = 1;

        $prepare = $this->prepareRedirect($data);

        if($prepare === false)
        {
            global $xtLink;
            $xtLink->_redirect($this->ERROR_URL);
        }

        $db->Execute("UPDATE " . TABLE_ORDERS . " SET orders_data=? WHERE orders_id=?", array($this->transaction_id, $orders_id));

        return $this->TARGET_URL .'?sid='.$prepare;
    }

    function pspSuccess ()
    {
        return true;
    }

    function generate_trid ()
    {
        $random = md5(time());
        $random = $random . XT_SKRILL_EMAILID . md5(XT_SKRILL_MERCHANT_ID);
        $random = md5($random);
        $trid = substr($random, 0, 16);
        $trid = 'XTC' . $trid;
        return $trid;
    }

    public function saved_ACTIVATED_PAYMENTS($data)
    {
        global $store_handler, $db;
        $shop_id = $data['shop_id'];
        $obj = new stdClass();
        $obj->topics = array();
        $obj->totalCount = 0;

        $val = $db->GetOne('SELECT config_value FROM '.TABLE_CONFIGURATION_PAYMENT." WHERE config_key='XT_SKRILL_ACTIVATED_PAYMENTS' AND shop_id=?", array($shop_id));

        if ($val)
        {
            $array = explode(',', $val);

            if (!empty($array)) {

                foreach ($array as $key) {
                    $value = self::$payments[$key];
                    $obj->topics[] = array('id' => $key, 'name' => $value['name'], 'desc' => '');
                }
            }
        }

        $obj->totalCount = count($obj->topics);
        return json_encode($obj);
    }

    public static function dropdownAllPayments()
    {
        $result = array();
        foreach(self::$payments as $key => $value)
        {
            $result[] = array('id' => $key, 'name' => $value['name']);
        }
        return $result;
    }

    public static function skrillActivated()
    {
        global $db;
        static $r = null;
        if($r === null)
        {
            $r = $db->GetOne("SELECT status FROM ".TABLE_PAYMENT." WHERE payment_code='xt_skrill'");
            $r = empty($r) ? false : true;
        }
        return $r;
    }

    public static function getSkrillPaymentId()
    {
        global $db;
        static $r = null;
        if($r === null)
        {
            $r = $db->GetOne("SELECT payment_id FROM ".TABLE_PAYMENT." WHERE payment_code='xt_skrill'");
            $r = empty($r) ? false : true;
        }
        return $r;
    }

    function setOrdersId($orders_id)
    {
        $this->orders_id = $orders_id;
    }

    function _addCallbackLog($log_data)
    {
        global $db;
        //if (is_null($log_data['transaction_id'])) return false;
        $log_data['module'] = 'xt_skrill';

        if(empty($log_data['orders_id']))
        {
            $log_data['orders_id'] = isset($this->orders_id) ? $this->orders_id : 0;
        }
        if (is_array($log_data['callback_data']))
        {
            $log_data['callback_data'] = serialize($log_data['callback_data']);
        }
        if (is_array($log_data['error_data']))
        {
            $log_data['error_data'] = serialize($log_data['error_data']);
        }
        if ($log_data['error_data'] == '')
        {
            $log_data['error_data'] = '';
        }
        $db->AutoExecute(TABLE_CALLBACK_LOG, $log_data, 'INSERT');

        return $db->Insert_ID();
    }

    /**
     * Calls to Merchant Query Interface (MQI)
     */
    public function getUpdateStatus($trn_id)
    {
        $mqi = new skrill_mqi(XT_SKRILL_EMAILID, XT_SKRILL_MQI_PASSWORD, $trn_id);
        $data = $mqi->getStatus();
        $cb = new callback_xt_skrill();
        $cb->processAdminAction($data);
        return $data;
    }


    /**
     * Calls to Automated Payment Interface (API)
     */
    public function refund($trn_id, $refunds_id, $amount = false, $note = false)
    {
        $api = new skrill_api(XT_SKRILL_EMAILID, XT_SKRILL_API_PASSWORD, $trn_id);
        // call prepare to get a sid
        $sid = $api->prepareRefund($this->NOTIFY_URL, $refunds_id, $amount, $note);
        $r   = $api->refund($sid);

        return $r;
    }

}
