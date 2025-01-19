<?php
defined('_VALID_CALL') or die('Direct Access is not allowed.');

class a_q_timed_banner {

    public function setPosition ($position) {
		$this->position = $position;
	}
    public function _getParams() {
        $params = array();

        $header['text'] 			= array('type'=>'text');
		$params['header']         	= $header;
        $params['master_key']		= 'id';
        return $params;
    }

    public function _get($id = 0) {
        
		$obj = new stdClass;
		  
		$obj->totalCount = 0;
		$obj->data = null;
		
		return $obj;
    }

    /*
    protected $_table = 'aq_timed_banner';
    protected $_master_key = 'banner_id';
    protected $_language_key = 'language_code';
    protected $_language_fields = ['banner_title', 'banner_description'];
 

    public function setPosition ($position) {
		$this->position = $position;
	}
	
    public function _getParams() {
        $header = [];
        $header['banner_id'] = ['type' => 'hidden'];
        $header['banner_name'] = [
            'type' => 'textfield',
            'required' => true,
            'width' => '300px'
        ];
        $header['banner_image'] = [
            'type' => 'mediafile',
            'required' => true,
            'width' => '300px'
        ];
        $header['banner_link'] = [
            'type' => 'textfield',
            'width' => '300px'
        ];
        $header['banner_start'] = [
            'type' => 'dateTime',
            'required' => true,
            'width' => '150px'
        ];
        $header['banner_end'] = [
            'type' => 'dateTime', 
            'required' => true,
            'width' => '150px'
        ];
        $header['banner_status'] = [
            'type' => 'status',
            'width' => '50px'
        ];
        $header['sort_order'] = [
            'type' => 'textfield',
            'width' => '50px'
        ];
        
        // Add language fields
        global $language;
        foreach ($language->_getLanguageList() as $key => $val) {
            $header['banner_title_'.$val['code']] = [
                'type' => 'textfield',
                'width' => '300px',
                'language' => $val['code']
            ];
            $header['banner_description_'.$val['code']] = [
                'type' => 'textarea',
                'width' => '300px', 
                'height' => '100px',
                'language' => $val['code']
            ];
        }

        $params = [
            'header' => $header,
            'master_key' => $this->_master_key,
            'default_sort' => 'sort_order',
            'SortField' => 'sort_order',
            'SortDir' => 'ASC',
            'display_deleteBtn' => true,
            'display_editBtn' => true,
            'display_newBtn' => true,
            'exclude_list' => ['banner_description']
        ];

        return $params;
    }

    public function _getList($search_filter = '') {
        global $db;
        
        $sql_where = "";
        if ($search_filter != '') {
            $sql_where .= " WHERE banner_name LIKE '%".$search_filter."%' ";
        }

        $sql = "SELECT * FROM ".DB_PREFIX."_".$this->_table.$sql_where;
        $result = $db->Execute($sql);
        
        $list = [];
        if ($result->RecordCount() > 0) {
            while (!$result->EOF) {
                $list[] = array_merge($result->fields, [
                    'banner_image_url' => _SRV_WEB_IMAGES.$result->fields['banner_image']
                ]);
                $result->MoveNext();
            }
        }
        return $list;
    }

    public function _delete($id) {
        global $db;
        
        if ($id && (int)$id > 0) {
            // Delete banner
            $db->Execute("DELETE FROM ".DB_PREFIX."_".$this->_table." WHERE ".$this->_master_key." = ?", [(int)$id]);
            // Delete descriptions
            $db->Execute("DELETE FROM ".DB_PREFIX."_".$this->_table."_description WHERE ".$this->_master_key." = ?", [(int)$id]);
            return true;
        }
        return false;
    }

    public function _get($id = 0) {
        global $db;
        
        if ($id === 'new') {
            return array();
        }
        
        $sql = "SELECT * FROM ".DB_PREFIX."_aq_timed_banner WHERE banner_id = ?";
        $result = $db->Execute($sql, array((int)$id));
        
         if ($result->RecordCount() > 0) {
            $data = $result->fields;
            
            // Get language data
            $sql = "SELECT * FROM ".DB_PREFIX."_aq_timed_banner_description 
                   WHERE banner_id = ?";
            $result = $db->Execute($sql, array((int)$id));
            
            while (!$result->EOF) {
                $data['banner_title_'.$result->fields['language_code']] = $result->fields['banner_title'];
                $data['banner_description_'.$result->fields['language_code']] = $result->fields['banner_description'];
                $result->MoveNext();
            }
            
            return $data;
        }
        return false;
    }
    
    public function _set($data, $mode = 'edit') {
        global $db;
        
        // Handle file upload if new image
        if (isset($_FILES['banner_image']) && $_FILES['banner_image']['size'] > 0) {
            $upload_dir = _SRV_WEBROOT.'media/images/banners/';
            $filename = time() . '_' . $_FILES['banner_image']['name'];
            
            if (move_uploaded_file($_FILES['banner_image']['tmp_name'], $upload_dir.$filename)) {
                $data['banner_image'] = 'banners/'.$filename;
            }
        }
        
        if ($mode == 'new') {
            $data['date_added'] = date('Y-m-d H:i:s');
            $db->AutoExecute(DB_PREFIX.'_aq_timed_banner', $data, 'INSERT');
            $banner_id = $db->Insert_ID();
        } else {
            $db->AutoExecute(DB_PREFIX.'_aq_timed_banner', $data, 'UPDATE', 'banner_id = '.(int)$data['banner_id']);
            $banner_id = $data['banner_id'];
        }
        
        // Save language data
        global $language;
        foreach ($language->_getLanguageList() as $lang) {
            $lang_data = array(
                'banner_id' => $banner_id,
                'language_code' => $lang['code'],
                'banner_title' => $data['banner_title_'.$lang['code']],
                'banner_description' => $data['banner_description_'.$lang['code']]
            );
            
            $db->AutoExecute(DB_PREFIX.'_aq_timed_banner_description', 
                            $lang_data, 
                            'REPLACE',
                            'banner_id = '.(int)$banner_id.' AND language_code = "'.$lang['code'].'"');
        }
        
        return true;
    }      
*/
}
