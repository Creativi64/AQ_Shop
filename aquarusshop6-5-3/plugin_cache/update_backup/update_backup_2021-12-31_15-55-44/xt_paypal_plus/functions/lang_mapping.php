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

function Plus_lang_mapping($lang = false)
{
    global $language;
    if(!empty($language->setlocale))
    {
        $lcls = explode(';',$language->setlocale);
        if(count($lcls))
        {
            return $lcls[0];
        }
    }

    return 'en_US';
}

function Plus_buildErrorMessage($result)
{
    if (is_object($result))
    {
        if (!empty($result->name) && !empty($result->message))
        {
            return $result->message . ' (' . $result->name . ')';
        }
        else
        {
            if (!empty($result->name))
            {
                return $result->name;
            }
            else
            {
                if (!empty($result->message))
                {
                    return $result->message;
                }
            }
        }
    }
    else if (is_string($result))
    {

        $dom = new DOMDocument;$dom->loadHTML($result);
        $bodies = $dom->getElementsByTagName('body');
        $body = $bodies->item(0);
        return nl2br($body->textContent);
    }
    return 'Unknown error.'; //serialize((array)$result);

}
