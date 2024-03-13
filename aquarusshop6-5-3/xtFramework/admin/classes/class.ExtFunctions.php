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

class ExtFunctions {

    var $Permission;
    var $SelectionItem, $SelectionImageItem, $SelectionFileItem;
    var $grouping;
    var $header;
    var $nonlang_data,$lang_data;
    var $params;
    var $tmpData;
    var $url_data;
    var $code;
    var $grid_data;
    var $PanelPos;
    var $activeElemet;
    var $_debugMsg;
    var $edit_id;
    var $master_key;

    function __construct () {
    }

    /**
     * @param $code
     * @throws Exception
     */
    function setCode($code) {
        $this->code = $code;
        $this->settngs();

    }

    function setSelectionItem () {
        $this->SelectionItem = 'selectedItem_'.time();
        $this->SelectionImageItem = 'selectedImageItem_'.time();
        $this->SelectionFileItem = 'selectedFileItem_'.time();
    }

    function getSelectionItem () {
        return $this->SelectionItem;
    }
    function getSelectionImageItem () {
        return $this->SelectionImageItem;
    }
    function getSelectionFileItem () {
        return $this->SelectionFileItem;
    }

    /**
     * @throws Exception
     */
    function settngs () {
        global $language;

        $add_to_url = (isset($_SESSION['admin_user']['admin_key']))? 'sec='.$_SESSION['admin_user']['admin_key'].'&': '';
        $default_params = array (

            'exclude' => array ('link', 'text', 'language_code'),
            'include' => '',
            'edit_masterkey' => false,  /* master key edit */
            'gridTitle' => 'Grid-Title',
            'headTitle' => 'xtAdmin - Heading',
            'gridwidth' => 0.7, /* gridwidth 0.7 => 70%  */
            'panelwidth' => 0.3, /* rightpanel width 0.3 => 30%  */
            'FormLabelPos' => 'top',
            'FormLabelWidth' => 200,
            'dateFormat' => 'n/j h:ia',
            'gridType' => 'Template', // EditGrid
            'gridView' => true,
            'gridTemplate' => '', // show template in grid (preview)
            'save_url' => "adminHandler.php?load_section=".$this->code."&",
            'save_grid_url' => "adminHandler.php?load_section=".$this->code."&save_all=true&",
            'readfull_url' =>  "adminHandler.php?load_section=".$this->code."&",
            'edit_url' =>  "adminHandler.php?load_section=".$this->code."&",
            'sort_url' => "adminHandler.php?load_section=".$this->code."&pg=sort&".$add_to_url,
            'icons_path' => "images/icons/",
            /* grouping grid */
            'GroupField' => '',
            'SortField' => '',
            'GroupStartCollapsed'=>false,
            'SortDir' => 'ASC',
            'RemoteSort' => false, // sorting by php funtions
            /* grouping grid end */
            'PageSize' => 25,
            'setWindowWidth' => 800,
            'setWindowHeight' => 400,
            'languageTab' => true,
            'languageStoreTab'=>false, // parameter for Stores and languages based items data
            'display_language' =>  $language->code,
            'display_adminActionStatus' => false,
            'display_searchPanel' => false,
            'display_editBtn' => true,
            'display_cancelBtn' => true,
            'display_copyBtn' => false,
            'display_deleteBtn' => true,
            'display_resetBtn' => true,
            'display_newBtn' => true,
            'display_checkCol' =>false,
            'display_checkItemsCheckbox' => false,

            'display_Btn' => array(),

            'display_PopupBtn' => false,
            'display_GetSelectedBtn' => false,
            'display_statusTrueBtn' => false,
            'display_statusFalseBtn' => false,

            'display_gridmenu' => true,
            'display_editmenu' => true,
            'rowActions' => array(),
            'menuActions' => array(),
        );

        if (is_array($this->params)) {
            $this->params = array_merge($default_params, $this->params);
        }

        if(is_array($this->url_data)) {
            $url_param = '';
            foreach ($this->url_data as $key => $val) {
                // filter default params
                if ($key != 'load_section' && $key != '_dc')
                    $url_param.= $key.'='.$val.'&';
            }
            $this->params['readfull_url'].= $url_param;
            $this->params['save_grid_url'].= $url_param;
            $this->params['edit_url'].= $url_param;
            $this->params['save_url'].= $url_param;
        }



        $this->_setData('data', array());
        // preload admin permissions
        $this->setPermission();

        $this->header = array();
        if (is_array($this->params['header']))
            $this->header = $this->params['header'];

        if (!isset($this->params['languageTab']))
            $this->params['languageTab'] = true;

    }

    function setSetting ($key, $val) {
        $this->params[$key] = $val;
    }

    /**
     * @param $key
     * @return bool|array
     */
    function getSetting ($key) {
        if (isset($this->params[$key]))
            return $this->params[$key];
        return false;
    }

    function checkDefaultPanelSortPosition($position) {
        $check = false;
        $panelSettings = $this->getSetting('panelSettings');
        if (is_array($panelSettings))
            foreach ($panelSettings as $sort => $val) {
                if ($position == $val['position']) {
                    $check = true;
                    break;
                }
            }
        if (!$check) {

            $s = array('position' => $position, 'text' => __define('TEXT_'.$position), 'groupingPosition' => array());
            $new_panelSettings[] = $s;
            if (is_array($panelSettings) && $position == 'MAIN') {
                foreach ($panelSettings as $s => $psettings) {
                    $new_panelSettings[] = $psettings;
                }
                $this->setSetting('panelSettings', $new_panelSettings);
            } else {
                $panelSettings[] = $s;
                $this->setSetting('panelSettings', $panelSettings);
            }
        }
        return;
    }

    function setPanelSortPosition ($groupingPosition, $position = 'default') {

        $this->checkDefaultPanelSortPosition($position);
        $panelSettings = $this->getSetting('panelSettings');
        if($panelSettings == false) $panelSettings = [];
        $break = false;
        $sort = 0;
        for ($i = 0; $i < count($panelSettings); $i++) {
            unset($val);
            $val = $panelSettings[$i];
            foreach ($val['groupingPosition'] as $k => $pos) {
                if ($groupingPosition == $pos) {
                    $sort = $i;
                }
            }
        }

        if (!in_array($groupingPosition, $panelSettings[$sort]['groupingPosition'])) {
            $panelSettings[$sort]['groupingPosition'][] = $groupingPosition;
        }
        $this->setSetting('panelSettings', $panelSettings);
    }


    function _setData ($key, $val) {
        $this->tmpData[$key] = $val;

        if ($key == 'obj_data') {
            if ($val->totalCount == "0" && $this->url_data['get_data'] == 'true') {
                $val->data = array();
            }
            $this->_setData('data', $val->data[0]);
        }
    }

    function setData ($obj) {

        foreach ($obj->data as $current => $values) {
            foreach ($values as $key => $val) {
                $set = $this->getSetting('exclude');
                if (is_array($set) && !in_array($key, $this->getSetting('exclude')))
                    $this->grid_data[$current][] = $val;
            }

        }
    }
    function setHeader (&$data) {
        $this->header = $data;

    }
    function getHeader() {
        return $this->header;
    }

    function setGrouping (&$data) {
        $this->grouping = $data;
    }
    function getGrouping () {
        return $this->grouping;
    }

    function setPanelPos (&$data) {
        $this->PanelPos = $data;
    }
    function getPanelPos () {
        return $this->PanelPos;
    }

    function setActivate($string) {
        if ($string != '')
            $this->activeElemet[$string] = true;
    }

    function getActivate($string) {
        if (isset($this->activeElemet[$string]))
            return true;
        return false;
    }





    //////////////////////////////
    // error

    function setDebugMessage ($key, $msg) {
        $this->_debugMsg[$key] = $msg;
    }

    function getDebugMessages () {
        $die = false;
        if (!is_array($this->_debugMsg) || count($this->_debugMsg) == 0) return;
        __debug($this->_debugMsg, 'Debug');
        foreach ($this->_debugMsg as $key => $val) {
            if ($key == 'die') $die = true;
        }

        __debug($this->params, 'params');
        __debug($_SESSION, 'session');

        if ($die) die;
    }

    //////////////////////////////
    /* permis

    */
    /**
     * @throws Exception
     */
    function setPermission () {
        global $xtc_acl;

        $new_buttons = $this->_systemButtons();
        $new_items = [];

        if(is_array($new_buttons) && count($new_buttons)>0){
            reset($new_buttons);
            foreach ($new_buttons as $key => $val) {

                if($this->getSetting('display_'.$key.'Btn')==true){

                    if(!$val['acl'])
                        $this->Permission['check_'.$key] = true;
                    else
                        $this->Permission['check_'.$key] = $xtc_acl->checkPermission($this->code, $val['acl']);

                }else{
                    $this->Permission['check_'.$key] = false;
                }
            }
        }

        $items = $this->getSetting('menuActions');

        if(is_array($items) && count($items)>0){
            reset($items);
            foreach ($items as $key => $val) {
                foreach ($val as $nkey => $nval) {
                    $new_items[$nkey] = $nval;
                }
            }
        }

        if(count($new_items)>0){
            reset($new_items);
            foreach ($new_items as $ikey => $ival) {
                if($this->getSetting('display_'.$ikey.'Mn')==true){
                    if(!$ival['acl'])
                        $this->Permission['check_'.$ikey] = true;
                    else
                        $this->Permission['check_'.$ikey] = $xtc_acl->checkPermission($this->code, $ival['acl']);
                }else{
                    $this->Permission['check_'.$ikey] = false;
                }
            }
        }

    }

    // view, new, edit, delete
    function checkPermission ($pos = 'view') {
        return isset($this->Permission['check_'.$pos]) && $this->Permission['check_'.$pos] ?: false;
    }

    function clean_sort($sort){

        $sort_array  = explode('_', $sort);
        $count = count($sort_array);

        if($count==2){
            $new_sort = 1;
        }else{
            $new_sort = $sort_array[1];
            $new_sort = $new_sort + 2;
        }

        return $new_sort;
    }

    function clean_id($id){

        $id = str_replace('subcat_','',$id);

        return $id;
    }



    function _itemSelect($label, $name, $url = '', $value = '', $valueUrl = '', $params = []) {

        $reader = new PhpExt_Data_JsonReader();
        $reader->setRoot("topics")
            ->setTotalProperty("totalCount");
        $reader->addField(new PhpExt_Data_FieldConfigObject("id"));
        $reader->addField(new PhpExt_Data_FieldConfigObject("name"));
        $reader->addField(new PhpExt_Data_FieldConfigObject("desc"));

        $fromstore = new PhpExt_Data_Store();
        $fromstore->setProxy(new PhpExt_Data_HttpProxy($url))
            ->setReader($reader);

        //preload in editGrid - mode
        //if ($this->getSetting('gridType') == 'EditGrid' && $url) {
        $alcoFrom = new PhpExt_AutoLoadConfigObject($url, $params);
        $alcoFrom->setScripts(true)->setMethod(PhpExt_AutoLoadConfigObject::AUTO_LOAD_METHOD_GET);
        $fromstore->setAutoLoad($alcoFrom);
        //}

        if (empty($valueUrl)) {
            $tostore = new PhpExt_Data_SimpleStore();
            $tostore->addField("id");
            $tostore->addField("name");
            $tostore->addField("desc");
            //$tostore->setData($value); // from states.js
        } else {
            // Load selected values from external url
            $tostore = new PhpExt_Data_Store();
            $tostore->setProxy(new PhpExt_Data_HttpProxy($valueUrl))
                ->setReader($reader);

            $alcoTo = new PhpExt_AutoLoadConfigObject($valueUrl, $params);
            $alcoTo->setScripts(true)->setMethod(PhpExt_AutoLoadConfigObject::AUTO_LOAD_METHOD_GET);
            $tostore->setAutoLoad($alcoTo);
        }


        $itemselector = new PhpExtUx_Form_ItemSelector();
        $itemselector
            ->setFromStore($fromstore)
            ->setToStore($tostore)
            ->setMsWidth($params['width'] ? $params['width'] : 250)
            ->setMsHeight($params['height'] ? $params['height'] : 250)
            ->setAllowDup($params['allowDup'] ? $params['allowDup'] : false)
            ->setImagePath(_SYSTEM_BASE_URL._SRV_WEB_UPLOAD._SRV_WEB_FRAMEWORK."library/ext/ux/images/")
            ->setValueField("id")
            ->setDisplayField("name")
            ->setName($name)
            ->setFieldLabel($label);

        return $itemselector;
    }


    // the json data example like
    /*
    $data['topics'][] = array('id' => '1', 'name' => 'test1', 'desc' => 'optional ;D');
    $data['totalCount'] = count($data['topics']);
    */
    function _comboBox($label, $name, $url = '',$listWidth='300',$listner='') {
        if(empty($listWidth)) $listWidth = '300';
        $reader = new PhpExt_Data_JsonReader();
        $reader->setRoot("topics")
            ->setTotalProperty("totalCount");
        $reader->addField(new PhpExt_Data_FieldConfigObject("id"));
        $reader->addField(new PhpExt_Data_FieldConfigObject("name"));
        $reader->addField(new PhpExt_Data_FieldConfigObject("desc"));

        $store = new PhpExt_Data_Store();
        $store->setProxy(new PhpExt_Data_HttpProxy($url))
            ->setReader($reader);
        // combobox with description tooltip
        $combo = new PhpExt_Form_ComboBox();
        $combo->setTypeAhead(true)->setTypeAheadDelay(1)->setMinChars(1);
        //preload in editGrid - mode
        if ($this->getSetting('gridType') == 'EditGrid' && $url) {
            $alco = new PhpExt_AutoLoadConfigObject($url);
            $alco->setScripts(true)->setMethod(PhpExt_AutoLoadConfigObject::AUTO_LOAD_METHOD_GET);
            $store->setAutoLoad($alco);
        }
        if (is_array($listner)){
            foreach($listner as $l=>$v){
                if ($l!=''){
                    $combo->attachListener($l, new PhpExt_Listener(PhpExt_Javascript::functionDef(null,$v) ));
                }
            }
        }
        $combo->setCssStyle(new PhpExt_Config_ConfigObject(array("width"=>"auto")));
        $combo->setTemplate('<tpl for="."><div ext:qtip="{id} {desc}" class="x-combo-list-item" >{name}</div></tpl>')
            ->setStore($store)
            ->setTitle(__define("TEXT_ADMIN_DROPDOWN_SELECT"))
            ->setHiddenName($label)
            ->setListWidth($listWidth)
            ->setTriggerAction(PhpExt_Form_ComboBox::TRIGGER_ACTION_ALL)
            ->setSelectOnFocus(true)
            ->setFieldLabel($name);

        if ($this->getSetting('gridType') == 'EditGrid') {
            $combo->setDisplayField("id");
            // $combo->setDisplayField("name");
            //preload in editGrid - mode
            $combo->setMode(PhpExt_Form_ComboBox::MODE_LOCAL);
        } else {

            $combo->setDisplayField("name");
            $combo->setValueField("id");
        }
        return $combo;
    }

    function _multiComboBox($label, $name, $url = '')
    {
        return $this->_multiComboBox2($label, $name, $url);
    }

    function _multiComboBox2($label, $name, $url = '', $width = 310)
    {
        $reader = new PhpExt_Data_JsonReader();
        $reader->setRoot("topics")
            ->setTotalProperty("totalCount");
        $reader->addField(new PhpExt_Data_FieldConfigObject("id"));
        $reader->addField(new PhpExt_Data_FieldConfigObject("name"));
        $reader->addField(new PhpExt_Data_FieldConfigObject("desc"));

        $store = new PhpExt_Data_Store();
        $store->setProxy(new PhpExt_Data_HttpProxy($url))
            ->setReader($reader);

        //preload in editGrid - mode
        if ($this->getSetting('gridType') == 'EditGrid' && $url) {
            $alco = new PhpExt_AutoLoadConfigObject($url);
            $alco->setScripts(true)->setMethod(PhpExt_AutoLoadConfigObject::AUTO_LOAD_METHOD_GET);
            $store->setAutoLoad($alco);
        }
        // combobox with description tooltip
        $combo = new PhpExt_Form_LovCombo2();

        $combo->setTitle($label)
            ->setTriggerAction(PhpExt_Form_LovCombo2::TRIGGER_ACTION_ALL)
            ->setStore($store)
            ->setSelectOnFocus(true)
            ->setHiddenName($name."_id")
            ->setListWidth($width-30)
            ->setWidth($width)
            // TODO check ->setWidth(width)->setListWidth(width)
            ->setFieldLabel($label)
            ->setId($name);

        if ($this->getSetting('gridType') == 'EditGrid')
        {
            $combo->setDisplayField("name");
            //preload in editGrid - mode
            $combo->setMode(PhpExt_Form_LovCombo2::MODE_LOCAL);
        } else {
            $combo->setValueField("id");
            $combo->setDisplayField("name");
        }
        return $combo;
    }

    function _multiComboBox3($label, $name, $url = '')
    {
        $reader = new PhpExt_Data_JsonReader();
        $reader->setRoot("topics")
            ->setTotalProperty("totalCount");
        $reader->addField(new PhpExt_Data_FieldConfigObject("id"));
        $reader->addField(new PhpExt_Data_FieldConfigObject("name"));
        $reader->addField(new PhpExt_Data_FieldConfigObject("desc"));

        // POST because of some fnc's asking for it in getAdminDropdownData.php
        $store = new PhpExt_Data_Store();
        $store->setProxy(new PhpExt_Data_HttpProxy($url, 'POST'))
            ->setReader($reader);

        // always preload  // query=1 because some fnc's asking for it in getAdminDropdownData.php
        $alco = new PhpExt_AutoLoadConfigObject($url, ['query'=>1], null, 'POST');
        $alco->setScripts(true)->setMethod(PhpExt_AutoLoadConfigObject::AUTO_LOAD_METHOD_POST);
        $store->setAutoLoad($alco);

        // combobox with description tooltip
        $combo = new PhpExt_Form_LovCombo2();

        $combo
            ->setStore($store)
            ->setHiddenName($name.'_id')
            ->setListWidth(160)
            ->setTitle($label)
            ->setTriggerAction(PhpExt_Form_LovCombo2::TRIGGER_ACTION_ALL)
            ->setSelectOnFocus(true)
            ->setFieldLabel($label)
            ->setId($name);

        // allway preload to set checked/unchecked
        $combo->setMode(PhpExt_Form_LovCombo2::MODE_LOCAL);
        $combo->setDisplayField("name");

        if ($this->getSetting('gridType') == 'EditGrid')
        {}
        else {
            $combo->setValueField("id");
        }
        return $combo;
    }

    function _Dropdown($name, $data) {

        $store = $this->_simpleStore($name, $name."_text", $data);

        $data = PhpExt_Form_ComboBox::createComboBox($name,__define('TEXT_DROPDOWN_'.$name))
            ->setStore($store)
            ->setValueField($name)
            ->setDisplayField($name."_text")
            ->setTypeAhead(true)
            ->setMode(PhpExt_Form_ComboBox::MODE_LOCAL)
            ->setTriggerAction(PhpExt_Form_ComboBox::TRIGGER_ACTION_ALL)
            ->setEmptyText(__define("TEXT_SELECT"))
            ->setSelectOnFocus(true);

        return $data;
    }

    /**
     * @param $name
     * @param $data
     * @return PhpExt_Panel
     * @throws Exception
     */
    function _Selection($name, $data) {
        $store = $this->_simpleStore($name, $name."_text", $data);
        $new_data1 = PhpExt_Form_Radio::createRadio("sectionType",__define("TEXT_".$name));
        $new_data1->setInputValue(1)->setValue($data[0][1]);
        $new_data2 = PhpExt_Form_Radio::createRadio("sectionType",__define("TEXT_".$name));
        $new_data2->setInputValue(2)->setValue($data[1][1]);

        $panel = new PhpExt_Panel();
        $panel->addItem($new_data1);
        $panel->addItem($new_data2);

        return $panel;
    }

    function _CloseButton($name) {
        //
        $submitBtn = PhpExt_Button::createTextButton($name,
            new PhpExt_Handler(PhpExt_Javascript::stm("
            contentTabs.remove(contentTabs.getActiveTab());
            ")
            ));
        //          ".$this->code."ds.reload();
        $submitBtn->setIcon($this->getSetting('icons_path')."cancel.png")->setIconCssClass("x-btn-text");

        return $submitBtn;
    }

    function _ResetButton_stm() {
        return $this->code."ds.reload();";
    }

    function _ResetButton($name, $id = '') {
        //
        if ($id) {
            $submitBtn = PhpExt_Button::createTextButton($name,
                new PhpExt_Handler(PhpExt_Javascript::stm($this->code."gridEditForm.getForm().load({url:'".$this->getSetting('readfull_url')."get_singledata=".$id."', waitMsg:'Loading',method: 'GET'})")));
        } else {
            $submitBtn = PhpExt_Button::createTextButton($name,
                new PhpExt_Handler(PhpExt_Javascript::stm($this->_ResetButton_stm())));

        }
        $submitBtn->setIcon($this->getSetting('icons_path')."done.gif")->setIconCssClass("x-btn-text");
        return $submitBtn;
    }
    /*
            function _multiaction($fname, $flag, $flag_value, $data='') {
                //		$data = $this->_ExtSubmitButtonHandler(array('url' => ));
                if ($this->getSetting('gridType') == 'EditGrid') {
                    $gets = ", 'new_data': new_data, 'edit_data': edit_data";
                }

                $renderer = PhpExt_Javascript::functionDef(''.$fname.'', "
                 if(btn == 'yes') {

                    var new_data = new Array();
                    var edit_data = new Array();
                   ".$this->multiselectStm('record_ids')."

                   if (record_ids == '') {
                      record_ids = ".$this->getSelectionItem().";
                   }

                     var conn = new Ext.data.Connection();
                     conn.request({
                     url: '".$this->getSetting('save_url')."',
                     method:'GET',
                     params: {'m_ids': record_ids, ".$flag.":'".$flag_value."' ".$gets." },
                     success: function(responseObject) {
                                ".$this->code."ds.reload();
                              },
                     waitMsg: '".__define("TEXT_LOADING")."',
                     failure: function(){".$this->MsgAlert(__define("TEXT_FAILURE"))."}
                     });

                 }
             ", array('btn'));
                return $renderer;
            }
            */

    function processImage () {
        // quick bugfix ($this->url_data['currentType'] is override by $this->class)
        $this->url_data['currentType'] = $_REQUEST['currentType'];

        $button_params = array('url' => $this->getSetting('sort_url')."edit_id='+".$this->getSelectionItem()."+'");
        //$data = $this->_ExtSubmitButtonHandler($button_params);
        $renderer = PhpExt_Javascript::functionDef('image_processing', "
		 	if(btn == 'yes') {
                var new_data = new Array();
                var edit_data = new Array();
		 	  ".$this->multiselectStm('record_ids')."

		 	  if (record_ids == '') {
		 	     record_ids = ".$this->getSelectionItem().";
		 	  }

    		 	var conn = new Ext.data.Connection();
                 conn.request({
                         url: 'adminHandler.php?load_section=".$this->code."&pg=image_processing&seckey=".constant("_SYSTEM_SECURITY_KEY")."&currentType=".$this->url_data['currentType']."&link_id=".$this->url_data['link_id']."',
                 method:'POST',
                 params: {'m_ids': record_ids},
                 success: function(responseObject) {
                            ".$this->code."ds.reload();
                          },
                 waitMsg: '".__define("TEXT_LOADING")."',
                 failure: function(){".$this->MsgAlert(__define("TEXT_FAILURE"))."}
                 });
		 	}

		 ", array('btn'));
        return $renderer;
    }

    function unlinkImage ()
    {
        // quick bugfix ($this->url_data['currentType'] is override by $this->class)
        $this->url_data['currentType'] = $_REQUEST['currentType'];

        $renderer = PhpExt_Javascript::functionDef('image_unlink', "
		 	if(btn == 'yes') {
                var new_data = new Array();
                var edit_data = new Array();
                ".$this->multiselectStm('record_ids')."
                
                 if (record_ids == '') {
                    record_ids = ".$this->getSelectionItem().";
                 }
                
                var conn = new Ext.data.Connection();
                conn.request({
                    url: 'adminHandler.php?load_section=".$this->code."&pg=_imageUnlink&seckey=".constant("_SYSTEM_SECURITY_KEY")."&currentType=".$this->url_data['currentType']."&link_id=".$this->url_data['link_id']."',
                    method:'POST',
                    params: {'m_ids': record_ids },
                    success: function(responseObject) {
                            ".$this->code."ds.reload();
                          },
                    waitMsg: '".__define("TEXT_LOADING")."',
                    failure: function(){".$this->MsgAlert(__define("TEXT_FAILURE"))."}
                 });
		 	}


		 ", array('btn'));
        return $renderer;
    }

    function importImage () {

        // quick bugfix ($this->url_data['currentType'] is override by $this->class)
        $this->url_data['currentType'] = $_REQUEST['currentType'];

        $button_params = array('url' => $this->getSetting('sort_url')."edit_id='+".$this->getSelectionItem()."+'");
        //$data = $this->_ExtSubmitButtonHandler($button_params);
        $renderer = PhpExt_Javascript::functionDef('image_importing', "
		 	if(btn == 'yes') {
				alert('Tuk');
                var new_data = new Array();
                var edit_data = new Array();
		 	  ".$this->multiselectStm('record_ids')."

		 	  if (record_ids == '') {
		 	     record_ids = ".$this->getSelectionItem().";
		 	  }

    		 	var conn = new Ext.data.Connection();
                 conn.request({
		 url: 'adminHandler.php?load_section=".$this->code."&pg=image_importing&seckey=".constant("_SYSTEM_SECURITY_KEY")."&currentType=".$this->url_data['currentType']."&link_id=".$this->url_data['link_id']."',
                 method:'POST',
                 params: {'m_ids': record_ids},
                 success: function(responseObject) {
                            ".$this->code."ds.reload();
                          },
                 waitMsg: '".__define("TEXT_LOADING")."',
                 failure: function(){".$this->MsgAlert(__define("TEXT_FAILURE"))."}
                 });

		 	}


		 ", array('btn'));
        return $renderer;
    }

    function _SubmitSort($pos = 'up') {

        // quick bugfix ($this->url_data['currentType'] is overriden by $this->class)
        $this->url_data['currentType'] = isset($_REQUEST['currentType']) ?  $_REQUEST['currentType'] : false;

        $button_params = array('url' => $this->getSetting('sort_url')."edit_id='+".$this->getSelectionItem()."+'&pos=".$pos);
        //$data = $this->_ExtSubmitButtonHandler($button_params);
        $renderer = PhpExt_Javascript::functionDef('sort_'.$pos.'', "
		 	if(btn == 'yes') {

                var new_data = new Array();
                var edit_data = new Array();
		 	  ".$this->multiselectStm('record_ids')."

		 	  if (record_ids == '') {
		 	     record_ids = ".$this->getSelectionItem().";
		 	  }

    		 	var conn = new Ext.data.Connection();
                 conn.request({
                 url: '".$this->getSetting('sort_url')."currentType=".$this->url_data['currentType']."&link_id=".$this->url_data['link_id']."&pos=".$pos."',
                 method:'POST',
                 params: {'m_ids': record_ids},
                 success: function(responseObject) {
                            ".$this->code."ds.reload();
                          },
                 waitMsg: '".__define("TEXT_LOADING")."',
                 failure: function(){".$this->MsgAlert(__define("TEXT_FAILURE"))."}
                 });

		 	}


		 ", array('btn'));
        return $renderer;
    }

    function _SubmitSort2($pos = 'up')
    {
        $renderer = PhpExt_Javascript::functionDef('sort2_'.$pos.'', "
        //console.log(btn);
		 	if(btn == 'yes') {

                var new_data = new Array();
                var edit_data = new Array();
		 	  ".$this->multiselectStm('record_ids')."

		 	  if (record_ids == '') {
		 	     record_ids = ".$this->getSelectionItem().";
		 	  }

    		 	var conn = new Ext.data.Connection();
                 conn.request({
                 url: '".$this->getSetting('sort_url')."master_item_id=".$this->url_data[$this->params['resort_key']]."&pos=".$pos."',
                 method:'POST',
                 params: {'m_ids': record_ids },
                 success: function(responseObject) {
                            ".$this->code."ds.reload();
                          },
                 waitMsg: '".__define("TEXT_LOADING")."',
                 failure: function(){".$this->MsgAlert(__define("TEXT_FAILURE"))."}
                 });
		 	}
		 ", array('btn'));
        return $renderer;
    }


    ////////////////////////////////////////
    // button functions
    function _SubmitButton_stm ($formname, $data = array(), $close='close', $success = '') {

        $button_params = array('url' => $this->getSetting('save_url')."edit_id='+".$this->getSelectionItem()."+'&save=true");

        if (is_array($data))
            $button_params = array_merge($button_params, $data);

        $button_params['close'] = $close;
        $button_params['success'] = $success;
        $data = $this->_ExtSubmitButtonHandler($button_params);


        if ($this->getSetting('gridType') != 'EditGrid') {
            return "Ext.getCmp(\"".$this->code.$this->getSelectionItem().$formname."\").getForm().submit({". $data ." })";
        }

        $string = "
        var records = ".$this->code."ds.getModifiedRecords();
        var new_data = new Array();
        var edit_data = new Array();
        edit_data[0] = '';
        for (var i = 0; i < records.length; i++) {";
        if(is_array($this->header)){
            foreach ($this->header as $header_data) {
                $string.= "edit_data[0]+= '##_key_##".$header_data['name']."##_val_##' + records[i].get('".$header_data['name']."');";
            }
        }
        $string.= "edit_data[0]+= '##_next_##';";
        $string.= "}";

        $string.= "
        var new_records = ".$this->code."_new_records;
        new_data[0] = '';
        for (var i = 0; i < new_records.length; i++) {";
        if(is_array($this->header)){
            foreach ($this->header as $header_data) {
                $string.= "new_data[0]+= '##_key_##".$header_data['name']."##_val_##' + new_records[i].get('".$header_data['name']."');";
            }
        }
        $string.= "new_data[0]+= '##_next_##';";
        $string.= "}";
        $string.= "if (records.length + new_records.length == 0) {";
        $string.= $this->MsgAlert(__define('TEXT_NO_CHANGES'));
        $string.= "} else {";
        $string.= $this->MsgConfirm(__define("TEXT_SAVE_ALL"), 'doSaveGrid');
        $string.= "}";

        $string.= "function doSaveGrid(btn) {
		 	if(btn == 'yes') {
    		  var conn = new Ext.data.Connection();
                 conn.request({
                 url: '".$this->getSetting('save_grid_url')."',
                 method:'POST',
                 params: {totalCount: records.length + new_records.length, totalEditCount: records.length, totalNewCount: new_records.length, edit_data: edit_data , new_data: new_data},
                 success: function(responseObject) {

                            ".$this->code."ds.reload();
                            ".$this->code."_new_records = new Array();
                            ".$this->code."ds.rejectChanges();
                          },
                 error: function(responseObject) {
                            ".$this->MsgAlert(__define('TEXT_NO_SUCCESS'))."
                          },
                 waitMsg: 'SAVED..'
                 });
               }
		 	}";

        /*
                $string.= "
                    var conn = new Ext.data.Connection();
                         conn.request({
                         url: '".$this->getSetting('save_grid_url')."',
                         method:'POST',
                         params: {get_data: true,totalCount: records.length + new_records.length, totalEditCount: records.length, totalNewCount: new_records.length, edit_data: edit_data , new_data: new_data},
                         success: function(responseObject) {
                                    ".$this->code."ds.reload();
                                  },
                         });

                        ";
        */

        return $string;

        //return "Ext.getCmp(\"".$this->code."gridForm\").getForm().submit({". $data ." })";


    }
    function _saveGrid() {
//		$data = $this->_ExtSubmitButtonHandler(array('url' => ));

        $renderer = PhpExt_Javascript::functionDef('doSaveGrid', "
		 	if(btn == 'yes') {
    		  var conn = new Ext.data.Connection();
                 conn.request({
                 url: '".$this->getSetting('save_grid_url')."',
                 method:'POST',
                 params: {get_data: true,totalCount: records.length + new_records.length, totalEditCount: records.length, totalNewCount: new_records.length, edit_data: edit_data , new_data: new_data},
                 success: function(responseObject) {
                            ".$this->code."ds.reload();
                          }
                 });

		 	}
		 ", array('btn'));
        return $renderer;

    }
    function _SubmitButton($name, $waitMsg = '', $formname = '-form', $close = 'close')
    {
        global $xtPlugin;

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':top')) ? eval($plugin_code) : false;
        if(isset($plugin_return_value))
            return $plugin_return_value;

        $data = $this->_ExtSubmitButtonHandler(array('url' => $this->getSetting('save_url')."edit_id='+".$this->getSelectionItem()."+'&save=true", "close" => $close));

        $submitBtn = PhpExt_Button::createTextButton($name,
            new PhpExt_Handler(PhpExt_Javascript::stm($this->_SubmitButton_stm($formname, $data, $close))));
        if($close=='close')
            $submitBtn->setIcon($this->getSetting('icons_path')."disk_multiple.png")->setIconCssClass("x-btn-text");
        else
            $submitBtn->setIcon($this->getSetting('icons_path')."disk.png")->setIconCssClass("x-btn-text");

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':bottom')) ? eval($plugin_code) : false;
        return $submitBtn;
    }

    function _MultiButton($text, $func)
    {
        global $xtPlugin;

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':top')) ? eval($plugin_code) : false;
        if(isset($plugin_return_value))
            return $plugin_return_value;

        $submitBtn = PhpExt_Button::createTextButton(__define($text),
            new PhpExt_Handler(PhpExt_Javascript::stm($this->_MultiButton_stm($text, $func))
            ));
        return $submitBtn;
    }

    function _MultiButton_stm($text, $func) {
        return $this->MsgConfirm(__define($text), $func);
    }

    function _GetSelectedButton_stm() {
        return $this->MsgConfirm(__define("TEXT_SELECT"), 'doGetSelected');
    }

    function _GetSelectedButton ()
    {
        global $xtPlugin;

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':top')) ? eval($plugin_code) : false;
        if(isset($plugin_return_value))
            return $plugin_return_value;

        $submitBtn = PhpExt_Button::createTextButton(__define('TEXT_SELECT'),
            new PhpExt_Handler(PhpExt_Javascript::stm($this->_GetSelectedButton_stm())

            ));
        return $submitBtn;
    }

    function _NewButton_stm() {

        $string = '';

        if ($this->getSetting('gridType') != 'EditGrid') {
            return "addTab('".$this->getSetting('readfull_url')."new=true&gridHandle=".$this->code."gridForm','".__define("TEXT_". $this->code).' ' . __define("TEXT_NEW") ."');";
        }

        if(is_array($this->header)){

            $string ="var p = new ".$this->getSelectionItem()."_new_record({";
            foreach ($this->header as $header_data) {
                switch ($header_data['type']) {
                    /*				case "textarea":
                     break; */
                    case "price":
                        $string.= $header_data['name'].": 0,";
                        break;
                    case "master_key":
                        $string.= $header_data['name'].":'new',";
                        break;

                    case "date":
                        $string.= $header_data['name'].": (new Date()).clearTime(),";
                        break;
                    case "status":
                        $string.= $header_data['name'].": false,";
                        break;
                    /*				case "image":
                     break;
                     */
                    default:
                        //                    $string.= $header_data['name'].":'".__define('TEXT_'.$header_data['name'].'_NEW')."',";
                        if ($header_data['default_data'])
                            $string.= $header_data['name'].":'".$header_data['default_data']."',";
                        else
                            $string.= $header_data['name'].":'',";
                        break;

                }
            }

            $string = substr($string,0, strlen($string)-1);
            $string.="});"
                .$this->code.$this->getSelectionItem()."gridForm.stopEditing();"
                .$this->code."ds.insert(0, p);"
                .$this->code."_new_records.push(p);"
                .$this->code.$this->getSelectionItem()."gridForm.startEditing(0, 0);";
        }
        return $string;
    }

    function _NewButton ()
    {
        global $xtPlugin;

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':top')) ? eval($plugin_code) : false;
        if(isset($plugin_return_value))
            return $plugin_return_value;

        $submitBtn = PhpExt_Button::createTextButton(__define('TEXT_NEW'),
            new PhpExt_Handler(PhpExt_Javascript::stm($this->_NewButton_stm())));

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':bottom')) ? eval($plugin_code) : false;

        return $submitBtn;
    }

    function _EditButton_stm() {
        if($this->checkPermission('edit'))
            return "
			//console.log(".$this->getSelectionItem().");
			if(typeof(".$this->getSelectionItem().") != 'undefined')
			    addTab('".$this->getSetting('edit_url')."edit_id='+".$this->getSelectionItem()."+'&gridHandle=".$this->code."gridForm','".__define("TEXT_". $this->code)/*.' ' . __define("TEXT_EDIT") */."');";
        return '';
    }

    function _EditButton()
    {
        global $xtPlugin;

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':top')) ? eval($plugin_code) : false;
        if(isset($plugin_return_value))
            return $plugin_return_value;

        $submitBtn = PhpExt_Button::createTextButton(__define("TEXT_EDIT"),
            new PhpExt_Handler(PhpExt_Javascript::stm($this->_EditButton_stm())));
        return $submitBtn;
    }

    /**
     * @return string
     * @throws Exception
     */
    function _PopupButton_stm() {
        //if($this->checkPermission('popup'))
        //$js.= $this->_RemoteWindow($this->getSetting('popup_window_text'), $this->getSetting('popup_text'), "adminHandler.php?load_section=product&products_id=1&pg=overview", $this->getSetting('popup_url_short'), $this->getSetting('popup_params'), $this->getSetting('popup_width'), $this->getSetting('popup_height')).' new_window.show();';
        $js= $this->_RemoteWindow($this->getSetting('popup_window_text'),$this->getSetting('popup_text'),$this->getSetting('popup_url'), $this->getSetting('popup_url_short'), $this->getSetting('popup_params'), $this->getSetting('popup_width'), $this->getSetting('popup_height')).' new_window.show();';
        return $js;
    }

    /**
     * @return string
     * @throws Exception
     */
    function _PopupButton()
    {
        global $xtPlugin;

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':top')) ? eval($plugin_code) : false;
        if(isset($plugin_return_value))
            return $plugin_return_value;

        $submitBtn = PhpExt_Button::createTextButton(__define("TEXT_POPUP"),
            new PhpExt_Handler(PhpExt_Javascript::stm($this->_PopupButton_stm())));
        return $submitBtn;
    }

    function  _ExtSubmitButtonHandler ($data) {
        $js = '';

        if (isset($data['url']) && !empty($data['url']))
            $js.= " url: '".$data['url']."'";

        if (isset($data['success']) && !empty($data['success']))
            $js.= ", success: ".$data['success'];
        elseif ($data['close']=='close')
        {
            $cmpId = $_REQUEST['gridHandle'];
            if($cmpId == 'productgridForm' && !empty($_REQUEST['catID']))
            {
                $cmpId = 'product'.$_REQUEST['catID'].'gridForm';
            }
            //	$js.= ", success: function(){contentTabs.remove(contentTabs.getActiveTab()); Ext.getCmp('".$_REQUEST['gridHandle']."').getStore().reload(); }";
            $js .= ", success: function(form,action){
                    var goto_url = Ext.encode(action.result.goto_url)
                    if (goto_url != 'null') {
                        goto_url = goto_url.replace('\"','');
                        Ext.Msg.alert('Registrierung','<a href=\"' + goto_url+'\" target=\"_blank\">[Anmeldung abschliessen]</a>');
                    } else {
                        if ((typeof(action.result.message) != 'undefined') && (action.result.message.length > 0)) {
                             Ext.Msg.alert('".__define("TEXT_SUCCESS")."', action.result.message);
                        }
                        contentTabs.remove(contentTabs.getActiveTab()); var cmp = Ext.getCmp('" . $cmpId . "'); if(typeof cmp != 'undefined') cmp.getStore().reload(); else console.log('cmp [" . $cmpId . "] not found');
                    }
                }";
        }
        else
            $js.= ", success: function(form,action){

                if(typeof action.result != 'undefined')
                {
                    var error_message = Ext.encode(action.result.error_message)
                    if (error_message != 'null') {
                        error_message = error_message.replace(/\"/g , '');
                        Ext.Msg.alert('".__define("TEXT_SUCCESS")."',error_message);
                        } else {
                        ".$this->MsgAlert(__define("TEXT_SUCCESS"))."
                        }
                    }
                    if(typeof action.result !== 'undefined' 
                        && typeof action.result.callback !== 'undefined' 
                        && typeof action.result.callback.fnc !== 'undefined')
                    {
                        try {
                            var fn = (typeof window[action.result.callback.fnc] == 'function') ?  window[action.result.callback.fnc] : window[action.result.callback.fnc]; 
                            var args = [];
                            args.push(arguments[0]);    
                            args.push(arguments[1]);    
                            if(typeof action.result.callback.args === 'object')
                                args.push(action.result.callback.args);
                            else if(typeof action.result.callback.args === 'Array')
                                args.push(action.result.callback.args);
                                
                            fn.apply(this, args);  
                        }
                        catch(e)
                        {
                            console.error(e);
                        }
                    }
                }
                
                
                ";

        if (isset($data['failure']) && !empty($data['failure']))
            $js.= ", failure: ".$data['failure']."";
        else // TODO implement logging for 404/500 returns from servers
            $js.= ", failure: function(form,action){
                
                    //console.log('error processing');
                if(typeof action.result != 'undefined')
                {
                    var error_message = Ext.encode(action.result.error_message)
                    if (error_message != 'null') {
                        error_message = error_message.replace(/\"/g , '');
                        Ext.Msg.alert('".__define("TEXT_FAILURE")."',error_message);
                        } else {
                        ".$this->MsgAlert(__define("TEXT_FAILURE"))."
                        }
                    }
                    if(typeof action.result !== 'undefined' 
                        && typeof action.result.callback !== 'undefined' 
                        && typeof action.result.callback.fnc !== 'undefined')
                    {
                        try {
                            var fn = (typeof window[action.result.callback.fnc] == 'function') ?  window[action.result.callback.fnc] : window[action.result.callback.fnc]; 
                            var args = [];
                            args.push(arguments[0]);    
                            args.push(arguments[1]);    
                            if(typeof action.result.callback.args === 'object')
                                args.push(action.result.callback.args);
                            else if(typeof action.result.callback.args === 'Array')
                                args.push(action.result.callback.args);
                                
                            fn.apply(this, args);  
                        }
                        catch(e)
                        {
                            console.error(e);
                        }
                    }
                }";


        //   $js.= ", failure: function(){".$this->MsgAlert(__define("TEXT_FAILURE"))."}";
        if (isset($data['params']) && !empty($data['params']))
            $js.= ", params: ".$data['params']."";

        $js.= ", waitMsg: '".__define("TEXT_LOADING")."'";

        return $js;
    }
    // button functions end
    ////////////////////////////////////////


    // label position
    function _getLabelPos(PhpExt_Form_FormPanel $gridForm) {
        switch ($this->getSetting('FormLabelPos')) {
            case "left":
                $gridForm->setLabelAlign(PhpExt_Form_FormPanel::LABEL_ALIGN_LEFT);
                break;
            case "right":
                $gridForm->setLabelAlign(PhpExt_Form_FormPanel::LABEL_ALIGN_RIGHT);
                break;
            default:
                $gridForm->setLabelAlign(PhpExt_Form_FormPanel::LABEL_ALIGN_TOP);
                break;

        }
        return $gridForm;
    }

    ////////////////////////////////////////
    // edit field functions
    /**
     * @param $header_key
     * @return string
     */
    function getEditFieldType ($header_key) {
        $type = 'textfield';
        if (preg_match('/desc/', $header_key) && !preg_match('/meta/', $header_key))
            $type = 'textarea';
        if (preg_match('/price/', $header_key))
            $type = 'price';
        if (preg_match('/status/', $header_key) && !preg_match('/adminActionStatus/', $header_key))
            $type = 'status';
        if (preg_match('/adminActionStatus/', $header_key))
            $type = 'adminActionStatus';
        if (preg_match('/shop/', $header_key)
            || preg_match('/flag/', $header_key)
            || preg_match('/permission/', $header_key)
            || preg_match('/fsk18/', $header_key)
            || preg_match('/startpage/', $header_key))
            $type = 'status';
        if (preg_match('/date/', $header_key) || preg_match('/last_modified/', $header_key))
            $type = 'date';
        if (preg_match('/image/', $header_key))
            $type = 'image';
        if (preg_match('/file/', $header_key))
            $type = 'file';
        if (preg_match('/icon/', $header_key))
            $type = 'icon';
        if (preg_match('/url/', $header_key))
            $type = 'url';
        if (preg_match('/_html/', $header_key))
            $type = 'htmleditor';
        if (preg_match('/_hidden/', $header_key))
            $type = 'hidden';
        if(preg_match('/template/',$header_key)) {
            $type = 'dropdown';
        }
        if (preg_match('/_permission_info/', $header_key))
            $type = 'admininfo';


        if ($this->getMasterKey() == $header_key)
            $type = 'master_key';
        return $type;
    }

    function setMasterKey($key) {
        $this->master_key = $key;
    }

    function getMasterKey() {
        if (!$this->master_key && $this->params['master_key']) {
            $this->master_key = $this->params['master_key'];
        }
        if (!$this->master_key) {
            echo 'class: "' .$this->code . '" function "_getParams()": $params[\'master_key\'] is empty or not set';
            die;
        }
        return $this->master_key;
    }

    function getEditFieldLang ($header_key, $value) {
        global $language;

        $split_name = preg_split('/_/',$header_key);
        foreach ($language->_getLanguageList() as $key => $val) {
            if ($split_name[(count($split_name)-1)] == $val['code']) {
                if (in_array(str_replace('_'.$val['code'], '', $header_key), $this->getSetting('exclude')))
                    return 'exclude';
                return $val['code'];
            }
        }
        return false;
    }

    // edit field functions end
    ////////////////////////////////////////
    function multiselectStm ($var = 'record_ids', $addModifiedRecordsData = true) {
        $js = "
             var records = new Array();
             records = ".$this->code."ds.getModifiedRecords();
             console.log('here in multiselectStm');
             ". ($addModifiedRecordsData ? $this->_getGridModifiedRecordsData():'') ."
		 	 var ".$var." = [];
		 	 for (var i = 0; i < records.length; i++) {
		 	     if (records[i].get('selectedItem'))
		 	      ".$var.".push(records[i].get('".$this->getMasterKey()."'));
		 	 }
             ".$this->code."ds.modified = [];

             ".$var." = ".$var.".join(',');   
                
		 	 ";
        return $js;
    }

    function _getGridModifiedRecordsData() {
        $string = "";
        if ($this->getSetting('gridType') == 'EditGrid') {
            $string = "

                edit_data[0] = '';
                for (var i = 0; i < records.length; i++) {";
            foreach ($this->header as $header_data) {
                $string.= "edit_data[0]+= '##_key_##".$header_data['name']."##_val_##' + records[i].get('".$header_data['name']."');";
            }
            $string.= "edit_data[0]+= '##_next_##';";
            $string.= "}";

            $string.= "
                var new_records = ".$this->code."_new_records;
                new_data[0] = '';
                for (var i = 0; i < new_records.length; i++) {";
            foreach ($this->header as $header_data) {
                $string.= "new_data[0]+= '##_key_##".$header_data['name']."##_val_##' + new_records[i].get('".$header_data['name']."');";
            }
            $string.= "new_data[0]+= '##_next_##';";
            $string.= "}";
            /*
                                $string.= "if (records.length + new_records.length == 0) {";
                                $string.= $this->MsgAlert(__define('TEXT_NO_CHANGES'));
                                $string.= "} else {";
                                $string.= $this->MsgConfirm(__define("TEXT_SAVE_ALL"), 'doSaveGrid');
                                $string.= "}";
                                */


        }
        return $string;
    }

    /**
     * @param $fname
     * @param $flag
     * @param $flag_value
     * @param string $data
     * @param string $page
     * @param string $page_url
     * @param string $page_title
     * @param string $success_code
     * @return PhpExt_JavascriptStm
     * @throws Exception
     */
    function _singleaction($fname, $flag, $flag_value, $data='', $page='self', $page_url='', $page_title='', $success_code = '') {
        //		$data = $this->_ExtSubmitButtonHandler(array('url' => ));
        $gets = "";
        if ($this->getSetting('gridType') == 'EditGrid') {
            $gets = ", 'new_data': new_data, 'edit_data': edit_data";
        }

        if (empty($success_code))
        {
            $success_code = "
				var r = Ext.decode(responseObject.responseText);
				//console.log(r);
				if(typeof(r)!='undefined' &&  typeof(r.success)!='undefined' && r.success==false)
				{
					var msg = typeof(r.messageText)!='undefined' && r.messageText!='' ? r.messageText : 'Unknown error. Check logs';
					Ext.Msg.alert('Error',msg);
				}
				";
        }

        $string = "if(btn == 'yes') {";

        $string .= "   var new_data = new Array();";
        $string .= "    var edit_data = new Array();";

        //$string .= "  	if (record_ids == '') {";
        $string .= "     record_ids = ".$this->getSelectionItem().";";
        //	$string .= "  	}";

        if($page=='tab'){

            $string .= "addTab('".$page_url."&value_ids='+record_ids,'".$page_title."');";

        }elseif($page=='window'){

            $string.= $this->_RemoteWindow("".$page_title."","".$page_title."","".$page_url."&value_ids='+record_ids+'", true, array(), 800, 600).' new_window.show();';

        }else{

            $string .= "	var conn = new Ext.data.Connection();";
            $string .= "    conn.request({";
            $string .= "     url: '".$this->getSetting('save_url')."',";
            $string .= "     method:'GET',";
            $string .= "     params: {'m_ids': record_ids, ".$flag.":'".$flag_value."' ".$gets." },";
            $string .= "     success: function(responseObject) {";
            //$string .= "     /* alert(Ext.encode(responseObject.responseText)); */";
            $string .= "				" . $success_code . "";
            $string .= "               ".$this->code."ds.reload();";
            $string .= "              },";
            $string .= "     waitMsg: '".__define("TEXT_LOADING")."',";
            $string .= "     failure: function(){".$this->MsgAlert(__define("TEXT_FAILURE"))."}";
            $string .= "     });";

        }

        $string .= "} ";

        $renderer = PhpExt_Javascript::functionDef(''.$fname.'', $string, array('btn'));

        return $renderer;
    }

    /**
     * @param $fname
     * @param $flag
     * @param $flag_value
     * @param string $data
     * @param string $page
     * @param string $page_url
     * @param string $page_title
     * @param string $success_code
     * @return PhpExt_JavascriptStm
     * @throws Exception
     */
    function _multiaction($fname, $flag, $flag_value, $data='', $page='self', $page_url='', $page_title='', $success_code = '') {
        //		$data = $this->_ExtSubmitButtonHandler(array('url' => ));
        $gets = "";
        if ($this->getSetting('gridType') == 'EditGrid') {
            $gets = ", 'new_data': new_data, 'edit_data': edit_data";
        }

        if (empty($success_code))
        {
            $success_code = "
				var r = Ext.decode(responseObject.responseText);
				//console.log(r);
				if(typeof(r)!='undefined' &&  typeof(r.success)!='undefined' && r.success==false)
				{
					var msg = typeof(r.messageText)!='undefined' && r.messageText!='' ? r.messageText : 'Unknown error. Check logs';
					Ext.Msg.alert('Error',msg);
				}
				";
        }

        $string = "if(btn == 'yes') {";

        $addModifiedRecordsData = true;
        if ($fname == 'doDelete') $addModifiedRecordsData = false;

        $string .= "   var new_data = new Array();\n";
        $string .= "    var edit_data = new Array();\n";
        $string .= "  ".$this->multiselectStm('record_ids', $addModifiedRecordsData)."\n";

        $string .= "  	if (record_ids == '') {\n";
        $string .= "     record_ids = ".$this->getSelectionItem().";\n";
        $string .= "  	}";

        if($page=='tab'){

            $string .= "addTab('".$page_url."&value_ids='+record_ids,'".$page_title."');";

        }elseif($page=='window'){

            $string.= $this->_RemoteWindow("".$page_title."","".$page_title."","".$page_url."&value_ids='+record_ids+'", true, array(), 800, 600).' new_window.show();';

        }else{

            $string .= "	var conn = new Ext.data.Connection();\n";
            $string .= "    conn.request({\n";
            $string .= "     url: '".$this->getSetting('save_url')."',\n";
            $string .= "     method:'POST',\n";
            $string .= "     params: {'m_ids': record_ids, ".$flag.":'".$flag_value."' ".$gets." },\n";
            $string .= "     success: function(responseObject) {\n";
            $string .= "   
                $('input[id^=check_all_prod]').prop( 'checked', false );\n
            ";
            //$string .= "     /* alert(Ext.encode(responseObject.responseText)); */";
            $string .= "				" . $success_code . "\n";
            $string .= "               ".$this->code."ds.reload();\n";
            $string .= "              },\n";
            $string .= "     waitMsg: '".__define("TEXT_LOADING")."',\n";
            $string .= "     failure: function(){".$this->MsgAlert(__define("TEXT_FAILURE"))." }\n";
            $string .= "     });\n";

        }

        $string .= "} \n";

        $renderer = PhpExt_Javascript::functionDef(''.$fname.'', $string, array('btn'));

        return $renderer;
    }

    /**
     * @return array
     * @throws Exception
     */
    function add_multiactions(){
        $this->setSetting('display_Btn',  $this->_systemButtons());
        $new_buttons = $this->getSetting('display_Btn');

        $js = [];
        if(is_array($new_buttons) && count($new_buttons)>0){
            reset($new_buttons);
            foreach ($new_buttons as $key => $val) {
                $page = isset($val['page']) ?: null;
                $page_url = isset($val['page_url']) ?: null;
                $page_title = isset($val['page_title']) ?: null;
                $success_code = isset($val['success_code']) ?: null;
                if($this->checkPermission($key) && isset($val['func']))
                    $js[] = $this->_multiaction($val['func'], $val['flag'], $val['flag_value'], $js, $page, $page_url, $page_title, $success_code);
                if (isset($val['func']) && ($val['func']=='doCopy' || $val['func']=='doDelete'))
                    $js[] = $this->_singleaction($val['func'].'_single', $val['flag'], $val['flag_value'], $js, $page, $page_url, $page_title, $success_code);
            }
        }

        return $js;
    }

    /**
     * @return array
     * @throws Exception
     */
    function add_multimenuactions(){
        $items = $this->getSetting('menuActions');
        $new_items = [];
        if(is_array($items) && count($items)>0){
            reset($items);
            foreach ($items as $key => $val) {
                if(!isset($val['func']) || !$val['func']){
                    foreach ($val as $nkey => $nval) {
                        $new_items[$nkey] = $nval;
                    }
                }else{
                    $new_items[] = $val;
                }
            }
        }
        $js = [];
        if(is_array($new_items) && count($new_items)>0){
            reset($new_items);
            foreach ($new_items as $key => $val) {
                if($this->checkPermission($key) && $val['func']){
                    $js[] = $this->_multiaction($val['func'], $val['flag'], $val['flag_value'], $js, $val['page'], $val['page_url'], $val['page_title']);
                }
            }
        }

        return $js;
    }

    ////////////////////////////////////////
    // message functions
    function MsgAlert($text, $title = '') {
        if(empty($title)) $title = __define("TEXT_ALERT") ;
        $text  = trim(str_replace(array("\r\n","\r","\n"), ' ', __define($text)));
        $title = trim(str_replace(array("\r\n","\r","\n"), ' ', __define($title)));
        $msg = "Ext.Msg.alert('".$title."','".$text."')";
        return $msg;

    }
    function MsgConfirm($text, $function = '', $title = '') {
        if(empty($title)) $title = __define("TEXT_CONFIRM") ;

        if ($function != '')
            $function = ', '.$function;

        $text  = trim(str_replace(array("\r\n","\r","\n"), ' ', __define($text)));
        $title = trim(str_replace(array("\r\n","\r","\n"), ' ', __define($title)));
        $msg = "Ext.Msg.confirm('".$title."','".$text."' ".$function.")";
        return $msg;
    }
    function MsgPrompt($text, $function = '', $title = '') {
        if(empty($title)) $title = __define("TEXT_LOADING") ;
        if ($function != '')
            $function = ', '.$function;

        $text  = trim(str_replace(array("\r\n","\r","\n"), ' ', __define($text)));
        $title = trim(str_replace(array("\r\n","\r","\n"), ' ', __define($title)));
        $msg = "Ext.Msg.prompt('".$title."','".$text."' ".$function.")";
        return $msg;
    }
    function MsgShow($text, $function = '') {
        if ($function != '')
            $function = ',fn: '.$function;
        $text  = trim(str_replace(array("\r\n","\r","\n"), ' ', __define($text)));
        $msg = "Ext.Msg.show({title: '".$text."',closable: true, wait: true ".$function."})";
        return $msg;
    }


    // message functions end
    ////////////////////////////////////////

    function _gridRowdblClick() {
        if ($this->getSetting('gridType') != 'EditGrid') {
            $gridRowdblClick = PhpExt_Javascript::variable(
                $this->code.$this->getSelectionItem()."gridForm.on('rowdblclick',function() {".$this->_EditButton_stm()."})");
            return $gridRowdblClick;
        } else {
            $gridRowdblClick = PhpExt_Javascript::variable("
    	      ");
            return $gridRowdblClick;
        }
    }


    function _RowAction() {

        $js='';
        $settings = $this->getSetting('rowActionsFunctions');
        $add_js = $this->getSetting('rowActionsJavascript');
        if (is_array($settings)) {
            foreach($settings as $action => $fkt_content) {
                $js.="if (action == '".$action."') { ".$fkt_content." }";
            }
        }

        $rowaction = PhpExt_Javascript::variable(
            "rowAction.on('action',function(grid, record, action, row, col) {".
            "if (action == 'edit') { ".$this->_EditButton_stm()."}".
            "if (action == 'copy') { ".$this->_MultiButton_stm('BUTTON_COPY', 'doCopy_single' )."}".
            "if (action == 'delete') { ".$this->_MultiButton_stm('BUTTON_DELETE', 'doDelete_single')."}".
            $js."}); ".$add_js."");
        return $rowaction;
    }

    function _MenuAction() {
        return "";
    }

    function _gridRightClick() {

        //var selectedItem = ds.getAt(rowIndex).id;
        $gridRowdblClick = PhpExt_Javascript::variable(
            $this->code.$this->getSelectionItem()."gridForm.on('contextmenu',function() {".
            $this->MsgAlert(__define("ERROR_TEXT_RIGHT_CLICK_FORBID"), __define("ERROR_HEADING_RIGHT_CLICK_FORBID"))."})");
        return $gridRowdblClick;
    }

    function _searchField($store) {
        $searchField = new PhpExtUx_App_SearchField();
        $searchField->setStore($store)
            ->setWidth(240);
        return $searchField;
    }

    function _checkboxField($parent_node) {
        $checkboxField = new PhpExtUx_App_CheckboxField($parent_node);
        return $checkboxField;
    }

    /**
     * @param $input_field
     * @param $inputID
     * @param array $data
     * @return PhpExt_Panel
     * @throws Exception
     */
    function _logoUploadPanel ($input_field, $inputID, $data = array()) {

        $mediaWindow = $this->getMediaWindow(false,true,  true, 'images', '','logo');

        // edit button

        $button_upload = PhpExt_Button::createTextButton(__define("BUTTON_EDIT_IMAGES"),
            new PhpExt_Handler(
                PhpExt_Javascript::stm($mediaWindow->getJavascript(false, "new_window")."new_window.show();")
            ));
        $button_upload->setIcon($this->getSetting('icons_path')."picture_add.png")->setIconCssClass("x-btn-text");

        $input = new PhpExt_Panel();
        $input->setBodyStyle("padding: 0; border:none;")
            ->setLayout(new PhpExt_Layout_FormLayout())
            ->addItem($input_field);

        $buttonPanelUpload = new PhpExt_Panel();
        $buttonPanelUpload->setBodyStyle("padding: 0; border:none; margin-top: 2px;");
        $buttonPanelUpload->addItem($button_upload);

        $columnPanel = new PhpExt_Panel();
        $columnPanel->setLayout(new PhpExt_Layout_ColumnLayout());
        $columnPanel->setBodyStyle("padding: 0; border:none;");
        $columnPanel->addItem($input);

        $columnPanel->addItem($buttonPanelUpload);

        $Panel = new PhpExt_Panel();
        $Panel->setBodyStyle("padding: 0; border:none;  margin-left:-3px");
        $Panel->setCssStyle("margin-left:-3px");
        $Panel->addItem($columnPanel);

        return $Panel;
    }

    /**
     * @param $input_field
     * @param $inputID
     * @param array $data
     * @return PhpExt_Panel
     * @throws Exception
     */
    function _fileUploadPanel ($input_field, $inputID, $data = array()) {

        $button_browse = PhpExt_Button::createTextButton(__define("BUTTON_BROWSE_FILES"),
            new PhpExt_Handler(PhpExt_Javascript::stm($this->getSelectionFileItem()." = '".$inputID."'; fchoose.show(this.getEl(),insertFile);")));
        $button_browse->setIcon($this->getSetting('icons_path')."pictures.png")->setIconCssClass("x-btn-text");

        $input = new PhpExt_Panel();
        $input->setBodyStyle("padding: 0; border:none;")
            ->setLayout(new PhpExt_Layout_FormLayout())
            ->addItem($input_field);

        $buttonPanelBrowse = new PhpExt_Panel();
        $buttonPanelBrowse->setBodyStyle("padding: 0 5px; border:none;")
            ->addItem($button_browse);

        $columnPanel = new PhpExt_Panel();
        $columnPanel->setLayout(new PhpExt_Layout_ColumnLayout());
        $columnPanel->setBodyStyle("padding: 0; border:none;");
        $columnPanel->addItem($input);
        $columnPanel->addItem($buttonPanelBrowse);

        //$columnPanel->addItem($buttonPanelUpload);

        $Panel = new PhpExt_Panel();
        $Panel->setBodyStyle("padding: 0; border:none; margin-bottom: 5px;");
        $Panel->addItem($columnPanel);
        return $Panel;
    }

    /**
     * @param bool $show_grid
     * @param bool $show_flash_upload
     * @param bool $show_simple_upload
     * @param string $type
     * @param string $params
     * @param string $force_code
     * @return PhpExt_Window|string|null
     * @throws Exception
     *
     */
    function getMediaWindow($show_grid = true, $show_flash_upload = true, $show_simple_upload = true, $type = 'images', $params = '',$force_code='')
    {
        if ($force_code!='') $code = $force_code;
        else $code = $this->code;
        if ($code=='custom_link')  {
            $code = 'category';
        }
        if ($this->url_data['edit_id'] && ! is_int($this->url_data['edit_id']))
        {
            $tmp_link_id_array = explode('_', $this->url_data['edit_id']);
            $tmp_link_id_array = array_reverse($tmp_link_id_array);
            $exp = explode("_catst_",$this->url_data['edit_id']);
            if (count($exp)>1){
                $this->url_data['edit_id'] = $exp[0];
            }
            else $this->url_data['edit_id'] = $tmp_link_id_array[0];
        }

        $tab = array();
        $add_to_url = (isset($_SESSION['admin_user']['admin_key']))? "&sec=".$_SESSION['admin_user']['admin_key']."": '';
        // tab 1 grid
        if ($show_grid)
        {
            $tab[] = array(
                'url' => 'adminHandler.php?load_section=MediaImageList&pg=overview&currentType='.$code.'&link_id='.$this->url_data['edit_id'].$add_to_url,
                'url_short' => true,
                'params' => $params,
                'title' => 'TEXT_IMAGES'
            );
        }

        // tab 2 search
        if ($show_grid)
        {
            $tab[] = array(
                'url' => 'adminHandler.php?load_section=MediaImageSearch&pg=overview&currentType='.$code.'&link_id='.$this->url_data['edit_id'].$add_to_url,
                'url_short' => true,
                'params' => $params,
                'title' => 'TEXT_SEARCH_IMAGES'
            );
        }


        // tab 4 simple upload
        if ($show_simple_upload)
        {
            $tab[] = array(
                'url' => 'upload.php',
                'url_short' => true,
                'params' => 'uploadtype=single&type='.$type.'&currentType='.$code.'&link_id='.$this->url_data['edit_id'].$params.$add_to_url,
                'title' => 'TEXT_SIMPLE_UPLOAD'
            );
        }


        // tab 5 ckfinder upload
        if ($code != 'logo') {
            $tab[] = array(
                'url' => 'uploadCKFinder.php',
                'url_short' => true,
                'params' => 'uploadtype=single&type='.$type.'&currentType='.$this->code.'&link_id='.$this->url_data['edit_id'].$params,
                'title' => 'TEXT_CKFINDER_UPLOAD'
            );
        }

        return $this->_TabRemoteWindow('TEXT_MEDIA_MANAGER', $tab);
    }

    /**
     * @return PhpExt_Panel
     * @throws Exception
     */
    function getAllImagesPreviewPanel () {

        $this->url_data['currentSort'] = $this->clean_sort('');
        $this->url_data['currentType'] = $this->code;
        $this->url_data['currentId'] = $this->edit_id;

        $code = $this->code;
        if ($this->code=='custom_link')  {
            $code = 'category';
            $this->url_data['currentType'] = $code;
        }
        $media = new MediaImages();
        $media->url_data = $this->url_data;
        $_data = $media->getCurrentData();
        $images = '';
        if (is_array($_data))
        {
            foreach ($_data as $key => $data){

                $it = new ImageTypes();
                $icon_image_data = $it->_getImagePath($code, 'min', 'width', '100');
                $info_image_data = $it->_getImagePath($code, 'max');

                $mf = new FileHandler();
                $mf->setParentDir(_SRV_WEB_IMAGES.$icon_image_data['folder']);
                $img_check = $mf->_checkFile($data['image']);

                if($img_check){
                    $data['icon'] = '../'._SRV_WEB_IMAGES.$icon_image_data['folder'].'/'.$data['image'];
                    $data['info'] = '../'._SRV_WEB_IMAGES.$info_image_data['folder'].'/'.$data['image'];
                }else{
                    $icon_image_data = $it->_getImagePath('default', 'min', 'width', '150');
                    $info_image_data = $it->_getImagePath('default', 'max');

                    $data['icon'] = '../'._SRV_WEB_IMAGES.$icon_image_data['folder'].'/'.$data['image'];
                    $data['info'] = '../'._SRV_WEB_IMAGES.$info_image_data['folder'].'/'.$data['image'];
                }

                if ($data['image']!='')
                {
                    $images .= '<div id="'.$key.'prewiew"  class="imagePreview"><img  alt="" width="auto" src="'.$data['icon'].'" ext:qtip="<img src='.$data['info'].' class=\'imgPreview-qtip\' alt=\'\' />" /></div>';
                }
            }
        }

        $Panel = new PhpExt_Panel();
        $Panel->setCollapsible(false);
        $Panel->setAutoWidth(true);
        $Panel->setBodyStyle("padding: 5; ");

        $mediaWindow = $this->getMediaWindow();

        // edit button
        $button_edit = PhpExt_Button::createTextButton(__define("BUTTON_EDIT_IMAGES"),
            new PhpExt_Handler(
                PhpExt_Javascript::stm($mediaWindow->getJavascript(false, "new_window")."new_window.show();")
            ));
        $button_edit->setIcon($this->getSetting('icons_path')."camera_go.png")->setIconCssClass("x-btn-text");

        $Panel->addButton($button_edit);

        if(is_array($_data)){
            $Panel->setHtml('<center>'.$images.'</center>');
        }else{
            $Panel->setHtml('<center><div id="'.$code.'_'.time().'prewiew" class="imagePreview">'.__define("TEXT_NO_IMAGE").'</div></center>');
        }

        return $Panel;
    }

    ////////////////////////////////////////
    // window functions
    /**
     * @param string|PhpExt_Component $content
     * @param string $var
     * @param string $return
     * @param bool $img_close
     * @param string $field
     * @return PhpExt_Window|string|null
     * @throws Exception
     */
    function _window ($content = '', $var = 'new_window', $return = 'js', $img_close=false, $field='') {

        $window = new PhpExt_Window();

        $window->setTitle(__define("TEXT_".$this->code."_WINDOW"))->setLayout(new PhpExt_Layout_FitLayout());
        $window->setMinWidth(300)->setMinHeight(200)->setResizable(true)
            ->setCloseAction(PhpExt_Window::CLOSE_ACTION_HIDE)
            ->setPlain(true)
            ->setBodyStyle("padding:5px")
            ->setButtonAlign(PhpExt_Ext::HALIGN_CENTER)
            ->setWidth($this->getSetting('setWindowWidth'))
            ->setHeight($this->getSetting('setWindowHeight')) ;

        if($img_close==true && $field!=''){
            $closeBtn = PhpExt_Button::createTextButton(__define("BUTTON_CLOSE_WINDOW"),new PhpExt_Handler(PhpExt_Javascript::stm("var img_file=''; var img = '".$field."';
            ".$this->code."gridEditForm.getForm().findField(img).setValue(img_file); console.log(img_file); ".$var.".close();")));
            //$closeBtn = PhpExt_Button::createTextButton(__define("BUTTON_CLOSE_WINDOW"),new PhpExt_Handler(PhpExt_Javascript::stm($var.".close()")));
        }else{
            $closeBtn = PhpExt_Button::createTextButton(__define("BUTTON_CLOSE_WINDOW"),new PhpExt_Handler(PhpExt_Javascript::stm($var.".close()")));
        }

        $closeBtn->setIcon($this->getSetting('icons_path')."cancel.png")->setIconCssClass("x-btn-text");
        $window->addButton($closeBtn);

        if (is_object($content)) {
            $jsTab = new PhpExt_Panel();
            $jsTab->setTitle(__define("TEXT_".$this->code."_MORE_DETAILS"))
                ->setAutoScroll(true)
                ->setLayout(new PhpExt_Layout_FitLayout());

            $tabs = new PhpExt_TabPanel();
            $tabs->setActiveTab(0)
                ->addItem($jsTab);

            $window->addItem($content);

        } else {
            $window->setHTML($content);
        }
        //return $window;
        if ($return = 'js')
            return $window->getJavascript(false, $var);
        return $window;
    }

    // window functions end
    ////////////////////////////////////////

    ////////////////////////////////////////
    // store functions


    function _simpleStore($id, $text, $data) {
        $store = new PhpExt_Data_SimpleStore();
        $store->addField($id);
        $store->addField($text);
        $store->setData($data);
        return $store;
    }

    // store functions end
    ////////////////////////////////////////

    ////////////////////////////////////////
    // store functions
    function _getHtmlTag ($text = '', $tag = 'h2') {
        return 	PhpExt_Javascript::stm($this->code."bd.createChild({tag: '".$tag."', html: '".$text."'})");
    }

    // store functions end
    ////////////////////////////////////////

    ////////////////////////////////////////
    // render functions

    function _italicRenderer() {
        $italicRenderer = PhpExt_Javascript::functionDef("italic","return '<i>' + value + '</i>'",array("value"));
        return $italicRenderer;
    }

    function _changeRenderer()
    {
        // todo: check decimal places
        $changeRenderer = PhpExt_Javascript::functionDef(
            "change",
            "var decimal_places = 2; \n
            let price_special = false;
            let price_group = false;
            if (sb.json && sb.json.products_price)
            {
                val = sb.json.products_price.price_db;
                price_special = sb.json.products_price.price_special ? parseFloat(sb.json.products_price.price_special).toFixed(decimal_places) : false;
                price_group = sb.json.products_price.price_group ? parseFloat(sb.json.products_price.price_special).toFixed(decimal_places) : false;
            }
            console.log(val, column,sb);\n" .
            "tmp_value = parseFloat(val).toFixed(decimal_places);\n" .
            "if (val > 0) {\n" .
            "   let r = '';
                if (price_special) 
                {
                    let price_special_txt = \"". HEADING_PRODUCT_SP_PRICE. "\" + ' ' + price_special;
                    r = '<span class=\"products_special_price\" qtip=\"' + price_special_txt + '\">&nbsp;</span>';
                }
                if (price_group) r += '<span class=\"products_group_price\" qtip=\"".HEADING_PRODUCT_PRICE."\">&nbsp;</span>';
                return r + '<span style=\"color:green;\">' + tmp_value + '</span>';\n" .
            "} else if(val < 0) {\n" .
            "   let r = '';
                if (price_special) r = '<span class=\"products_special_price\">&nbsp;</span>';
                return r + '<span style=\"color:red;\">' + tmp_value + '</span>';\n" .
            "} return val;\n",
            array("val", 'column', 'sb')
        );
        return $changeRenderer;
    }

    function _pctChangeRenderer() {
        $pctChangeRenderer = PhpExt_Javascript::functionDef("pctChange",
            "if(val > 0){".
            "return '<span style=\"color:green;\">' + val + '%</span>';".
            "}else if(val < 0){".
            "return '<span style=\"color:red;\">' + val + '%</span>';} ".
            "return val;",array("val"));
        return $pctChangeRenderer;
    }

    /**
     * render function for status image
     *
     */
    function _statusRenderer() {
        $renderer = PhpExt_Javascript::functionDef('status_icon',
            "if (data == 1) { return '<i class=\"fas fa-circle green\"></i>';".
            "} else {".
            "return '<i class=\"fas fa-minus-square red\"></i>';	}",
            array('data'));
        return $renderer;

    }

    function _statusOrEmptyRenderer() {
        $renderer = PhpExt_Javascript::functionDef('status_icon_or_empty',
            "if(data!=0 && (data=='' || typeof data == 'undefined' || data == null)) return ''; if (data == 1) { return '<img src=\"images/icons/bullet_green.png\" alt=\"\"/><img src=\"images/icons/bullet_white.png\" alt=\"\"/>';".
            "} else if (data == 0) {".
            "return '<img src=\"images/icons/bullet_white.png\"  alt=\"\"/><img src=\"images/icons/bullet_red.png\"  alt=\"\"/>';	}
        else return ''",
            array('data'));
        return $renderer;

    }

    function _bulletOrEmptyRenderer() {
        $renderer = PhpExt_Javascript::functionDef('bullet_or_empty',
            "if(data!=0 && (data=='' || typeof data == 'undefined' || data == null)) return '';
            return '<img src=\"images/icons/bullet_'+data+'.png\"  alt=\"\"/>';",
            array('data'));
        return $renderer;

    }

    function _iconOrEmptyRenderer() {
        $renderer = PhpExt_Javascript::functionDef('icon_or_empty',
            "if(data!=0 && (data=='' || typeof data == 'undefined' || data == null)) return '';
            return '<img src=\"images/icons/'+data+'.png\"  alt=\"\"/>';",
            array('data'));
        return $renderer;

    }

    /**
     * @return PhpExt_JavascriptStm
     * render one or more awesome font icons
     */
    function _awesomeRenderer() {
        $renderer = PhpExt_Javascript::functionDef('awesomeRenderer',
            <<<TAG
                var s = '';
                var defaultColor = "#555";
                var defaultSize = 'lg';
                var defaultStyle = 's'; // solid
                var defaultCssClass = '';
                var defaultTooltip = '';
                        
                try {
                    if (data !== null && typeof data === 'object')
                    {
                        data.forEach(function(el)
                        {
                            var color = defaultColor;
                            var size = defaultSize;
                            var style = defaultStyle;
                            var cssClass = defaultCssClass;
                            var tooltip = defaultTooltip;
                        
                            if (el !== null && typeof el === 'object')
                            {
                                var icon = el.icon || '';
                                color = el.color || color;
                                size = el.size || size;
                                cssClass = el.class || cssClass;
                                tooltip = el.tooltip || tooltip;
                        
                                if (el.style == 'solid') style = 's';
                                else if (el.style == 'regular') style = 'r';
                                else if (el.style == 'brand') style = 'b';
                                else if (el.style == 'light') style = 'l';
                        
                                s += `<i ext:qtip="\${tooltip}" class="fa-renderer fa\${style} fa-\${icon} fa-\${size} \${cssClass}" style="color:\${color}"></i>`;
                            }
                            else
                            {
                                s += `<i class="fa-renderer fa\${style} fa-\${el} fa-\${size} \${cssClass}" style="color:\${color}"></i>`;
                            }
                        });
                    }
                    else
                    {
                        s = `<i class="fa-renderer fa\${defaultStyle} fa-\${data} fa-\${defaultSize} \${defaultCssClass}" style="color:\${color}"></i>`;
                    }
                }
                catch(e)
                {
                    console.error(e);
                }
                return s;
                        
TAG
            ,
            array('data'));
        return $renderer;

    }

    function _RightsRenderer() {

        $renderer = '';

        if(constant("_SYSTEM_ADMIN_PERMISSIONS")=='whitelist'){
            $renderer = PhpExt_Javascript::functionDef('RightsRenderer',
                "if (data == 1) { return '<img src=\"images/icons/bullet_green.png\" alt=\"\"/><img src=\"images/icons/bullet_white.png\" alt=\"\"/>';".
                "} else {".
                "return '<img src=\"images/icons/bullet_white.png\" alt=\"\"/><img src=\"images/icons/bullet_red.png\" alt=\"\"/>';	}",
                array('data'));
        }elseif(constant("_SYSTEM_ADMIN_PERMISSIONS")=='blacklist'){
            $renderer = PhpExt_Javascript::functionDef('RightsRenderer',
                "if (data == 0) { return '<img src=\"images/icons/bullet_green.png\" alt=\"\"/><img src=\"images/icons/bullet_white.png\"  alt=\"\"/>';".
                "} else {".
                "return '<img src=\"images/icons/bullet_white.png\" alt=\"\"/><img src=\"images/icons/bullet_red.png\"  alt=\"\"/>';	}",
                array('data'));
        }

        return $renderer;
    }

    /**
     * render slave icons
     *
     */
    function _slaveRenderer() {
        $renderer = PhpExt_Javascript::functionDef('slaveRenderer',
            "var a=data.split('_');if (a[0]) { return '<i class=\"fas fa-cubes\"></i>';". // slave
            "} else {".
            " if(a[1]==1) return '<i class=\"fas fa-cube\"></i>'; 
                     else return '<i class=\"fas fa-stop\"></i>';    }",
            array('data'));
        return $renderer;

    }

    /**
     * display star icon, based on int
     * @return PhpExt_JavascriptStm
     */
    function _starRenderer() {
        if ($this->getActivate('starRenderer')) {
            $renderer = PhpExt_Javascript::functionDef('starRenderer',
                "var i=0; var text='<span style=\"color: orange\">'; while (i < data) { text+='<i class=\"fas fa-star yellow\"></i> '; i++;} text+='</span>'; return text;", array('data'));
        }else{
            $renderer = PhpExt_Javascript::functionDef('starRenderer', "", array('data'));
        }
        return $renderer;
    }

    /**
     * display male, female, company icons
     * @return PhpExt_JavascriptStm
     */
    function _genderRenderer() {

        if ($this->getActivate('genderRenderer')) {

            $renderer = PhpExt_Javascript::functionDef('genderRenderer',
                "if (data == 'm') { return '<i class=\"fas fa-male\"></i>'; } ".
                "if (data == 'f') { return '<i class=\"fas fa-female\"></i>'; } ".
                "if (data == 'c') { return '<i class=\"fas fa-building\"></i>'; } ", array('data'));
        } else {
            $renderer = PhpExt_Javascript::functionDef('genderRenderer', "", array('data'));
        }
        return $renderer;
    }

    function _adminActionStatusRenderer() {
        $renderer = PhpExt_Javascript::functionDef('adminActionStatusRenderer',
            "if (data == 1) { return '<img src=\"images/icons/lock_edit.png\" alt=\"\"/>'; } else { return '<img src=\"images/icons/lock_open.png\" alt=\"\"/>'; } ", array('data'));
        return $renderer;

    }

    function _taxRenderer() {
        $js = '';
        if ($this->getActivate('taxRenderer')) {
            require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'classes/class.tax_class.php';
            $t = new tax_class();
            $records = $t->_getTaxClassList();
            if (count($records) > 0)
                foreach ($records as $record) {
                    if ($record['tax_class_id']!='') $js.= " if (data == ".$record['tax_class_id'].") return '".$record['tax_class_title']."';";
                }
        }
        $renderer = PhpExt_Javascript::functionDef('taxRenderer',
            $js." return '".__define('TEXT_NO_TAX_CLASS')."'; ", array('data'));
        return $renderer;

    }

    /**
     * display payment icons
     * @return PhpExt_JavascriptStm
     */
    function _paymentIconRenderer() {

        global $xtPlugin;

        $add_renderer = '';
        ($plugin_code = $xtPlugin->PluginCode('ExtFunctions:_paymentIconRenderer_top')) ? eval($plugin_code) : false;

        $renderer = PhpExt_Javascript::functionDef('paymentIconRenderer',
            "if (data == 'xt_invoice') { return '<i class=\"pf pf-invoice-sign-o\"></i>'; } ".
            "if (data == 'xt_paypal') { return '<i class=\"pf pf-paypal-alt\"></i>'; } ".
            "if (data == 'xt_paypal_plus') { return '<i class=\"pf pf-paypal-alt\"></i>'; } ".
            "if (data == 'xt_skrill') { return '<i class=\"pf pf-skrill\"></i>'; } ".
            "if (data == 'xt_sofortueberweisung') { return '<i class=\"pf pf-sofort\"></i>'; } ".
            "if (data == 'klarna_kco') { return '<i class=\"pf pf-klarna\"></i>'; } ".
            "if (data == 'xt_klarna') { return '<i class=\"pf pf-klarna\"></i>'; } ".
            "if (data == 'xt_cashpayment') { return '<i class=\"pf pf-cash-on-pickup\"></i>'; } ".
            "if (data == 'xt_banktransfer') { return '<i class=\"pf pf-sepa\"></i>'; } ".
            "if (data == 'xt_prepayment') { return '<i class=\"pf pf-bank-transfer\"></i>'; } ".
            "if (data == 'xt_cashondelivery') { return '<i class=\"pf pf-cash-on-delivery\"></i>'; } ".
            "if (data == 'xt_paypal_installments') { return '<i class=\"pf pf-paypal-alt\"></i>'; } ".
            $add_renderer.
            " return data;"
            , array('data'));
        return $renderer;

    }

    function _externLinkRenderer() {


        $renderer = PhpExt_Javascript::functionDef('externLinkRenderer',
            "return '<a ext:qtip=\"".__define('TEXT_OPEN_IN_NEW_TAB')."\"  href=\"'+data+'\" class=\"open_web\" target=\"_blank\"></a>&nbsp;&nbsp;<a href=\"'+data+'\" onclick=\"return false\"><i ext:qtip=\"".__define('TEXT_COPY_ADDRESS')."\"  class=\"far fa-clipboard copy_address\" onclick=\"navigator.clipboard.writeText(this.parentNode.href); this.classList.add(`blink-fast`); return false\" ></a>';  "
            , array('data'));
        return $renderer;

    }

    function _zoneRenderer() {
        global $db;
        $js = '';
        if ($this->getActivate('zoneRenderer')) {
            require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'classes/class.system_status.php';
            $s_status = new system_status();

            $records = $s_status->values['zone'];

            $rs = $db->Execute("SELECT zone_name,zone_id FROM ".TABLE_SHIPPING_ZONES);
            if ($rs->RecordCount()>0) {
                while (!$rs->EOF) {
                    $records[] =  array('id' => '9999'.$rs->fields['zone_id'],
                        'name' => $rs->fields['zone_name'].' ('.__define("TEXT_SHIPPING_ZONE").')');
                    $rs->MoveNext();
                }
            }

            if (count($records) > 0)
                foreach ($records as $record) {
                    if ($record['id']!='') $js.= " if (data == ".$record['id'].") return '".$record['name']."';";
                }



        }
        $renderer = PhpExt_Javascript::functionDef('zoneRenderer',
            //$js." return '".__define('TEXT_NO_ZONE')."'; ", array('data'));
            $js." return ''; ", array('data'));
        return $renderer;

    }

    function _manufacturersRenderer() {
        $js = '';
        if ($this->getActivate('manufacturersRenderer')) {
            $m = new manufacturer();
            $_data = $m->getManufacturerList('admin');
            if (is_array($_data) && count($_data) > 0)
                foreach ($_data as $mdata) {
                    if ($mdata['manufacturers_id']!='') $js.= " if (data == ".$mdata['manufacturers_id'].") return '"._filterText($mdata['manufacturers_name'])."';";
                }
        }
        $renderer = PhpExt_Javascript::functionDef('manufacturersRenderer',
            $js." return '".__define('TEXT_NO_MANUCFACTURER')."'; ", array('data'));
        return $renderer;

    }
    function _languageIsoRenderer() {
        $js = "";
        global $db,$language,$filter;

        if ($this->getActivate('languageIsoRenderer')) {
            foreach ($language->_getLanguageList() as $val) {

                $js.= " if (data == '".$val['id']."') return '".$val['name']." (".$val['id'].")';";
            }
        }
        $renderer = PhpExt_Javascript::functionDef('languageIsoRenderer',
            $js." return '".__define('TEXT_NO_LANGUAGE')."'; ", array('data'));
        return $renderer;

    }

    function _languageIsoRendererPlusAll() {
        $js = "";
        global $db,$language,$filter;

        if ($this->getActivate('languageIsoRendererPlusAll')) {
            foreach ($language->_getLanguageList() as $val) {

                $js.= " if (data == '".$val['id']."') return '".$val['name']." (".$val['id'].")';";
            }
        }
        $renderer = PhpExt_Javascript::functionDef('languageIsoRendererPlusAll',
            $js." return '".__define('TEXT_ALL_LANGUAGES')."'; ", array('data'));
        return $renderer;

    }

    // multiple languages  renders ie 'Englisch (en), Deutsch (de)'
    function _languageIsoRenderer2() {
        $js = "";
        global $db,$language,$filter;

        if ($this->getActivate('languageIsoRenderer2')) {
            $js = "
            var ret = [];                  
            data = data.replace(/\s/g, \"X\");  
            ";

            foreach ($language->_getLanguageList() as $val) {

                $js.= " 
                var regex = /(^|,|)".$val['id']."(,|$)/gm;
                if (data.match(regex) ) ret.push(`".$val['name']."`);
                ";
            }

            $js .= " return ret.length > 0 ? ret.join() : `".__define('TEXT_NO_LANGUAGE')."`;   ;
            ";
        }
        $renderer = PhpExt_Javascript::functionDef('languageIsoRenderer2',
            $js, array('data'));
        return $renderer;

    }

    // multiple languages  renders ie 'en, de'
    function _languageIsoRenderer3() {
        $js = "";
        global $db,$language,$filter;

        if ($this->getActivate('languageIsoRenderer3')) {
            $js .= "
            var ret = [];           
            data = data.replace(/\s/g, \"X\");
            ";

            foreach ($language->_getLanguageList() as $val) {

                $js.= " 
                var regex = /(^|,|)".$val['id']."(,|$)/gm;
                if (data.match(regex) ) ret.push(`".$val['id']."`);
                ";
            }

            $js .= " return ret.length > 0 ? ret.join() : `".__define('TEXT_NO_LANGUAGE')."`;   ;
            ";
        }
        $renderer = PhpExt_Javascript::functionDef('languageIsoRenderer3',
            $js, array('data'));
        return $renderer;

    }


    function _imageRenderer() {

        $it = new ImageTypes();

        if($this->code=='MediaImageList' || $this->code=='MediaImageSearch' || $this->code=='MediaList')
            if($this->url_data['currentType'])
                $code = $this->url_data['currentType'];
            elseif($this->url_data['galType'])
                $code = $this->url_data['galType'];
            else
                $code = 'default';
        else
            $code = $this->code;

        $icon_image_data = $it->_getImagePath($code, 'min', 'width');
        $info_image_data = $it->_getImagePath($code, 'max', 'width', '', '300');


        //__debug($info_image_data, 'INFO_DATA_1');

        if(!is_array($icon_image_data)){
            $icon_image_data = $it->_getImagePath('default', 'min', 'width');
            $info_image_data = $it->_getImagePath('default', 'max', 'width', '', '300');
        }

        //__debug($icon_image_data, 'ICON_DATA_1');

        //$icon_image_default_data = $it->_getImagePath('default', 'min', 'width');
        //$info_image_default_data = $it->_getImagePath('default', 'max', 'width', '', '300');

        $renderer = PhpExt_Javascript::functionDef('image_thumb',
            "if (img != '') { return '<img width=".$icon_image_data['width']." height=".$icon_image_data['height']." src=\"../"._SRV_WEB_IMAGES.$icon_image_data['folder']."/'+ img +'\" ext:qtip=\"<img width=".$info_image_data['width']." height=".$info_image_data['height']." src=../"._SRV_WEB_IMAGES.$info_image_data['folder']."/'+ img +'>\" />';} ", array('img'));

        return $renderer;
    }

    function _iconRenderer() {
        $renderer = PhpExt_Javascript::functionDef('image_icon',
            "if (img != '') { return '<img src=\"'+ img +'\" alt=\"\"/>';} ", array('img'));
        return $renderer;

    }

    function _dateRenderer () {
        $renderer = PhpExt_Javascript::variable("Ext.util.Format.dateRenderer('m/d/Y')");
        return $renderer;
    }

    /**
     * @param $window_title
     * @param array $tabData
     * @param int $width
     * @param int $height
     * @param string $return
     * @return PhpExt_Window|string|null
     * @throws Exception
     */
    function _TabRemoteWindow($window_title, $tabData = array(), $width = 800, $height = 500, $return = 'obj')
    {
        global $link_params, $xtPlugin;

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':top')) ? eval($plugin_code) : false;
        if(isset($plugin_return_value))
            return $plugin_return_value;

        $window = new PhpExt_Window();
        $window->setModal(true)
            ->setResizable(true)
            ->setCloseAction(PhpExt_Window::CLOSE_ACTION_HIDE)
            ->setPlain(true)
            ->setBodyStyle("padding:5px")
            ->setButtonAlign(PhpExt_Ext::HALIGN_CENTER)->setTitle(__define($window_title))
            ->setLayout(new PhpExt_Layout_FitLayout())
            ->setWidth($width)
            ->setHeight($height);

        $closeBtn = PhpExt_Button::createTextButton(__define("BUTTON_CLOSE_WINDOW"),new PhpExt_Handler(PhpExt_Javascript::stm("if (new_window) { new_window.destroy() } else{ this.destroy() } ")));
        $closeBtn->setIcon("images/icons/cancel.png")
            ->setIconCssClass("x-btn-text");
        $window->addButton($closeBtn);


        $tabs = new PhpExt_TabPanel();
        $tabs->setEnableTabScroll(false)->setAutoWidth(true);
        $i=0;
        foreach ($tabData as $currentTab) {
            $id = $this->code.'RemoteWindowTab'.(++$i);

            if($currentTab['url_short']==true){
                $new_url = $currentTab['url'];
            }else{
                $new_url = $this->getSetting('readfull_url').$currentTab['url'];
            }

            $jsTab = new PhpExt_Panel();
            $jsTab->setId( $id );
            $jsTab->setTitle(__define($currentTab['title']))
                ->setAutoScroll(true)
                ->setLayout(new PhpExt_Layout_FitLayout());

            $currentTab['params'] .= '&parentNode='.$id;
            $alco = new PhpExt_AutoLoadConfigObject($new_url,$currentTab['params']);
            $alco->setScripts(true)->setMethod(PhpExt_AutoLoadConfigObject::AUTO_LOAD_METHOD_GET);
            $jsTab->setAutoLoad($alco);

            // add panel to tab
            $tabs->addItem($jsTab);

        }
        $tabs->setActiveTab(0);

        if (is_object($tabs))
            $window->addItem($tabs);

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':bottom')) ? eval($plugin_code) : false;

        if ($return == 'js')
            return $window->getJavascript(false, "new_window");
        else
            return $window;
    }

    /**
     * @param $window_title
     * @param $title
     * @param $url
     * @param bool $url_short
     * @param array $params
     * @param int $width
     * @param int $height
     * @param string $return
     * @return PhpExt_Window|string|null
     * @throws Exception
     */
    function _RemoteWindow ($window_title, $title, $url, $url_short=false, $params = array(), $width = 300, $height = 200, $return = 'js') {
        global $link_params, $xtPlugin;

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':top')) ? eval($plugin_code) : false;
        if(isset($plugin_return_value))
            return $plugin_return_value;

        $id = $this->code.'RemoteWindow';

        $window = new PhpExt_Window();
        $window
            ->setResizable(true)
            ->setPlain(true)
            ->setBodyStyle("padding-right:0px; padding-bottom:0px;")
            ->setButtonAlign(PhpExt_Ext::HALIGN_CENTER)
            ->setTitle(__define($window_title))
            ->setWidth($width)
            ->setHeight($height)
            ->setId( $id );
        $window->setLayout(new PhpExt_Layout_FitLayout());

        $closeBtn = PhpExt_Button::createTextButton(__define("BUTTON_CLOSE_WINDOW"),new PhpExt_Handler(PhpExt_Javascript::stm("if (new_window) { new_window.destroy() } else{ this.destroy() } ")));
        $closeBtn->setIcon("images/icons/cancel.png")
            ->setIconCssClass("x-btn-text");
        $window->addButton($closeBtn);
        $add_to_url = (isset($_SESSION['admin_user']['admin_key']))? '&sec='.$_SESSION['admin_user']['admin_key']: '';
        if($url_short==true){
            $new_url = $url.$add_to_url;
        }else{
            $new_url = $this->getSetting('readfull_url').$url.$add_to_url;
        }

        $params['parentNode'] = $id;
        $arr_tmp = array_filter(explode('&', $new_url));
        $arr_new_url = array();
        foreach($arr_tmp as $item) {
            list($new_url_k, $new_url_v) = explode('=', $item, 2);
            $arr_new_url[$new_url_k] = $new_url_v;
        }
        unset($arr_tmp);
        if($arr_new_url['pg'] != 'overview' && $arr_new_url['pg'] != '') {
            // es wird z.B. ein Treepanel zur�ckgeliefert in einem tab
            $jsTab = new PhpExt_Panel();
            $jsTab->setTitle(__define($title))
                ->setAutoScroll(true)
                ->setLayout(new PhpExt_Layout_FitLayout());

            $alco = new PhpExt_AutoLoadConfigObject($new_url,$params);
            $alco->setScripts(true)->setMethod(PhpExt_AutoLoadConfigObject::AUTO_LOAD_METHOD_GET);
            $jsTab->setAutoLoad($alco);

            $tabs = new PhpExt_TabPanel();
            $tabs->setActiveTab(0)
                ->addItem($jsTab);
            if (is_object($tabs))  {
                $window->addItem($tabs);
            }
        } else {
            // normales Grid
            $alco = new PhpExt_AutoLoadConfigObject($new_url,$params);
            $alco->setScripts(true)->setMethod(PhpExt_AutoLoadConfigObject::AUTO_LOAD_METHOD_GET);
            $window->setAutoLoad($alco);
        }

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':bottom')) ? eval($plugin_code) : false;

        if ($return == 'js')
            return $window->getJavascript(false, "new_window");
        else
            return $window;
    }

    /**
     * @param $window_title
     * @param $title
     * @param $url
     * @param bool $url_short
     * @param array $params
     * @param int $width
     * @param int $height
     * @param string $return
     * @param bool $id
     * @return PhpExt_Window|string|null
     * @throws Exception
     */
    function _RemoteWindow3 ($window_title, $title, $url, $url_short=false, $params = array(), $width = 300, $height = 200, $return = 'js', $id = false)
    {
        global $link_params, $xtPlugin;

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':top')) ? eval($plugin_code) : false;
        if(isset($plugin_return_value))
            return $plugin_return_value;

        if($id==false)
            $id = $this->code.'RemoteWindow';
        else $id .= 'RemoteWindow';

        $window = new PhpExt_Window();
        $window->setResizable(true)
            ->setPlain(true)
            ->setBodyStyle("padding-right:2px; padding-bottom:20px;")
            ->setButtonAlign(PhpExt_Ext::HALIGN_CENTER)
            ->setTitle(__define($window_title))
            ->setWidth($width)
            ->setHeight($height)
            ->setAutoHeight(true)
            ->setId( $id );
        $window->setModal(true)
            ->setLayout(new PhpExt_Layout_FitLayout());

        $closeBtn = PhpExt_Button::createTextButton(__define("BUTTON_CLOSE_WINDOW"),new PhpExt_Handler(PhpExt_Javascript::stm("if (new_window) { new_window.destroy() } else{ this.destroy() } ")));
        $closeBtn->setIcon("images/icons/cancel.png")
            ->setIconCssClass("x-btn-text");
        $window->addButton($closeBtn);
        $add_to_url = (isset($_SESSION['admin_user']['admin_key']))? '&sec='.$_SESSION['admin_user']['admin_key']: '';
        if($url_short==true){
            $new_url = $url.$add_to_url;
        }else{
            $new_url = $this->getSetting('readfull_url').$url.$add_to_url;
        }

        $params['parentNode'] = $id;
        $arr_tmp = explode('&', $new_url);
        $arr_new_url = array();
        foreach($arr_tmp as $item) {
            list($new_url_k, $new_url_v) = explode('=', $item, 2);
            $arr_new_url[$new_url_k] = $new_url_v;
        }
        unset($arr_tmp);
        if($arr_new_url['pg'] != 'overview' && $arr_new_url['pg'] != '') {

            $jsTab = new PhpExt_Panel();
            $jsTab->setLayout(new PhpExt_Layout_FitLayout())
                ->setHideBorders(true)
                ->setAutoHeight(true);

            $alco = new PhpExt_AutoLoadConfigObject($new_url,$params);
            $alco->setScripts(true)->setMethod(PhpExt_AutoLoadConfigObject::AUTO_LOAD_METHOD_GET);
            $jsTab->setAutoLoad($alco);

            $window->addItem($jsTab);

        } else {
            // normales Grid
            $alco = new PhpExt_AutoLoadConfigObject($new_url,$params);
            $alco->setScripts(true)->setMethod(PhpExt_AutoLoadConfigObject::AUTO_LOAD_METHOD_GET);
            $window->setAutoLoad($alco);
        }

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':bottom')) ? eval($plugin_code) : false;

        if ($return == 'js')
            return $window->getJavascript(false, "new_window");
        else
            return $window;
    }

    /**
     * @param $window_title
     * @param $code
     * @param $panel
     * @param int $width
     * @param int $height
     * @param string $return
     * @return PhpExt_Window|string|null
     * @throws Exception
     */
    static function _RemoteWindow2($window_title, $code, $panel, $width = 300, $height = 200, $return = 'js')
    {
        global $xtPlugin;

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':top')) ? eval($plugin_code) : false;
        if(isset($plugin_return_value))
            return $plugin_return_value;
        $id = $code.'RemoteWindow2';

        $window = new PhpExt_Window();
        $window
            ->setResizable(true)
            ->setPlain(true)
            ->setBodyStyle("padding-right:20px; padding-bottom:20px;max-height:800px")
            ->setButtonAlign(PhpExt_Ext::HALIGN_CENTER)->setTitle(__define($window_title))
            ->setWidth($width)
            ->setHeight($height)
            ->setAutoHeight(false)
            ->setId( $id );
        $window ->setLayout(new PhpExt_Layout_FitLayout());
        $window->addItem($panel);

        $closeBtn = PhpExt_Button::createTextButton(__define("BUTTON_CLOSE_WINDOW"),new PhpExt_Handler(PhpExt_Javascript::stm("if (new_window) { new_window.destroy() } else{ this.destroy() } ")));
        $closeBtn->setIcon("images/icons/cancel.png")
            ->setIconCssClass("x-btn-text");
        $window->addButton($closeBtn);

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':bottom')) ? eval($plugin_code) : false;

        if ($return == 'js')
            return $window->getJavascript(false, "new_window");
        else
            return $window;
    }

    /**
     * @param $window_title
     * @param $code
     * @param $panel
     * @param int $width
     * @param int $height
     * @param string $return
     * @param array $buttons
     * @param bool $close_button
     * @return PhpExt_Window|string|null
     * @throws Exception
     */
    static function _RemoteModalWindow3($window_title, $code, $panel, $width = 300, $height = 200, $return = 'js', $buttons = array(), $close_button = false)
    {
        $id = $code.'RemoteWindow3';

        $window = new PhpExt_Window();
        $window
            ->setResizable(true)
            ->setPlain(true)->setModal(true)
            ->setBodyStyle("")
            ->setButtonAlign(PhpExt_Ext::HALIGN_CENTER)->setTitle(__define($window_title))
            ->setWidth($width)
            ->setHeight($height)
            ->setAutoHeight(true)
            ->setId( $id )
            ->attachListener('render',
                new PhpExt_Listener(PhpExt_Javascript::functionDef(null, "
                this.setPosition(this.x, 40);
            "))
            );
        $window ->setLayout(new PhpExt_Layout_FitLayout());

        $window->addItem($panel);

        if($close_button)
        {
            $closeBtn = PhpExt_Button::createTextButton(__define("BUTTON_CLOSE_WINDOW"), new PhpExt_Handler(PhpExt_Javascript::stm("if (new_window) { new_window.destroy() } else{ this.destroy() } ")));
            $closeBtn->setIcon("images/icons/cancel.png")
                ->setIconCssClass("x-btn-text");
            $window->addButton($closeBtn);
        }

        if(is_array($buttons))
        {
            foreach ($buttons as $btn)
            {
                $window->addButton($btn);
            }
        }

        if ($return == 'js')
            return $window->getJavascript(false, "new_window");
        else
            return $window;
    }

    //////////////////////////////////////////////////////////////////////////////////////

    /**
     * @param PhpExt_Toolbar_Toolbar $top
     * @param string $pos
     * @return mixed
     * @throws Exception
     */
    function getActionMenu ($top, $pos='edit') {
        global $xtPlugin;

        $action_menu = new PhpExt_Menu_Menu();
        $menu_set = false;

        if($pos=='edit'){
            if($this->checkPermission('edit')){
                $s_data = $this->_ExtSubmitButtonHandler(array('url' => $this->getSetting('save_url')."edit_id='+".$this->getSelectionItem()."+'&save=true", "close" => 'close'));
                $u_data = $this->_ExtSubmitButtonHandler(array('url' => $this->getSetting('save_url')."edit_id='+".$this->getSelectionItem()."+'&save=true", "close" => 'update'));

                $menu_set = true;
                $action_menu->addTextItem('add', __define('BUTTON_SAVE'), "add x-menu-item",
                    new PhpExt_Handler(PhpExt_Javascript::stm($this->_SubmitButton_stm('-grideditform', $s_data,'close'))));

                $action_menu->addTextItem('upd', __define('BUTTON_UPDATE'), "upd x-menu-item",
                    new PhpExt_Handler(PhpExt_Javascript::stm($this->_SubmitButton_stm('-grideditform', $u_data,'update'))));
            }

            if($this->checkPermission('cancel')) {
                $menu_set = true;
                $action_menu->addTextItem('cancel', __define('BUTTON_CANCEL'), "cancel x-menu-item",
                    new PhpExt_Handler(PhpExt_Javascript::stm("contentTabs.remove(contentTabs.getActiveTab());")));
            }
        }

        if($pos=='list'){
            $menu_set = true;
            $action_menu = $this->_getSystemButtons($action_menu, 'menu');
        }

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'getActionMenu_items')) ? eval($plugin_code) : false;

        if ($menu_set) {
            $action_menu->addTextItem('', '');

            $action_menuButton = new PhpExt_Toolbar_Button();
            $action_menuButton->setMenu($action_menu);
            $action_menuButton->setText(__define('TEXT_ACTION'));
            $top->addItem('action_menu', $action_menuButton);
        }
        return $top;
    }

    /**
     * @param PhpExt_Toolbar_Toolbar $obj
     * @param $ToolbarPos
     * @param string $Pos
     * @return PhpExt_Toolbar_Toolbar
     */
    function getUserMenu($obj, $ToolbarPos, $Pos='grid') {
        $groups = $this->getSetting('menuGroups');
        $items = $this->getSetting('menuActions');

        if (is_array($groups) && count($groups) > 0) {
            reset($groups);
            foreach($groups as $gkey => $gval) {
                if($gval['ToolbarPos']==$ToolbarPos || $Pos=='edit' || $Pos=='both'){
                    if($gval['Pos']==$Pos || $Pos=='both'){
                        $menus = $this->_buildMenus($gval['group_name'], $items[$gval['group']]);
                        if($menus) $obj->addItem($gval['group'], $menus);
                    }
                }
            }
        }

        return $obj;
    }

    /**
     * @param $menu_name
     * @param $settings
     * @param string $iconstyle
     * @return PhpExt_Toolbar_Button|bool
     */
    function _buildMenus($menu_name, $settings,$iconstyle='')
    {
        $menuButton = false;
        if (is_array($settings) && count($settings) > 0) {
            $menu = new PhpExt_Menu_Menu();
            reset($settings);
            foreach($settings as $key => $val) {
                $text = trim(str_replace(array("\r\n","\r","\n"), ' ', __define($val['text'])));
                $menu->addTextItem($val['style'], $text, $val['style']." x-menu-item",
                    new PhpExt_Handler(PhpExt_Javascript::stm($val['stm'])));
            }

            $menu->addTextItem('', '');

            $menuButton = new PhpExt_Toolbar_Button();
            $menuButton->setMenu($menu);
            if ($iconstyle!='') $iconstyle='<i class="'.$iconstyle.' icon-toolbar-menu"></i>';
            $text = trim(str_replace(array("\r\n","\r","\n"), ' ', __define($menu_name)));
            $menuButton->setText($iconstyle.trim(preg_replace('/\s\s+/', ' ', $text)));

        }

        return $menuButton;
    }

    /**
     * @param PhpExt_Toolbar_Toolbar $top
     * @return PhpExt_Toolbar_Toolbar
     */
    function getDynamicMenu	($top) {
        $settings = $this->getSetting('rowActionsFunctions');

        if (is_array($settings) && count($settings) > 1) {
            $menu = new PhpExt_Menu_Menu();
            reset($settings);
            foreach($settings as $action => $fkt_content) {
                $menu->addTextItem($action.'_menu', __define('TEXT_'.$action), $action." x-menu-item",
                    new PhpExt_Handler(PhpExt_Javascript::stm($fkt_content)));
            }
            $menuButton = new PhpExt_Toolbar_Button();
            $menuButton->setMenu($menu);
            $menuButton->setText(__define('TEXT_EDIT'));
            $top->addItem($this->code, $menuButton);
        }
        return $top;
    }

    /**
     * @param PhpExt_Toolbar_Toolbar $top
     * @return mixed
     * @throws Exception
     */
    function getFileMenu($top)
    {
        if ($this->getActivate('image') || $this->getActivate('file'))
        {
            $file = new PhpExt_Menu_Menu();
            if ($this->getActivate('image'))
            {
                $mediaWindow = $this->getMediaWindow();
                $file->addTextItem(
                    'image_imageupload',
                    __define('BUTTON_EDIT_IMAGES'),
                    'image_imageupload x-menu-item',
                    new PhpExt_Handler(
                        PhpExt_Javascript::stm(
                            $mediaWindow->getJavascript(false, 'new_window').'new_window.show();'
                        )
                    )
                );
            }

            if ($this->getActivate('file'))
            {
                // file upload button

                if (constant("_SYSTEM_UPLOAD_TYPE") === 'simple_upload')
                {
                    $getUploadFn = 'getSimpleUpload';
                }
                elseif (constant("_SYSTEM_UPLOAD_TYPE") === 'flash_upload_10')
                {
                    $getUploadFn = 'getSwfUpload';
                }
                elseif (constant("_SYSTEM_UPLOAD_TYPE") === 'ckfinder_upload')
                {
                    $getUploadFn = 'getCKFinderUpload';
                }
                else
                {
                    $getUploadFn = 'getUploadPanel';
                }

                $name = 'namenotdefined';
                $file->addTextItem(
                    'image_fileupload',
                    __define('BUTTON_UPLOAD_FILES'),
                    'image_fileupload x-menu-item',
                    new PhpExt_Handler(
                        PhpExt_Javascript::stm(
                            $this->_window($this->$getUploadFn($name, 'files')).'new_window.show();'
                        )
                    )
                );
            }

            $file->addTextItem('', '');

            $fileButton = new PhpExt_Toolbar_Button();
            $fileButton->setMenu($file);
            $fileButton->setText(__define('TEXT_FILE'));
            $top->addItem('image_fileupload_menu', $fileButton);
        }

        return $top;
    }

    /**
     * @return array
     * @throws Exception
     */
    function _systemButtons(){
        global $xtPlugin;

        $button = array();

// 		$button['copy2'] = array('status'=>true, 'text'=>'nowy button', 'style'=>'copy2', 'icon'=>'page_copy.png', 'stm'=>$this->_MultiButton_stm('new button', 'doCopy'),'func'=>'doCopy2', 'flag'=>'multiFlag_copy2', 'flag_value'=>'true');
        $button['new'] = array('status'=>true, 'text'=>'BUTTON_NEW', 'style'=>'add', 'icon'=>'add.png','font-icon'=>'fa fa-plus-square', 'acl'=>'new', 'stm'=>$this->_NewButton_stm());

        if ($this->getSetting('gridType') == 'EditGrid') {
            $button['save'] = array('status'=>true, 'text'=>'BUTTON_SAVE', 'style'=>'save', 'icon'=>'disk_multiple.png','font-icon'=>'far fa-save', 'acl'=>'new', 'stm'=>$this->_SubmitButton_stm(__define('TEXT_SAVE')));
        }

        $button['edit'] = array('status'=>true, 'text'=>'BUTTON_EDIT', 'style'=>'edit', 'icon'=>'pencil.png','font-icon'=>'fa fa-edit', 'acl'=>'edit', 'stm'=>$this->_EditButton_stm());

        $button['GetSelected'] = array('status'=>false, 'text'=>'BUTTON_SELECT', 'style'=>'getselected', 'icon'=>'accept.png', 'acl'=>'edit', 'stm'=>$this->_GetSelectedButton_stm());

        $button['popup'] = array('status'=>false, 'text'=>'BUTTON_SELECT', 'style'=>'select', 'icon'=>'magnifier_zoom_in.png', 'stm'=>$this->_PopupButton_stm());

        $button['delete'] = array('status'=>true, 'text'=>'BUTTON_DELETE', 'style'=>'delete', 'icon'=>'delete.png','font-icon'=>'fa fa-trash-alt', 'acl'=>'delete', 'stm'=>$this->_MultiButton_stm('BUTTON_DELETE', 'doDelete'), 'func'=>'doDelete', 'flag'=>'multiFlag_unset', 'flag_value'=>'true');

        $button['reset'] = array('status'=>true, 'text'=>'BUTTON_RESET', 'style'=>'reload', 'icon'=>'done.gif','font-icon'=>'fa fa-sync-alt', 'stm'=>$this->_ResetButton_stm());

        $button['statusTrue'] = array('status'=>false, 'text'=>'BUTTON_STATUS_TRUE', 'style'=>'statusTrue', 'acl'=>'edit', 'icon'=>'lightbulb.png','font-icon'=>'fa fa-check-square', 'stm'=>$this->_MultiButton_stm('BUTTON_STATUS_TRUE', 'doStatusTrue'),'func'=>'doStatusTrue', 'flag'=>'multiFlag_setStatus', 'flag_value'=>'true');

        $button['statusFalse'] = array('status'=>false, 'text'=>'BUTTON_STATUS_FALSE', 'style'=>'statusFalse', 'acl'=>'edit', 'icon'=>'lightbulb_off.png','font-icon'=>'far fa-square', 'stm'=>$this->_MultiButton_stm('BUTTON_STATUS_FALSE', 'doStatusFalse'),'func'=>'doStatusFalse', 'flag'=>'multiFlag_setStatus', 'flag_value'=>'false');

        $button['copy'] = array('status'=>false, 'text'=>'BUTTON_COPY', 'style'=>'copy', 'icon'=>'page_copy.png','font-icon'=>'fa fa-copy', 'acl'=>'new', 'stm'=>$this->_MultiButton_stm('BUTTON_COPY', 'doCopy'),'func'=>'doCopy', 'flag'=>'multiFlag_copy', 'flag_value'=>'true');


        $user_buttons = $this->getSetting('UserButtons');

        if(is_array($user_buttons) && count($user_buttons)>0){
            $button = array_merge($user_buttons, $button);
        }

        return $button;
    }

    /**
     * @param PhpExt_Menu_Menu|PhpExt_Toolbar_Toolbar $obj
     * @param $type
     * @return mixed
     * @throws Exception
     */
    function _getSystemButtons($obj, $type){
        global $xtPlugin;

        $data = $this->_systemButtons();

        if(is_array($data) && count($data)>0){
            reset($data);
            foreach ($data as $key => $val) {
                if($this->checkPermission($key)){

                    $text = trim(str_replace(array("\r\n","\r","\n"), ' ', __define($val['text'])));
                    if($type=='button'){

                        $icon = '';
                        if ($val['font-icon']) $icon = '<i class="'.$val['font-icon'].' icon-toolbar-menu"></i>';


                        $btn = $obj->addButton($val['style'], $icon.$text, $this->getSetting('icons_path').$val['icon'], new PhpExt_Handler(PhpExt_Javascript::stm($val['stm'])));

                        if(isset($val['cls']))
                        {
                            $btn->setCssClass($btn->getCssClass().' '.$val['cls']);
                        }
                    }

                    if($type=='menu' && isset($val['style'])){
                        $obj->addTextItem($val['style'], $text);
                    }
                }
            }
        }

        return $obj;
    }

    ////  übernahmen aus altem hermes_ExtAdminHandler
    function _multiactionPopup($fname, $page_url='', $page_title='')
    {
        $string = "  ".$this->multiselectStm('record_ids').PHP_EOL;
        // fbo test // $string .= "console.log(record_ids); return;";
        $string .= "  	if (record_ids == '') {".PHP_EOL;
        $string .= "     return ; /* record_ids = ".$this->getSelectionItem()."*/;".PHP_EOL;
        $string .= "  	}".PHP_EOL;

        $string.= $this->_RemoteWindow("".$page_title."","".$page_title."","".$page_url."&value_ids='+record_ids+'", true, array(), 500, 300).PHP_EOL.' new_window.show();'.PHP_EOL;

        $renderer = PhpExt_Javascript::functionDef(''.$fname.'', $string, array());
        $renderer->Statement = $fname.'();'.$renderer->Statement;

        return $renderer;

    }
    function _multiactionWindow($fname, $page_url='', $page_title='') {
        //		$data = $this->_ExtSubmitButtonHandler(array('url' => ));

        $string = "  ".$this->multiselectStm('record_ids')."";

        // fbo test // $string .= "console.log(record_ids); return;";


        $string .= "  	if (record_ids == '') {";
        $string .= "     return ; /* record_ids = ".$this->getSelectionItem()."*/;";
        $string .= "  	}\n";

        $string .= "    window.open('".$page_url."&value_ids='+record_ids,'_blank');";

        $renderer = PhpExt_Javascript::functionDef(''.$fname.'', $string, array());
        $renderer->Statement = $fname.'();'.$renderer->Statement;

        return $renderer;

    }

}
