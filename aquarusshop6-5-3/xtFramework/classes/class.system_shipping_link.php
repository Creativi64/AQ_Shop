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


class system_shipping_link {

    /**
     * @deprecated use system_shipping_link::getShippingLink() instead
     * @var string
     */
	var $shipping_link;
	
	protected static $_shipping_link = null;

    public static function getShippingLink()
    {
        if(is_null(self::$_shipping_link))
            self::$_shipping_link = self::_getShippingLink();
        return self::$_shipping_link;
        }

    /**
     * @deprecated use system_shipping_link::getShippingLink() instead
     * system_shipping_link constructor
     */
	function __construct()
    {
		$this->shipping_link = self::_getShippingLink();
    }
	
	protected static function _getShippingLink()
    {
		global $_content,$xtLink;

        if(empty($_content)) $_content = new content();

        $shipping_link = '';
		$shipping = $_content->getHookContent('1');
		if (isset($shipping['content_id'])) {
			$shipping_content_data =  $_content->getHookContent($shipping['content_id'], 'true');
			$shipping_link = $xtLink->_link(array('page'=>'content', 'params'=>'coID='.$shipping_content_data['content_id'],'seo_url' => $shipping_content_data['url_text']));
		}
        return $shipping_link;
	}
}
