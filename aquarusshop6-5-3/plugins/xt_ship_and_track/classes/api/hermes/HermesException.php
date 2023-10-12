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

defined('_VALID_CALL') or die('Direct Access is not allowed.');


class HermesException extends Exception{

    private $_codes = false;
    private $_msgs = false;

    private $_code = 0;

    public function get_Code()
    {
        return $this->_code;
    }

    function bak__construct($msg, $code, $previous, $msgs = false, $codes = false)
    {
        parent::__construct($msg, $code, $previous);

        if (is_array($msgs))
        {
            $this->_msgs = $msgs;
        }
        if (is_array($codes))
        {
            $this->_codes = $codes;
        }
    }

    function getHermesMessage()
    {
        $msg = $this->getMessage();
        if ($this->_msgs)
        {
            $msg = !empty($msg) ? $msg. ': ' : '';
            $c = count($this->_msgs);
            for ($i =0; $i<$c; $i++)
            {
                $msg .= $this->_codes[$i].'-'.$this->_msgs[$i];
                if ($i<$c-1)
                {
                    $msg .= ' | ';
                }
            }
        }
        return $msg;
    }

    public function __construct($e, $action)
    {
        $this->message = $action;

        if ($e->detail)
        {
            if (is_array($e->detail->ServiceException->exceptionItems->ExceptionItem))
            {
                $this->message = $e->detail->ServiceException->exceptionItems->ExceptionItem[0]->errorMessage;
                $this->_code = $e->detail->ServiceException->exceptionItems->ExceptionItem[0]->errorCode;

                $codes = array();
                $msgs = array();
                foreach($e->detail->ServiceException->exceptionItems->ExceptionItem as $excItem)
                {
                    $msgs[] = $excItem->errorMessage;
                    $codes[] = $excItem->errorCode;
                }
            }
            else{
                $codes = array();
                $msgs = array();
                if(is_array($e->detail->ServiceException->exceptionItems))
                {
                    foreach($e->detail->ServiceException->exceptionItems as $excItem)
                    {
                        $msgs[] = $excItem->errorMessage;
                        $codes[] = $excItem->errorCode;
                    }
                    $this->message = implode(' | ', $msgs);
                    $this->_code = $e->detail->ServiceException->exceptionItems[0]->errorCode;
                }
                else {
                    $this->message = $e->detail->ServiceException->exceptionItems->errorMessage;
                    $this->_code = $e->detail->ServiceException->exceptionItems->errorCode;
                }

            }
        }
        else
        {
            $this->message =  ' | '. $e->getMessage();
        }

    }


}