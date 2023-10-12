<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');


class shipcloud_api
{
    protected static $_instance = null;

    protected $_base_url = "https://api.shipcloud.io/v1/";

    protected $_api_key = '';
    protected $_carriers = null;
    protected $_log = null;
    protected $_affId = null;

    static function getInstance($api_key = '', $affId = null, $log_file = null)
    {
        if(empty(self::$_instance))
        {
            self::$_instance = new shipcloud_api($api_key, $affId);
        }
        self::$_instance->_log = $log_file;
        return self::$_instance;
    }

    protected function __construct($api_key = '', $affId = null)
    {
        $this->_api_key = $api_key;
        $this->_affId = $affId;
    }

    public function getCarriers()
    {
        if(empty($this->_carriers))
        {
            $this->_carriers = $this->api_carriers();
        }
        return $this->_carriers;
    }

    /**
     * @param $data
     * @return Shipcloud\ShipmentQuotesResponse
     * @throws Exception
     */
    public function quotesGet($data)
    {
        $endpoint = $this->_base_url.'shipment_quotes';
        $a = json_decode(json_encode($data), true);
        $r = $this->ApiJSONCall($a, $endpoint, __FUNCTION__, 'POST');
        $r = shipcloud_helper::cast('Shipcloud\ShipmentQuotesResponse', $r->shipment_quote);
        return $r;
    }

    public function shipmentCreate($data)
    {
        $endpoint = $this->_base_url.'shipments';
        $a = json_decode(json_encode($data), true);
        $r = $this->ApiJSONCall($a, $endpoint, __FUNCTION__, 'POST');
        $r = shipcloud_helper::cast('Shipcloud\ShipmentCreateResponse', $r);
        return $r;
    }

    /**
     * @param $id
     * @param bool|true $cast
     * @return Shipcloud\ShipmentIndexResponse
     */
    public function shipmentRead($id, $cast = true)
    {
        $endpoint = $this->_base_url.'shipments/'.$id;
        $a = array();
        $r = $this->ApiJSONCall($a, $endpoint, __FUNCTION__, 'GET');
        if($cast) $r = shipcloud_helper::cast('Shipcloud\ShipmentIndexResponse', $r);
        return $r;
    }

    public function shipmentDelete($id, $cast = true)
    {
        $endpoint = $this->_base_url.'shipments/'.$id;
        $a = array();
        $r = $this->ApiJSONCall($a, $endpoint, __FUNCTION__, 'DELETE');
        return true;
    }

    public function shipmentIndex(/*$data = array(),*/ $pageSize = false, $page = false, $filter = array())
    {
        $endpoint = $this->_base_url.'shipments';

        $params = array();
        if($pageSize) $params['per_page'] = $pageSize;
        if($pageSize) $params['page'] = $page;
        array_merge($params, $filter);
        if(count($params))
        {
            $endpoint .= '?'.http_build_query($params);
        }

        //$a = json_decode(json_encode($data), true);
        $r = $this->ApiJSONCall(array(), $endpoint, __FUNCTION__, 'GET');
        $r = shipcloud_helper::cast(array('Shipcloud\ShipmentIndexResponse'), $r);
        return $r;
    }


    /**
     * API calls
     */
    protected function api_carriers()
    {
        $endpoint = $this->_base_url.'carriers';
        $r = $this->ApiJSONCall(array(), $endpoint, __FUNCTION__, 'GET');
        $r = shipcloud_helper::cast(array('Shipcloud\CarrierResponse'), $r);
        return $r;
    }


    protected function ApiJSONCall($data, $endpoint, $error_message = '', $method = "POST")
    {
        global $store_handler;
        $uid = 0;
        if ($this->_log)
        {
            $uid = uniqid();
            $this->_requestLog($uid, 'request', $data, $endpoint, $error_message, $method);
        }

        $ch = curl_init();
        switch ($method)
        {
            case 'POST':
            case 'PATCH':
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, true));
                break;
            case 'DELETE':
                break;
            case 'GET':
                break;
        }
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        //curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Basic ' . base64_encode( $this->_api_key ),
                'Affiliate-ID: '.$this->_affId,
                'User-Agent: xtCommerce '._SYSTEM_VERSION.'/ plugin version '.SHIP_AND_TRACK_PLUGIN_VERSION
            )
        );

        // value should be an integer for the following values of the option parameter: CURLOPT_SSLVERSION
        // CURL_SSLVERSION_TLSv1_2 (6)
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
        // to ensure curls verify functions working in dev:
        // download http://curl.haxx.se/ca/cacert.pem
        // point _CURL_CURLOPT_CAINFO to cacert.pem, eg in config.php
        if (defined('_CURL_CURLOPT_CAINFO') && _CURL_CURLOPT_CAINFO !== '_CURL_CURLOPT_CAINFO')
        {
            curl_setopt($ch, CURLOPT_CAINFO, _CURL_CURLOPT_CAINFO);
        }

        $result = curl_exec($ch);
        if ($this->_log)
        {
            $uid = uniqid();
            $this->_requestLog($uid, 'response', json_decode($result,true), $endpoint, $error_message, $method);
        }

        $msg[] = __FUNCTION__;
        $msg[] = $error_message;
        $r = $this->checkResponse($ch, $result, $msg, $data);

        return $r;
    }

    protected function  checkResponse(&$ch, $result, $msg = array(), $curlInputData = array())
    {
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch))
        {
            $log_data = array();
            $log_data['class'] = 'error_ApiJSONCall';

            $log_data['data'][] = curl_error($ch) . ' - ' . curl_errno($ch);
            $log_data['data'][] = $curlInputData;
            $this->_addSystemLog($log_data);

            throw new Exception(curl_error($ch) . ' - ' . curl_errno($ch));
        }
        curl_close($ch);

        $r = json_decode($result);
        if($r->errors && is_array($r->errors) && count($r->errors))
        {
            foreach($r->errors as $err)
            {
                $msg[] = $err;
            }
            $this->throwException($msg);
        }

        switch($http_status)
        {
            case 0:
                $this->throwException(array_merge($msg, array(
                    'Could not connect. Check internet connection.',
                    $result)));
                break;
            case 200:
            case 204:
                break;
            case 400:
            case 401:
                $this->throwException(array_merge($msg, array(
                    '400 bad request',
                    'Your request was not correct. Please see the response body for more detailed information.',
                    $result)));
                break;
            case 402:
                $this->throwException(array_merge($msg, array(
                    '402 payment required',
                    'You\'ve reached the maximum of your current plan. Please upgrade to a higher plan.')));
                break;
            case 403:
                $this->throwException(array_merge($msg, array(
                    '403 forbidden',
                    'You are not allowed to talk to this endpoint. This can either be due to a wrong authentication or when you\'re trying to reach an endpoint that your account isn\'t allowed to access.')));
                break;
            case 404:
                $this->throwException(array_merge($msg, array(
                    '404 not found',
                    'The api endpoint you were trying to reach can\'t be found.')));
                break;
            case 422:
                $this->throwException(array_merge($msg, array(
                    '422 unprocessable entity',
                    'Your request was well-formed but couldn\'t be followed due to semantic errors. Please see the response body for more detailed information.')));
                break;
            case 500:
                $this->throwException(array_merge($msg, array(
                    '500 internal server error',
                    'Something has seriously gone wrong. Don\'t worry, we\'ll have a look at it.')));
                break;
            case 502:
                $this->throwException(array_merge($msg, array(
                    '502 bad gateway',
                    'Something has gone wrong while talking to the carrier backend. Please see the response body for more detailed information.')));
                break;
            case 504:
                $this->throwException(array_merge($msg, array(
                    '504 gateway timeout',
                    'Unfortunately we couldn\'t connect to the carrier backend. It is either very slow or not reachable at all. If you want to stay informed about the carrier status, follow our developer twitter account at @shipcloud_devs.')));
                break;
            default:
                $this->throwException(array_merge($msg, array(
                "Unexpexted http status code [$http_status]")));
                break;
        }

        return $r;
    }

    protected function throwException($msg)
    {
        if(is_string($msg)) $msg = array($msg);
        throw new Exception(implode('|', $msg));
    }

    function _addCallbackLog($log_data)
    {
        global $db;
        //if (is_null($log_data['transaction_id'])) return false;
        $log_data['module'] = 'xt_paypal_plus';

        $log_data['orders_id'] = isset($this->orders_id) ? $this->orders_id : $this->customer;
        if (is_array($log_data['callback_data']))
        {
            $log_data['callback_data'] = serialize($log_data['callback_data']);
        }
        if (is_array($log_data['error_data']))
        {
            $log_data['error_data'] = serialize($log_data['error_data']);
        }
        $db->AutoExecute(TABLE_CALLBACK_LOG, $log_data, 'INSERT');

        return $db->Insert_ID();
    }

    function _addSystemLog($log_data)
    {
        global $db;

        $log_data['message_source'] = 'shipcloud_api';

        if (is_array($log_data['data']))
        {
            $log_data['data'] = serialize($log_data['data']);
        }
        $db->AutoExecute(TABLE_SYSTEM_LOG, $log_data, 'INSERT');

        return $db->Insert_ID();
    }

    function _requestLog($uid, $ReqOrResp, $data, $endpoint, $error_message, $method)
    {
        $max_size = 20 * 1024;
        if (file_exists($this->_log) && filesize($this->_log)>$max_size && $ReqOrResp == 'request')
        {
            unlink($this->_log);
        }
        $log_data = array(
            'time' => date("Y-m-d H:i:s"),
            'affiliate_id' => $this->_affId,
            'endpoint' => $endpoint,
            'method' => $method,
            'data' => json_encode($data, JSON_PRETTY_PRINT)
        );
        $log_string = '-------  '.$ReqOrResp.' ['.trim($error_message).'] ' . $uid . PHP_EOL . print_r($log_data, true);

        error_log($log_string, 3, $this->_log);
    }
}