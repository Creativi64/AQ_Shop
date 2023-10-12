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

if(!function_exists('xt_shutdown_function'))
{
    function xt_shutdown_function()
    {
        $error = error_get_last();

        if ( !empty($error) && in_array($error['type'],
            [
                E_ERROR,
                E_RECOVERABLE_ERROR,
                E_PARSE
            ]))
        {

            $error_file = constant('_SRV_WEBROOT').'xtCore/GlobalErrorPage.html';
            $msg = __FUNCTION__.'<br /><pre>'.print_r($error, true).'</pre>';

            if(file_exists($error_file))
            {
                $fp = fopen($error_file,"rb");
                $buffer = fread($fp, filesize($error_file));
                fclose($fp);
                $buffer = str_replace('[ERR_MSG]', $msg, $buffer);
                $buffer = str_replace('[BG_COLOR]', '#f5cbbe', $buffer);
                if(!headers_sent())
                {
                    header($_SERVER["SERVER_PROTOCOL"] . ' 503 Service Temporarily Unavailable');
                    header('Status: 503 Service Temporarily Unavailable', true, 503);
                    header('Retry-After: 300');
                }
                die ($buffer);
            }
            else {
                die($msg);
            }
        }

        global $db;
        if($db && method_exists($db, 'Close'))
        {
            $db->Close();
        }
    }
}
register_shutdown_function('xt_shutdown_function');

if(!function_exists('xt_error_handler'))
{
    function xt_error_handler($errno , $errstr = '', $errfile = '', $errline = 0, $errcontext = [])
    {
            if(in_array($errno, [
                E_ERROR, E_PARSE
            ]))
            {
                $error_file = constant('_SRV_WEBROOT') . 'xtCore/GlobalErrorPage.html';

                $msg_arr = ['errno' => $errno, 'errstr' => $errstr, 'errfile' => $errfile, 'errline' => $errline];
                $msg = __FUNCTION__ . '<br /><pre>' . print_r($msg_arr, true) . '</pre>';

                if (file_exists($error_file))
                {
                    $fp = fopen($error_file, "rb");
                    $buffer = fread($fp, filesize($error_file));
                    fclose($fp);
                    $buffer = str_replace('[ERR_MSG]', $msg, $buffer);
                    $buffer = str_replace('[BG_COLOR]', '#f5cbbe', $buffer);
                    $die_msg = $buffer;
                }
                else
                {
                    $die_msg = $msg;
                }

                global $db;
                if($db && method_exists($db, 'Close'))
                {
                    $db->Close();
                }

                die ($die_msg);
            }

    }
}
set_error_handler('xt_error_handler') ;

