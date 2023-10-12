<?php
/*
 #########################################################################
 #                       xt:Commerce Shopsoftware
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # Copyright 2007-2018 xt:Commerce International Ltd. All Rights Reserved.
 # This file may not be redistributed in whole or significant part.
 # Content of this file is Protected By International Copyright Laws.
 #
 # ~~~~~~ xt:Commerce Shopsoftware IS NOT FREE SOFTWARE ~~~~~~~
 #
 # http://www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # @copyright xt:Commerce International Ltd., www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # xt:Commerce International Ltd., Kafkasou 9, Aglantzia, CY-2112 Nicosia
 #
 # office@xt-commerce.com
 #
 #########################################################################
 */

defined('_VALID_CALL') or die('Direct Access is not allowed.');

class cross_selling_products extends product {

	protected $_table_xsell = TABLE_PRODUCTS_CROSS_SELL;

	function _getParams() {
		global $language;

		$params = array();
		$header['products_id'] = array('type'=>'hidden');

		$params['display_checkCol']  = true;
		$params['display_editBtn']  = false;
		$params['display_newBtn']  = false;
		$params['display_GetSelectedBtn'] = true;

        $extF = new ExtFunctions();
        $rowActionsFunctions['sort2_up'] = 'sort2_up("yes")';
        $rowActionsFunctions['sort2_down'] = 'sort2_down("yes")';
        $rowActions[] = array('iconCls' => 'sort2_up', 'qtipIndex' => 'qtip1', 'tooltip' => TEXT_SORT_UP);
        $rowActions[] = array('iconCls' => 'sort2_down', 'qtipIndex' => 'qtip1', 'tooltip' => TEXT_SORT_DOWN);
        $params['resort_key'] = $this->_master_key;

        $params['rowActions'] = $rowActions;
        $params['rowActionsFunctions'] = $rowActionsFunctions;

		$params['display_searchPanel']  = true;		
		
		$params['header']         = $header;
		$params['master_key']     = $this->_master_key;
		$params['default_sort']   =  $this->_master_key;

		if($this->url_data['pg']=='overview' && !$this->url_data['edit_id'] && $this->url_data['new'] != true){
			$params['include'] = array ('products_id', 'products_name_'.$language->code, 'products_model', 'products_price', 'products_status');
		}

		return $params;
	}

	function _getIDs($id) {
		global $xtPlugin, $db, $language, $seo;

		$query = "select products_id_cross_sell from ".$this->_table_xsell." where products_id = ? order by sort_order";

		$record = $db->Execute($query,array((int)$id));
		if ($record->RecordCount() > 0) {

			while(!$record->EOF){
				$records = $record->fields;
				$data[] = $records['products_id_cross_sell'];
				$record->MoveNext();
			} $record->Close();
		}

		return $data;
	}

	function _get($ID = 0) {
		global $xtPlugin, $db, $language;
        $obj = new stdClass;
		if ($this->position != 'admin') return false;
		
		if(!$this->url_data['query']){

			if ($this->url_data['get_data']){
	
				$search_result = $this->_getIDs($this->url_data['products_id']);
	
				if(is_array($search_result) && count($search_result) != 0){
					$sql_where = " products_id IN (".implode(',', $search_result).")";

					$table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key, $sql_where, '', $this->perm_array);
					$data = $table_data->getData();
                    foreach ($search_result as $ks => &$v)
                    {
                        foreach($data as $kp => $p)
                        {
                            if($p['products_id'] == $v)
                            {
                                $v = $p;
                                unset($data[$kp]);
                                break;
                            }
                        }
                    }
                    $data = $search_result;
				}else{
					$data = array();
				}
			}else{
				$table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key, '', '', $this->perm_array);
				$data = $table_data->getHeader();
			}

		}else{
		
			$sql_where = " products_id!=".(int)$this->url_data['products_id'];
	
					
			if(XT_MASTER_SLAVE_ACTIVE==1 && XT_CROSS_SELLING_USE_SLAVES!=1){
				
				$sql_where .= " and (products_master_model = '' or products_master_model IS NULL)";
				
			}				
			
			if ($this->url_data['query']) {
				$search_result = $this->_getSearchIDs($this->url_data['query']);
				if(is_array($search_result) && count($search_result)>0){
					$sql_where .= " and products_id IN (".implode(',', $search_result).")";
				}
			}
	
			if (!isset($this->sql_limit)) {
				$this->sql_limit = "0,25";
			}			

			$table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key, $sql_where, $this->sql_limit, $this->perm_array);
	
			if ($this->url_data['get_data']){
				$data = $table_data->getData();

			}else{
				$data = $table_data->getHeader();
			}		
		
		}
		
		if($table_data->_total_count!=0 || !$table_data->_total_count)
		$count_data = $table_data->_total_count;
		else
		$count_data = count($data);

		$obj->totalCount = $count_data;
		$obj->data = $data;

		return $obj;
	}

	function _set($id, $set_type = 'edit') {
		global $db,$language,$filter;

        $obj = new stdClass;
        $obj->success = true;

        $p_id = (int)$this->url_data['products_id'];
        $cs_id = (int)$id;
        if($p_id && $cs_id)
        {
            $data = array();
            $data['products_id'] = $p_id;
            $data['products_id_cross_sell'] = $cs_id;

            $o = new adminDB_DataSave($this->_table_xsell, $data, false, __CLASS__);
            $obj = $o->saveDataSet();
        }

		return $obj;
	}	
	
	function _unset($id = 0) {
	    global $db, $xtPlugin;
		$pID=(int)$this->url_data['products_id'];
		$id=(int)$id;
		
	    if (!$id || !$pID || $this->position != 'admin') return false;
	    $db->Execute("DELETE FROM ". $this->_table_xsell ." WHERE products_id_cross_sell = ? and products_id = ?",array($id,$pID) );
	}

    function sort ($data)
    {
        $obj = new stdClass();
        $obj->success = false;

        $elements = array_filter(explode(',', $this->url_data['m_ids']));
        $mIds = $this->getCurrentIds($this->url_data['master_item_id']);
        $elementsOrdered = array();
        foreach ($mIds->currentIds as $cid)
        {
            if(in_array($cid, $elements))
            {
                $elementsOrdered[] = $cid;
            }
        }
        if ($this->url_data['pos'] == 'down')
        {
            $elementsOrdered = array_reverse($elementsOrdered);
        }
        foreach($elementsOrdered as $currentElement)
        {
            $mIds = $this->getCurrentIds($this->url_data['master_item_id']);

            $sortPos = array_search($currentElement, $mIds->currentIds);
            $count = count($mIds->currentIds);

            if ($this->url_data['pos'] == 'up' && $sortPos > -1)
            {
                $newPos = $sortPos - 1;
            }

            if ($this->url_data['pos'] == 'down' && $sortPos < $count)
            {
                $newPos = $sortPos + 1;
            }

            if ($newPos !== false)
            {
                $swapElement = $mIds->currentIds[$newPos];
                $obj = new stdClass();

                if (($currentElement && $newPos !== false) && ($swapElement && $sortPos !== false))
                {
                    $currentData = array('products_id' => $this->url_data['master_item_id'], 'products_id_cross_sell' => $currentElement, 'sort_order' => $newPos);
                    $swapData = array('products_id' => $this->url_data['master_item_id'], 'products_id_cross_sell' => $swapElement, 'sort_order' => $sortPos);
                    $this->_setSortOrder($currentData);
                    $this->_setSortOrder($swapData);

                    $obj->success = true;
                }
                else {
                    $obj->success = false;
                    break;
                }
            }
        }

        if (!$obj->success)
            $obj->failed = true;
    }

    public function getCurrentIds($pid) {
        global $db;

        $record = $db->Execute(
            "SELECT * FROM ".$this->_table_xsell." WHERE products_id = ? order by sort_order ",
            array($pid)
        );
        if ($record->RecordCount() > 0) {
            while(!$record->EOF){
                $currentIds[] = $record->fields['products_id_cross_sell'];
                if (!$record->fields['sort_order'] > 0 || count($currentIds)-1 != $record->fields['sort_order']) {
                    $sortData = $record->fields;
                    $sortData['sort_order'] = count($currentIds)-1;
                    $this->_setSortOrder($sortData);
                    $sortCount = $sortData['sort_order'];
                } else {
                    $sortCount = $record->fields['sort_order'];
                }

                $sortedIds[$record->fields['products_id_cross_sell']] = $sortCount;

                $record->MoveNext();
            } $record->Close();
        }
        $obj = new stdClass();
        $obj->sortedIds = $sortedIds;
        $obj->currentIds = $currentIds;
        return $obj;
    }

    function _setSortOrder ($data)
    {
        global $db;

        $where = ' products_id = ? and products_id_cross_sell = ?';
        $where2 = " products_id = ".$db->Quote($data['products_id'])." and products_id_cross_sell = ".$db->Quote($data['products_id_cross_sell']);
        $qryCheck = "SELECT * FROM " . $this->_table_xsell . " WHERE " . $where;
        $record = $db->Execute($qryCheck, array($data['products_id'], $data['products_id_cross_sell']));
        if ($record->RecordCount() == 0) {
            $default = array(
                'products_id' => $data['products_id'],
                'products_id_cross_sell' => $data['products_id_cross_sell'],
                'sort_order' =>$data['sort_order']
            );
            $data = array_merge($default, $data);
            $db->AutoExecute($this->_table_xsell, $data, 'INSERT');
        } else {
            $newData['sort_order'] = $data['sort_order'];
            $db->AutoExecute($this->_table_xsell, $newData, 'UPDATE', $where2);
        }

        $obj = new stdClass();
        $obj->success = true;
        return $obj;
    }
}
