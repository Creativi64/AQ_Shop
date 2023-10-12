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

class payment_query extends SQL_query
{
	function __construct() {
		global $xtPlugin;

        parent::__construct();

		$this->setSQL_TABLE(TABLE_PAYMENT . " p ");
		($plugin_code = $xtPlugin->PluginCode(__CLASS__.':class_top')) ? eval($plugin_code) : false;
	}

	function getSQL_query($sql_cols = '', $filter_type='string')
    {
        return parent::getSQL_query($sql_cols, $filter_type);
	}


	//////////////////////////////////////////////////////

	function F_Language($lang_code=''){
		global $db, $language, $store_handler;

		if(empty($lang_code))
			$lang_code = $language->code;

		$this->setSQL_TABLE("LEFT JOIN " . TABLE_PAYMENT_DESCRIPTION . " pd ON p.payment_id = pd.payment_id AND pd.payment_description_store_id =" . $db->Quote($store_handler->shop_id) ." AND pd.language_code = " . $db->Quote($lang_code) ."");
		// putting language_code into on-clause is faster, but
		$this->setSQL_WHERE(" 1 "); // next filter F_GroupCheck creates where-statement beginning with " and ..."
        // that why qnd "where 1"
	}

	function F_GroupCheck () {
		global $customers_status;

		if (_SYSTEM_GROUP_CHECK == 'true' && isset($customers_status->customers_status_id)) {
			$perm_array = array(
				array(
					'type'=>'group_permission',
					'key'=>'payment_id',
					'value_type'=>'payment',
					'pref'=>'p'
				)
			);

			$perm = new item_permission($perm_array);

			$this->setSQL_TABLE($perm->_table);
			$this->setSQL_WHERE($perm->_where);
		}

	}

	function F_StoreCheck() {
		global $store_handler;

		if (_SYSTEM_GROUP_CHECK == 'true' && isset($store_handler->shop_id)) {
			$perm_array = array(
				array(
					'type'=>'shop',
					'key'=>'payment_id',
					'value_type'=>'payment',
					'pref'=>'p'
				)
			);

			$perm = new item_permission($perm_array);

			$this->setSQL_TABLE($perm->_table);
			$this->setSQL_WHERE($perm->_where);
		}
	}
	
	function F_MultiCheck ($params='') {
		global $xtPlugin;

	    ($plugin_code = $xtPlugin->PluginCode(__CLASS__.':F_MultiCheck')) ? eval($plugin_code) : false;
	}

    /**
     * @deprecated
     */
    function F_MobileCheck() {

        if (isset($_SESSION['isMobile']) && $_SESSION['isMobile']==true) {
            $this->setSQL_WHERE("and 1 = 1 ");
        }

    }
	
	function F_ShippingCheck() {
		global $store_handler;

		if (isset($_SESSION['selected_shipping'])) {
			$perm_array = array(
				array(
					'type'=>'shipping_permission',
					'key'=>'payment_id',
					'value_type'=>'payment',
					'pref'=>'p'
				)
			);

			$perm = new item_permission($perm_array);

			$this->setSQL_TABLE($perm->_table);
			$this->setSQL_WHERE($perm->_where);
		}
	}


	function F_StatusCheck() {
		$this->setSQL_WHERE("and p.status = 1 ");
	}
}
