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

class WizardErrorHandler extends Singleton {

    protected $_outputErrros = DISPLAY_ERRORS;

    protected function __construct() {
        set_error_handler(array($this, 'handle'));
        register_shutdown_function(array($this, 'shutdown_function'));//
        set_exception_handler(array($this, 'handleException'));
        parent::__construct();
    }

    /**
     * Set if the errors should be outputed to the browser or logged to file
     * @param boolean $bool
     * @return WizardErrorHandler
     */
    public function setOutputErros($bool) {
        $this->_outputErrros = (bool)$bool;
        return $this;
    }

    public function shutdown_function()
    {
        $error = error_get_last();

        if ( !empty($error) && in_array($error['type'],
            [
                E_ERROR,
                E_RECOVERABLE_ERROR,
                E_PARSE
            ]))
        {

            $time = date('Y-m-d h:i:s');

            WizardLogger::getInstance()->log("[$time] ".print_r($error, true));

            $r = [
                "HasNext" => true,
                "Offset" => -1,
                "CurrentMessage" => "[$time][<strong class=\"msg-error font-weight-bold\">ERROR (shutdown)</strong>] ".$error['message']. "<br/>",
                "LogMessages" => "[$time][<strong class=\"msg-error font-weight-bold\">ERROR (shutdown)</strong>] ".$error['message']. "<br/><pre>".print_r($error,true)."</pre>",
                "Progress" => '',
                "ExtraParams" =>  [],
                "NextUrl" =>  "",
                "hasError" => true

            ];
            echo json_encode($r);
            die();
        }
    }

    public function handleException($ex)
    {
        $time = date('Y-m-d h:i:s');

        WizardLogger::getInstance()->log("[$time] ".print_r($ex, true));

        $r = [
            "HasNext" => true,
            "Offset" => -1,
            "CurrentMessage" => "[$time][<strong class=\"msg-error font-weight-bold\">ERROR (exception)</strong>] ".$ex->getMessage(). "<br/>",
            "LogMessages" => "[$time][<strong class=\"msg-error font-weight-bold\">ERROR (exception)</strong>] ".$ex->getMessage(). "<br/><pre>".$ex->getTraceAsString()."</pre>",
            "Progress" => '',
            "ExtraParams" =>  [],
            "NextUrl" =>  "",
            "hasError" => true

        ];
        echo json_encode($r);
        die();

    }

    /**
     * Error handling function
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     * @param array $errcontext
     * @return void|boolean
     */
    public function handle($errno, $errstr, $errfile, $errline, $errcontext = []) {
        if (!(error_reporting() & $errno)) {
            // This error code is not included in error_reporting
            return;
        }

        $time = date('Y-m-d h:i:s');
        $stop = false;
        switch ($errno) {
            case E_USER_ERROR:
            case E_ERROR:
                $msg = "[$time] Error [$errno] $errstr. Fatal error on line $errline in file $errfile\n";
                $stop = true;
                break;
            case E_USER_WARNING:
            case E_WARNING:
                $msg = "[$time] WARNING [$errno] $errstr. Error ocurred in $errfile on line $errline\n";
                break;

            case E_USER_NOTICE:
            case E_NOTICE:
                $msg = "[$time] NOTICE [$errno] $errstr. Error ocurred in $errfile on line $errline\n";
                break;

            default:
                $msg = "[$time] Error [$errno] $errstr. Error ocurred in $errfile on line $errline\n";
                break;
        }

        if ($this->_outputErrros) {
            echo $msg;
        } else {
            WizardLogger::getInstance()->log($msg);
        }

        if ($stop) {
            //@TODO Stop the wizard
            exit(1);
        }

        /* Don't execute PHP internal error handler */
        return true;
    }

    /**
     * @return WizardErrorHandler
     */
    public static function getInstance() {
        return parent::getInstance();
    }
}