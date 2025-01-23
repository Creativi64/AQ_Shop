<?php
defined('_VALID_CALL') or die('Direct Access is not allowed.');

class aq_timed_banner extends default_table
{

    public function setPosition($position)
    {
        $this->position = $position;
    }

    public $_table =  TABLE_BANNER;
    public $_master_key = 'id';
    public $_language_key = 'language_code';
    public $_language_fields = ['title', 'description'];

    public function _getParams()
    {
        $header['id'] = array('type' => 'hidden');
        $header['name'] = [
            'type' => 'textfield',
            'required' => true
        ];
        $header['image'] = array('type' => 'image');
        $header['link'] = ['type' => 'url'];
        $header['start'] = [ 
            'type' => 'date',
            'required' => true
        ];
        $header['ende'] = [ 
            'type' => 'date',
            'required' => true
        ];
        $header['status'] = array('type' => 'status');
        $header['sort_order'] = array('type' => 'textfield');
        $header['date_added'] =[
            'type' => 'date',
            'readonly' => true
        ];
        
        global $language;
        foreach ($language->_getLanguageList() as $key => $val) {
            $header['title_' . $val['code']] = [
                'type' => 'textfield',
                'width' => '300px',
                'language' => $val['code']
            ];
            $header['description_' . $val['code']] = [
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
            'SortDir' => 'ASC',
            'display_deleteBtn' => true,
            'display_editBtn' => true,
            'display_newBtn' => true,
            'exclude_list' => ['description']
        ];

        return $params;
    }

    public function _getList($search_filter = '')
    {
        global $db;

        $sql_where = "";
        if ($search_filter != '') {
            $sql_where .= " WHERE name LIKE '%" . $search_filter . "%' ";
        }

        $sql = "SELECT * FROM " . $this->_table . $sql_where;
        $result = $db->Execute($sql);

        $list = [];
        if ($result->RecordCount() > 0) {
            while (!$result->EOF) {
                $list[] = array_merge($result->fields, [
                    'image_url' => _SRV_WEB_IMAGES . $result->fields['image']
                ]);
                $result->MoveNext();
            }
        }
        return $list;
    }

    public function _delete($id)
    {
        global $db;

        if ($id && (int)$id > 0) {
            // Delete banner
            $db->Execute("DELETE FROM " . DB_PREFIX . "_" . $this->_table . " WHERE " . $this->_master_key . " = ?", [(int)$id]);
            // Delete descriptions
            $db->Execute("DELETE FROM " . DB_PREFIX . "_" . $this->_table . "_description WHERE " . $this->_master_key . " = ?", [(int)$id]);
            return true;
        }
        return false;
    }

    public function _get($ID = 0)
    {
        global $db, $language;

        if ($this->position != 'admin') return false;

        $obj = new stdClass;

        if ($ID === 'new') {
            $obj = $this->_set(array(), 'new');
            $ID = $obj->new_id;
        }

        $ID = (int)$ID;

        if ($this->url_data['get_data']) {
            // Get list of banners
            $table_data = new adminDB_DataRead($this->_table, $this->_table.'_description', null, $this->_master_key);
            $data = $table_data->getData();

            foreach ($data as &$item) {
                $item['image_url'] = _SRV_WEB_IMAGES . $item['image'];
            }

        } elseif ($ID) {
            // Get single banner data
            $table_data = new adminDB_DataRead($this->_table, $this->_table.'_description', null, $this->_master_key);
            $data = $table_data->getData($ID);

            if (!empty($data)) {
                $data[0]['image_url'] = _SRV_WEB_IMAGES . $data[0]['image'];
            }

        } else {
            // Get header data
            $table_data = new adminDB_DataRead($this->_table, $this->_table.'_description', null, $this->_master_key);
            $data = $table_data->getHeader();
        }

        if ($table_data->_total_count != 0 || !$table_data->_total_count) {
            $count_data = $table_data->_total_count;
        } else {
            $count_data = count($data);
        }

        $obj->totalCount = $count_data;
        $obj->data = $data;

        return $obj;
    }

    function _set($data, $set_type='edit') {
        global $db, $language;
    
        $obj = new stdClass;
        
        // Set timestamp if new record
        if ($set_type == 'new') {
            $data['date_added'] = $db->BindTimeStamp(time());
        }
        
        // Handle language specific fields by mapping them to description table
        foreach ($language->_getLanguageList() as $key => $val) {
            $lang_code = $val['code'];
            foreach ($this->_language_fields as $field) {
                if (isset($data[$field.'_'.$lang_code])) {
                    $data[$this->_table.'_description'][$field][$lang_code] = $data[$field.'_'.$lang_code];
                    unset($data[$field.'_'.$lang_code]);
                }
            }
        }
    
        // Save main table data
        $o = new adminDB_DataSave($this->_table, $data, false, __CLASS__);
        $obj = $o->saveDataSet();
    
        // Save language data if we have description table data
        if (isset($data[$this->_table.'_description']) && is_array($data[$this->_table.'_description'])) {
            $description = new adminDB_DataSave($this->_table.'_description', $data[$this->_table.'_description'], true, __CLASS__);
            $description->saveDataSet();
        }
    
        return $obj;
    }

    function _unset($id = 0) {
        global $db;
        
        if ($id == 0) return false;
        if ($this->position != 'admin') return false;
        
        $id = (int)$id;
        if (!is_int($id)) return false;
    
        // Delete main entry
        $db->Execute("DELETE FROM ". $this->_table ." WHERE ".$this->_master_key." = ?", array($id));
        
        // Delete language entries
        if ($this->_table_lang) {
            $db->Execute("DELETE FROM ". $this->_table."_description" ." WHERE ".$this->_master_key." = ?", array($id));
        }
    
        return true;
    }
}