<?php
/**
 * 888888ba                 dP  .88888.                    dP                
 * 88    `8b                88 d8'   `88                   88                
 * 88aaaa8P' .d8888b. .d888b88 88        .d8888b. .d8888b. 88  .dP  .d8888b. 
 * 88   `8b. 88ooood8 88'  `88 88   YP88 88ooood8 88'  `"" 88888"   88'  `88 
 * 88     88 88.  ... 88.  .88 Y8.   .88 88.  ... 88.  ... 88  `8b. 88.  .88 
 * dP     dP `88888P' `88888P8  `88888'  `88888P' `88888P' dP   `YP `88888P' 
 *
 *                          m a g n a l i s t e r
 *                                      boost your Online-Shop
 *
 * -----------------------------------------------------------------------------
 * $Id$
 *
 * (c) 2010 - 2013 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

class MLShopHelper {
	
	static public function getVariationGroup() {
		if (   (defined('TABLE_PRODUCTS_ATTRIBUTES_DESCRIPTION') && defined('TABLE_PRODUCTS_ATTRIBUTES'))
			&& (MagnaDB::gi()->tableExists(TABLE_PRODUCTS_ATTRIBUTES_DESCRIPTION) && MagnaDB::gi()->tableExists(TABLE_PRODUCTS_ATTRIBUTES))
		) {
			$aAttributes = MagnaDB::gi()->fetchArray('
			    SELECT a.`attributes_id` as `id`,
			           ad.`attributes_name` as `name`
			      FROM '.TABLE_PRODUCTS_ATTRIBUTES_DESCRIPTION.' ad
			INNER JOIN '.TABLE_PRODUCTS_ATTRIBUTES.' a ON a.attributes_id = ad.attributes_id
			     WHERE     a.attributes_parent = 0
			           AND ad.language_code = \''.$_SESSION['magna']['selected_language'].'\'
			');

			return $aAttributes;
		}

		return array();
	}

}
