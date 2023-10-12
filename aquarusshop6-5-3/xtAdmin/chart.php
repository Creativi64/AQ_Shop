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

include '../xtFramework/admin/main.php';

if (!$xtc_acl->isLoggedIn()) {
    die('login required');
}

class ChartData {

	const CHART_TYPE_ORDERS = 'orders';
	const CHART_TYPE_ORDERS_BY_STORE = 'ordersbystore';
	const CHART_TYPE_CUSTOMERS_COUNT = 'customerscount';
	const CHART_TYPE_CUSTOMERS_COUNT_BY_STORE = 'customerscountbystore';
	const CHART_TYPE_PRODUCT_SELLS = 'product_sells';
	const CHART_TYPE_PRODUCT_SELLS_BY_STORE = 'product_sells_by_store';
	const CHART_TYPE_SHOPPING_CARTS = 'shopping_carts';
	const CHART_TYPE_SHOPPING_CARTS_BY_STORE = 'shopping_carts_by_store';

	protected $_chartType = null;

	public function __construct() {
		global $filter,$system_status;

		if (isset($_GET['ChartType'])) {
			$this->_chartType = $filter->_quote($_GET['ChartType']);
		}

		unset($_GET['ChartType']);

    $order_status_values = $system_status->values['order_status'];
    $this->_relevant_status_ids = $this->getRelevantOrderStatusID($order_status_values);
	}

	public function getJsonData() {
		global $xtPlugin;
		//header('Content-Type: application/json; charset='._SYSTEM_CHARSET);
		//header("Cache-Control: no-cache, must-revalidate");
		//header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

		$obj = new stdClass();
		$obj->topics = array();
		$obj->totalCount = 0;

		if (null !== $this->_chartType) {
			switch ($this->_chartType) {
				case static::CHART_TYPE_ORDERS:
					$this->loadOrdersData($obj);
					break;
				case static::CHART_TYPE_ORDERS_BY_STORE:
					$this->loadOrdersDataByStore($obj);
					break;
				case static::CHART_TYPE_CUSTOMERS_COUNT:
					$this->loadCustomersData($obj);
					break;
				case static::CHART_TYPE_CUSTOMERS_COUNT_BY_STORE:
					$this->loadCustomersDataByStore($obj);
					break;
				case static::CHART_TYPE_PRODUCT_SELLS:
					$this->loadProductSells($obj);
					break;
				case static::CHART_TYPE_PRODUCT_SELLS_BY_STORE:
					$this->loadProductSellsByStore($obj);
					break;
				case static::CHART_TYPE_SHOPPING_CARTS:
					$this->loadShoppingCarts($obj);
					break;
				case static::CHART_TYPE_SHOPPING_CARTS_BY_STORE:
					$this->loadShoppingCartsByStore($obj);
					break;
				default:
					($plugin_code = $xtPlugin->PluginCode('chart.php:getJsonData')) ? eval($plugin_code) : false;
					break;
			}
		}

		echo json_encode($obj);
	}

	protected function loadShoppingCarts(stdClass $response) {
		global $db,$store_handler,$system_status, $filter;

		$stores = $store_handler->getStores();

		$fromFilter 					= $this->get_post_param_default('ShoppingCartsFromDate', date('Y-m-d', strtotime('-1 month')));
		$toFilter 						= $this->get_post_param_default('ShoppingCartsToDate', date('Y-m-d', strtotime('now')));
		$customer_status 				= $this->get_post_param_default('ShoppingCartsCustomersStatusFilter', '');
		$periodTypeFilter 				= $this->get_post_param_default('ShoppingCartsPeriodTypeFilter', 'day');
		$shoppingCartsTypeFilter 		= $this->get_post_param_default('ShoppingCartsTypeFilter', 'sc_added');
		$customer_status_condition 		= empty($customer_status) ? '' : "customers_status='" . $filter->_quote($customer_status) . "' ";
		$shoppingCartsDisplayByFilter 	= $this->get_post_param_default('ShoppingCartsDisplayByFilter', 'carts_count');
		$data = array();

		$storeIds = array();
		$selectStatement = array();
		switch ($periodTypeFilter) {
			case 'day':
				$selectStatement[] = 'DATE(s.date_added) as day';
				break;
			case 'month':
				$selectStatement[] = 'CONCAT(YEAR(s.date_added), "-", MONTH(s.date_added)) as day';
				break;
			default:
				$selectStatement[] = 'YEAR(s.date_added) as day';
				break;
		}

		$mainQueryConditions = array(
				"s.date_added >= '" . $filter->_quote($fromFilter) . "'",
				"s.date_added <= '" . $filter->_quote($toFilter) . "'",
		);

		if (!empty($customer_status_condition)) {
			$mainQueryConditions[] = $customer_status_condition;
		}

		if ($shoppingCartsTypeFilter == 'sc_checkout') {
			$mainQueryConditions[] = 's.sales_stat_type=1';
		}

		if ($shoppingCartsTypeFilter == 'sc_not_checkout') {
			$mainQueryConditions[] = 's.sales_stat_type=0';
		}

		foreach ($stores as $sdata) {

			switch ($shoppingCartsDisplayByFilter) {
				case 'carts_count':
					$query = sprintf("SELECT count(*) as count, %s FROM %s s WHERE %s AND s.shop_id=%s GROUP BY day",
						join(',', $selectStatement), TABLE_SALES_STATS, join(' AND ', $mainQueryConditions), $sdata['id']
					);
					break;
				case 'products_count':
					$query = sprintf("SELECT sum(s.products_count) as count, %s FROM %s s WHERE %s AND s.shop_id=%s GROUP BY day",
						join(',', $selectStatement), TABLE_SALES_STATS, join(' AND ', $mainQueryConditions),  $sdata['id']
					);
					break;
				}

			$storeIds[] = $sdata['id'];

			$rs = $db->Execute($query);

			while (!$rs->EOF) {
				$data[$rs->fields['day']][$sdata['id']] = $rs->fields['count'];
				$data[$rs->fields['day']]['date'] = $rs->fields['day'];
				$rs->MoveNext();
			}$rs->Close();
		}

		foreach ($data as $date => &$salesData) {
			foreach ($storeIds as $id) {
				if (!isset($salesData[$id])) {
					$salesData[$id] = '0';
				}
			}
			$total = 0;
			foreach ($salesData as $key => $count) {
				if (!is_numeric($key)) {
					continue;
				}
				$total += $count;
			}

			$data[$date]['total'] = sprintf("%d", $total);
			ksort($salesData);
		}

		if (empty($data)) {
			foreach ($stores as $sdata) {
				$data[] = array($sdata['id'] => 0, 'date' => date('Y-m-d', strtotime('now')));
			}
		}

		// Sort by date
		$data = $this->normalizeData($data, $periodTypeFilter);
		// Reset data
		$data = array_values($data);
		$response->topics = $data;
		$response->totalCount = count($data);
	}

	protected function loadShoppingCartsByStore(stdClass $response) {
		global $db,$store_handler,$system_status, $filter;

		$store_names = array();
		$stores = $store_handler->getStores();

		foreach ($stores as $store_data) {
			$store_names[$store_data['id']] = $store_data['text'];
		}

		$fromFilter 					= $this->get_post_param_default('ShoppingCartsFromDate', date('Y-m-d', strtotime('-1 month')));
		$toFilter 						= $this->get_post_param_default('ShoppingCartsToDate', date('Y-m-d', strtotime('now')));
		$customer_status 				= $this->get_post_param_default('ShoppingCartsCustomersStatusFilter', '');
		$periodTypeFilter 				= $this->get_post_param_default('ShoppingCartsPeriodTypeFilter', 'day');
		$shoppingCartsTypeFilter 		= $this->get_post_param_default('ShoppingCartsTypeFilter', 'sc_added');
		$customer_status_condition 		= empty($customer_status) ? '' : "customers_status='" . $filter->_quote($customer_status) . "' ";
		$shoppingCartsDisplayByFilter 	= $this->get_post_param_default('ShoppingCartsDisplayByFilter', 'carts_count');
		$data = array();

		$storeIds = array();

		$mainQueryConditions = array(
				"s.date_added >= '" . $filter->_quote($fromFilter) . "'",
				"s.date_added <= '" . $filter->_quote($toFilter) . "'",
		);

		if (!empty($customer_status_condition)) {
			$mainQueryConditions[] = $customer_status_condition;
		}

		if ($shoppingCartsTypeFilter == 'sc_checkout') {
			$mainQueryConditions[] = 's.sales_stat_type=1';
		}

		if ($shoppingCartsTypeFilter == 'sc_not_checkout') {
			$mainQueryConditions[] = 's.sales_stat_type=0';
		}

		foreach ($stores as $sdata) {

			switch ($shoppingCartsDisplayByFilter) {
				case 'carts_count':
					$query = sprintf("SELECT count(*) as count, s.shop_id FROM %s s WHERE %s AND s.shop_id=%s GROUP BY s.shop_id",
					TABLE_SALES_STATS, join(' AND ', $mainQueryConditions), $sdata['id']
					);
					break;
				case 'products_count':
					$query = sprintf("SELECT sum(s.products_count) as count, s.shop_id FROM %s s WHERE %s AND s.shop_id=%s GROUP BY s.shop_id",
					TABLE_SALES_STATS, join(' AND ', $mainQueryConditions),  $sdata['id']
					);
					break;
			}

			$storeIds[] = $sdata['id'];

			$rs = $db->Execute($query);

			while (!$rs->EOF) {
				$data[] = array('store_total' => $rs->fields['count'], 'store_name' => $store_names[$rs->fields['shop_id']]);
				$rs->MoveNext();
			}$rs->Close();
		}

		if (empty($data)) {
			foreach ($stores as $sdata) {
				$data[] = array('store_total' => 0, 'store_name' => $store_names[$sdata['id']]);
			}
		}

		$response->topics = $data;
		$response->totalCount = count($data);
	}

	protected function loadProductSells(stdClass $response) {
		global $db,$store_handler,$system_status, $filter;
		$productId = $filter->_int($_GET['view_id']);

		$stores = $store_handler->getStores();

		$fromFilter 				= $this->get_post_param_default('ProductsFrom-' . $productId . '-Date', date('Y-m-d', strtotime('-1 month')));
		$toFIlter 					= $this->get_post_param_default('ProductsTo-' . $productId . '-Date', date('Y-m-d', strtotime('now')));
		$periodTypeFilter 			= $this->get_post_param_default('ProductsPeriodTypeFilter-' . $productId, 'day');
		$customer_status 			= $this->get_post_param_default('ProductsCustomersStatusFilter-' . $productId, '');
		$customer_status_condition 	= empty($customer_status) ? '' : " AND o.customers_status='" . $filter->_quote($customer_status) . "' ";
		$chartDisplayType 			= $this->get_post_param_default('ChartDisplayType-' . $productId, 'quantity_sold');
		$data = array();

		$selectStatement = array();
		switch ($periodTypeFilter) {
			case 'day':
				$selectStatement[] = 'DATE(o.date_purchased) as day';
				break;
			case 'month':
				$selectStatement[] = 'CONCAT(YEAR(o.date_purchased), "-", MONTH(o.date_purchased)) as day';
				break;
			default:
				$selectStatement[] = 'YEAR(o.date_purchased) as day';
				break;
		}

		switch ($chartDisplayType) {
			case 'quantity_sold':
				$selectStatement[] = 'sum(op.products_quantity) as count';
				break;
			case 'amount':
				$selectStatement[] = 'sum(op.products_price) as count';
				break;
		}

		$storeIds = array();
		foreach ($stores as $sdata) {
			$storeIds[] = $sdata['id'];
			$rs = $db->Execute("SELECT " . join(',', $selectStatement) . " FROM " . TABLE_ORDERS_PRODUCTS . " op " .
					"LEFT JOIN " . TABLE_ORDERS . " AS o USING(orders_id) " .
                    " WHERE o.date_purchased >= '" . $filter->_quote($fromFilter) . "' AND o.date_purchased <= '" . $filter->_quote($toFIlter) . "'" .
					" and o.shop_id='".$sdata['id']."' AND op.products_id='" . $productId . "' " . $customer_status_condition . " GROUP BY day");
			while (!$rs->EOF) {
				$data[$rs->fields['day']][$sdata['id']] = $rs->fields['count'];
				$data[$rs->fields['day']]['date'] = $rs->fields['day'];
				$rs->MoveNext();
			}$rs->Close();
		}

		foreach ($data as $date => &$customerData) {
			foreach ($storeIds as $id) {
				if (!isset($customerData[$id])) {
					$customerData[$id] = '0.0000';
				}
			}
			$total = 0;
			foreach ($customerData as $key => $count) {
				if (!is_numeric($key)) {
					continue;
				}
				$total += $count;
			}

			$data[$date]['total'] = sprintf("%.4f", $total);
			ksort($customerData);
		}

		if (empty($data)) {
			foreach ($stores as $sdata) {
				$data[] = array($sdata['id'] => 0, 'date' => date('Y-m-d', strtotime('now')));
			}
		}

		// Sort by date
		$data = $this->normalizeData($data, $periodTypeFilter);
		// Reset data
		$data = array_values($data);
		$response->topics = $data;
		$response->totalCount = count($data);
	}

	protected function loadProductSellsByStore(stdClass $response) {
		global $db,$store_handler,$system_status, $filter;
		$productId = $filter->_int($_GET['view_id']);

		$store_names = array();
		$stores = $store_handler->getStores();

		foreach ($stores as $store_data) {
			$store_names[$store_data['id']] = $store_data['text'];
		}

		$fromFilter 				= $this->get_post_param_default('ProductsFrom-' . $productId. '-Date', date('Y-m-d', strtotime('-1 month')));
		$toFIlter 					= $this->get_post_param_default('ProductsTo-'.$productId.'-Date', date('Y-m-d', strtotime('now')));
		$periodTypeFilter 			= $this->get_post_param_default('ProductsPeriodTypeFilter-'.$productId, 'day');
		$customer_status 			= $this->get_post_param_default('ProductsCustomersStatusFilter-'.$productId, '');
		$customer_status_condition 	= empty($customer_status) ? '' : " AND o.customers_status='" . $filter->_quote($customer_status) . "' ";
		$chartDisplayType 			= $this->get_post_param_default('ChartDisplayType-'.$productId, 'quantity_sold');
		$data = array();

		$selectStatement = array('shop_id');
		switch ($chartDisplayType) {
			case 'quantity_sold':
				$selectStatement[] = 'sum(op.products_quantity) as count';
				break;
			case 'amount':
				$selectStatement[] = 'sum(op.products_price) as count';
				break;
		}

		foreach ($stores as $sdata) {
			$rs = $db->Execute("SELECT " . join(',', $selectStatement) . " FROM " . TABLE_ORDERS_PRODUCTS . " op ".
					"LEFT JOIN " . TABLE_ORDERS . " AS o USING(orders_id) " .
					 " WHERE o.date_purchased >= '" . $filter->_quote($fromFilter) . "' AND o.date_purchased <= '" . $filter->_quote($toFIlter) . "'" .
					" and o.shop_id='".$sdata['id']."' AND op.products_id='" . $productId . "' " . $customer_status_condition . " GROUP BY o.shop_id");
			while (!$rs->EOF) {
				$amount = ($rs->fields['count']) ? round($rs->fields['count'],2) : '0';
				$data[] = array('store_total' => $amount, 'store_name' => $store_names[$rs->fields['shop_id']]);
				$rs->MoveNext();
			}$rs->Close();
		}

		if (empty($data)) {
			foreach ($stores as $sdata) {
				$data[] = array('store_total' => 0, 'store_name' => $store_names[$sdata['id']]);
			}
		}

		$response->topics = $data;
		$response->totalCount = count($data);
	}

	protected function loadCustomersData(stdClass $response) {
		global $db,$store_handler,$system_status, $filter;

		$stores = $store_handler->getStores();

		$fromFilter 			= $this->get_post_param_default('CustomersFromDate', date('Y-m-d', strtotime('-1 month')));
		$toFIlter 				= $this->get_post_param_default('CustomersToDate', date('Y-m-d', strtotime('now')));
		$periodTypeFilter 		= $this->get_post_param_default('CustomersPeriodTypeFilter', 'day');
		$customerDobFrom 		= $this->get_post_param_default('CustomerDobFromFilterDate', '');
		$customerDobTo 			= $this->get_post_param_default('CustomerDobToFilterDate', '');
		$customerCountryCode	= $this->get_post_param_default('CustomersCountryCodeFilter', '');
		$customer_status 		= $this->get_post_param_default('CustomersStatusFilter', '');
		$customer_status_condition = empty($customer_status) ? '' : "customers_status='" . $filter->_quote($customer_status) . "' ";
		$data = array();

		$selectStatement = '';
		switch ($periodTypeFilter) {
			case 'day':
				$selectStatement = 'DATE(date_added)';
				break;
			case 'month':
				$selectStatement = 'CONCAT(YEAR(date_added), "-", MONTH(date_added))';
				break;
			default:
				$selectStatement = 'YEAR(date_added)';
				break;
		}

		$mainQueryConditions = array(
			"date_added >= '" . $filter->_quote($fromFilter) . "'",
			"date_added <= '" . $filter->_quote($toFIlter) . "'",
		);

		if (!empty($customer_status_condition)) {
			$mainQueryConditions[] = $customer_status_condition;
		}

		$subqueryConditions = array();

		// Conditions based on address table
		if (!empty($customerDobFrom)) {
			$subqueryConditions[] = '`customers_dob`>="' . $filter->_quote($customerDobFrom) . '"';
		}

		if (!empty($customerDobTo)) {
			$subqueryConditions[] = '`customers_dob`<="' . $filter->_quote($customerDobTo) . '"';
		}

		if (!empty($customerCountryCode)) {
			$subqueryConditions[] = '`customers_country_code`="' . $filter->_quote($customerCountryCode) . '"';
		}

		if (!empty($subqueryConditions)) {
			$customersIds = array();
			$customerIdsQuery = sprintf('SELECT customers_id FROM %s WHERE %s GROUP BY customers_id', TABLE_CUSTOMERS_ADDRESSES, join(' AND ', $subqueryConditions));
			$rs = $db->Execute($customerIdsQuery);
			while (!$rs->EOF) {
				$customersIds[] = $rs->fields['customers_id'];
				$rs->MoveNext();
			}$rs->Close();

			// If there are no customers for given filters append fake ID so the query will return zero results
			// Otherwise we will shold wrong results
			if (empty($customersIds)) {
				$customersIds[] = 0;
			}

			$mainQueryConditions[] = sprintf('customers_id IN (%s)', join(',', $customersIds));
		}
		// End conditions based on address table

		$storeIds = array();
		foreach ($stores as $sdata) {
			$storeIds[] = $sdata['id'];
			$query = 'SELECT COUNT(*) as count, %s as day FROM %s WHERE %s AND shop_id="%s"  GROUP BY day';
			$rs = $db->Execute(sprintf($query, $selectStatement, TABLE_CUSTOMERS, join(' AND ', $mainQueryConditions), $sdata['id']));
			while (!$rs->EOF) {
				$data[$rs->fields['day']][$sdata['id']] = $rs->fields['count'];
				$data[$rs->fields['day']]['date'] = $rs->fields['day'];
				$rs->MoveNext();
			}$rs->Close();
		}

		foreach ($data as $date => &$customerData) {
			foreach ($storeIds as $id) {
				if (!isset($customerData[$id])) {
					$customerData[$id] = '0.0000';
				}
			}
			$total = 0;
			foreach ($customerData as $key => $count) {
				if (!is_numeric($key)) {
					continue;
				}
				$total += $count;
			}

			$data[$date]['total'] = sprintf("%.4f", $total);
			ksort($customerData);
		}

		if (empty($data)) {
			foreach ($stores as $sdata) {
				$data[] = array($sdata['id'] => 0, 'date' => date('Y-m-d', strtotime('now')));
			}
		}

		// Sort by date
		$data = $this->normalizeData($data, $periodTypeFilter);
		// Reset data
		$data = array_values($data);
		$response->topics = $data;
		$response->totalCount = count($data);
	}

	protected function loadCustomersDataByStore(stdClass $response) {
		global $db,$store_handler,$system_status, $filter;

		$store_names = array();
		$stores = $store_handler->getStores();

		foreach ($stores as $store_data) {
			$store_names[$store_data['id']] = $store_data['text'];
		}

		$fromFilter 				= $this->get_post_param_default('CustomersFromDate', date('Y-m-d', strtotime('-1 month')));
		$toFIlter 					= $this->get_post_param_default('CustomersToDate', date('Y-m-d', strtotime('now')));
		$periodTypeFilter 			= $this->get_post_param_default('CustomersPeriodTypeFilter', 'day');
		$customerDobFrom 			= $this->get_post_param_default('CustomerDobFromFilterDate', '');
		$customerDobTo 				= $this->get_post_param_default('CustomerDobToFilterDate', '');
		$customerCountryCode 		= $this->get_post_param_default('CustomersCountryCodeFilter', '');
		$customer_status 			= $this->get_post_param_default('CustomersStatusFilter', '');
		$customer_status_condition 	= empty($customer_status) ? '' : "customers_status='" . $filter->_quote($customer_status) . "' ";
		$data = array();

		$mainQueryConditions = array(
				"date_added >= '" . $filter->_quote($fromFilter) . "'",
				"date_added <= '" . $filter->_quote($toFIlter) . "'",
		);

		if (!empty($customer_status_condition)) {
			$mainQueryConditions[] = $customer_status_condition;
		}

		$subqueryConditions = array();

		// Conditions based on address table
		if (!empty($customerDobFrom)) {
			$subqueryConditions[] = '`customers_dob`>="' . $filter->_quote($customerDobFrom) . '"';
		}

		if (!empty($customerDobTo)) {
			$subqueryConditions[] = '`customers_dob`<="' . $filter->_quote($customerDobTo) . '"';
		}

		if (!empty($customerCountryCode)) {
			$subqueryConditions[] = '`customers_country_code`="' . $filter->_quote($customerCountryCode) . '"';
		}

		if (!empty($subqueryConditions)) {
			$customersIds = array();
			$customerIdsQuery = sprintf('SELECT customers_id FROM %s WHERE %s GROUP BY customers_id', TABLE_CUSTOMERS_ADDRESSES, join(' AND ', $subqueryConditions));
			$rs = $db->Execute($customerIdsQuery);
			while (!$rs->EOF) {
				$customersIds[] = $rs->fields['customers_id'];
				$rs->MoveNext();
			}$rs->Close();

			// If there are no customers for given filters append fake ID so the query will return zero results
			// Otherwise we will shold wrong results
			if (empty($customersIds)) {
				$customersIds[] = 0;
			}

			$mainQueryConditions[] = sprintf('customers_id IN (%s)', join(',', $customersIds));
		}
		// End conditions based on address table

		foreach ($stores as $sdata) {
			$query = sprintf("SELECT count(*) as count, shop_id FROM %s WHERE %s AND shop_id=%d GROUP BY shop_id", TABLE_CUSTOMERS, join(' AND ', $mainQueryConditions), $sdata['id']);
			$rs = $db->Execute($query);
			while (!$rs->EOF) {
				$amount = ($rs->fields['count']) ? round($rs->fields['count'],2) : '0';
				$data[] = array('store_total' => $amount, 'store_name' => $store_names[$rs->fields['shop_id']]);
				$rs->MoveNext();
			}$rs->Close();
		}

		if (empty($data)) {
			foreach ($stores as $sdata) {
				$data[] = array('store_total' => 0, 'store_name' => $store_names[$sdata['id']]);
			}
		}

		$response->topics = $data;
		$response->totalCount = count($data);
	}

	protected function loadOrdersDataByStore(stdClass $response) {
		global $db,$store_handler,$system_status, $filter;

		$store_names = array();
		$stores = $store_handler->getStores();

		foreach ($stores as $store_data) {
			$store_names[$store_data['id']] = $store_data['text'];
		}

		$fromFilter 		= $this->get_post_param_default('OrdersFromDate', date('Y-m-d', strtotime('-1 month')));
		$toFIlter 			= $this->get_post_param_default('OrdersToDate', date('Y-m-d', strtotime('now')));
		$customer_status 	= $this->get_post_param_default('OrdersCustomersStatusFilter', '');
        $payment_method 	= $this->get_post_param_default('OrdersPaymentMethodFilter', '');
        $order_status 	    = $this->get_post_param_default('OrdersStatusFilter', '');
		$customerDobFrom 	= $this->get_post_param_default('OrderDobFromFilterDate', '');
		$customerDobTo 		= $this->get_post_param_default('OrderDobToFilterDate', '');
		$customerCountryCode= $this->get_post_param_default('OrderCountryCodeFilter', '');
		$data = array();

		$whereConditions = array();
		$whereConditions[] = "o.date_purchased >= '" . $filter->_quote($fromFilter) . "'";
		$whereConditions[] = "o.date_purchased <='".$filter->_quote($toFIlter)."'";
    $select_ids = implode("','", $this->_relevant_status_ids);
    $whereConditions[] = "o.orders_status IN ('".$select_ids."')";
		$whereConditions[] = "o.orders_id = s.orders_id";
		if (!empty($customer_status))
			$whereConditions[] = "o.customers_status='" . $filter->_quote($customer_status) . "'";

        if (!empty($payment_method))
        {
            if((int)$payment_method > 0)
            {
                $payment_method = $db->GetOne("SELECT payment_code FROM ".TABLE_PAYMENT." WHERE payment_id=?", [$payment_method] );
            }
            $whereConditions[] = "o.payment_code='" . $filter->_quote($payment_method) . "'";
        }

        if (!empty($order_status))
        {
            $whereConditions[] = " o.orders_status in (".$order_status.")";
        }

		$subqueryConditions = array();

		// Conditions based on address table
		if (!empty($customerDobFrom)) {
			$subqueryConditions[] = '`customers_dob`>="' . $filter->_quote($customerDobFrom) . '"';
		}

		if (!empty($customerDobTo)) {
			$subqueryConditions[] = '`customers_dob`<="' . $filter->_quote($customerDobTo) . '"';
		}

		if (!empty($customerCountryCode)) {
			$subqueryConditions[] = '`customers_country_code`="' . $filter->_quote($customerCountryCode) . '"';
		}

		if (!empty($subqueryConditions)) {
			$customersIds = array();
			$customerIdsQuery = sprintf('SELECT customers_id FROM %s WHERE %s GROUP BY customers_id', TABLE_CUSTOMERS_ADDRESSES, join(' AND ', $subqueryConditions));
			$rs = $db->Execute($customerIdsQuery);
			while (!$rs->EOF) {
				$customersIds[] = $rs->fields['customers_id'];
				$rs->MoveNext();
			}$rs->Close();

			// If there are no customers for given filters append fake ID so the query will return zero results
			// Otherwise we will shold wrong results
			if (empty($customersIds)) {
				$customersIds[] = 0;
			}

			$whereConditions[] = sprintf('o.customers_id IN (%s)', join(',', $customersIds));
		}

		$rs = $db->Execute("SELECT sum(s.orders_stats_price) as amount, o.shop_id as shopid FROM " . TABLE_ORDERS . " o LEFT JOIN " . TABLE_ORDERS_STATS . " s USING(orders_id) " .
							" LEFT JOIN ". TABLE_CUSTOMERS_ADDRESSES . " ca ON ca.address_book_id=(SELECT MIN(address_book_id) as address_book_id FROM " . TABLE_CUSTOMERS_ADDRESSES . " cap WHERE o.customers_Id=cap.customers_id)" .
                            " WHERE  " . join(' AND ', $whereConditions) . "  GROUP BY shopid ORDER BY shopid");

		while (!$rs->EOF) {
			$amount = ($rs->fields['amount']) ? round($rs->fields['amount'],2) : '0';
			$data[] = array('store_total' => $amount, 'store_name' => $store_names[$rs->fields['shopid']]);
			$rs->MoveNext();
		}$rs->Close();

		if (empty($data)) {
			foreach ($stores as $sdata) {
				$data[] = array('store_total' => 0, 'store_name' => $store_names[$sdata['id']]);
			}
		}

		$response->topics = $data;
		$response->totalCount = count($data);
	}

	protected function loadOrdersData(stdClass $response) {
		global $db,$store_handler,$system_status, $filter;

		$stores = $store_handler->getStores();

		$fromFilter 		= $this->get_post_param_default('OrdersFromDate', date('Y-m-d', strtotime('-1 month')));
		$toFIlter 			= $this->get_post_param_default('OrdersToDate', date('Y-m-d', strtotime('now')));
		$periodTypeFilter 	= $this->get_post_param_default('OrdersPeriodTypeFilter', 'day');
		$customerDobFrom 	= $this->get_post_param_default('OrderDobFromFilterDate', '');
		$customerDobTo 		= $this->get_post_param_default('OrderDobToFilterDate', '');
		$customerCountryCode= $this->get_post_param_default('OrderCountryCodeFilter', '');
		$customer_status 	= $this->get_post_param_default('OrdersCustomersStatusFilter', '');
        $payment_method 	= $this->get_post_param_default('OrdersPaymentMethodFilter', '');
        $order_status 	    = $this->get_post_param_default('OrdersStatusFilter', '');
		$data = array();

		$selectStatement = '';
		switch ($periodTypeFilter) {
			case 'day':
				$selectStatement = 'DATE(o.date_purchased)';
				break;
			case 'month':
				$selectStatement = 'CONCAT(YEAR(o.date_purchased), "-", MONTH(o.date_purchased))';
				break;
			default:
				$selectStatement = 'YEAR(o.date_purchased)';
				break;
		}

		$whereConditions = array();
		$whereConditions[] = "o.date_purchased >= '" . $filter->_quote($fromFilter) . "'";
		$whereConditions[] = "o.date_purchased <='".$filter->_quote($toFIlter)."'";
    $select_ids = implode("','", $this->_relevant_status_ids);
    $whereConditions[] = "o.orders_status IN ('".$select_ids."')";
		$whereConditions[] = "o.orders_id = s.orders_id";
		if (!empty($customer_status))
            $whereConditions[] = "o.customers_status='" . $filter->_quote($customer_status) . "'";

        if (!empty($payment_method))
        {
            if((int)$payment_method > 0)
            {
                $payment_method = $db->GetOne("SELECT payment_code FROM ".TABLE_PAYMENT." WHERE payment_id=?", [$payment_method] );
            }
            $whereConditions[] = "o.payment_code='" . $filter->_quote($payment_method) . "'";
        }

        if (!empty($order_status))
        {
            $whereConditions[] = " o.orders_status in (".$order_status.")";
        }

		$subqueryConditions = array();

		// Conditions based on address table
		if (!empty($customerDobFrom)) {
			$subqueryConditions[] = '`customers_dob`>="' . $filter->_quote($customerDobFrom) . '"';
		}

		if (!empty($customerDobTo)) {
			$subqueryConditions[] = '`customers_dob`<="' . $filter->_quote($customerDobTo) . '"';
		}

		if (!empty($customerCountryCode)) {
			$subqueryConditions[] = '`customers_country_code`="' . $filter->_quote($customerCountryCode) . '"';
		}

		if (!empty($subqueryConditions)) {
			$customersIds = array();
			$customerIdsQuery = sprintf('SELECT customers_id FROM %s WHERE %s GROUP BY customers_id', TABLE_CUSTOMERS_ADDRESSES, join(' AND ', $subqueryConditions));
			$rs = $db->Execute($customerIdsQuery);
			while (!$rs->EOF) {
				$customersIds[] = $rs->fields['customers_id'];
				$rs->MoveNext();
			}$rs->Close();

			// If there are no customers for given filters append fake ID so the query will return zero results
			// Otherwise we will shold wrong results
			if (empty($customersIds)) {
				$customersIds[] = 0;
			}

			$whereConditions[] = sprintf('o.customers_id IN (%s)', join(',', $customersIds));
		}

		$storeIds = array();
		$customer_status_condition = empty($customer_status) ? '' : " AND o.customers_status='" . $filter->_quote($customer_status) . "' ";
		foreach ($stores as $sdata) {
			$storeIds[] = $sdata['id'];
			/*$rs = $db->Execute("SELECT sum(s.orders_stats_price) as amount, " .  $selectStatement . " as day FROM " . TABLE_ORDERS . " o, " . TABLE_ORDERS_STATS . " s ".
					"WHERE o.date_purchased >= '" . $filter->_quote($fromFilter) . "' and o.date_purchased <='".$filter->_quote($toFIlter)."' and o.shop_id='".$sdata['id']."' " .
					"and o.orders_id = s.orders_id " . $customer_status_condition . " GROUP BY day ORDER BY day");*/

			$sql = "SELECT sum(s.orders_stats_price) as amount, " .  $selectStatement . " as day FROM " . TABLE_ORDERS . " o LEFT JOIN " . TABLE_ORDERS_STATS . " s USING(orders_id) " .
                " LEFT JOIN ". TABLE_CUSTOMERS_ADDRESSES . " ca ON ca.address_book_id=(SELECT MIN(address_book_id) as address_book_id FROM " . TABLE_CUSTOMERS_ADDRESSES . " cap WHERE o.customers_Id=cap.customers_id)" .
                " WHERE  " . join(' AND ', $whereConditions) . " AND o.shop_id='".$sdata['id']."' GROUP BY day ORDER BY day";
			//error_log($sql);
			$rs = $db->Execute($sql);

			while (!$rs->EOF) {
				$data[$rs->fields['day']][$sdata['id']] = $rs->fields['amount'];
				$data[$rs->fields['day']]['date'] = $rs->fields['day'];
					$rs->MoveNext();
			}$rs->Close();
		}

		foreach ($data as $date => &$orderData) {
			foreach ($storeIds as $id) {
				if (!isset($orderData[$id])) {
					$orderData[$id] = '0.0000';
				}
			}
			$total = 0;
			foreach ($orderData as $key => $price) {
				if (!is_numeric($key)) {
					continue;
				}
				$total += $price;
			}

			$data[$date]['total'] = sprintf("%.4f", $total);
			ksort($orderData);
		}

		if (empty($data)) {
			foreach ($stores as $sdata) {
				$data[] = array($sdata['id'] => 0, 'date' => date('Y-m-d', strtotime('now')));
			}
		}
		// Sort by date
		$data = $this->normalizeData($data, $periodTypeFilter);
        // Reset data
        $data = array_values($data);
        $response->topics = $data;
        $response->totalCount = count($data);
	}

	/**
	 * Get $_POST param with $name and if it is not set return $default
	 * @param string $name
	 * @param string $default
	 * @return string
	 */
	protected function get_post_param_default($name, $default) {
		return ((isset($_POST[$name]) && $_POST[$name] !== '') ? $_POST[$name] : $default);
	}

	protected function normalizeData($data, $type) {
		$tmp = array();
		$format = 'Y';

		if ($type == 'day') {
			$format = 'Ymd';
		} else if ($type == 'month') {
			$format = 'Ym';
		}
		foreach ($data as $period => $periodData) {
			if ($format == 'Y') {
				$period = $period . '-01-01';
			}
			$date = date($format, strtotime($period));

			$tmp[$date] = $periodData;
		}

		ksort($tmp);
		return $tmp;
	}

  protected function getRelevantOrderStatusID($status_array) {


      $include_id = array();

      if (!is_array($status_array)) return $include_id;

      foreach ($status_array as $key => $val) {
          if ($val['data']['calculate_statistic'] == '1') {
              $include_id[]=$val['id'];
          }
      }

      return $include_id;
  }

}

$chartData = new ChartData();
echo $chartData->getJsonData();
die;
?>
