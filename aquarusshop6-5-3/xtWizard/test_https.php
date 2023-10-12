<?php
/*
 #########################################################################
 #                       xt:Commerce Shopsoftware
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # Copyright 2021 xt:Commerce GmbH All Rights Reserved.
 # This file may not be redistributed in whole or significant part.
 # Content of this file is Protected By International Copyright Laws.
 #
 # ~~~~~~ xt:Commerce Shopsoftware IS NOT FREE SOFTWARE ~~~~~~~
 #
 # https://www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # @copyright xt:Commerce GmbH, www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # xt:Commerce GmbH, Maximilianstrasse 9, 6020 Innsbruck
 #
 # office@xt-commerce.com
 #
 #########################################################################
 */

$r = checkHttpsAvailable(443);
echo '<pre>'.print_r($r,true).'</pre>';


function checkHTTPS()
{
    $isSecure = false;
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
        $isSecure = true;
    }
    elseif($_SERVER['SERVER_PORT'] == '443' || $_SERVER['SERVER_PORT'] == '8443'){
        $isSecure = true;
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
        $isSecure = true;
    }
    return $isSecure;
}

/**
 * @return int 0 - not available, 1 - available but http, 2 - available and called over https
 */
function checkHttpsAvailable($port = 443)
{
    $isSecure = checkHTTPS();
    $result = ['httpsAvailable' => 0, 'httpsUrl' => ''];

    $url = _SRV_WEB . 'xtWizard/test_https.html';
    $host = $_SERVER['HTTP_HOST'];

    if($isSecure)
    {
        $httpsUrl = 'https://'.$_SERVER['HTTP_HOST'];
        $httpsUrl .= '/xtWizard/index.php';
        $result = ['httpsAvailable' => 2, 'httpsUrl' => $httpsUrl];
    }
    else
    {
        $connection_timeout = 2;
        $data_read_timeout = 2;

        $context = stream_context_create();

        $schema = 'ssl://';
        stream_context_set_option($context, 'ssl', 'verify_peer', false); // fix ssl certificate verify issues, eg mammp on osx

        $stream_source = $schema . $_SERVER['HTTP_HOST'];
        if (strpos(':', $stream_source) == false)
        {
            $stream_source .= ':' . $port;
        }

        $remote = stream_socket_client($stream_source,
            $errno, $errstr, $connection_timeout, STREAM_CLIENT_CONNECT, $context);

        if (is_resource($remote))
        {
            $response = '';

            if (stream_set_timeout($remote, $data_read_timeout))
            {


                $headers[] = "GET " . $url . " HTTP/1.1";
                $headers[] = "Host: " . $host;
                $headers[] = "Connection: close";

                fputs($remote, join("\r\n", $headers) . "\r\n\r\n");


                while (!feof($remote))
                {
                    $response .= fread($remote, 1024);
                }

                $stream_info = stream_get_meta_data($remote);

                if ($stream_info['timed_out'])
                {
                    echo 'checkHttpsAvailable: Data connection timed out '. $stream_source;
                }
            }
            else
            {
                echo 'checkHttpsAvailable: Could not set timeout for socket' . $stream_source;

            }

            fclose($remote);

            if (strpos($response, 'test_https_ok') != false)
            {
                $httpsUrl = 'https://'.$_SERVER['HTTP_HOST'];
                $httpsUrl .= '/xtWizard/index.php';
                $result = ['httpsAvailable' => 1, 'httpsUrl' => $httpsUrl];
            }
        }
        else
        {
            echo 'checkHttpsAvailable: Could not create socket '.$stream_source .$errno. '  '. $errstr.' '. $stream_source;
        }
    }

    return $result;
}
