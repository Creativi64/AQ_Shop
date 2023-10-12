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

// defined('_VALID_CALL') or die('Direct Access is not allowed.');

function __debug ($data, $text='', $file_suffix = '') {
	if (defined ('__DEBUG_OUT_FILE') && !empty(__DEBUG_OUT_FILE)) {

	    if(!empty($file_suffix))
        {
            $file_suffix .= '_';
        }

        $file_name = _SRV_WEBROOT.'xtLogs/'.$file_suffix. __DEBUG_OUT_FILE;

        if (defined('__DEBUG_IP') && !empty(__DEBUG_IP))
        {
            $acl = new acl();
            $ip = $acl->getCurrentIp();
            if ($ip != __DEBUG_IP)
            {
                return;
            }
            $file_name = _SRV_WEBROOT.'xtLogs/'.$file_suffix. preg_replace('/[^\w]/','_',__DEBUG_IP) . __DEBUG_OUT_FILE;
        }
        $mode = 'a+';
        if(file_exists($file_name) && filesize($file_name)>20971520) // 20MB
        {
            $mode = 'w+';
        }
        $f = fopen($file_name, $mode);
        if(is_resource($f))
        {
            $date = date("Y-m-d h:i:s");
            $text = trim($date.' '.$text);
            if (!empty($text))
            {
			fwrite ($f, " ----- $text ------ \n");
		}
		if($data) fwrite ($f, var_export ($data, true));
		fwrite ($f, "\n\n");
		fclose ($f);
        }
		return;
	}

    if (defined('__DEBUG_IP') && !empty(__DEBUG_IP))
    {
        $acl = new acl();
        $ip = $acl->getCurrentIp();
        if ($ip != __DEBUG_IP)
        {
            return;
        }
    }

	echo '<br />';
	echo '<br />';
	if(!empty($text))
	echo '<b>'.$text.':</b><br />';

	echo '<pre>';
	if ($data) print_r($data);
	echo '</pre>';
	echo '<br />';
	echo '<HR SIZE=3>';
	echo '<br />';
}
