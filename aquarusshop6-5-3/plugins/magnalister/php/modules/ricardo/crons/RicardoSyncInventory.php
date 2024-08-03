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
 * (c) 2010 - 2014 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

require_once(DIR_MAGNALISTER_MODULES.'magnacompatible/crons/MagnaCompatibleSyncInventory.php');

class RicardoSyncInventory extends MagnaCompatibleSyncInventory {
	protected function updateQuantity() {
		if (!$this->syncStock) return false;

		$data = false;
		if (($hp = magnaContribVerify('SyncInventory_updateQuantity_setNewQuantity', 1)) !== false) {
			require($hp);
		} else {
			$curQty = $this->calcNewQuantity();
		}

		if (!isset($this->cItem['Quantity'])) {
			$this->cItem['Quantity'] = 0;
		}

		if (isset($this->cItem['Quantity']) && (int)$curQty != $this->cItem['Quantity']) {
			$data = array (
				'Mode' => 'SET',
				'Value' => (int)$curQty,
				'IncreaseQuantity' => false
			);
			
			if ((int)$curQty > (int)$this->cItem['Quantity'] && $this->config['StockSync'] === 'auto_reduce') {
				$data['IncreaseQuantity'] = true;
			}
			
			$this->log("\n\t".
				'Quantity changed (old: '.$this->cItem['Quantity'].'; new: '.$curQty.')'
			);

		} else {
			$this->log("\n\t".
				'Quantity not changed ('.$curQty.')'
			);
		}
		return $data;
	}

	protected function updatePrice() {
		if (!$this->syncPrice) return false;

		$data = false;
		
		$productTax = SimplePrice::getTaxByPID($this->cItem['pID']);
		$taxFromConfig = getDBConfigValue($this->marketplace . '.checkin.mwst', $this->mpID);
		$priceSignal = getDBConfigValue($this->marketplace . '.price.signal', $this->mpID);

		$this->simplePrice->setFinalPriceFromDB($this->cItem['pID'], $this->mpID);
		if (isset($taxFromConfig) && $taxFromConfig !== '') {
			$this->simplePrice
				->removeTax($productTax)
				->addTax($taxFromConfig)
				->makeSignalPrice($priceSignal);
		}

		$price = $this->simplePrice
				->roundPrice()
				->getPrice();

        //Check if last digit (second decimal) is 0 or 5. If not set 5 as default last digit
        $price =
            ((int)($price * 100) % 5) == 0
                ? $price
                : ((int)($price * 10) / 10) + 0.05
        ;

		$price = round($price, 2);
		if ($price > 0 && (float)$price != (float)$this->cItem['Price']) {
			$this->log("\n\t".
				'Price changed (old: '.$this->cItem['Price'].'; new: '.$price.')'
			);
			$data = $price;
		} else {
			$this->log("\n\t".
				'Price not changed ('.$price.')'
			);
		}
		return $data;
	}
	
	protected function updateItem() {
		$this->identifySKU();
		
		$sTitle = isset($this->cItem['Title'])
			? $this->cItem['Title']
			: (isset($this->cItem['ItemTitle'])
				? $this->cItem['ItemTitle']
				: 'unknown'
			);
		
		/* {Hook} "SyncInventory_PreUpdateItem": Runs at the beginning of an item synchronization. Here you can try to fix the identification of
			   the item to make sure it gets processed in case the SKU can not be found in the first try.<br>
			   Variables that can be used: 
			   <ul><li>$this->mpID: The ID of the marketplace.</li>
			       <li>$this->marketplace: The name of the marketplace.</li>
			       <li>$this->cItem (array): The current product from the marketplaces inventory including some identification information.
			           <ul><li>SKU: Article number of marketplace</li>
			               <li>pID: products_id of product</li></ul>
			       </li>
			   </ul>
			   <p>Notice: In case the identification of the item was successfull the value of $this->cItem['pID'] is > 0.</p>
		*/
		if (($hp = magnaContribVerify('SyncInventory_PreUpdateItem', 1)) !== false) {
			require($hp);
		}
		
		if ((int)$this->cItem['pID'] <= 0) {
			$this->log("\n".
				'SKU: '.$this->cItem['SKU'].' ('.$sTitle.') not found'
			);
			return;
		} else {
			$this->log("\n".
				'SKU: '.$this->cItem['SKU'].' ('.$sTitle.') found ('.
				'pID: '.$this->cItem['pID'].')');
		}
		
		$data = array();
		
		$qU = $this->updateQuantity();
		if ($qU !== false) {
			$data['NewQuantity'] = $qU;
		}
		
		$pU = $this->updatePrice();
		if ($pU !== false) {
            $data['Price'] = $pU;
            $data['IncreasePrice'] = false;
            if ($pU > $this->cItem['Price'] && $this->config['PriceSync'] === 'auto_reduce') {
                $data['IncreasePrice'] = true;
            }
        }
		
		$this->updateCustomFields($data);
		
		$this->addToBatch($data);
	}
	
	protected function isAutoSyncEnabled() {
		$this->syncStock = ($this->config['StockSync'] == 'auto') || ($this->config['StockSync'] == 'auto_reduce') || ($this->config['StockSync'] == 'auto_fast');
		$this->syncPrice = ($this->config['PriceSync'] == 'auto') || ($this->config['PriceSync'] == 'auto_reduce');
		
		//$this->syncStock = $this->syncPrice = true;

		if (!($this->syncStock || $this->syncPrice)) {
			$this->log('== '.$this->marketplace.' ('.$this->mpID.'): no autosync =='."\n");
			return false;
		}
		$this->log(
			'== '.$this->marketplace.' ('.$this->mpID.'): '.
			'Sync stock: '.($this->syncStock ? 'true' : 'false').'; '.
			'Sync price: '.($this->syncPrice ? 'true' : 'false')." ==\n"
		);
		return true;
	}
}
