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

use Jaybizzle\CrawlerDetect\CrawlerDetect;

class agent_check{
    
    var $browser_list = array('Boxely', 'Gecko', 'GtkHTML', 'HTMLayout', 'KHTML', 'NetFront', 'NetSurf', 'Presto', 'Prince XML','Robin','Tasman','Trident','Tkhtml','WebKit','Blink','XEP','Hubbub','iCab');
    
	function isBot() {

        if($this->isBrowser()) return false;

		$bot_ID = strtolower($_SERVER['HTTP_USER_AGENT']);
		$bot_ID2 = strtolower(getenv("HTTP_USER_AGENT"));

        $cd = new CrawlerDetect();

        if($cd->isCrawler($bot_ID) || $cd->isCrawler($bot_ID2)) {
            return true;
            }

		return false;
	}
    
    function isBrowser(){
        $bot_ID = strtolower($_SERVER['HTTP_USER_AGENT']);
        $bot_ID2 = strtolower(getenv("HTTP_USER_AGENT"));
        
        foreach ($this->browser_list as $key => $val) {

			if (strstr($bot_ID, strtolower($val)) or strstr($bot_ID2, strtolower($val))) {
                return true;
            } 
		}
        
        return false;
    }
}