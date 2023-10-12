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

class zone extends system_status
{

	protected $_master_status = 'zone';

    function _defineFields()
    {
        global $db,$store_handler, $xtPlugin;

        $arr  = array();
        $stores = $store_handler->getStores();
        foreach ($stores as $sdata)
        {
            $arr['data']['_ZONE_CUSTOMERS_STATUS_shop_'.$sdata['id']] = '';
            $arr['data']['_ZONE_VAT_CUSTOMERS_STATUS_shop_'.$sdata['id']] = '';
        }

        $this->status_fields_array = serialize($arr);
	}

	function _getParams() {
		global $language;

		$params = array();

        $params['include']   = array ('status_id', 'status_class');

		foreach ($language->_getLanguageList() as $key => $val) {
			$header['status_name_'.$val['code']] = array('type' => '');
            $params['include'][]   = 'status_name_'.$val['code'];

			$header['status_image_'.$val['code']] = array('type' => '');
            $params['include'][]   = 'status_image_'.$val['code'];

			$header['language_code_'.$val['code']] = array('type' => 'hidden');
            $params['include'][]   = 'language_code_'.$val['code'];
		}

		$header['status_class'] = array('type' => 'hidden');
		$header['status_id'] = array('type' => 'hidden');

        if(!$this->url_data['edit_id'] && $this->url_data['new'] != true){

        }else{

            $edit_data = $this->getConfigHeaderData();

            if (count($edit_data['header'])>0) {
                $header = array_merge($header,$edit_data['header']);
                foreach ($edit_data['header'] as $key => $arr) {
                    $params['include'] = array_merge($params['include'],array($key));
                }

                $params['grouping'] = $edit_data['grouping'];
                $params['panelSettings']  = $edit_data['panelSettings'];
            }
        }

		$params['header']         = $header;
		$params['master_key']     = $this->_master_key;
		$params['default_sort']   = $this->_master_key;

		$params['exclude']        = array ('');

        return $params;
    }

    function getConfigHeaderData() {
        global $db,$store_handler, $xtPlugin;

        $stores = $store_handler->getStores();
        $header = array();
        $grouping = array();
        // query config_payment
        foreach ($stores as $sdata) {

            $store_names[] = 'SHOP_'.$sdata['id'];

            $dropdown_fields = array(
                '_ZONE_VAT_CUSTOMERS_STATUS','_ZONE_CUSTOMERS_STATUS'
            );

            foreach ($dropdown_fields as $field)
            {
                $required = false;

                $groupingPosition = 'SHOP_'.$sdata['id'];
                $grouping[$field.'_shop_'.$sdata['id']] = array('position' => $groupingPosition);
                // set header data
                $header[$field.'_shop_'.$sdata['id']] = $tmp_data = array(
                    'name' => $field.'_shop_'.$sdata['id'],
                    'text' => __define($field.'_TITLE'),
                    'masterkey' => false,
                    'lang' => false,
                    //'value' => _filterText($record->fields['config_value'], $type == 'textarea' ? 'notfull' : 'full'),
                    'hidden' => false,
                    'min' => null,
                    'max' => null,
                    'readonly' => false,
                    'required' => $required,
                    'type' => 'dropdown',
                    'url' => 'DropdownData.php?get=customers_status',
                    'renderer' => null
                );
            }
		}

        $panelSettings[] = array('position' => 'store_settings', 'text' => __text('TEXT_EXPORT_SETTINGS'), 'groupingPosition' => $store_names);
        return array('header'=>$header,'panelSettings'=>$panelSettings,'grouping'=>$grouping);
	}
}