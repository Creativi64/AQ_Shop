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
 * $Id:$
 *
 * (c) 2010 - 2014 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

require_once('magnacompatible.php');

class HitmeisterMarketplace extends MagnaCompatMarketplace {

    # Hitmeister uses EANs to identify products. Don't allow multiple products with the same EAN,
    # as this would make the identification unpredictable and break the synchronisation.

    protected function extraChecks() {
        global $_MagnaSession; 
        // the select field has country_iso as keys
        if (    isset($_POST)
             && isset($_POST['conf'])
             && isset($_POST['conf']['hitmeister.currency.key'])) {
            $aHitmeisterCurrencies = HitmeisterHelper::GetCurrencies();
            $_POST['conf']['hitmeister.currency'] = $aHitmeisterCurrencies[$_POST['conf']['hitmeister.currency.key']];
        }
        if (($hp = magnaContribVerify('HitmeisterExtraChecksReplacement', 1)) !== false) {
            require($hp);
        } else {

            # skip EAN check If switched off by user
            if (getDBConfigValue(array('hitmeister.multipleeans', 'val'), $_MagnaSession['mpID'], false)) {
                return;
            }

            $distinctEANCount = MagnaDB::gi()->fetchOne('SELECT COUNT(DISTINCT products_ean)
				FROM ' . TABLE_PRODUCTS . ' WHERE products_ean <> \'\' AND products_ean IS NOT NULL');
            $totalEANCount = MagnaDB::gi()->fetchOne('SELECT COUNT(*)
				FROM ' . TABLE_PRODUCTS . ' WHERE products_ean <> \'\' AND products_ean IS NOT NULL');
            if ($distinctEANCount != $totalEANCount) {
                $this->resources['query']['mode'] = 'conf';
                $this->resources['query']['messages'][] = '<p class="errorBox">' .
                    ML_HITMEISTER_ERROR_PRODUCTS_WITHDOUBLE_EAN_EXIST . '</p>';
                $dblEANQuery = MagnaDB::gi()->query('
				SELECT products_ean, COUNT(products_ean) as cnt
          		FROM ' . TABLE_PRODUCTS . ' 
         		WHERE products_ean <> \'\' AND products_ean IS NOT NULL
      		GROUP BY products_ean
        		HAVING cnt > 1');
                $dblProdEAN = array();
                while ($row = MagnaDB::gi()->fetchNext($dblEANQuery)) {
                    $dblProdEAN[] = $row['products_ean'];
                }
                $dblEANProducts = MagnaDB::gi()->fetchArray('
				SELECT p.products_id, p.products_model, p.products_ean, pd.products_name
		  		FROM ' . TABLE_PRODUCTS . ' p
	 		LEFT JOIN ' . TABLE_PRODUCTS_DESCRIPTION . ' pd ON p.products_id=pd.products_id AND pd.language_code = \'' .
                    $_SESSION['selected_language'] . '\'
		 		WHERE products_ean IN (\'' . implode('\', \'', $dblProdEAN) . '\')
      		ORDER BY p.products_ean ASC, p.products_model ASC, pd.products_name ASC
			');
                if (!empty($dblEANProducts)) {
                    $traitorTable = '
		    		<table class="datagrid">
		    			<thead><tr>
		    				<th>' . str_replace(' ', '&nbsp;', ML_LABEL_PRODUCT_ID) . '</th>
		    				<th>' . ML_LABEL_ARTICLE_NUMBER . '</th>
		    				<th>' . ML_LABEL_EAN . '</th>
		    				<th>' . ML_LABEL_PRODUCTS_WITH_MULTIPLE_EAN . '</th>
		    			</tr></thead>
		    			<tbody>';
                    $oddEven = true;
                    foreach ($dblEANProducts as $item) {
						$traitorTable .= '
							<tr class="'.(($oddEven = !$oddEven) ? 'odd' : 'even').'">
								<td style="width: 1px;">'.$item['products_id'].'</td>
								<td style="width: 1px;">'.(empty($item['products_model']) ? '<i class="grey">'.ML_LABEL_NOT_SET.'</i>' : $item['products_model']).'</td>
								<td style="width: 1px;"><a href="http://www.hitmeister.de/item/search/?search_value='.$item['products_ean'].'" target="_blank">'.$item['products_ean'].'</a></td>
								<td>'.(empty($item['products_name']) ? '<i class="grey">'.ML_LABEL_UNKNOWN.'</i>' : $item['products_name']).'</td>
							</tr>';
					}
				$traitorTable .= '
						</tbody>
					</table>';
    			$this->resources['query']['messages'][] = $traitorTable;
			}
		}
	  } 
	}

}

new HitmeisterMarketplace($_MagnaSession['currentPlatform']);
