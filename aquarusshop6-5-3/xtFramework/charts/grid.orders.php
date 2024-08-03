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


class grid_orders {

    private $_relevant_status_ids = array();

    function __construct() {
        global $system_status;

        $order_status_values = $system_status->values['order_status'];
        $this->_relevant_status_ids = $this->getRelevantOrderStatusID($order_status_values);

    }

    function _getTotalGrid(){
        global $store_handler;

        $stores = $store_handler->getStores();
        
        $data = array();
        foreach ($stores as $sdata) {

            $customers = $this->_totalCustomers($sdata['id']);
            $products = $this->_totalProducts($sdata['id']);
            $today = $this->_totalAmount($sdata['id'], 'today');
            $yesterday = $this->_totalAmount($sdata['id'], 'yesterday');
            $week = $this->_totalAmount($sdata['id'], 'week');
            $month = $this->_totalAmount($sdata['id'], 'month');
            $year = $this->_totalAmount($sdata['id'], 'year');
            
            $data[] = array(
                $sdata['text'],
                $customers['customers'],
                $products['products'],
                $today['sales'],
                $yesterday['sales'],
                $week['sales'],
                $month['sales'],
                $year['sales']
            );
            
        }

        return $data;
        
    }
	
	function _totalAmount($shopid='', $totalRange='') {
		global $db,$store_handler;		

		$year  = date('Y');
		$month = date('n');
		$week = date('W');
		$day = date('z')+1; // fix differnt start day for php and mysql function
		
		switch ($totalRange){
			
			case 'today':
				$dateRange= 'YEAR(o.date_purchased) = ' . $year . ' and DAYOFYEAR(o.date_purchased) =' . $day;
				break;
			case 'yesterday':
				$day = $day -1;
				if($day<=0){
					$year =  $year -1;
                    $day =365;
                    if ((date("z", mktime(0, 0, 0, 12, 31, $year))) == 365) $day = 366 ; // fix leap year
				}
				$dateRange= 'YEAR(o.date_purchased) = ' . $year . ' and DAYOFYEAR(o.date_purchased) =' . $day;
				break;
			case 'week':
				$dateRange= 'YEAR(o.date_purchased) = ' . $year . ' and WEEKOFYEAR(o.date_purchased) =' . $week;
				break;
			case 'month':
				$dateRange= 'year(o.date_purchased) = ' . $year . ' and MONTH(o.date_purchased) = ' . $month ;
				break;
			case 'year':
				$dateRange= 'year(o.date_purchased) = ' . $year;
		}

        $data = array('sales' => 0, 'orders' => 0);
		if(count($this->_relevant_status_ids))

        {
            $select_ids = implode("','", $this->_relevant_status_ids);

            $rs = $db->Execute("SELECT sum(s.orders_stats_price) AS sales, count(s.orders_id) AS orders, o.shop_id AS shopid 
                            FROM " . TABLE_ORDERS . " o, " . TABLE_ORDERS_STATS . " s 
                            WHERE " . $dateRange . " AND o.orders_id = s.orders_id AND o.shop_id = " . $shopid . "
                            AND o.orders_status IN ('".$select_ids."')
                            GROUP BY shopid");

        $data['sales'] = $rs->fields['sales'] ?? 0;
        $data['orders'] = $rs->fields['orders'] ?? 0;

        $data['sales'] = round($data['sales'], 2);
        }
		return $data;
	}

    function _totalCustomers($shopid='') {
		global $db,$store_handler;

        $rs = $db->Execute("SELECT count(*) as amount FROM " . TABLE_CUSTOMERS . "
                            WHERE shop_id = '".$shopid."'  GROUP BY shop_id");

        $data = array();
        $data['customers'] = $rs->fields['amount'];
        return $data;
    }

    function _totalProducts($shopid='') {
		global $db,$store_handler;

        $rs = $db->Execute("SELECT count(*) as amount FROM " . TABLE_PRODUCTS . "
                            WHERE products_owner = '".$shopid."'  GROUP BY products_owner");

        $data = array();
        $data['products'] = $rs->fields['amount'] ?? 0;
        return $data;
    }

    function getRelevantOrderStatusID($status_array) {


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
?>