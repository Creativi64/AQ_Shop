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

    public function _get($id = 0)
    {
        global $db;

        $obj = new stdClass;

		if ($this->position != 'admin') return false;

        if ($id === 'new') {
            $obj = $this->_set(array(), 'new');
			$id = $obj->new_id;
        }

        // $table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key);

        // if ($this->url_data['get_data'])
		// $data = $table_data->getData();
		// elseif($id)
		// $data = $table_data->getData($id);
		// else
		// $data = $table_data->getHeader();

		// $obj = new stdClass;
		// $obj->totalCount = count($data);
		// $obj->data = $data;

		// return $obj;

        $sql = "SELECT * FROM " . TABLE_BANNER . " WHERE id = ?";
        $result = $db->Execute($sql, array((int)$id));
        
        $recordCount = $result->RecordCount();
        
        if ($recordCount > 0) {
            $data = $result->fields;

            // Get language data
            $sql = "SELECT * FROM " . TABLE_BANNER_DESCRIPTION . " WHERE id = ?";
            $result = $db->Execute($sql, array((int)$id));

            while (!$result->EOF) {
                $data['title_' . $result->fields['language_code']] = $result->fields['title'];
                $data['description_' . $result->fields['language_code']] = $result->fields['description'];
                $result->MoveNext();
            }
            $obj -> totalCount = $recordCount;
            $obj -> data = $data;
        
            return $obj;
        }
        return;
    }

    public function _set($data, $mode = 'edit')
    {
        global $db;

        // Handle file upload if new image
        if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
            $upload_dir = _SRV_WEBROOT . 'media/images/banners/';
            $filename = time() . '_' . $_FILES['image']['name'];

            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename)) {
                $data['image'] = 'banners/' . $filename;
            }
        }

        if ($mode == 'new') {
            $data['date_added'] = date('Y-m-d H:i:s');
            $db->AutoExecute(TABLE_BANNER , $data, 'INSERT');
            $banner_id = $db->Insert_ID();
        } else {
            $db->AutoExecute(TABLE_BANNER , $data, 'UPDATE', 'id = ' . (int)$data['id']);
            $banner_id = $data['id'];
        }

        // Save language data
        global $language;
        foreach ($language->_getLanguageList() as $lang) {
            $lang_data = array(
                'id' => $banner_id,
                'language_code' => $lang['code'],
                'title' => $data['title_' . $lang['code']],
                'description' => $data['description_' . $lang['code']]
            );

            $db->AutoExecute(
                TABLE_BANNER_DESCRIPTION,
                $lang_data,
                'REPLACE',
                'id = ' . (int)$banner_id . ' AND language_code = "' . $lang['code'] . '"'
            );
        }

        return true;
    }
}
