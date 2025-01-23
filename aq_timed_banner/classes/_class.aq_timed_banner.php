<?php
defined('_VALID_CALL') or die('Direct Access is not allowed.');

class aq_timed_banner {
    
    protected $position;

    public function setPosition ($position) {
		$this->position = $position;
	}
    
    public function _getParams() {
        global $language;
        
        $csrf_param = '&sec='. $_SESSION['admin_user']['admin_key'];
		
        $header['banner_id'] = array('type' => 'hidden');
        $header['banner_name'] = array('type' => 'textfield');
        $header['banner_image'] = array('type' => 'mediafile');
        $header['banner_link'] = array('type' => 'textfield');
        $header['banner_start'] = array('type' => 'dateTime');
        $header['banner_end'] = array('type' => 'dateTime');
        $header['banner_status'] = array('type' => 'status');
        $header['sort_order'] = array('type' => 'textfield');
        
        $params = array(
            'header' => $header,
            'master_key' => 'banner_id',
            'default_sort' => 'sort_order',
            'SortField' => 'sort_order',
            'SortDir' => 'ASC',
            'display_deleteBtn' => true,
            'display_editBtn' => true
        );
        
        return $params;
    }
    
    public function _get($ID = 0) {
        global $xtPlugin, $db, $language;
        
        if ($this->position != 'admin') return false;
        
        if ($ID === 'new') {
            $obj = $this->_getParams();
            $ID = 0;
        } else {
            $sql = "SELECT * FROM ".DB_PREFIX."_aq_timed_banner WHERE banner_id = ?";
            $obj = $db->Execute($sql, array((int)$ID));
            if ($obj->RecordCount() == 1) {
                $obj = $obj->fields;
                foreach ($language->_getLanguageList() as $key => $val) {
                    $sql = "SELECT * FROM ".DB_PREFIX."_aq_timed_banner_description WHERE banner_id = ? AND language_code = ?";
                    $description = $db->Execute($sql, array((int)$ID, $val['code']));
                    if ($description->RecordCount() == 1) {
                        $obj['banner_title_'.$val['code']] = $description->fields['banner_title'];
                        $obj['banner_description_'.$val['code']] = $description->fields['banner_description'];
                    }
                }
            }
        }
        return $obj;
    }

    public function _set($data, $set_type='edit') {
        global $db, $language;
        
        if ($set_type == 'new') {
            $data['date_added'] = date('Y-m-d H:i:s');
            $obj = new adminDB_DataSave(DB_PREFIX.'_aq_timed_banner', $data, false, __CLASS__);
            $inserted_id = $obj->saveDataSet();
            
            foreach ($language->_getLanguageList() as $key => $val) {
                $desc_data = array(
                    'banner_id' => $inserted_id,
                    'language_code' => $val['code'],
                    'banner_title' => $data['banner_title_'.$val['code']],
                    'banner_description' => $data['banner_description_'.$val['code']]
                );
                $obj = new adminDB_DataSave(DB_PREFIX.'_aq_timed_banner_description', $desc_data, false, __CLASS__);
                $obj->saveDataSet();
            }
            
            return $inserted_id;
        } elseif ($set_type == 'edit') {
            $obj = new adminDB_DataSave(DB_PREFIX.'_aq_timed_banner', $data, 'banner_id', __CLASS__);
            $obj->saveDataSet();
            
            foreach ($language->_getLanguageList() as $key => $val) {
                $desc_data = array(
                    'banner_id' => $data['banner_id'],
                    'language_code' => $val['code'],
                    'banner_title' => $data['banner_title_'.$val['code']],
                    'banner_description' => $data['banner_description_'.$val['code']]
                );
                $obj = new adminDB_DataSave(DB_PREFIX.'_aq_timed_banner_description', $desc_data, array('banner_id','language_code'), __CLASS__);
                $obj->saveDataSet();
            }
            
            return true;
        }
    }
    
    public function _getList() {
        global $db;
        
        $now = date('Y-m-d H:i:s');
        $sql = "SELECT * FROM ".DB_PREFIX."_aq_timed_banner WHERE banner_status = 1 
                AND banner_start <= ? AND banner_end >= ? 
                ORDER BY sort_order ASC";
        
        $result = $db->Execute($sql, array($now, $now));
        $list = array();
        
        if ($result->RecordCount() > 0) {
            while (!$result->EOF) {
                $list[] = array_merge($result->fields, array(
                    'banner_image_url' => _SRV_WEB_IMAGES.$result->fields['banner_image']
                ));
                $result->MoveNext();
            }
        }
        
        return $list;
    }
}
