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

closeConnection('ok', 200);

if(!empty($_REQUEST['cron_id']) && !empty($_GET['seckey']))
{
    // hoffentlich FastCGI Process Manager (FPM)
    if (is_callable('fastcgi_finish_request'))
    {
        session_write_close();
        fastcgi_finish_request();
    }

    include 'xtCore/main.php';

    if (_SYSTEM_SECURITY_KEY == $_GET['seckey'])
    {
        $cron_id = (int)$_REQUEST['cron_id'];

        $xt_cron = new xt_cron();
        $xt_cron->cron_start_by_id($cron_id, true);
    }
}

function closeConnection($body, $responseCode)
{
    // Set to zero so no time limit is imposed from here on out.
    set_time_limit(0);

    // Client disconnect should NOT abort our script execution
    ignore_user_abort(true);

    // Clean (erase) the output buffer and turn off output buffering
    // in case there was anything up in there to begin with.
    ob_end_clean();

    // Turn on output buffering, because ... we just turned it off ...
    // if it was on.
    ob_start();

    echo $body;

    // Return the length of the output buffer
    $size = ob_get_length();

    // send headers to tell the browser to close the connection
    // remember, the headers must be called prior to any actual
    // input being sent via our flush(es) below.
    header("Connection: close\r\n");
    header("Content-Encoding: none\r\n");
    header("Content-Length: $size");

    // Set the HTTP response code
    // this is only available in PHP 5.4.0 or greater
    http_response_code($responseCode);

    // Flush (send) the output buffer and turn off output buffering
    ob_end_flush();

    // Flush (send) the output buffer
    // This looks like overkill, but trust me. I know, you really don't need this
    // unless you do need it, in which case, you will be glad you had it!
    @ob_flush();

    // Flush system output buffer
    // I know, more over kill looking stuff, but this
    // Flushes the system write buffers of PHP and whatever backend PHP is using
    // (CGI, a web server, etc). This attempts to push current output all the way
    // to the browser with a few caveats.
    flush();
}


