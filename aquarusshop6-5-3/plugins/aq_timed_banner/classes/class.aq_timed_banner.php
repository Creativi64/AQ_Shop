<?php
defined('_VALID_CALL') or die('Direct Access is not allowed.');

class aq_timed_banner
{

    public function setPosition($position)
    {
        $this->position = $position;
    }

    public $_table = TABLE_BANNER;

    public $_table_lang = TABLE_BANNER_DESCRIPTION;

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
        $header['start_date'] = [
            'type' => 'date',
        ];
        $header['expire_date'] = [
            'type' => 'date',
        ];
        $header['status'] = array('type' => 'status');
        $header['sort_order'] = array('type' => 'textfield');
        $header['date_added'] = [
            'type' => 'date',
        ];
        global $language;

        foreach ($language->_getLanguageList() as $key => $val) {
            $header['title_' . $val['code']] = [
                'type' => 'textfield',
                'language' => $val['code']
            ];
            $header['description_' . $val['code']] = [
                'type' => 'htmleditor',
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
            // Excludes from Postback
            # 'exclude_list' => $exclude
        ];

        return $params;
    }

    public function _get($ID = 0)
    {
        global $db, $language;

        if ($this->position != 'admin')
            return false;

        $obj = new stdClass;

        if ($ID === 'new') {
            $obj = $this->_set(array(), 'new');
            $ID = $obj->new_id;
        }

        $ID = (int) $ID;

        $table_data = new adminDB_DataRead($this->_table, $this->_table . '_description', null, $this->_master_key);

        if ($this->url_data['get_data']) {
            // Get list of banners
            $data = $table_data->getData();
        } elseif ($ID) {
            // Get single banner data
            $data = $table_data->getData($ID);
        } else {
            // Get header data             
            $data = $table_data->getHeader();
        }

        $obj->totalCount = count($data);
        $obj->data = $data;

        $this->_getList();

        return $obj;
    }

    function _set($data, $set_type = 'edit')
    {
        global $db, $language;

        $obj = new stdClass;

        // Set timestamp if new record
        if ($set_type == 'new') {
            $data['date_added'] = date('Y-m-d H:i:s');
            $data['expire_date'] = date('Y-m-d H:i:s');
            $data['start_date'] = date('Y-m-d H:i:s');
        }

        // Save main table data
        $o = new adminDB_DataSave($this->_table, $data, false, __CLASS__);
        $obj = $o->saveDataSet();

        $oCD = new adminDB_DataSave($this->_table_lang, $data, true, __CLASS__);
        $objCD = $oCD->saveDataSet();

        $obj->success = true;
        return $obj;
    }

    function _unset($id = 0)
    {
        global $db;

        if ($id == 0)
            return false;
        if ($this->position != 'admin')
            return false;

        $id = (int) $id;
        if (!is_int($id))
            return false;

        // Delete main entry
        $db->Execute("DELETE FROM " . $this->_table . " WHERE " . $this->_master_key . " = ?", array($id));

        // Delete language entries
        if ($this->_table_lang) {
            $db->Execute("DELETE FROM " . $this->_table . "_description" . " WHERE " . $this->_master_key . " = ?", array($id));
        }

        return true;
    }

    public function _getListasd()
    {
        global $db, $current_language_code;

        $sql = "SELECT * FROM " . $this->_table . "WHERE start_date <= NOW() AND expire_date >= NOW() AND status = 1";
        $result = $db->Execute($sql);

        if (!empty($result)) {
            // Get language specific content for each banner
            foreach ($result as &$banner) {
                // Get banner description for current language
                $sql = "SELECT banner_title, banner_description 
                        FROM " . TABLE_BANNER_DESCRIPTION . "  
                        WHERE banner_id = ? AND language_code = ?";
                $resultLang = $db->Execute($sql, array($banner['banner_id'], $current_language_code));

                if ($result->RecordCount() > 0) {
                    $banner['title'] = $resultLang->fields['banner_title'];
                    $banner['description'] = $resultLang->fields['banner_description'];
                }
            }
        }

        return $result;
    }

    public function _getList()
    {
        global $db, $language;
        
        $sql = "SELECT bd.title, bd.description 
            FROM " . $this->_table . " b
            LEFT JOIN " . $this->_table_lang . " bd 
            ON b.id = bd.id AND bd.language_code =?
            WHERE b.start_date <= NOW() AND b.expire_date >= NOW() AND b.status = 1
            ORDER BY b.sort_order";
          
        $result = $db->Execute($sql,[$language->code]);
        
        if ($result->RecordCount() > 0) { 
            $tdf = $result->getArray();
            
            return $tdf;
        }

        return null;
    }
}