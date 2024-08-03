<?php
/*
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
 * (c) 2010 - 2023 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

require_once(DIR_MAGNALISTER_MODULES.'magnacompatible/crons/MagnaCompatibleCronBase.php');

abstract class MagnaCompatibleImportOrders extends MagnaCompatibleCronBase {
	protected $hasNext = true;
	protected $offset = array();
	protected $beginImportDate = false;

	/** @var MagnaDB $db */
	protected $db = null;
	protected $dbCharSet = '';

	/** @var SimplePrice $simplePrice */
	protected $simplePrice = null;
	
	/* specific to one order only */
	protected $cur = array();
	protected $o = array(); /* the current order */
	protected $p = array(); /* the current product */
	protected $addressIDs = array ( /* the customers addessIDs */ 
		'default' => 0,
		'shipping' => 0,
		'payment' => 0,
	);
	protected $taxValues = array(); /* tax values for the current order */
	protected $tax2classID = array();
	protected $sumStats = 0;
	
	protected $mailOrderSummary = array();
	protected $comment = '';
	
	/* specific to all orders */
	protected $syncBatch = array(); /* sync batch for other marketplaces */
	protected $allCurrencies = array(); /* list of different currencies */
	
	/* For acknowledging */
	protected $processedOrders = array ();
	protected $unprocessedComments = array();
	protected $lastOrderDate = false;

	/*to prevent getOrders from repeating itself*/
	private $getOrdersResultMd5 = '';
	
	protected $verbose = false;

	public function __construct($mpID, $marketplace) {
		parent::__construct($mpID, $marketplace);

		$this->initImport();
	}
	
	protected function initImport() {
		if (isset($_GET['MLDEBUG']) && ($_GET['MLDEBUG'] == 'true')) {
			require_once(DIR_MAGNALISTER_INCLUDES . 'lib/MagnaTestDB.php');
			$this->db = MagnaTestDB::gi();
		} else {
			$this->db = MagnaDB::gi();
		}

		$this->dbCharSet = MagnaDB::gi()->mysqlVariableValue('character_set_client');
		if (('utf8mb3' == $this->dbCharSet) || ('utf8mb4' == $this->dbCharSet)) {
			# means the same for us
			$this->dbCharSet = 'utf8';
		}
		$this->verbose = (
				(defined('MAGNA_CALLBACK_MODE') && MAGNA_CALLBACK_MODE == 'STANDALONE')
				|| (defined('MAGNALISTER_PLUGIN') && (MAGNALISTER_PLUGIN == true))
			) && (get_class($this->db) == 'MagnaTestDB');
		
		require_once(DIR_MAGNALISTER_INCLUDES.'lib/classes/SimplePrice.php');
		$this->simplePrice = new SimplePrice();
		$this->getOrdersResultMd5 = '';

		if (!class_exists('order')) {
			if (file_exists(_SRV_SERVER_ROOT.'xtFramework/classes/class.order.php')) {
				require_once(_SRV_SERVER_ROOT.'xtFramework/classes/class.order.php');
			}
		}
	}
	
	protected function getConfigKeys() {
		return array (
			'ShopMandant' => array (
				'key' => 'shopmandant',
				'default' => 1,
			),
			'UpdateExchangeRate' => array (
				'key' => array('exchangerate', 'update'),
				'default' => false,
			),
			'LastImport' => array (
				'key' => 'orderimport.lastrun',
				'default' => 0,
			),
			'FirstImportDate' => array (
				'key' => 'preimport.start',
				'default' => '1970-01-01',
			),
			'CustomerGroup' => array (
				'key' => 'CustomerGroup',
				'default' => 1
			),
			'MwStFallback' => array (
				'key' => 'mwst.fallback',
				'default' => 0
			),
			'MwStShipping' => array (
				'key' => 'mwst.shipping',
				'default' => 0
			),
			'StockSync.FromMarketplace' => array (
				'key' => 'stocksync.frommarketplace',
				'default' => 'no'
			),
			'MailSend' => array (
				'key' => 'mail.send',
				'default' => 'false',
			),
			'ShippingMethod' => array (
				'key' => 'orderimport.shippingmethod',
				'default' => 'textfield',
			),
			'ShippingMethodName' => array (
				'key' => 'orderimport.shippingmethod.name',
				'default' => 'marketplace',
			),
			'PaymentMethod' => array (
				'key' => 'orderimport.paymentmethod',
				'default' => 'textfield',
			),
			'PaymentMethodName' => array (
				'key' => 'orderimport.paymentmethod.name',
				'default' => 'marketplace',
			),
			'OrderStatusOpen' => array (
				'key' => 'orderstatus.open',
				'default' => '',
			),
		);
	}
	
	protected function initConfig() {
		$this->config['CIDAssignment'] = getDBConfigValue('customers_cid.assignment', '0', 'none');

		parent::initConfig();
		#echo print_m($this->config);
		if (  ($this->config['ShippingMethod'] == 'textfield')
		    ||($this->config['ShippingMethod'] == '__ml_lump')) {
			$this->config['ShippingMethod'] = trim($this->config['ShippingMethodName']);
		}
		if (empty($this->config['ShippingMethod'])) {
			$k = $this->getConfigKeys();
			$this->config['ShippingMethod'] = $k['ShippingMethodName']['default'];
		}
		if ($this->config['PaymentMethod'] == 'textfield') {
			$this->config['PaymentMethod'] = trim($this->config['PaymentMethodName']);
		}
		if (empty($this->config['PaymentMethod'])) {
			$k = $this->getConfigKeys();
			$this->config['PaymentMethod'] = $k['PaymentMethodName']['default'];
		}

		# Get the mandants store country
		$this->config['StoreCountry'] = strtolower(MagnaDB::gi()->fetchOne('
			SELECT config_value
			  FROM '.TABLE_CONFIGURATION.'_'.$this->config['ShopMandant'].'
			 WHERE config_key = "_STORE_COUNTRY"'));

		//Bugfix for floats as array keys
		$this->config['MwStShipping'] = (string)round($this->config['MwStShipping'], 2);

		#echo var_dump_pre($this->config['PaymentMethod'], 'PaymentMethod');
		#echo var_dump_pre($this->config['ShippingMethod'], 'ShippingMethod');
		
		# Import order with or without tax (for merchants for example)
		$this->config['AllowTax'] = ((int)MagnaDB::gi()->fetchOne('
			SELECT count(*)
			  FROM '.TABLE_CUSTOMERS_STATUS.'
			 WHERE customers_status_id = '.$this->config['CustomerGroup'].' 
			       AND customers_status_show_price_tax = 0
				   AND customers_status_add_tax_ot = 0
		') == 0);
		
		$this->config['Table_OrdersStatus_HasCollumn_ShowComment'] = MagnaDB::gi()->columnExistsInTable('customer_show_comment', TABLE_ORDERS_STATUS_HISTORY);
		
		# Get the mandants store language
		if (strlen($this->config['ShopMandant']) > 0) {
			$this->config['StoreLanguage'] = strtolower(MagnaDB::gi()->fetchOne('
				SELECT config_value
				  FROM '.TABLE_CONFIGURATION.'_'.$this->config['ShopMandant'].'
				 WHERE config_key = "_STORE_LANGUAGE"
			'));
		} else {
			$this->config['StoreLanguage'] = MagnaDB::gi()->fetchOne('
				SELECT code FROM '.TABLE_LANGUAGES.' LIMIT 1');
		}
		
	}

	/**
	 * How many hours, days, weeks or whatever we go back in time to request older orders?
	 * @return int - time in seconds
	 */ 
	protected function getPastTimeOffset() {
		return 60 * 60 * 24 * 7;
	}

	protected function getBeginDate() {
		global $_modules;
		if ($this->beginImportDate !== false) {
			return $this->beginImportDate;
		}
		$begin = strtotime($this->config['FirstImportDate']);
        if ($begin <= strtotime('1970-01-01 00:00:00')) {
			# not configured. Check if this is a required key for the platform.
			# If so, return false, which stops the import.
			if (in_array($this->marketplace.'.preimport.start', $_modules[$this->marketplace]['requiredConfigKeys'])) {
				return false;
			}
		}
		if ($begin > time()) {
			if ($this->verbose) echo "Date in the future --> no import\n";
			return false;
		}

		$dateRegexp = '/^([1-2][0-9]{3})-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])'.
			'(\s([0-1][0-9]|2[0-4]):([0-5][0-9]):([0-5][0-9]))?$/';

		$lastImport = $this->config['LastImport'];
		if (preg_match($dateRegexp, $lastImport)) {
			# Since we only request non acknowledged orders, we go back in time by 7 days.
			$lastImport = strtotime($lastImport.' +0000') - $this->getPastTimeOffset();
		} else {
			$lastImport = 0;
		}
	
		if ( ($lastImport > 0) && ($begin < $lastImport) ) {
			$begin = $lastImport;
		}
		
		if (isset($_GET['ForceBeginImportDate']) && preg_match($dateRegexp, $_GET['ForceBeginImportDate'])) {
			$begin = strtotime($_GET['ForceBeginImportDate']);
		}
		
		return $this->beginImportDate = gmdate('Y-m-d H:i:s', $begin);
	}

	protected function buildRequest() {
		if (empty($this->offset)) {
			$this->offset = array (
				'COUNT' => 32,
				'START' => 0,
			);
		}
		return array (
			'ACTION' => 'GetOrdersForDateRange',
			'SUBSYSTEM' => $this->marketplace,
			'MARKETPLACEID' => $this->mpID,
			'BEGIN' => $this->getBeginDate(),
			'OFFSET' => $this->offset,
		);
	}

	protected function getOrders() {
		if ($this->hasNext != true) {
			return false;
		}
		$request = $this->buildRequest();
		if ($this->verbose) {
			echo print_m($request, '$request');
		}
		if ($request['BEGIN'] === false) {
			echo "No BEGIN Date has been set, so no import yet.\n";
			return false;
		}
		try {
			$res = MagnaConnector::gi()->submitRequest($request);
		} catch (MagnaException $e) {
			if ((defined('MAGNA_CALLBACK_MODE') && MAGNA_CALLBACK_MODE == 'STANDALONE') || $this->verbose) {
				echo print_m($e->getErrorArray(), 'Error: '.$e->getMessage());
			}
			if (MAGNA_DEBUG && ($e->getMessage() == ML_INTERNAL_API_TIMEOUT)) {
				$e->setCriticalStatus(false);
			}
			return false;
		}
		/* prevent getOrders from repeating itself*/
		$getOrdersResultMd5 = md5(serialize($res));
		if ($getOrdersResultMd5 == $this->getOrdersResultMd5) {
			if ($this->verbose) {
				echo "getOrdersResultMd5 doubled: $getOrdersResultMd5\n";
			}
			$this->hasNext = false;
			$res = array();
		}
		if (!array_key_exists('DATA', $res) || empty($res['DATA'])) {
			$this->out($this->marketplace.' ('.$this->mpID.") order import: No data.\n");
			return false;
		}
		$this->getOrdersResultMd5 = $getOrdersResultMd5;
		$this->hasNext = $res['HASNEXT'];
		$this->offset['START'] += $this->offset['COUNT'];
		
		$orders = $res['DATA'];
		$res['DATA'] = 'Cleaned';
		
		if ($this->verbose) echo print_m($res, '$res');

		if (!is_array($orders)) return false;

		# ggf. Zeichensatz korrigieren
		if ($this->dbCharSet != 'utf8') {
			arrayEntitiesToLatin1($orders);
		}
		
		return $orders;
	}
	
	protected function updateOrderCurrency($currency) {
		# Gibts die Waehrung auch im Shop?
		if (!$this->simplePrice->currencyExists($currency)) {
			if ($this->verbose) echo "Currency [".$currency."] does not exist.\n";
			return false;
		}
		#if ($this->verbose) echo 'Set Currency to: ['.$currency."]\n";
		$this->simplePrice->setCurrency($currency);

		if (array_key_exists($currency, $this->allCurrencies)) {
			return true;
		}

		if ($this->config['UpdateExchangeRate']) {
			$this->simplePrice->updateCurrencyByService();
		}

		$currencyValue = $this->simplePrice->getCurrencyValue();
		if ((float)$currencyValue <= 0.0) {
			if ($this->verbose) echo "CurrencyValue <= 0.\n";
			return false;
		}
		$this->allCurrencies[$currency] = $currencyValue;
		return true;
	}

	protected function getCountryName($code) {
		$code = strtoupper($code);
		$name = MagnaDB::gi()->fetchOne('
			SELECT countries_name FROM '.TABLE_COUNTRIES_DESCRIPTION.'
			 WHERE language_code=\''._STORE_LANGUAGE.'\' AND countries_iso_code_2=\''.$code.'\'
			 LIMIT 1
		');
		if ($name == false) {
			$name = MagnaDB::gi()->fetchOne('
				SELECT countries_name FROM '.TABLE_COUNTRIES_DESCRIPTION.'
				 WHERE countries_iso_code_2=\''.$code.'\'
				 LIMIT 1
			');
		}
		if ($name == false) {
			$name = $code;
		}
		return $name;
	}

	protected function getCustomer($email) {
		$fields = array('customers_id as ID');
		if ($this->config['CIDAssignment'] != 'none') {
			$fields[] = 'customers_cid as CID';
		}
		$c = MagnaDB::gi()->fetchRow('
		    SELECT '.implode(',', $fields).'
		      FROM '.TABLE_CUSTOMERS.' 
	 	     WHERE customers_email_address=\''.$email.'\' 
	 	     LIMIT 1
		');
		if (!is_array($c)) {
			return false;
		}
		return $c;
	}

	protected function addAddess($addr) {
        $qE = MagnaDB::gi()->recordExists(TABLE_CUSTOMERS_ADDRESSES, array (
			'customers_id' => $addr['customers_id'],
			'address_class' => $addr['address_class']
		), true);
		
		$qE = str_replace('*', 'address_book_id', $qE);
		$abID = MagnaDB::gi()->fetchOne($qE);

        if (($hp = magnaContribVerify('MagnaCompatibleImportOrders_PreAddAddress', 1)) !== false) {
            try {
              require($hp);
            } catch (MagnaException $e) {
              $this->storeLogging('MagnaException in Contrib MagnaCompatibleImportOrders_PreAddAddress ', $e);
            }
        }

        // newer xt:Commerce Versions have a customers_address_addition field
        if ( /*  (stripos($addr['customers_street_address'], 'Packstation') !== false)
             &&  ctype_digit($addr['customers_suburb'])
             &&*/MagnaDB::gi()->columnExistsInTable('customers_address_addition', TABLE_CUSTOMERS_ADDRESSES)) {
            $addr['customers_address_addition'] = $addr['customers_suburb'];
            $addr['customers_suburb'] = '';
        }

		$addr = array_filter_keys($addr, MagnaDB::gi()->getTableColumns(TABLE_CUSTOMERS_ADDRESSES));
		// customers_city breaks the processing with PHP >= 8.1 if too long
		if (strlen($addr['customers_city']) > 32) {
			$addr['customers_city'] = substr(trim($addr['customers_city']), 0, 32);
		}
		if ($abID === false) {
            MagnaDB::gi()->validateDataLength($addr, TABLE_CUSTOMERS_ADDRESSES);
            MagnaDB::gi()->addNonNullableEntries($addr, TABLE_CUSTOMERS_ADDRESSES);
            $this->db->insert(TABLE_CUSTOMERS_ADDRESSES, $addr);
			$this->addressIDs[$addr['address_class']] = $this->db->getLastInsertID();
		} else {
			$this->db->update(TABLE_CUSTOMERS_ADDRESSES, $addr, array (
				'address_book_id' => $abID,
				'customers_id' => $addr['customers_id']
			));
			$this->addressIDs[$addr['address_class']] = $abID;
		}
	}

	protected function insertCustomer() {
		$customer = array();
		$customer['Password'] = randomString(10);
		if (class_exists('xt_password') && method_exists('xt_password', 'hash_password')) {
			$oPassword = new xt_password();
			$this->o['customer']['customers_password'] = $oPassword->hash_password($customer['Password']);
		} else {
			$this->o['customer']['customers_password'] = md5($customer['Password']);
			if (MagnaDB::gi()->columnExistsInTable('password_type', TABLE_CUSTOMERS)) {
				$this->o['customer']['password_type'] = 1;
			}
		}
		
		$this->o['customer']['customers_status'] = $this->config['CustomerGroup'];
		$this->o['customer']['account_type'] = '0';
	
		$this->o['customer']['shop_id'] = $this->config['ShopMandant'];
        MagnaDB::gi()->validateDataLength($this->o['customer'], TABLE_CUSTOMERS);
        MagnaDB::gi()->addNonNullableEntries($this->o['customer'], TABLE_CUSTOMERS);
		$this->db->insert(TABLE_CUSTOMERS, $this->o['customer']);
		$cupdate = array();
		
		# Kunden-ID herausfinden
		$customer['ID'] = $this->db->getLastInsertID();
		# customers_cid bestimmen
		switch ($this->config['CIDAssignment']) {
			case 'sequential': {
				$customer['CID'] = MagnaDB::gi()->fetchOne('
				    SELECT MAX(CAST(IFNULL(customers_cid,0) AS SIGNED))+1
				      FROM '.TABLE_CUSTOMERS
				);
				break;
			}
			case 'customers_id': {
				$customer['CID'] = $customer['ID'];
				break;
			}
		}
		if (isset($customer['CID'])) {
			$cupdate['customers_cid'] = $customer['CID'];
		}

		# Kundendatensatz updaten.
		if (!empty($cupdate)) {
			$this->db->update(TABLE_CUSTOMERS, $cupdate, array (
				'customers_id' => $customer['ID']
			));
		}
		return $customer;
	}

	protected function processCustomer() {
		$customer = $this->getCustomer($this->o['customer']['customers_email_address']);
		if (is_array($customer)) {
			switch (strtolower($this->o['customer']['customers_default_language'])) {
				case 'de':
				case 'at':
				case 'ch': {
					$customer['Password'] = '(wie bekannt)';
					break;
				}
				default: {
					$customer['Password'] = '(as known)';
					break;
				}
			}
		} else {
			$customer = $this->insertCustomer();
		}

		# Adressbuchdatensatz ergaenzen.
		foreach ($this->o['adress'] as $addr) {
			$addr['customers_id'] = $customer['ID'];
			$this->addAddess($addr);
		}
		if ($this->addressIDs['default'] == 0) {
			$this->addressIDs['default'] = (int)MagnaDB::gi()->fetchOne('
				SELECT address_class 
				  FROM '.TABLE_CUSTOMERS_ADDRESSES.'
				 WHERE customers_id=\''.$customer['ID'].'\'
			');
		}
		// echo 'DELETE FROM '.TABLE_CUSTOMERS_ADDRESSES.' WHERE customers_id=\''.$customer['ID'].'\';'."\n\n";

		return $customer;
	}

	/**
	 * Load some basic info
	 */
	protected function prepareOrderInfo() {
		$this->cur['ShippingCountry']['Name'] = $this->getCountryName($this->o['order']['delivery_country_code']);
		$this->cur['BuyerCountry']['Name'] = $this->getCountryName($this->o['order']['billing_country_code']);
	}

	/**
	 * Returns the marketplace specific order ID from $this->o.
	 *
	 * @return string
	 *    OrderID of the marketplace used in magnalister_orders.special (Database)
	 */
	protected function getMarketplaceOrderID() {
		return $this->o['orderInfo']['MOrderID'];
	}

	protected function orderExists() {
		$mOID = $this->getMarketplaceOrderID();
		$aRow = MagnaDB::gi()->fetchRow('
		    SELECT orders_id, processed
		      FROM '.TABLE_MAGNA_ORDERS.'
		     WHERE platform = "'.MagnaDB::gi()->escape($this->marketplace).'"
		           AND special like "%'.MagnaDB::gi()->escape($mOID).'%"
		  ORDER BY orders_id DESC
		     LIMIT 1
		');
		if (empty($aRow)) {
			return false;
		} elseif ($aRow['processed'] == 0) {
			// order can be delted but perhaps shop-owner have already edited order
			return false;
		} else {
			if ($this->verbose) echo 'orderExists('.$mOID.')'."\n";
			/* Ack again */
			$this->processedOrders[] = array (
				'MOrderID' => $mOID,
				'ShopOrderID' => $aRow['orders_id'],
			);
			return true;
		}
	}
	
	/**
	 * Returns the status that the order should have as string.
	 * Use $this->o['order'].
	 *
	 * @return String	The order status for the currently processed order.
	 */
	protected abstract function getOrdersStatus();
	
	/**
	 * Returns the comment for orders.comment (Database). 
	 * E.g. the comment from the customer or magnalister related information.
	 * Use $this->o['order'].
	 *
	 * @return String
	 *    The comment for the order.
	 */
	protected function generateOrderComment($blForce = false) {
		if (!$blForce && !getDBConfigValue(array('general.order.information', 'val'), 0, true)) {
			return ''; 
		}
		return trim(
			sprintf(ML_GENERIC_AUTOMATIC_ORDER_MP_SHORT, $this->marketplaceTitle)."\n".
			ML_LABEL_MARKETPLACE_ORDER_ID.': '.$this->getMarketplaceOrderID()."\n\n".
			$this->comment
		);
	}
	
	/**
	 * Returns the comment for orders_status.comment (Database). 
	 * E.g. the comment from the customer or magnalister related information.
	 * May differ from self::generateOrderComment()
	 * Use $this->o['order'].
	 *
	 * @return String
	 *    The comment for the order.
	 */
	protected function generateOrdersStatusComment() {
		return $this->generateOrderComment(true);
	}
	
	/**
	 * In child classes this method can be used to extend the data for the DB-table
	 * orders before it is inserted.
	 * Use $this->o['order'].
	 */
	protected function doBeforeInsertOrder() {
		/* Do nothing here. */
	}

	/**
	 * In child classes this method can be used to extend the data for the DB-table
	 * magnalister_orders before it is inserted.
	 *
	 * @return array	Associative array that will be stored serialized
	 * 					in magnalister_orders.internaldata (Database)
	 */
	protected function doBeforeInsertMagnaOrder() {
		/* Do nothing here. */
		return array();
	}

	/**
	 * In child classes this method can be used to extend the data for the DB-table
	 * orders_history before it is inserted.
	 * Use $this->o['orderStatus']
	 */
	protected function doBeforeInsertOrderHistory() {
		/* Do nothing here. */
	}

	/**
	 * Returs the payment method for the current order.
	 * @return string
	 */
	protected function getPaymentMethod() {
		return $this->config['PaymentMethod'];
	}
	
	/**
	 * Returs the shipping method for the current order.
	 * @return string
	 */
	protected function getShippingMethod() {
		return $this->config['ShippingMethod'];
	}

	protected function insertOrder() {
		$this->comment = $this->o['order']['comments'];
		$this->o['order']['customers_id'] = $this->cur['customer']['ID'];
		if (isset($this->cur['customer']['CID'])) {
			$this->o['order']['customers_cid'] = $this->cur['customer']['CID'];
		}
		$this->o['order']['account_type'] = '0';
		$this->o['order']['customers_status'] = $this->config['CustomerGroup'];

		$this->o['order']['orders_status'] = $this->getOrdersStatus();

		// delivery_city breaks the processing with PHP >= 8.1 if too long
		if (strlen($this->o['order']['delivery_city']) > 32) {
			$this->o['order']['delivery_city'] = substr(trim($this->o['order']['delivery_city']), 0, 32);
		}
		$this->o['order']['delivery_country'] = $this->cur['ShippingCountry']['Name'];
		if ($this->addressIDs['shipping'] > 0) {
			$this->o['order']['delivery_address_book_id'] = $this->addressIDs['shipping'];
		} else {
			$this->o['order']['delivery_address_book_id'] = $this->addressIDs['default'];
		}

		// billing_city breaks the processing with PHP >= 8.1 if too long
		if (strlen($this->o['order']['billing_city']) > 32) {
			$this->o['order']['billing_city'] = substr(trim($this->o['order']['billing_city']), 0, 32);
		}
		$this->o['order']['billing_country'] = $this->cur['BuyerCountry']['Name'];
		if ($this->addressIDs['payment'] > 0) {
			$this->o['order']['billing_address_book_id'] = $this->addressIDs['payment'];
		} else {
			$this->o['order']['billing_address_book_id'] = $this->addressIDs['default'];
		}
		
		$this->o['currency_value'] = $this->allCurrencies[$this->o['order']['currency_code']];

		$this->o['order']['comments'] = $this->generateOrderComment();
		
		$this->o['order']['allow_tax'] = '1';
		
		$this->o['order']['shop_id'] = $this->config['ShopMandant'];
		
		if (MagnaDB::gi()->columnExistsInTable('allow_newsletter', TABLE_MAGNA_ORDERS)) {
			$this->o['order']['allow_newsletter'] = '0';
		}

		/* check if the language exists in shop - if not, take default */
		if (0 == MagnaDB::gi()->fetchOne('SELECT COUNT(*)
			FROM '.TABLE_LANGUAGES.'
			 WHERE code = \''.$this->o['order']['language_code'].'\'')
		) {
			$this->o['order']['language_code'] = $this->config['StoreLanguage'];
		}
		
		/* Change Shipping and Payment Methods */
		$this->o['order']['payment_code'] = $this->getPaymentMethod();
		$this->o['order']['shipping_code'] = $this->getShippingMethod();

		// overwrite some order total values (only shipping model and name)
		$this->o['orderTotal']['Shipping']['orders_total_model'] = $this->getShippingMethod();
		$this->o['orderTotal']['Shipping']['orders_total_name'] = $this->getShippingMethod();

		$this->doInsertOrder();
                if (empty($this->cur['OrderID'])) {
                    return;
                }
                $this->o['orderStatus']['orders_id'] = $this->cur['OrderID'];
		$this->o['orderStatus']['orders_status_id'] = $this->o['order']['orders_status'];
		
                $this->o['orderStatus']['comments'] = $this->generateOrdersStatusComment();
	
		if (!$this->config['Table_OrdersStatus_HasCollumn_ShowComment']) {
                    unset($this->o['orderStatus']['customer_show_comment']);
		}
	
		$this->doBeforeInsertOrderHistory();
		$this->db->insert(TABLE_ORDERS_STATUS_HISTORY, $this->o['orderStatus']);
		$this->unprocessedComments[$this->cur['OrderID']] = array(
			'customers_id' => $this->o['order']['customers_id'],
			'orders_status' => $this->o['order']['orders_status'],
			'comment' => $this->generateOrdersStatusComment(),
			'insert_id' => $this->db->getLastInsertID(),
		);
		// echo 'DELETE FROM '.TABLE_ORDERS_STATUS_HISTORY.' WHERE orders_id=\''.$this->cur['OrderID'].'\';'."\n\n";
		// insert order source data
		if (defined('TABLE_ORDERS_SOURCE') && MagnaDB::gi()->tableExists(TABLE_ORDERS_SOURCE)) {
			if (!MagnaDB::gi()->recordExists(TABLE_ORDERS_SOURCE, array(
				'source_name' => $this->marketplace,
			))) {
				$this->db->insert(TABLE_ORDERS_SOURCE, array(
					'source_name' => $this->marketplace,
				));
			}

			$iSourceId = MagnaDB::gi()->fetchOne("
				SELECT source_id
				  FROM ".TABLE_ORDERS_SOURCE."
				 WHERE source_name = '".MagnaDB::gi()->escape($this->marketplace)."'
				 LIMIT 1
			");
			if ($iSourceId !== false) {
				$this->db->update(TABLE_ORDERS, array(
					'source_id' => $iSourceId
				), array(
					'orders_id' => $this->cur['OrderID']
				));
			}
		}

		/* {Hook} "MagnaCompatibleImportOrders_PostInsertOrder": Is called after the order in <code>$this->o['order']</code> is imported.
				Usefull to manipulate some of the data in the database
				Variables that can be used:
				<ul><li>$this->o['order']: The order that is going to be imported. The order is an 
				        associative array representing the structures of the order and customer related shop tables.</li>
				    <li>$this->mpID: The ID of the marketplace.</li>
					<li>$this->marketplace: The name of the marketplace.</li>
				    <li>$this->cur['OrderID']: The Order ID of the shop (<code>orders_id</code>).</li>
				    <li>$this->o['order']['customers_id']: The Customers ID of the shop (<code>customers_id</code>).</li>
				    <li>$this->db: Instance of the magnalister database class. USE THIS for accessing the database during the
				        order import. DO NOT USE the shop functions for database access or MagnaDB::gi()!</li>
				</ul>
		*/
		if (function_exists('magnaContribVerify') && (($hp = magnaContribVerify('MagnaCompatibleImportOrders_PostInsertOrder', 1)) !== false)) {
			try {
				require($hp);
			} catch (MagnaException $e) {
				$this->storeLogging('MagnaException in Contrib MagnaCompatibleImportOrders_PostInsertOrder ', $e);
			}
		}
	}

	/**
	 * May be overwritten to allow additional identification of the product based on EAN or title.
	 * @todo: Replace products_ean in the query with the constant.
	 */
	protected function additionalProductsIdentification() {

	}
	
	protected function doInsertOrder() {
            $sOrderCountSql = "SELECT * FROM ".TABLE_ORDERS." WHERE customers_email_address = '".MagnaDB::gi()->escape($this->o['order']['customers_email_address'])."'";
            $iPrevOrdersCount = (int) $this->db->fetchRow($sOrderCountSql);
		$this->doBeforeInsertOrder();
	        // newer xt:Commerce Versions have a address_addition fields
	        if ( /*    (stripos($this->o['order']['delivery_street_address'], 'Packstation') !== false)
	             &&  ctype_digit($this->o['order']['delivery_suburb'])
	             &&*/  MagnaDB::gi()->columnExistsInTable('delivery_address_addition', TABLE_ORDERS)) {
	            $this->o['order']['delivery_address_addition'] = $this->o['order']['delivery_suburb'];
	            $this->o['order']['delivery_suburb'] = '';
	        }
	        if ( /*  (stripos($this->o['order']['billing_street_address'], 'Packstation') !== false)
	             &&  ctype_digit($this->o['order']['billing_suburb'])
	             &&*/MagnaDB::gi()->columnExistsInTable('billing_address_addition', TABLE_ORDERS)) {
	            $this->o['order']['billing_address_addition'] = $this->o['order']['billing_suburb'];
	            $this->o['order']['billing_suburb'] = '';
	        }
		$this->o['order'] = array_filter_keys($this->o['order'], MagnaDB::gi()->getTableColumns(TABLE_ORDERS));
        MagnaDB::gi()->validateDataLength($this->o['order'], TABLE_ORDERS);
        MagnaDB::gi()->addNonNullableEntries($this->o['order'], TABLE_ORDERS);
        $this->db->insert(TABLE_ORDERS, $this->o['order']);

		# OrderId merken
		$this->cur['OrderID'] = $this->db->getLastInsertID();
        if (empty($this->cur['OrderID'])) {
            $iInsertId = (int)$this->db->fetchOne("SELECT LAST_INSERT_ID()");
            $sOrderId = (int)$this->db->fetchOne("
                SELECT orders_id 
                  FROM ".TABLE_ORDERS."
                 WHERE customers_email_address = '".MagnaDB::gi()->escape($this->o['order']['customers_email_address'])."'
              ORDER BY orders_id DESC
              LIMIT 1
            ");
            if ($iInsertId == $sOrderId) {
                $this->cur['OrderID'] = $iInsertId;
            } elseif((int) $this->db->fetchRow($sOrderCountSql) > $iPrevOrdersCount) {// count is now higher
                $this->cur['OrderID'] = $sOrderId;
            } else {// there is no order-id
                return;
            }
        }
		// echo 'DELETE FROM '.TABLE_ORDERS.' WHERE orders_id=\''.$this->cur['OrderID'].'\';'."\n\n";

		/* Bestellung in unserer Tabelle registrieren */
		$internalData = $this->doBeforeInsertMagnaOrder();
		$this->db->insert(TABLE_MAGNA_ORDERS, array(
			'mpID' => $this->mpID,
			'orders_id' => $this->cur['OrderID'],
			'orders_status' => $this->o['order']['orders_status'],
			'data' => serialize($this->o['magnaOrders']),
			'internaldata' => is_array($internalData) ? serialize($internalData) : '',
			'special' => $this->getMarketplaceOrderID(),
			'platform' => $this->marketplace,
			'processed' => 0,
		));
		// echo 'DELETE FROM '.TABLE_MAGNA_ORDERS.' WHERE orders_id=\''.$this->cur['OrderID'].'\';'."\n\n";
	}

    /**
     * Converts whatever the API has submitted in $this->p['products_tax'] to a
     * real tax value.
     * Here it just returns the parameter. Child Clases however may override this
     * method and convert the parameter.
     *
     * @param mixed $tax    Something that represents a tax value
     * @return float The actual tax value
     */
	protected function getTaxValue($tax) {
		return $tax;
	}
	
	protected function getTaxIDByTaxValue($tax) {
		if ($tax == 0) {
			return 0;
		}
		if (isset($this->tax2classID[$tax.''])) {
			return $this->tax2classID[$tax.''];
		}
		$this->tax2classID[$tax.''] = (int)MagnaDB::gi()->fetchOne('
			SELECT tax_class_id FROM '.TABLE_TAX_RATES.' WHERE tax_rate=\''.$tax.'\' LIMIT 1
		');
		return $this->tax2classID[$tax.''];
	}

	/**
	 * In child classes this method can be used to extend the data for the DB-table
	 * orders_history before it is inserted.
	 * Use $this->p['products_discount']
	 */
	protected function doBeforeInsertProduct() {
		/* Do nothing here. */
	}

	/**
	 * Returns true if the stock of the imported and identified item has to be reduced.
	 * @return bool
	 */
	protected function hasReduceStock() {
		return $this->config['StockSync.FromMarketplace'] != 'no';
	}

	/**
	 *
	 */
	protected function insertProduct() {
		$this->p['orders_id'] = $this->cur['OrderID'];

		$this->p['products_id'] = magnaSKU2pID($this->p['products_id']);
		$this->additionalProductsIdentification();

		$this->mailOrderSummary[] = array(
			'quantity' => $this->p['products_quantity'],
			'name' => $this->p['products_name'],
			'price' => $this->simplePrice->setPrice($this->p['products_price'])->format(),
			'finalprice' => $this->simplePrice->setPrice($this->p['products_price'] * (int)$this->p['products_quantity'])->format(),
		);
		if (array_key_exists($this->p['products_id'], $this->syncBatch)) {
			$this->syncBatch[$this->p['products_id']]['NewQuantity']['Value'] += (int)$this->p['products_quantity'];
		} else {
			$this->syncBatch[$this->p['products_id']] = array (
				'SKU' => $this->p['products_id'],
				'NewQuantity' => array (
					'Mode' => 'SUB',
					'Value' => (int)$this->p['products_quantity']
				),
			);
		}

		$tax = false;
		if (isset($this->p['products_tax'])) {
			$tax = $this->getTaxValue($this->p['products_tax']);
		}

		if (!MagnaDB::gi()->recordExists(TABLE_PRODUCTS, array('products_id' => (int)$this->p['products_id']))) {
			$this->p['products_id'] = 0;
		} else {
			/* Lagerbestand reduzieren */
			if (($hp = magnaContribVerify('MagnaCompatibleImportOrders_PreReduceStock', 1)) !== false) {
				try {
					require($hp);
				} catch (MagnaException $e) {
					$this->storeLogging('MagnaException in Contrib MagnaCompatibleImportOrders_PreReduceStock ', $e);
				}
			}
			if ($this->hasReduceStock()) {
				$this->db->query('
					UPDATE '.TABLE_PRODUCTS.'
					   SET products_quantity = products_quantity - '.(int)$this->p['products_quantity'].' 
					 WHERE products_id='.(int)$this->p['products_id'].'
				');
			}
			/* Steuersatz und Model holen */
			$row = MagnaDB::gi()->fetchRow('
				SELECT products_tax_class_id, products_model 
				  FROM '.TABLE_PRODUCTS.' 
				 WHERE products_id=\''.(int)$this->p['products_id'].'\'
			');
			if ($row !== false) {
				if ($tax === false || !array_key_exists('ForceMPTax', $this->o['orderInfo']) || !$this->o['orderInfo']['ForceMPTax']) {
					$tax = SimplePrice::getTaxByClassID((int)$row['products_tax_class_id'], $this->o['order']['delivery_country_code']);
				}
				$this->p['products_model'] = $row['products_model'];
				$this->tax2classID[$tax.''] = (int)$row['products_tax_class_id'];
			}
		}
		if ($tax === false) {
			$tax = (float)$this->config['MwStFallback'];
		}
		//Bug fix for floats as array keys
		$tax = (string)round($tax, 2);

		$this->p['allow_tax'] = $this->config['AllowTax'] ? 1 : 0;
		$this->p['products_tax'] = $tax;
		$this->p['products_tax_class'] = $this->getTaxIDByTaxValue($this->p['products_tax']);

		$priceWOTax = $this->simplePrice->setPrice($this->p['products_price'])->removeTax($tax)->getPrice();

		if (!isset($this->taxValues[$tax])) {
			$this->taxValues[$tax] = 0.0;
		}
		$this->taxValues[$tax] += $priceWOTax * (int)$this->p['products_quantity'];

		$this->p['products_price'] = $priceWOTax;

		//echo print_m($this->p);

		$this->doBeforeInsertProduct();
		
		$this->o['_processingData']['ProductsCount'] += (int)$this->p['products_quantity'];
		
		# Produktdatensatz in Tabelle "orders_products".
		$this->db->insert(TABLE_ORDERS_PRODUCTS, array_filter_keys($this->p, MagnaDB::gi()->getTableColumns(TABLE_ORDERS_PRODUCTS)));
	}
	
	protected function proccessCouponTax() {
		if (!array_key_exists('Coupon', $this->o['orderTotal'])) {
			return;
		}
		if (!isset($this->config['MwStCoupon'])) {
			$this->config['MwStCoupon'] = $this->config['MwStFallback'];
		}
		if (!isset($this->taxValues[$this->config['MwStShipping']])) {
			$this->taxValues[$this->config['MwStShipping']] = 0.0;
		}
		$this->o['orderTotal']['Coupon']['orders_total_tax'] = (float)$this->config['MwStCoupon'];
		$this->o['orderTotal']['Coupon']['orders_total_tax_class'] = $this->getTaxIDByTaxValue($this->config['MwStCoupon']);
		$this->o['orderTotal']['Coupon']['orders_total_price'] = $this->simplePrice->setPrice(
			$this->o['orderTotal']['Coupon']['orders_total_price']
		)->removeTax($this->config['MwStCoupon'])->getPrice();
		
		$this->taxValues[$this->config['MwStCoupon']] += $this->o['orderTotal']['Coupon']['value'];
		
		$this->o['orderTotal']['Coupon']['allow_tax'] = $this->config['AllowTax'] ? 1 : 0;
	}
	
	protected function proccessShippingTax() {
		if (!array_key_exists('Shipping', $this->o['orderTotal'])) {
			return;
		}
		
		if (!isset($this->taxValues[$this->config['MwStShipping']])) {
			$this->taxValues[$this->config['MwStShipping']] = 0.0;
		}
		$this->o['orderTotal']['Shipping']['orders_total_tax'] = (float)$this->config['MwStShipping'];
		$this->o['orderTotal']['Shipping']['orders_total_tax_class'] = $this->getTaxIDByTaxValue($this->config['MwStShipping']);
		$this->o['orderTotal']['Shipping']['orders_total_price'] = $this->simplePrice->setPrice(
			$this->o['orderTotal']['Shipping']['orders_total_price']
		)->removeTax($this->config['MwStShipping'])->getPrice();
		
		$this->taxValues[$this->config['MwStShipping']] += $this->o['orderTotal']['Shipping']['orders_total_price'];
		
		$this->o['orderTotal']['Shipping']['allow_tax'] = $this->config['AllowTax'] ? 1 : 0;
	}
	
	/**
	 * This method prepares and inserts data for orders_total.
	 * Child-Classes may extend this method. However this method should be called
	 * at the end to do the actual inertion of data in the database.
	 * parent::insertOrdersTotal(); as last statement.
	 */
	protected function insertOrdersTotal() {
		//echo print_m($this->o['orderTotal']);
		foreach ($this->o['orderTotal'] as $key => &$entry) {
			$entry['orders_id'] = $this->cur['OrderID'];
			
			// Update the record if it exists (e.g. merged orders)
			if (MagnaDB::gi()->recordExists(TABLE_ORDERS_TOTAL, array (
				'orders_id' => $entry['orders_id'],
				'orders_total_key' => $entry['orders_total_key'],
			))) {
				$this->db->update(TABLE_ORDERS_TOTAL, $entry, array (
					'orders_id' => $entry['orders_id'],
					'orders_total_key' => $entry['orders_total_key'],
				));
			} else {
				$this->db->insert(TABLE_ORDERS_TOTAL, $entry);
			}
		}
		// echo 'DELETE FROM '.TABLE_ORDERS_TOTAL.' WHERE orders_id=\''.$this->cur['OrderID'].'\';'."\n\n";	
	}
	
	protected function insertOrdersStats() {
		if (!$this->config['AllowTax']) {
			$sum = 0;
			foreach ($this->taxValues as $tax => $value) {
				$sum += $value;
			}
			$this->o['ordersStats']['orders_stats_price'] = $sum;
		}
		$this->o['ordersStats']['orders_id'] = $this->cur['OrderID'];
		$this->db->insert(TABLE_ORDERS_STATS, $this->o['ordersStats'], true);
	}
	
	protected function sendPromoMail() {
		if ($this->config['MailSend'] != 'true') return;
		sendSaleConfirmationMail(
			$this->mpID,
			$this->o['customer']['customers_email_address'],
			array (
				'#FIRSTNAME#' => $this->o['order']['billing_firstname'],
				'#LASTNAME#' => $this->o['order']['billing_lastname'],
				'#EMAIL#' => $this->o['customer']['customers_email_address'],
				'#PASSWORD#'  => $this->cur['customer']['Password'],
				'#MORDERID#' => $this->o['orderInfo']['MOrderID'],
				'#ORDERSUMMARY#' => $this->mailOrderSummary,
				'#MARKETPLACE#' => $this->marketplaceTitle,
				'#SHOPURL#' => HTTP_SERVER.DIR_WS_CATALOG,
			)
		);
	}
	
	protected function completeImport() {}

	private function setCurrentShippingTax() {
		// set the shipping tax to the highest of products' taxes
		$this->config['MwStShipping'] = (float)round($this->db->fetchOne(eecho("
			SELECT max(products_tax)
			  FROM ".TABLE_ORDERS_PRODUCTS."
			 WHERE orders_id = '".$this->cur['OrderID']."'
		", false)), 2);
	}

	protected function processSingleOrder() {
		if ($this->verbose) echo print_m($this->o, 'order');
		$this->o['_processingData'] = array();
		
		if (!$this->updateOrderCurrency($this->o['order']['currency_code'])) {
			/* Currency is not available in this shop or 
			   the currency value can't be determined. */
			if ($this->verbose) echo '!updateOrderCurrency'."\n";
			return;
		}
		/* Reset order specific class atributes */
		$this->cur = array();
		$this->addressIDs = array (
			'default' => 0,
			'shipping' => 0,
			'payment' => 0,
		);
		$this->taxValues = array();
		$this->mailOrderSummary = array();
		$this->sumStats = 0;

		/* Prepare order specific informations */
		$this->prepareOrderInfo();

		$this->cur['customer'] = $this->processCustomer();
		#echo print_m($this->cur['customer'], '$customer');

		if ($this->orderExists()) {
			return;
		}
		$this->insertOrder();
                if (empty($this->cur['OrderID'])) {
                    return;
                }
                
		$this->o['_processingData']['ProductsCount'] = 0;
		foreach ($this->o['products'] as $p) {
			$this->p = $p;
			$this->insertProduct();
		}
		$this->setCurrentShippingTax();
		//echo 'DELETE FROM '.TABLE_ORDERS_PRODUCTS.' WHERE orders_id=\''.$this->cur['OrderID'].'\';'."\n\n";
		
		$this->proccessCouponTax();
		$this->proccessShippingTax();
		
		$this->insertOrdersTotal();
		$this->insertOrdersStats();

		/* catch the case when crucial parts of the order are lacking
		 * (rare case, we could not find the reason yet) */
		$aProductEntriesCount = (int)$this->db->fetchOne('SELECT COUNT(*)
			 FROM '.TABLE_ORDERS_PRODUCTS.'
			WHERE orders_id = '.$this->cur['OrderID']
		);
		$aTotalEntriesCount = (int)$this->db->fetchOne('SELECT COUNT(*)
			 FROM '.TABLE_ORDERS_TOTAL.'
			WHERE orders_id = '.$this->cur['OrderID']
		);
		if ( ($aProductEntriesCount < 1) || ($aTotalEntriesCount < 1) ) {
			$this->db->delete(TABLE_ORDERS_PRODUCTS, array ('orders_id' => $this->cur['OrderID']));
			$this->db->delete(TABLE_ORDERS_TOTAL, array ('orders_id' => $this->cur['OrderID']));
			$this->db->delete(TABLE_ORDERS_STATS, array ('orders_id' => $this->cur['OrderID']));
			$this->db->delete(TABLE_ORDERS, array ('orders_id' => $this->cur['OrderID']));
			$this->db->delete(TABLE_MAGNA_ORDERS, array ('orders_id' => $this->cur['OrderID']));
			/* we quit processing of the current order before we stored it in processedOrders,
			 * so that the order is not acknowledged and will be dispatched again
			 * with the next order import */
			return;
		}
		$this->db->update(TABLE_MAGNA_ORDERS, array('processed' => 1), array('special' => $this->getMarketplaceOrderID()));

		$this->processedOrders[] = array (
			'MOrderID' => $this->getMarketplaceOrderID(),
			'ShopOrderID' => $this->cur['OrderID'],
		);
		$this->out($this->marketplace.' order '.$this->getMarketplaceOrderID().' imported with No. '.$this->cur['OrderID']."\n");
		
		$this->sendPromoMail();
		
		$this->lastOrderDate = $this->o['order']['date_purchased'];
		
		$this->completeImport();
	}
	
	protected function acknowledgeImportedOrders() {
		if (empty($this->processedOrders)) return;
		/* Acknowledge imported orders */
		$request = array(
			'ACTION' => 'AcknowledgeImportedOrders',
			'SUBSYSTEM' => $this->marketplace,
			'MARKETPLACEID' => $this->mpID,
			'DATA' => $this->processedOrders,
		);
		if (get_class($this->db) == 'MagnaTestDB') {
			if ($this->verbose) echo print_m($request);
			$this->processedOrders = array();
			return;
		}
		try {
			MagnaConnector::gi()->submitRequest($request);
			# Statuseintrag fuer Historie vornehmen.
			// if class exists and we are not in dummy debug mode
			if (class_exists('order') && (get_class($this->db) == 'MagnaDB')) {
				foreach ($this->processedOrders as $iOrderId => $aProcessedOrder) {
					if (array_key_exists($aProcessedOrder['ShopOrderID'], $this->unprocessedComments)) {
						$aUnprocessedComment = $this->unprocessedComments[$aProcessedOrder['ShopOrderID']];
						/* die order Klasse gibt eine Warning aus, da orders_total noch nicht gesetzt ist.
						 * Hat keine weiteren Folgen, daher ignorieren.
						 */
						$iOldErrorLevel = error_reporting();
						error_reporting(E_ERROR);
						$oOrderSource = new order($aProcessedOrder['ShopOrderID'], $aUnprocessedComment['customers_id']);
						// order data could be loaded
						if (!empty($oOrderSource->order_data)) {
							$oOrderSource->_updateOrderStatus(
								$aUnprocessedComment['orders_status'],
								$aUnprocessedComment['comment'],
								'false',
								($this->config['Table_OrdersStatus_HasCollumn_ShowComment'] ? 'true' : 'false')
							);
							//delete old comment
							MagnaDb::gi()->delete(TABLE_ORDERS_STATUS_HISTORY, array(
								'orders_status_history_id' => $aUnprocessedComment['insert_id'], 
								'orders_id' => $aProcessedOrder['ShopOrderID'])
							);
						}
					}
				}
				error_reporting($iOldErrorLevel);
			}
			$this->unprocessedComments = array();
			$this->processedOrders = array();
		} catch (MagnaException $e) {
			if ((defined('MAGNA_CALLBACK_MODE') && MAGNA_CALLBACK_MODE == 'STANDALONE') || $this->verbose) {
				echo print_m($e->getErrorArray(), 'Error: '.$e->getMessage(), true);
			}
			if ($e->getCode() == MagnaException::TIMEOUT) {
				$e->saveRequest();
				$e->setCriticalStatus(false);
			}
		}

	}

	final public function process() {
		// search for incomplete orders add comment to them and delte them from magnalister-orders
		foreach (MagnaDB::gi()->fetchArray("SELECT * FROM ".TABLE_MAGNA_ORDERS." WHERE processed='0'") as $aOrder) {
			$aLastComment = MagnaDB::gi()->fetchRow("SELECT orders_status_id FROM ".TABLE_ORDERS_STATUS_HISTORY." WHERE orders_id='".MagnaDb::gi()->escape($aOrder['orders_id'])."' ORDER BY orders_status_history_id DESC LIMIT 1");
			if (!empty($aLastComment)) {
				$aOrderStatus = array(
					'orders_id' => $aOrder['orders_id'],
					'orders_status_id' => $aLastComment['orders_status_id'],
					'comments' => sprintf(ML_GENERIC_AUTOMATIC_ORDER_MP_SHORT, $aOrder['platform'])."\n".'Order was not imported completely new order will be generated.',
				);
				if ($this->config['Table_OrdersStatus_HasCollumn_ShowComment']) {
					$aOrderStatus['customer_show_comment'] = 0;
				}
				$this->db->insert(TABLE_ORDERS_STATUS_HISTORY, $aOrderStatus);
			}
		}
		MagnaDb::gi()->delete(TABLE_MAGNA_ORDERS, array('processed' => '0'));// deletes incomplete orders from magnalister orders
		if (defined('MAGNA_CALLBACK_MODE')) {
			// giving continous output if callback
			$this->out("Start[".get_class()."]: ".$this->marketplace." (".$this->mpID.")\n");
		}
		if ($this->verbose) echo print_m($this->config, '$this->config');
		while (($orders = $this->getOrders()) !== false) {
			#if ($this->verbose) echo print_m($orders, 'orders');
			while ($order = array_shift($orders)) {
				$this->cur = array();
				$this->o = $order;
				
				$continue = false;
				/* {Hook} "MagnaCompatibleImportOrders_PreOrderImport": Is called before the order in <code>$this->o</code> is imported.
					Variables that can be used:
					<ul><li>$this->o: The order that is going to be imported. The order is an 
					        associative array representing the structures of the order and customer related shop tables.</li>
					    <li>$this->mpID: The ID of the marketplace.</li>
					    <li>$this->marketplace: The name of the marketplace.</li>
					    <li>$this->db: Instance of the magnalister database class. USE THIS for writing or changing data in the database during the
					        order import. DO NOT USE the shop functions or MagnaDB::gi() for this purpose!</li>
						<li>$continue (bool): Set this to true to skip the processing of current order.</li>
					</ul>
				*/
				if (($hp = magnaContribVerify('MagnaCompatibleImportOrders_PreOrderImport', 1)) !== false) {
					try {
						require($hp);
					} catch (MagnaException $e) {
						$this->storeLogging('MagnaException in Contrib MagnaCompatibleImportOrders_PreOrderImport ', $e);
					}
				}
				if ($continue) {
					continue;
				}
				
				if (defined('MAGNA_CALLBACK_MODE')) {
					// giving continous output if callback
					$this->out($this->getMarketplaceOrderID()."\n");
				}
				
				$this->processSingleOrder();
				
				/* {Hook} "MagnaCompatibleImportOrders_PostOrderImport": Is called after the order in <code>$this->o</code> is imported.
					Usefull to manipulate some of the data in the database
					Variables that can be used:
					<ul><li>$this->o: The order that has just been imported. The order is an associative array representing the
					        structures of the order and customer related shop tables.</li>
					    <li>$this->mpID: The ID of the marketplace.</li>
					    <li>$this->marketplace: The name of the marketplace.</li>
					    <li>$this->cur['OrderID']: The Order ID of the shop (<code>orders_id</code>).</li>
					    <li>$this->cur['customer']['ID']: The Customers ID of the shop (<code>customers_id</code>).</li>
					    <li>$this->db: Instance of the magnalister database class. USE THIS for writing or changing data in the database during the
					        order import. DO NOT USE the shop functions or MagnaDB::gi() for this purpose!</li>
					</ul>
				*/
				if (($hp = magnaContribVerify('MagnaCompatibleImportOrders_PostOrderImport', 1)) !== false) {
					try {
						require($hp);
					} catch (MagnaException $e) {
						$this->storeLogging('MagnaException in Contrib MagnaCompatibleImportOrders_PostOrderImport ', $e);
					}
				}
				#break;
			}
			$this->acknowledgeImportedOrders();
			#break;
		}
		if ($this->lastOrderDate !== false) {

		}
		if (defined('MAGNA_CALLBACK_MODE')) {
			// giving continous output if callback
			$this->out("End[".get_class()."]: ".$this->marketplace." (".$this->mpID.")\n");
		}
	}

}
