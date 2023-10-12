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

$curlVersion = curl_version();
$curlSslBackend = $curlVersion['ssl_version'];

$r = array(
    'SSL_LIB' => $curlSslBackend,
    'SSL_VERSION' => CURL_SSLVERSION_TLSv1_2
);

if (XT_PAYPAL_SSL_VERSION == 'autodetect')
{

    if (substr_compare($curlSslBackend, "NSS/", 0, strlen("NSS/")) === 0)
    {
        $r['CIPHER_LIST'] = "";
    }
    else
    {
        $r['CIPHER_LIST'] = "TLSv1";
    }
    return $r;
}
else
{
    return array(
        'SSL_LIB' => $curlSslBackend,
        'SSL_VERSION' => XT_PAYPAL_SSL_VERSION,
        'CIPHER_LIST' => (XT_PAYPAL_CIPHER_LIST == 'autodetect') ? '' : XT_PAYPAL_CIPHER_LIST,
    );
}
