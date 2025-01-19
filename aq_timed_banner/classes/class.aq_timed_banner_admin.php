<?php
defined('_VALID_CALL') or die('Direct Access is not allowed.');

class aq_timed_banner_admin {
    
    protected $_table = 'aq_timed_banner';
    protected $_master_key = 'banner_id';
    protected $_language_key = 'language_code';
    protected $_language_fields = ['banner_title', 'banner_description'];

    function __construct() {
        global $language;
    }

    function _getParams() {
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

    function _getList($search_filter = '') {
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

    function _delete($id) {
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
}
