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


class xt_im_export_log extends LogHandler
{

    public function _getParams()
    {
        $params = parent::_getParams();

        $params['display_editBtn'] = false;
        $params['display_deleteBtn']  = true;

		return $params;
	}


    public function _get($ID = 0) {
		global $xtPlugin, $db, $language;

		if ($this->position != 'admin') return false;

        if ($ID === 'new') {
            $obj = $this->_set(array(), 'new');
            $ID = $obj->new_id;
        }

        $ID=(int)$ID;

        $table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key,"message_source='xt_im_export' AND identification=".(int)$this->url_data['id'], $this->sql_limit,'','','');
		

		if ($this->url_data['get_data']){
			$data = $table_data->getData();
        }elseif($ID){
            $data = $table_data->getData($ID);

            if (strlen($data[0]['data'])>0) {
                $callback_data = unserialize($data[0]['data']);
                $callback = array();
                if (is_array($callback_data)) {
                    foreach ($callback_data as $key=>$val) {
                        define('TEXT_DATA_'.strtoupper($key),$key);
                        $callback['data_'.$key] = $val;
                    }

                    unset($data[0]['data']);
                    $data[0] = array_merge($data[0],$callback);
                }
            }

		}else{
			$data = $table_data->getHeader();
		}

        $obj = new stdClass;
        $obj->totalCount = count($data);
		$obj->data = $data;

		return $obj;
	}

    function _unset($id = 0)
    {
        global $db;

        if ($this->position != 'admin') return false;

        if ($id == 'all')
        {
            $db->Execute("DELETE FROM ". $this->_table ." WHERE message_source='xt_im_export' AND identification=?", array($this->url_data['id']));
	}
        else {
		$id=(int)$id;
		if (!is_int($id)) return false;
		$db->Execute("DELETE FROM ". $this->_table ." WHERE ".$this->_master_key." = '".$id."'");
	}
    }

}
