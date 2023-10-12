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

require_once(_SRV_WEBROOT._SRV_WEB_FRAMEWORK.'admin/classes/class.ExtFunctions.php');
require_once(_SRV_WEBROOT._SRV_WEB_FRAMEWORK.'admin/classes/class.ExtEditForm.php');
require_once(_SRV_WEBROOT._SRV_WEB_FRAMEWORK.'admin/classes/class.ExtGrid.php');
require_once(_SRV_WEBROOT._SRV_WEB_FRAMEWORK.'admin/classes/class.ExtAdminHandler.php');


set_include_path(_SRV_WEBROOT._SRV_WEB_FRAMEWORK.'library/');

include_once 'PhpExt/Javascript.php';


include_once 'PhpExt/Ext.php';
include_once 'PhpExt/Data/Store.php';
include_once 'PhpExt/Data/ArrayReader.php';
include_once 'PhpExt/Data/FieldConfigObject.php';
include_once 'PhpExt/Data/SimpleStore.php';
include_once 'PhpExt/Data/GroupingStore.php';
include_once 'PhpExt/Data/SortInfoConfigObject.php';
include_once 'PhpExt/Data/ScriptTagProxy.php';
include_once 'PhpExt/Data/StoreLoadOptions.php';
include_once 'PhpExt/Data/JsonReader.php';
include_once 'PhpExt/Data/HttpProxy.php';
include_once 'PhpExt/Data/JsonStore.php';
include_once 'PhpExt/Data/XmlReader.php';
include_once 'PhpExt/Data/Record.php';


include_once 'PhpExt/Grid/ColumnModel.php';
include_once 'PhpExt/Grid/ColumnConfigObject.php';
include_once 'PhpExt/Grid/GridPanel.php';
include_once 'PhpExt/Grid/RowSelectionModel.php';
include_once 'PhpExt/Grid/CheckboxSelectionModel.php';
include_once 'PhpExt/Grid/EditorGridPanel.php';


include_once 'PhpExt/DataView.php';
include_once 'PhpExt/Panel.php';
include_once 'PhpExt/Listener.php';
include_once 'PhpExt/Config/ConfigObject.php';

include_once 'PhpExt/Form/FormPanel.php';
include_once 'PhpExt/Form/FieldSet.php';
include_once 'PhpExt/Form/TextField.php';
include_once 'PhpExt/Form/TimeField.php';
include_once 'PhpExt/Form/HtmlEditor.php';
include_once 'PhpExt/Form/CKEditor.php';
include_once 'PhpExt/Form/DateField.php';
include_once 'PhpExt/Form/Hidden.php';
include_once 'PhpExt/Form/NumberField.php';
include_once 'PhpExt/Form/PasswordField.php';
//include_once 'PhpExt/Form/UploadField.php';
include_once 'PhpExt/Form/Radio.php';
include_once 'PhpExt/Form/ComboBox.php';
include_once 'PhpExt/Form/Checkbox.php';
include_once 'PhpExt/Form/TriggerField.php';
include_once 'PhpExt/Form/TextArea.php';
include_once 'PhpExt/Form/ComboBox.php';

include_once 'PhpExt/QuickTips.php';

include_once 'PhpExt/Layout/ColumnLayout.php';
include_once 'PhpExt/Layout/ColumnLayoutData.php';
include_once 'PhpExt/Layout/FitLayout.php';
include_once 'PhpExt/Layout/FitLayoutData.php';
include_once 'PhpExt/Layout/BorderLayout.php';
include_once 'PhpExt/Layout/BorderLayoutData.php';
include_once 'PhpExt/Layout/CardLayout.php';
include_once "PhpExt/Layout/AccordionLayout.php";
include_once "PhpExt/Layout/AccordionLayoutData.php";


include_once 'PhpExt/Window.php';
include_once 'PhpExt/AutoLoadConfigObject.php';
include_once 'PhpExt/DataView.php';

include_once 'PhpExt/Handler.php';
include_once 'PhpExt/Button.php';
include_once 'PhpExt/TabPanel.php';


include_once 'PhpExt/Layout/FormLayout.php';
include_once 'PhpExt/Layout/FitLayout.php';
include_once 'PhpExt/Layout/AnchorLayoutData.php';
include_once 'PhpExt/Layout/TableLayout.php';
include_once 'PhpExt/Layout/TableLayoutData.php';


include_once 'PhpExt/Toolbar/Toolbar.php';
include_once 'PhpExt/Toolbar/Button.php';
include_once 'PhpExt/Toolbar/PagingToolbar.php';


include_once 'PhpExt/Template.php';
include_once 'PhpExt/XTemplate.php';

include_once 'PhpExtUx/ImageChooser.php';

include_once 'PhpExtUx/Form/LovCombo.php';
include_once 'PhpExtUx/Form/LovCombo2.php';
include_once 'PhpExtUx/Form/RadioGroup.php';
include_once 'PhpExtUx/Form/ItemSelector.php';
include_once 'PhpExtUx/Form/Multiselect.php';

// Amchart objects
include_once 'PhpExtUx/Form/AmchartObjects/AmChartInvokable.php';
include_once 'PhpExtUx/Form/AmchartObjects/AmChartConfigObject.php';
include_once 'PhpExtUx/Form/AmchartObjects/AmchartExecutableObject.php';
include_once 'PhpExtUx/Form/AmchartObjects/AmchartCallable.php';
include_once 'PhpExtUx/Form/AmchartObjects/AmAxisBase.php';
include_once 'PhpExtUx/Form/AmchartObjects/AmChart.php';
include_once 'PhpExtUx/Form/AmchartObjects/AmCoordinateChart.php';
include_once 'PhpExtUx/Form/AmchartObjects/AmRectangularChart.php';
include_once 'PhpExtUx/Form/AmchartObjects/AmSerialChart.php';
include_once 'PhpExtUx/Form/AmchartObjects/AmSlicedChart.php';
include_once 'PhpExtUx/Form/AmchartObjects/AmPieChart.php';
include_once 'PhpExtUx/Form/AmchartObjects/AmCategoryAxis.php';
include_once 'PhpExtUx/Form/AmchartObjects/AmValueAxis.php';
include_once 'PhpExtUx/Form/AmchartObjects/AmGraph.php';
include_once 'PhpExtUx/Form/AmchartObjects/AmChartCursor.php';
include_once 'PhpExtUx/Form/AmchartObjects/AmChartScrollbar.php';
include_once 'PhpExtUx/Form/AmchartObjects/AmChartLegend.php';
include_once 'PhpExtUx/Form/ExtAmchart.php';

include_once 'PhpExt/Grid/GroupingView.php';


include_once 'PhpExt/Tree/TreePanel.php';
include_once 'PhpExt/Tree/TreeNode.php';
include_once 'PhpExt/Tree/AsyncTreeNode.php';
include_once 'PhpExt/Tree/TreeLoader.php';
include_once 'PhpExt/Tree/TreeEditor.php';


// User Extension
include_once 'PhpExtUx/App/SearchField.php';
include_once 'PhpExtUx/App/CheckboxField.php';

include_once 'PhpExtUx/Grid/CheckColumn.php';
include_once 'PhpExtUx/Grid/RowAction.php';


include_once 'PhpExtUx/DataView/DragSelector.php';
include_once 'PhpExtUx/DataView/LabelEditor.php';
include_once 'PhpExtUx/View/ImageDragZone.php';

include_once 'PhpExt/Menu/Menu.php';
include_once 'PhpExt/Menu/TextItem.php';

class hermes_ExtAdminHandler  extends  ExtAdminHandler
{
    function __construct($extAdminHandler = array())
    {
        foreach ($extAdminHandler as $key => $value)
        {
            $this->$key = $value;
        }
    }

    function _switchFormField ($line_data, $lang_data = array()) {

        $label = $line_data['name'];
        //		$name = __define($line_data['text']);
        $name = $line_data['text'];
        $value = true;


        switch ($line_data['type']) {
            case "textarea":
                $data = PhpExt_Form_TextArea::createTextArea($label, $name);
                if (isset($line_data['height'])) {
                    $data->setHeight($line_data['height']);
                } else {
                    $data->setHeight('150');
                }
                if (isset($line_data['width'])) {
                    $data->setWidth($line_data['width']);
                } else {
                    $data->setWidth('70%');
                }

                break;
            case "admininfo":
                $data = PhpExt_Form_TextArea::createTextArea($label, $name);
                if (isset($line_data['height'])) {
                    $data->setHeight($line_data['height']);
                } else {
                    $data->setHeight('80');
                }
                if (isset($line_data['width'])) {
                    $data->setWidth($line_data['width']);
                } else {
                    $data->setWidth('70%');
                }
                $data->setCssClass('form_info');

                break;
            case "htmleditor":

                if(_SYSTEM_USE_WYSIWYG=='SimpleHtmlEditor'){

                    $data = PhpExt_Form_HtmlEditor::createHtmlEditor($label, $name);
                    if (isset($line_data['required'])) {
                        $line_data['required'] = '';
                    }
                    break;

                }elseif(_SYSTEM_USE_WYSIWYG=='TinyMce'){

                    $data = PhpExt_Form_TextArea::createTextArea($label, $name);
                    $data->setCssClass('TinyMce');
                    break;

                }else{

                    $data = PhpExt_Form_TextArea::createTextArea($label, $name);
                    if (isset($line_data['height'])) {
                        $data->setHeight($line_data['height']);
                    } else {
                        $data->setHeight('150');
                    }
                    if (isset($line_data['width'])) {
                        $data->setWidth($line_data['width']);
                    } else {
                        $data->setWidth('70%');
                    }

                    break;
                }

            case "image":
                $uniqueID = $name.'_'.time();

                // dummy field
                $data = PhpExt_Form_Hidden::createHidden($label, $name);

                if (!$line_data['value'])
                    $value = false;
                break;
            case "file":
                $uniqueID = $name.'_'.time();
                $data = PhpExt_Form_TextField::createTextField($label, $name, $uniqueID);

                $panel = $this->_fileUploadPanel($data, $uniqueID, $line_data);

                if (!$line_data['value'])
                    $value = false;
                break;
            case "date":
                $data = PhpExt_Form_DateField::createDateField($label, $name);
                $data->setFormat('Y-m-d');

                break;
            case "dropdown":
                if (empty($line_data['url'])) {
                    // default dropdownstatus
                    $url = 'DropdownData.php?get=status_truefalse';
                } else {
                    $url = $line_data['url'];
                }
                $data = $this->_comboBox($label, $name, $line_data['url'],$line_data['width']);
                break;
            case "dropdown_ms":
            case "dropdown_multi":
            case "dropdown_multiselect":
                if (empty($line_data['url'])) {
                    // default dropdownstatus
                    $url = 'DropdownData.php?get=status_truefalse';
                } else {
                    $url = $line_data['url'];
                }
                // dropdown with checkbox
                $data = $this->_multiComboBox($label, $name, $line_data['url']);
                break;
            case "itemselect":
                if (empty($line_data['url'])) {
                    // default dropdownstatus
                    $url = 'DropdownData.php?get=status_truefalse';
                } else {
                    $url = $line_data['url'];
                }
                $data = $this->_itemSelect($label, $name, $line_data['url']);
                break;


            case "truefalse": //for admin configuration
            case "status":

                $data = PhpExt_Form_Checkbox::createCheckbox($label, $name);
                $data->setCssClass("checkBox");
                if ($line_data['value'] && $line_data['value'] != 'false')
                    $data->setChecked(true);
                else
                    $data->setChecked(false);

                // fix: set 'on' value to 1
                $data->setInputValue(1);

                $line_data['required'] = false; // combo not required
                break;
            case "master_key":
                //$data = PhpExt_Form_TextField::createTextField($label, $name);
                //$line_data['required'] = true; // default combo required
                //$data->setDisabled(true);
                //break;

            case "hidden":
                $data = PhpExt_Form_Hidden::createHidden($label, $name);
                $line_data['required'] = false; // not required, user can not enter anything
                break;

            case "password":
                $data = PhpExt_Form_PasswordField::createPasswordField($label,$name);

                break;

            case "upload":
                $data = PhpExt_Form_UploadField::createUploadField($label,$name);
                break;

            default:
                $data = PhpExt_Form_TextField::createTextField($label, $name);
                if (isset($line_data['width'])) {
                    $data->setWidth($line_data['width']);
                } else {
                    $data->setWidth('300');
                }
                if (isset($line_data['disabled'])) $data->setDisabled(true);

                break;
        }
        ////////////////////////////////////////////////////////////////////////
        // field validation
        // readonly
        if ($line_data['readonly'] && !$this->getSetting('edit_masterkey')) {
            $data->setDisabled(true);
        }
        // minimum
        if ($line_data['min']) {
            $data->setMinLength($line_data['min']);
            $data->setMinLengthText(__define("ERROR_MIN"));
        }
        // maximum
        if ($line_data['max']) {
            $data->setMaxLength($line_data['max']);
            $data->setMaxLengthText(__define("ERROR_MAX"));
        }
        // required
        if ($line_data['required']) {
            $data->setAllowBlank(false);
            $data->setBlankText(__define("ERROR_BLANK"));
        }
        // field validation end
        ////////////////////////////////////////////////////////////////////////

        // set field value
        if ($value && $line_data['value']!=='' && $line_data['value']!=='0000-00-00 00:00:00') {
            $data->setValue($line_data['value']);
        }
        if ($panel)
            $data = $panel;
        return $data;
    }

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

    function multiselectStm ($var = 'record_ids') {
        $js = "
             var records = new Array();
             records = ".$this->code."ds.getModifiedRecords();
             ".$this->_getGridModifiedRecordsData()."
		 	 var ".$var." = '';
		 	 for (var i = 0; i < records.length; i++) {
		 	     if (records[i].get('selectedItem'))
		 	      ".$var." += records[i].get('".$this->getMasterKey()."') + ',';
		 	 }
 ".$this->code."ds.modified = [];

		 	 ";
        return $js;
    }

    function  _ExtSubmitButtonHandler ($data) {
        $js = '';

        if ($data['url'])
            $js.= " url: '".$data['url']."'";

        if ($data['success'])
        {
            $js.= ", success: ".$data['success'];
        }
        elseif ($data['close']=='close')
        {
            $js.= ", success: function(form,action){var r = action.result; if (!r.success){Ext.Msg.alert('".__define('TEXT_ALERT')."', r.errorMsg);} else if(r.failed || r.msg){ Ext.MessageBox.alert('".__define('TEXT_ALERT')."',r.msg ? r.msg : '".__define('TEXT_ALERT')."');} contentTabs.remove(contentTabs.getActiveTab()); var gh=Ext.getCmp('".$_REQUEST['gridHandle']."'); if (gh) gh.getStore().reload(); }";
        }
        else
        {
            $js.= ", success: function(form,action){var r = action.result; if (!r.success){Ext.Msg.alert('".__define('TEXT_ALERT')."', r.errorMsg);} else if(r.failed || r.msg){ Ext.MessageBox.alert('".__define('TEXT_ALERT')."',r.msg ? r.msg : '".__define('TEXT_ALERT')."');} contentTabs.remove(contentTabs.getActiveTab()); var gh=Ext.getCmp('".$_REQUEST['gridHandle']."'); if (gh) gh.getStore().reload(); }";
        }

        if ($data['failure'])
        {
            $js.= ", failure: ".$data['failure']."";
        }
        else
        {
            $js.= ", failure: function(form,action){var r = action.result; Ext.Msg.alert('".__define('TEXT_ALERT')."', r.errorMsg);}";
        }
        if ($data['params'])
            $js.= ", params: ".$data['params']."";
        $js.= ", waitMsg: '".__define("TEXT_LOADING")."'";

        return $js;
    }


    static function _comboBox2($label, $name, $listWidth='300')
    {
        $reader = new PhpExt_Data_JsonReader();
        $reader->setRoot("topics")
            ->setTotalProperty("totalCount");
        $reader->addField(new PhpExt_Data_FieldConfigObject("id"));
        $reader->addField(new PhpExt_Data_FieldConfigObject("name"));
        $reader->addField(new PhpExt_Data_FieldConfigObject("desc"));

        $store = new PhpExt_Data_JsonStore();
        $store->setReader($reader);

        $combo = new PhpExt_Form_ComboBox();
        $combo->setTypeAhead(true)
            ->setTypeAheadDelay(1)
            ->setMinChars(1)
            ->setCssStyle(new PhpExt_Config_ConfigObject(array("width"=>"auto")))
            ->setTemplate('<tpl for="."><div ext:qtip="{id} {desc}" class="x-combo-list-item" >{name}</div></tpl>')
            ->setStore($store)
            ->setMode(PhpExt_Form_ComboBox::MODE_LOCAL)
            ->setFieldLabel($name)
            ->setTitle(TEXT_ADMIN_DROPDOWN_SELECT)
            ->setHiddenName($label)
            ->setListWidth($listWidth)
            ->setMode('local')
            ->setTriggerAction(PhpExt_Form_ComboBox::TRIGGER_ACTION_ALL)
            ->setSelectOnFocus(true)
            ->setDisplayField("name")
            ->setValueField("id");

        return $combo;
    }

    static function _RemoteWindow2($window_title, $code, $panel, $width = 300, $height = 200, $return = 'js') {
        global $link_params;

        $id = $code.'RemoteWindow';

        $window = new PhpExt_Window();
        $window->setTitle(__define($window_title))
            ->setWidth($width)
            ->setHeight($height)
            ->setAutoHeight(true)
            ->setId( $id )
            //	       ->setMinWidth($width)
            //	       ->setMinHeight($height)
            ->setLayout(new PhpExt_Layout_FitLayout())
            ->setResizable(true)
//				   ->setCloseAction(PhpExt_Window::CLOSE_ACTION_HIDE)
            ->setPlain(true)
            ->setBodyStyle("padding-right:20px; padding-bottom:20px;max-height:800px")
            ->setButtonAlign(PhpExt_Ext::HALIGN_CENTER);

        $window->addItem($panel);

        $closeBtn = PhpExt_Button::createTextButton(__define("BUTTON_CLOSE_WINDOW"),new PhpExt_Handler(PhpExt_Javascript::stm("if (new_window) { new_window.destroy() } else{ this.destroy() } ")));
        $closeBtn->setIcon("images/icons/cancel.png")
            ->setIconCssClass("x-btn-text");
        $window->addButton($closeBtn);

        if ($return == 'js')
            return $window->getJavascript(false, "new_window");
        else
            return $window;
    }


}

