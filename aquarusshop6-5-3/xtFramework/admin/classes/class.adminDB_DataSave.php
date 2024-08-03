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

class adminDB_DataSave {

    protected $masterKey, $table, $sql_where,
        $new, $newID,
        $data, $langCheck, $storelangCheck,
        $fields, $PrimaryFields, $fieldData, $excludeFields
    ;

    function __construct($table, $data, $langCheck = false, $class='' ,$storeLangCheck=false) {
        $this->setTable($table);
        $this->setDataValues($data);
        $this->setLangCheck($langCheck);
        $this->setStoreLangCheck($storeLangCheck);

        $this->readTabelFields();
        $this->runFilter();
        //return $this->saveDataSet ();
    }

    function setMasterKey($data) {
        $this->masterKey = $data;
    }
    function getMasterKey() {
        return $this->masterKey;
    }

    function setNew() {
        $this->new = true;
    }
    function getNew() {
        return $this->new;
    }
    function setNewID($value) {
        $this->newID = $value;
    }
    function getNewID() {
        return $this->newID;
    }

    function setDataValues($data) {
        $this->data = $data;
    }
    function getDataValues() {
        return $this->data;
    }

    function setLangCheck($data) {
        $this->langCheck = $data;
    }
    function getLangCheck() {
        return $this->langCheck;
    }

    function setStoreLangCheck($data) {
        $this->storelangCheck = $data;
    }
    function getStoreLangCheck() {
        return $this->storelangCheck;
    }



    function setTable($data) {
        $this->table = $data;
    }
    function getTable() {
        return $this->table;
    }

    function setTableField($data) {
        $this->fields[] = $data;
    }
    function getTableFields() {
        return $this->fields;
    }

    function setPrimaryTableField($data) {
        $this->PrimaryFields[] = $data;
    }
    function getPrimaryTableFields() {
        return $this->PrimaryFields;
    }

    function setTableFieldData($key, $data) {
        $this->fieldData[$key] = $data;
    }
    function getTableFieldData($key) {
        return $this->fieldData[$key];
    }

    function setExcludeFields($data){
        $this->excludeFields = $data;
    }

    function getExcludeFields(){
        return $this->excludeFields;
    }

    function readTabelFields ()
    {
        /** @var $db ADOConnection */
        /** @var $record ADORecordSet */
        global $db;

        $query = "SHOW FIELDS FROM ".$this->getTable()." ";
        $record = $db->Execute($query);
        if ($record->RecordCount() > 0) {
            while(!$record->EOF){
                $records = $record->fields;
                if ($records['Key'] == 'PRI') {
                    $this->setPrimaryTableField($records['Field']);
                }
                $this->setTableField($records['Field']);
                $this->setTableFieldData($records['Field'], $records);
                $record->MoveNext();
            } $record->Close();
        }
        return true;
    }

    function completeDataSet($data){

        $fields = $this->getTableFields();

        if(is_array($fields)){
            foreach ($fields as $field) {

                if($data[$field]){
                    //$data[$field] = $data[$field];
                }else{
                    $data[$field] = '';
                }

            }
        }

        return $data;

    }


    function runFilter() {
        global $filter, $language,$store_handler;
        $data = $this->getDataValues();

        $new_data = array();
        if ($this->getStoreLangCheck()) {
            $pTblFields = $this->getPrimaryTableFields();
            $stores = $store_handler->getStores();
            foreach ($stores as $store) {

                foreach ($language->_getLanguageList() as $langData) {
                    foreach ($this->getTableFields() as $field) {

                        $fieldData = $this->getTableFieldData($field);

                        if (isset($data[$field.'_store'.$store['id'].'_'.$langData['code']])) {

                            $data[$field.'_store'.$store['id'].'_'.$langData['code']] = _stripslashes($data[$field.'_store'.$store['id'].'_'.$langData['code']]);
                            $new_data['store'.$store['id']][$langData['code']][$field]=$data[$field.'_store'.$store['id'].'_'.$langData['code']];
                        }

                        if (in_array($field,$pTblFields)) {

                            if ($field == 'language_code'){
                                $new_data['store'.$store['id']][$langData['code']][$field]=_stripslashes($langData['code']);
                            }
                            elseif (strpos($field,'_store_id')!==false) {
                                $new_data['store'.$store['id']][$langData['code']][$field]=_stripslashes($store['id']);
                            }
                            else{
                                //$new_data[$langData['code']][$field]=urldecode(stripslashes($data[$field]));
                                $new_data['store'.$store['id']][$langData['code']][$field]=stripslashes($data[$field]);
                            }
                        }
                    }
                }
            }

        }
        elseif ($this->getLangCheck()) {
            $pTblFields = $this->getPrimaryTableFields();
            foreach ($language->_getLanguageList() as $langData) {
                foreach ($this->getTableFields() as $field) {

                    $fieldData = $this->getTableFieldData($field);

                    if (isset($data[$field.'_'.$langData['code']])) {

                        $data[$field.'_'.$langData['code']] = _stripslashes($data[$field.'_'.$langData['code']]);
                        $new_data[$langData['code']][$field]=$data[$field.'_'.$langData['code']];
                    }

                    if (in_array($field,$pTblFields)) {

                        if ($field == 'language_code'){
                            $new_data[$langData['code']][$field]=_stripslashes($langData['code']);
                        }else{
                            if (isset($data[$field]))
                                $new_data[$langData['code']][$field]=stripslashes($data[$field]);
                        }
                    }
                }
            }

        } else {
            $primaryKeySet = true;
            foreach($this->getPrimaryTableFields() as $pkf)
            {
                if (!array_key_exists($pkf, $data))
                {
                    $primaryKeySet = false;
                }
            }

            foreach ($this->getTableFields() as $field) {
                $fieldData = $this->getTableFieldData($field);

                if (isset($data[$field])) {


                    if($field != 'status_values')
                        $new_data[$field]=_stripslashes($data[$field]);
                    else
                        $new_data[$field]=$data[$field];

                }
                else  if (!empty($fieldData['Default']))
                {
                    $new_data[$field] = $fieldData['Default'];
                }

            }
        }
        // set filter data

        $this->setDataValues($new_data);
    }

    function _checkData()
    {
        global $language,$store_handler;

        // separate processing of lang and lang-independent tables
        $data = array();
        if($this->getStoreLangCheck())
        {

            $stores = $store_handler->getStores();
            foreach ($stores as $store) {
                foreach ($language->_getLanguageList() as $key => $val) {
                    $data['store'.$store['id']][$val['code']] = $this->_checkStoreDataLang($val['code'],$store['id']);
                }
            }
            $this->setDataValues($data);
        }
        elseif (!$this->getLangCheck()) {
            $data = $this->_checkDataLang();
            $this->setDataValues($data);
        } else {
            foreach ($language->_getLanguageList() as $key => $val) {
                $data[$val['code']] = $this->_checkDataLang($val['code']);
            }
            $this->setDataValues($data);
        }
    }

    function _checkStoreDataLang($lang = null,$store_id=null){
        $data = $this->getDataValues();
        if (!empty($lang) && isset($data['store'.$store_id][$lang])) {
            $data = $data['store'.$store_id][$lang];
        }

        foreach ($this->getTableFields() as $field) {

            $fieldData = $this->getTableFieldData($field);
            $prime_keys = $this->getPrimaryTableFields();

            $check_fields = true;
            if(count($data)>0){

                // for dynamically created fields ending with "_status"
                if (preg_match('|^[a-z0-9_]+_status$|i', $fieldData['Field']) && !isset($data[$fieldData['Field']])) {
                    $data[$fieldData['Field']] = 0;
                }

                if(!array_key_exists($fieldData['Field'], $data)){
                    $check_fields = false;
                }

            }

            if($check_fields == true){
                if(in_array($fieldData['Field'], $prime_keys))
                    $check_primary = true;
                else
                    $check_primary = false;

                if($data[$fieldData['Field']] == '' && $fieldData['Null'] =='NO' && $check_primary != true){
                    // mysql strict mode umgehen
                    // wenn feld zb char(2) kann kein New eingetragen werden -> invalid char length!
                    if ($fieldData['Default']!='') {
                        if ($fieldData['Type']=='char(2)')
                            $data[$fieldData['Field']] = $fieldData['Default'];

                        if ($fieldData['Type']=='datetime')
                            $data[$fieldData['Field']] = $fieldData['Default'];
                    } else {
                        $data[$fieldData['Field']] = $fieldData['Default']; //'New';
                    }

                    $this->setNew();
                }
            }

        }
        return $data;

    }

    function _checkDataLang($lang = null){
        $data = $this->getDataValues();
        if (!empty($lang) && isset($data[$lang])) {
            $data = $data[$lang];
        }

        foreach ($this->getTableFields() as $field) {

            $fieldData = $this->getTableFieldData($field);
            $prime_keys = $this->getPrimaryTableFields();

            $check_fields = true;
            if(count($data)>0){

                // for dynamically created fields ending with "_status"
                if (preg_match('|^[a-z0-9_]+_status$|i', $fieldData['Field']) && !isset($data[$fieldData['Field']])) {
                    $data[$fieldData['Field']] = 0;
                }

                if(!array_key_exists($fieldData['Field'], $data)){
                    $check_fields = false;
                }

            }

            if($check_fields == true){
                if(in_array($fieldData['Field'], $prime_keys))
                    $check_primary = true;
                else
                    $check_primary = false;

                if($data[$fieldData['Field']] == '' && $fieldData['Null'] =='NO' && $check_primary != true){
                    // mysql strict mode umgehen
                    // wenn feld zb char(2) kann kein New eingetragen werden -> invalid char length!
                    if ($fieldData['Default']!='') {
                        if ($fieldData['Type']=='char(2)')
                            $data[$fieldData['Field']] = $fieldData['Default'];

                        if ($fieldData['Type']=='datetime')
                            $data[$fieldData['Field']] = $fieldData['Default'];
                    } else {
                        $data[$fieldData['Field']] = $fieldData['Default']; //'New';
                    }

                    $this->setNew();
                }
            }

        }
        return $data;

    }

    function _checkNewData()
    {
        /** @var $db ADOConnection */
        /** @var $record ADORecordSet */
        global $filter, $language, $db,$store_handler;

        $data = $this->getDataValues();
        $pTblFields = $this->getPrimaryTableFields();

        foreach ($pTblFields as $field) {
            if(!$data[$field])
                $data[$field] = '';
        }

        if (!is_array($data) || count($data) == 0) return $data;

        // runFilter ermittelt default-Werte, kann also 'CURRENT_TIMESTAMP' sein
        // das führt mit addslashes in mysql8 zum Fehler Incorrect DATETIME value: 'CURRENT_TIMESTAMP' zb wg
        // date_added = 'CURRENT_TIMESTAMP'
        // aber für die prüfung, ob ein record existiert wäre ff auch falsch
        // date_added = CURRENT_TIMESTAMP
        $skipAndFixTypes = ['datetime', 'timestamp'];
        foreach ($data as $key => $val)
        {
            if (!is_array($val) && !in_array($this->fieldData[$key]['Type'], $skipAndFixTypes))
            {
                $where[] = $key .' = "'.addslashes($val).'"';
            }
            else if (in_array($this->fieldData[$key]['Type'], $skipAndFixTypes))
            {
                // mit CURRENT_TIMESTAMP markierte felden überlassen wir der db
                if($this->fieldData[$key]['Default'] == 'CURRENT_TIMESTAMP' ||
                    strpos($this->fieldData[$key]['Extra'],'CURRENT_TIMESTAMP'))
                {
                    unset($data[$key]);
                }
            }
        }
        // ! getPrimarySelectSQL funktioniert nicht richtig für compound PK's
        // ? scheint aber noch nie ein problem gewesen zu sein
        $primary = $this->getPrimarySelectSQL();
        if(!empty($primary))
        {
            // hier wird auf unique'niss der gesamten zeile geprüft
            // sollte nicht der PK reichen, bzw ein test auf unique keys
            // puu
            $recordId = $db->GetOne("SELECT " . $primary . " as id FROM " . $this->getTable() . " WHERE " . implode(' and ', $where) . "  order by " . $primary . " DESC LIMIT 1");

            if ($recordId)
            {
                $data[$primary] = $recordId;
                $this->setNewID($recordId);
            }
        }

        // set current owner by storedomain
        $tablefields = $this->getTableFields();
        $tablefields = array_keys($tablefields);

        $perm_field = false;
        foreach($tablefields as $fKey=>$fVal){
            if(preg_match('/owner/', $fVal) || preg_match('/shop_id/', $fVal)){
                $perm_field = $fVal;
                break;
            }
        }

        if ($perm_field && $data[$perm_field])
            $data[$perm_field] = $store_handler->shop_id;

        $this->setDataValues($data);
    }

    function saveDataSet ()
    {
        /** @var $db ADOConnection */
        /** @var $record ADORecordSet */
        global $filter, $language, $db,$store_handler;
        $obj = new stdClass();
        $obj->success = false;
        $obj->new_id = 0;


        $pTblFields = $this->getPrimaryTableFields();

        $this->_checkData();

        if (!$this->getLangCheck())
            $this->_checkNewData();
        $data = $this->getDataValues();

        $this->setSqlWhere();
        $_where = $this->getSqlWhere();

        if ($this->getStoreLangCheck()) {

            $stores = $store_handler->getStores();
            foreach ($stores as $store) {
                foreach ($language->_getLanguageList() as $langData) {
                    $cnt = array_value($pTblFields, 'store'.$store['id']) && array_value($pTblFields['store'.$store['id']], [$langData['code']]) ? count($pTblFields['store'.$store['id']][$langData['code']]) : 0;
                    if (is_array($data) && count($data) != $cnt) {

                        //	$data[$langData['code']] = $this->completeDataset($data[$langData['code']]);

                        $where = implode(' and ', $_where['store'.$store['id']][$langData['code']]);

                        $record = $db->Execute("SELECT * FROM " . $this->getTable() . " WHERE ".$where." ");
	    		        if ($record->RecordCount() == 0) {
                            $db->AutoExecute($this->getTable(), $data['store'.$store['id']][$langData['code']]);
                            $obj->new_id = $db->Insert_ID();
                            $obj->last_id = $db->Insert_ID();
                            $obj->success = true;
                        } else {
                            $db->AutoExecute($this->getTable(), $data['store'.$store['id']][$langData['code']], 'UPDATE', $where);
                            $obj->success = true;
                        }
                    }
                }
            }
        }
        elseif ($this->getLangCheck()) {

            foreach ($language->_getLanguageList() as $langData) {
                $cnt = is_array($pTblFields[$langData['code']]) ? count($pTblFields[$langData['code']]) : 0;
                if (is_array($data) && count($data) != $cnt) {

                    //	$data[$langData['code']] = $this->completeDataset($data[$langData['code']]);

                    $where = implode(' and ', $_where[$langData['code']]);

                    $record = $db->Execute("SELECT * FROM " . $this->getTable() . " WHERE ".$where." ");
    		        if ($record->RecordCount() == 0) {
                        $db->AutoExecute($this->getTable(), $data[$langData['code']]);
                        $obj->new_id = $db->Insert_ID();
                        $obj->last_id = $db->Insert_ID();
                        $obj->success = true;
                    } else {
                        $db->AutoExecute($this->getTable(), $data[$langData['code']], 'UPDATE', $where);
                        $obj->success = true;
                    }
                }
            }
        } else {

            //		$data = $this->completeDataset($data);

            $data = $this->filterFields($data);

            $where = implode(' and ', $_where);

            $qry = "SELECT * FROM " . $this->getTable() . " WHERE ".$where." ";

            $record = $db->Execute($qry);

	        if ($record->RecordCount() == 0) {
                $db->AutoExecute($this->getTable(), $data);
                $obj->new_id = $db->Insert_ID();
                $obj->last_id = $db->Insert_ID();
                $obj->success = true;
            } else {
                $db->AutoExecute($this->getTable(), $data, 'UPDATE', $where);
                $obj->success = true;
            }
        }
        $new_id = $this->getNewID();
		if (!$obj->new_id && $new_id)
            $obj->new_id = $new_id;
        return $obj;
    }

    function setSqlWhere() {
        global $language,$store_handler;
        $pTblFields = $this->getPrimaryTableFields();
        $data = $this->getDataValues();
        if ($this->getStoreLangCheck()) {

            $stores = $store_handler->getStores();

            foreach ($pTblFields as $field) {
                foreach ($stores as $store) {
                    foreach ($language->_getLanguageList() as $langData) {

                        $where['store'.$store['id']][$langData['code']][] = " " . $field . " = '" . $data['store'.$store['id']][$langData['code']][$field] . "'";
                    }

                }
            }

        }
        elseif ($this->getLangCheck()) {
            foreach ($pTblFields as $field) {
                foreach ($language->_getLanguageList() as $langData) {
                    $where[$langData['code']][] = " " . $field . " = '" . $data[$langData['code']][$field] . "'";
                }
            }
        } else {
            foreach ($pTblFields as $field) {
                $where[] = " " . $field . " = '" . $data[$field] . "'";
            }
        }
        $this->sql_where = $where;
    }
    function getSqlWhere() {
        return $this->sql_where;
    }

    /**
     * Ermittelt den ersten Teil eines PK
     * @return bool|string
     */
    function getPrimarySelectSQL ()
    {
        $pTblFields = $this->getPrimaryTableFields();
        foreach ($pTblFields as $field) {
            if ($field != 'language_code' && !is_array($field)) {
                return $field;
            }
        }
        return false;
    }

    function getAutoIncrementID($primary = '')
    {
        /** @var $db ADOConnection */
        /** @var $record ADORecordSet */
        global $db;
        if (!$primary)
            $primary = $this->getPrimarySelectSQL();

        $record = $db->Execute("SELECT ".$primary." as id FROM " . $this->getTable() . " order by ".$primary." DESC LIMIT 1");
        $records = $record->fields;
        // ++ for next record
        return $records['id']+1;

    }

    protected function filterFields($data){

        if(is_array($this->excludeFields) && count($this->excludeFields)!=0){
            foreach($this->excludeFields as $key => $val){
                unset($data[$val]);
            }
        }

        return $data;
    }

}
