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


class ExtGrid extends ExtEditForm{

    /**
     * @var PhpExtUx_Grid_CheckColumn
     */
    protected $checkColumn;
    /**
     * @var PhpExtUx_Grid_RowAction
     */
    protected $rowAction;

    function _reader($type = 'Json')
    {
        switch ($type) {
            case "Array":
                $reader = new PhpExt_Data_ArrayReader();
                break;
            case "Json":
            default:
                $reader = new PhpExt_Data_JsonReader();
                $reader->setRoot("data");
                $reader->setTotalProperty("totalCount")
                    ->setId($this->getMasterKey());
                break;
        }

        if ($this->getSetting('display_adminActionStatus'))
            $reader->addField(new PhpExt_Data_FieldConfigObject('adminActionStatus',null, null, null ,null,null,null));
        if (is_array($this->header))
            foreach ($this->header as $header_data) {
                switch ($header_data['type']) {
                    case "date":
                        $reader->addField(new PhpExt_Data_FieldConfigObject($header_data['name'],null,"date","Y-m-d H:i:s",null,null,null));
                        break;
                    case "price":
                        $reader->addField(new PhpExt_Data_FieldConfigObject($header_data['name'],null,"float"));
                        break;
                    default:
                        $col_int_array = array('products_id','payment_id','orders_id', 'customers_id', 'customers_status','sort_order');
                        $col_float_array = array('products_quantity');
                        if(in_array($header_data['name'],$col_int_array)){
                            $reader->addField(new PhpExt_Data_FieldConfigObject($header_data['name'],null,"int"));
                        }elseif(in_array($header_data['name'],$col_float_array)){
                            $reader->addField(new PhpExt_Data_FieldConfigObject($header_data['name'],null,"float"));
                        }else{
                            $reader->addField(new PhpExt_Data_FieldConfigObject($header_data['name'],null,null, null, $header_data['lang_code']));
                        }
                        break;
                }
            }

        return $reader;
    }


    function _gridCheckbox()
    {
        // custom column plugin example
        $checkColumn = new PhpExtUx_Grid_CheckColumn(__define("TEXT_SELECTED_ITEMS"));
        $checkColumn->setDataIndex("selectedItem")->setId('col_'.$this->code.'_checkboxColumn');
        $checkColumn->setWidth(55)->setSortable(true);
        $this->checkColumn = $checkColumn;
    }

    function _gridRowAction()
    {
        $rowAction = new PhpExtUx_Grid_RowAction(__define("TEXT_SELECTED_ITEMS"));
        $rowAction->__set("header",__define('TEXT_ACTIONS'));

        $actions = new PhpExtUx_Grid_RowActionCollection();

        $settings = $this->getSetting('rowActions');
        if (is_array($settings)) {
            foreach ($settings as $setting) {
                $actions->add(PhpExt_Grid_RowActionObject::createAction($setting['iconCls'],'',$setting['qtipIndex'],$setting['tooltip']));
            }
        }

        if($this->checkPermission('edit'))
            $actions->add(PhpExt_Grid_RowActionObject::createAction('edit','','qtip1',__define("BUTTON_EDIT")));

        if($this->checkPermission('copy'))
            $actions->add(PhpExt_Grid_RowActionObject::createAction('copy','','qtip1', __define("BUTTON_COPY")));

        if($this->checkPermission('delete'))
            $actions->add(PhpExt_Grid_RowActionObject::createAction('delete','','qtip1',__define("BUTTON_DELETE")));

        //fake action
        if(count($actions->Collection) < 1){
            $actions->add(PhpExt_Grid_RowActionObject::createAction('','','',''));
            $this->has_rowaction = false;
        } else {
            $this->has_rowaction = true;
        }

        $rowAction->__set("actions",$actions);

        $this->rowAction = $rowAction;
    }

    function _recordModel()
    {
        // type so we can add records dynamically
        $fields = new PhpExt_Data_FieldConfigObjectCollection();
        if ($this->getSetting('display_adminActionStatus'))
            $fields->add(new PhpExt_Data_FieldConfigObject(__define('TEXT_ADMIN_ACTON_STATUS'),'adminActionStatus',PhpExt_Data_FieldConfigObject::TYPE_STRING));

        switch ($this->getSetting('gridType')) {
            case "EditGrid":

                foreach ($this->header as $header_data) {
                    if (!(in_array($header_data["name"], $this->getSetting('exclude')))
                        && ((is_array($this->getSetting('include')) && in_array($header_data["name"], $this->getSetting('include'))) || !is_array($this->getSetting('include')) )) {
                        switch ($header_data['type']) {
                            /*				case "textarea":
                                                break; */
                            case "price":
                                $fields->add(new PhpExt_Data_FieldConfigObject($header_data["text"],$header_data["name"],PhpExt_Data_FieldConfigObject::TYPE_FLOAT));
                                break;
                            case "masterkey":
                                //					$col = PhpExt_Grid_ColumnConfigObject::createColumn($header_data["text"],$header_data["name"],null,null, null, null, true, true);
                                break;

                            case "date":
                                $fields->add(new PhpExt_Data_FieldConfigObject($header_data["text"],$header_data["name"],PhpExt_Data_FieldConfigObject::TYPE_DATE,"m/d/Y"));
                                break;
                            case "status":
                                $fields->add(new PhpExt_Data_FieldConfigObject($header_data["text"],$header_data["name"],PhpExt_Data_FieldConfigObject::TYPE_BOOLEAN));
                                break;
                            /*case "image":
                                $col = PhpExt_Grid_ColumnConfigObject::createColumn($header_data["text"],$header_data["name"],null,75,null,PhpExt_Javascript::variable('image_thumb'), true, false);
                                break;
                              */
                            default:

                                $fields->add(new PhpExt_Data_FieldConfigObject($header_data["text"],$header_data["name"],PhpExt_Data_FieldConfigObject::TYPE_STRING));
                                break;
                        }
                    }

                }
                break;
            default:
                $fields->add(new PhpExt_Data_FieldConfigObject('dummy','dummy',PhpExt_Data_FieldConfigObject::TYPE_STRING));
                break;

        }

        $plant = PhpExt_Data_Record::create($fields);
        return $plant;
    }


    function _ColumnModel()
    {
        $this->_gridCheckbox();
        $this->_gridRowAction();
        // ColumnModel
        $colModel = new PhpExt_Grid_ColumnModel();

        if($this->getSetting('display_checkCol')==true) {
            $colModel->addColumn($this->checkColumn);
        }

        if ($this->getSetting('display_adminActionStatus'))
            $colModel->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn(__define('TEXT_ADMIN_ACTON_STATUS'),'adminActionStatus','col_'.$this->code.'_adminActionStatus'.$this->code,null, null, PhpExt_Javascript::variable('adminActionStatusRenderer'), true, true));
        foreach ($this->header as $header_data) {

            $align = null;
            switch ($header_data['type'])
            {
                case "price":
                    $col = PhpExt_Grid_ColumnConfigObject::createColumn($header_data["text"], $header_data["name"], null, 75, PhpExt_Ext::HALIGN_RIGHT, PhpExt_Javascript::variable('change'), true, true);
                    break;
                case "master_key":
                    $col = PhpExt_Grid_ColumnConfigObject::createColumn($header_data["text"],$header_data["name"],null,null, null, null, true, true);
                    break;

                case "date":
                    $col = PhpExt_Grid_ColumnConfigObject::createColumn($header_data["text"],$header_data["name"],null,75,null,$this->_dateRenderer(), true, false);
                    break;
                case "status":
                    if(!$header_data['renderer']){
                        $col = PhpExt_Grid_ColumnConfigObject::createColumn($header_data["text"],$header_data["name"],null,40,null,PhpExt_Javascript::variable('status_icon'), true, false);
                    }else{
                        $renderer = null;
                        if ($header_data['renderer']) {
                            $renderer = PhpExt_Javascript::variable($header_data['renderer']);
                        }
                        if ($header_data['align']) {
                            $align = null;
                            switch ($header_data['align']) {
                                case "right":
                                    $align = PhpExt_Ext::HALIGN_RIGHT;
                                    break;
                                case "center":
                                    $align = PhpExt_Ext::HALIGN_CENTER;
                                    break;
                            }
                        }
                        $width = null;
                        if ($header_data['width']) {
                            $width = (int)$header_data['width'];
                        }

                        $col = PhpExt_Grid_ColumnConfigObject::createColumn($header_data["text"],$header_data["name"],null, $width, $align, $renderer, true, true);
                    }
                    break;
                case "image":
                    $col = PhpExt_Grid_ColumnConfigObject::createColumn($header_data["text"],$header_data["name"],null,30,null,PhpExt_Javascript::variable('image_thumb'), true, false);
                    break;

                case "icon":
                    $col = PhpExt_Grid_ColumnConfigObject::createColumn($header_data["text"],$header_data["name"],null,100,null,PhpExt_Javascript::variable('image_icon'), true, false);
                    break;

                default:
                    $renderer = null;
                    if ($header_data['renderer']) {
                        $renderer = PhpExt_Javascript::variable($header_data['renderer']);
                    }
                    if (isset($header_data['align']) && $header_data['align']) {

                        switch ($header_data['align']) {
                            case "right":
                                $align = PhpExt_Ext::HALIGN_RIGHT;
                                break;
                            case "center":
                                $align = PhpExt_Ext::HALIGN_CENTER;
                                break;
                        }
                    }
                    $width = null;
                    if ($header_data['width']) {
                        $width = (int)$header_data['width'];
                    }

                    $col = PhpExt_Grid_ColumnConfigObject::createColumn($header_data["text"],$header_data["name"],null, $width, $align, $renderer, true, true);

                    break;

            }
            $col->setId('col_'.$this->code.'_'.$header_data["name"]);

            $editor = $this->_switchFormField($header_data);
            if ($editor && !$header_data['masterkey'] && $this->getSetting('gridType') == 'EditGrid')
                $col->setEditor($editor);
            if ($header_data['hidden'] || $header_data['type'] == 'textarea')
                $col->setHidden(true);

            $colModel->addColumn($col);
        }
       if ($this->has_rowaction==true) $colModel->addColumn($this->rowAction);
        return $colModel;
    }

    function _isGridGrouping ()
    {
        $groupfield = false;
        $sortfield = false;
        if ($this->getSetting('GroupField') && $this->getSetting('SortField')) {
            foreach ($this->header as $key => $_values) {
                if ($_values['name'] == $this->getSetting('GroupField'))
                    $groupfield = true;
                if ($_values['name'] == $this->getSetting('SortField'))
                    $sortfield = true;
            }
            if ($groupfield && $sortfield)
                return true;
            else {
                if (!$groupfield)
                    $this->setDebugMessage('GroupField', 'No GroupField or GroupField parameter wrong!');
                if (!$sortfield)
                    $this->setDebugMessage('SortField', 'No SortField or SortField parameter wrong!');
                return false;
            }
        }
        return false;
    }


    function _view ()
    {
        $view = new PhpExt_Grid_GroupingView();
        $view->setGroupTextTemplate('{text} ({[values.rs.length]} {[values.rs.length > 1 ? "Items" : "Item"]})')
            ->setStartCollapsed($this->getSetting('GroupStartCollapsed'))
            ->setForceFit(true);

        return $view;
    }

    function _sort()
    {
        $sort = new PhpExt_Data_SortInfoConfigObject($this->getSetting('SortField'));
        $sort->setDirection(strtoupper($this->getSetting('SortDir')));
        return $sort;
    }

    // Store
    function _store()
    {
        if ($this->_isGridGrouping()) {
            $store = new PhpExt_Data_GroupingStore();
            $store->setGroupField($this->getSetting('GroupField'));
            $store->setSortInfo($this->_sort());
        } else {
            $store = new PhpExt_Data_Store();
        }

        $reader = $this->_reader();

        $store->setProxy(new PhpExt_Data_HttpProxy($this->getSetting('readfull_url').'get_data=true'))
            ->setReader($reader)
            ->setBaseParams(array("limit"=> $this->getSetting('PageSize')));

        if ($this->getSetting('RemoteSort'))
            $store->setRemoteSort(true);

        if ($this->getSetting('SortField') && $this->getSetting('SortDir'))
            $store->setSortInfo(new PhpExt_Data_SortInfoConfigObject($this->getSetting('SortField'), $this->getSetting('SortDir')));

        return $store;
    }

    function _selModel()
    {
        $selModel = new PhpExt_Grid_RowSelectionModel();
        $preview = "";
        $selModel->setSingleSelect(true);
        if ($this->getSetting('gridView') && $this->getSetting('gridTemplate') != '') {
            $preview = "var detailPanel = Ext.getCmp('".$this->getSelectionItem()."previewPanel'); "
                .$this->getSelectionItem()."previewTpl.overwrite(detailPanel.body, rec.data);";
        }

        $selModel->attachListener("rowselect",
            new PhpExt_Listener(PhpExt_Javascript::functionDef(null,
                "".$this->getSelectionItem()." = rec.data.".$this->getMasterKey().";
						".$preview."


						",
                array("sm","row","rec")))
        );

        return $selModel;
    }


    /**
     * @return string
     * @throws Exception
     */
    function displayGridJS ()
    {
        global $xtc_acl, $xtPlugin, $db;

        if (!isset($_REQUEST['parentNode']) || empty($_REQUEST['parentNode'])) {
            $_REQUEST['parentNode'] = 'node_' . $_REQUEST['load_section'];
        }

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':top')) ? eval($plugin_code) : false;
        if(isset($plugin_return_value))
            return $plugin_return_value;

        $this->getLayout();
        $store = $this->_store();

        //$store->setBaseParams();

        $colModel = $this->_ColumnModel();
        $selModel = $this->_selModel();

        // new record Item in GridEdit
        $recordModel = $this->_recordModel();

        $gridView = new PhpExt_Grid_GridView();
        // text for empty grid
        $gridView->setEmptyText(__define("TEXT_EMPTY"));
        $gridView->setForceFit(true);

        // Setup Grid
        if ($this->getSetting('gridType') == 'EditGrid') {
            $grid = new PhpExt_Grid_EditorGridPanel();
            $grid->setClicksToEdit(1);
        } else {
            $grid = new PhpExt_Grid_GridPanel();
        }
        $grid->setEnableRowHeightSync(true);
        if(isset($_REQUEST['catID']))
            $grid->setId( $this->code.(int)$_REQUEST['catID']."gridForm" );
        else  $grid->setId( $this->code."gridForm" );

        $grid->setStore($store)
            ->setView($gridView)
            ->setColumnModel($colModel)
            ->setSelectionModel($selModel)
            ->setStripeRows(true)
            ->setLoadMask(true)
            ->setBorder(true);

        $grid->setStateful(true)->setStateId($this->code."gridForm");

        //	TODO $grid->__set('autoExpandColumn',"rowAction");

        $grid->getPlugins()->add($this->checkColumn);
        $grid->getPlugins()->add($this->rowAction);

        // grid toolbar
        $tb = $grid->getTopToolbar();

        if($this->getSetting('display_checkItemsCheckbox'))
        {
            $tmp= $this->_checkboxField($_REQUEST['parentNode']);
            $tb->addTextItem(1, __define('TEXT_CHECKALLITEMS'));
            $tb->addSpacer(2);
            $tb->addItem(3, $tmp);
            $tb->addSpacer(4);
        }

        if ($this->getSetting('display_searchPanel')) {
            $tmp2=$this->_searchField($store);
            $tb->addTextItem(5,__define('TEXT_SEARCH'));
            $tb->addSpacer(6);
            $tb->addItem(7, $tmp2);
            $tb->addSpacer(8);
        }

        $tb = $this->getUserMenu($tb, 'TopToolbar', 'grid');
        $tb->addSpacer(2);

        $tb = $this->_getSystemButtons($tb, 'button');

        $help_link = $db->GetOne("SELECT url_h FROM ".TABLE_ADMIN_NAVIGATION." where `text` = ?", [$this->code]);
        if(!empty($help_link))
        {
            $js_help_link = PhpExt_Javascript::stm('
                            window.open("'.$help_link.'", "xt-help-wnd");
                        ');
            $tb->addButton(1000, '<i class="fa fa-question-circle" aria-hidden="true"></i>', null, new PhpExt_Handler($js_help_link));
        }

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.':displayGridJS_afterSystemMenu')) ? eval($plugin_code) : false;

        if ($this->_isGridGrouping()) {
            $grid->setView($this->_view());
        }

        $paging = new PhpExt_Toolbar_PagingToolbar();
        $paging->setStore($store)
            ->setPageSize($this->getSetting('PageSize'))
            ->setDisplayInfo("Topics {0} - {1} of {2}")
            ->setEmptyMessage("No topics to display");

        $grid->setBottomToolbar($paging);

        $this->getDebugMessages();

        $tpl = new PhpExt_Template($this->getSetting('gridTemplate'));

        if ($this->getSetting('gridView') && $this->getSetting('gridTemplate') != '') {

            $gw = new PhpExt_Grid_GridView();
            $gw->setForceFit(true);
            $grid->setView($gw);

            $viewPanel = new PhpExt_Panel();
            $viewPanel->setTitle(__define('TEXT_PREVIEW'))
                ->setBodyStyle("padding: 10px;")
                ->setAutoScroll(true)
                ->setHtml(__define('TEXT_PLEASE_SELECT'))
                ->setLayout(new PhpExt_Layout_FitLayout())
                ->setWidth(250)
                ->setHeight(300)
                ->setId($this->getSelectionItem().'previewPanel');

            $layout = new PhpExt_Panel();
            $layout->setLayout(new PhpExt_Layout_BorderLayout());
            $layout->setAutoWidth(true);

            $layout->addItem($viewPanel,  PhpExt_Layout_BorderLayoutData::createEastRegion());
            $layout->addItem($grid, PhpExt_Layout_BorderLayoutData::createCenterRegion());
            unset ($grid);
            $grid = $layout;
        }

        $wrapperPanel = new PhpExt_Panel();
        $wrapperPanel->setLayout(new PhpExt_Layout_BorderLayout());    //changed layout vvv
        $wrapperPanel->setId( $_REQUEST['parentNode'].'Wrapper' );

        $top = new PhpExt_Toolbar_Toolbar();
        $top = $this->getUserMenu($top, 'Toolbar', 'grid');

        $wrapperPanel->setTopToolbar($top);
        $wrapperPanel->addItem( $grid );

        $wrapperPanel->addItem( $grid,PhpExt_Layout_BorderLayoutData::createCenterRegion() );	 // added PhpExt_Layout_BorderLayoutData::createCenterRegion() vvv

        if(constant("_SYSTEM_ADMIN_FILTER") == 'true'){
            require_once(_SRV_WEBROOT._SRV_WEB_FRAMEWORK.'admin/filter/show_panel.php');
        }

        $first_data = $this->_getData('data', 0);
        $defaultselect = "";
        if ($first_data[$this->getMasterKey()])
            $defaultselect = ' = "'.$first_data[$this->getMasterKey()].'"';
        $js = 'var '.$this->getSelectionItem();
        $js.= $defaultselect.';';
        $js.= $this->code.'_new_records = new Array(); ';
        $js.= PhpExt_Ext::onReady(
            PhpExt_Javascript::stm(PhpExt_QuickTips::init()),

            PhpExt_Javascript::stm( '
    	    Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
    	    '),

            $this->checkColumn->getJavascript(false, "checkColumn"),
            $this->rowAction->getJavascript(false, "rowAction"),
            $this->_italicRenderer(),
            $this->_changeRenderer(),
            $this->_pctChangeRenderer(),
            $this->_imageRenderer(),
            $this->_iconRenderer(),
            $this->_genderRenderer(),
            $this->_statusRenderer(),
            $this->_statusOrEmptyRenderer(),
            $this->_bulletOrEmptyRenderer(),
            $this->_iconOrEmptyRenderer(),
            $this->_awesomeRenderer(),
            $this->_starRenderer(),
            $this->_slaveRenderer(),
            $this->_RightsRenderer(),
            $this->_taxRenderer(),
            $this->_zoneRenderer(),
            $this->_manufacturersRenderer(),
            $this->_languageIsoRenderer(),
            $this->_languageIsoRendererPlusAll(),
            $this->_languageIsoRenderer2(),
            $this->_languageIsoRenderer3(),
            $this->_adminActionStatusRenderer(),
            $this->_externLinkRenderer(),
            $this->_RowAction(),
            $this->_MenuAction(),
            $this->_paymentIconRenderer(),
            PhpExt_Javascript::assignNew($this->getSelectionItem()."_new_record", $recordModel),
            $store->getJavascript(false, $this->code."ds"),
            $colModel->getJavascript(false, $this->code."colModel"),
            $grid->getJavascript(false, $this->code.$this->getSelectionItem()."gridForm"),
            $tpl->getJavascript(false, $this->getSelectionItem()."previewTpl"),
            $wrapperPanel->getJavascript(false, $_REQUEST['parentNode']."Wrapper"),
            $this->_gridRowdblClick(),
            $store->load(new PhpExt_Data_StoreLoadOptions(array(
                    "start"=>0,"limit"=>$this->getSetting('PageSize')))
            ),
            $this->_saveGrid(),

            ($plugin_code = $xtPlugin->PluginCode(__CLASS__.':displayGridJS_multifunctions')) ? eval($plugin_code) : false,

            $this->_SubmitSort('up'),
            $this->_SubmitSort('down'),
            $this->_SubmitSort2('up'),
            $this->_SubmitSort2('down'),
            $this->processImage (),
            $this->unlinkImage (),
            $this->importImage (),
            $this->add_multiactions(),
            $this->add_multimenuactions(),
            $this->_multiaction('doGetSelected', 'get_select_flag', 'true'),

            PhpExt_Javascript::stm( '
            try{
                Ext.getCmp("'.$_REQUEST['parentNode'].'").add( '.$_REQUEST['parentNode'].'Wrapper );
                /*'.$_REQUEST['parentNode'].'Wrapper.doLayout();*/
                Ext.getCmp("'.$_REQUEST['parentNode'].'").doLayout();
                Ext.getCmp("'.$_REQUEST['parentNode'].'").syncSize();
            }catch(e){
                console.log("exception in reload tab for parentNode ['.$_REQUEST['parentNode'].'] => ",e);
            } ' )
        );

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':bottom')) ? eval($plugin_code) : false;

        return '<script type="text/javascript"  charset="utf-8">'. $js .' </script>';
    }

    function editGridSettings()
    {
        $edit = new PhpExt_Form_FieldSet();
        $edit->setTitle(__define("TEXT_LANG_SETTINGS"));
        $edit->setFrame(true)
            ->setBodyStyle("padding:5px 5px 0")
            ->setWidth(350)
            ->setAutoHeight(true);

        return $edit;
    }

    function saveData($_data) {
        return true;
    }
}

