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

/**
 * Class for Last viewed Products of Customers on SESSION
 * 
 * */
class last_viewed_products {

    /**
     * get List of Products from $_SESSION['last_viewed_products']
     *
     * @return array
     */
	public function getLastViewedProductListing()
	{
        $module_content = [];

		if(is_array($_SESSION['last_viewed_products']))
		{
			$last_viewed_products_reverse = array_reverse($_SESSION['last_viewed_products']);
			foreach ($last_viewed_products_reverse as $p_id)
			{
				$product = product::getProduct($p_id, 'default');
				if ($product->is_product) {
					$module_content[] = $product->data;
				}			
			}
		}

		return $module_content; 
	}

	/**
	 * add products_id into $_SESSION['last_viewed_products']
	 *
	 * @param int $products_id
	 * @return bool
	 */	
	public function _addLastViewedProduct($products_id)
	{
	    if(empty($products_id)) return false;

        if(is_array($_SESSION['last_viewed_products']))
        {
            //check last viewed products in SESSION < XT_LAST_VIEWED_PRODUCTS_MAX (in xml)
            if(count($_SESSION['last_viewed_products']) < constant('XT_LAST_VIEWED_PRODUCTS_MAX'))
            {
                //check exists in session
                if(in_array($products_id,$_SESSION['last_viewed_products']))
                {
                    $key = array_search($products_id,$_SESSION['last_viewed_products']);
                    unset($_SESSION['last_viewed_products'][$key]);
                    $_SESSION['last_viewed_products'] = array_values($_SESSION['last_viewed_products']);
                }
                $_SESSION['last_viewed_products'][] = $products_id;
            }
            else
            {
                if(in_array($products_id,$_SESSION['last_viewed_products']))
                {
                    $key = array_search($products_id,$_SESSION['last_viewed_products']);
                    unset($_SESSION['last_viewed_products'][$key]);
                    $_SESSION['last_viewed_products'] = array_values($_SESSION['last_viewed_products']);
                }
                $a_pop = array_shift($_SESSION['last_viewed_products']);
                $_SESSION['last_viewed_products'][] = $products_id;
            }
        }
        else
        {
            $_SESSION['last_viewed_products'][] = $products_id;
        }

        return true;
	}

}
