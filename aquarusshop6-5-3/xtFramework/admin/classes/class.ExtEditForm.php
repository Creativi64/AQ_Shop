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

class ExtEditForm extends ExtFunctions {

    /**
     * Creating Panel with tabs for each Store
     *
     * @param null $langPanel
     * @return bool|PhpExt_TabPanel|null
     * @throws Exception
     */
	function _getTabStoreLangPanel ($langPanel = null) {
		global $language,$store_handler;
		if (count($this->lang_data) == 0) return false;

        if ($langPanel == null) {
    		$langPanel = new PhpExt_TabPanel();
    		$langPanel->setActiveTab(0)
    		->setDeferredRender(false)
    		->setEnableTabScroll(true)
    		->setDefaults(new PhpExt_Config_ConfigObject(array("autoHeight"=>true,"bodyStyle"=>"padding: 5px; margin:0px","hideMode"=>'offsets')));;
        }
		

		$stores = $store_handler->getStores();
		$activeTab = 0;
		 foreach($stores as $store)
		 {
			 static $store_tab_counter = 0;
			 $store_obj = $store_handler->_get($store['id']);

			 if($store_obj->data[0]['shop_ssl_domain'] == $_SERVER['HTTP_HOST'])
			 {
				 $activeTab = $store_tab_counter;
			 }
			 $store_tab_counter++;
		 	
			$rightPanel = new PhpExt_Panel();
			$rightPanel ->setCollapseFirst(true)
			    ->setBodyStyle("padding: 5px;")
                ->setTitle($store['text'])
			    ->setLayout(new PhpExt_Layout_FormLayout())
                ->setCssClass('lang_panel_store_'.$store['id']);

			$rightPanel->addItem($this->_getTabLangPanel(null,$store['id']));
			$langPanel->addItem($rightPanel);
		 }
		$langPanel->setActiveTab($activeTab);
		return $langPanel;
	}

    /**
     * Creating Panel with tabs for each language
     *
     * @param null $langPanel
     * @param string $store_id
     * @return bool|PhpExt_TabPanel|null
     * @throws Exception
     */
	function _getTabLangPanel ($langPanel = null,$store_id='') {
		global $language, $store_handler;
		if (count($this->lang_data) == 0) return false;

        if ($langPanel == null) {
    		$langPanel = new PhpExt_TabPanel();
			$langPanel->setDeferredRender(false)
    		->setEnableTabScroll(true)
    		->setDefaults(new PhpExt_Config_ConfigObject(array("autoHeight"=>true,"bodyStyle"=>"padding: 5px; margin:10px","hideMode"=>'offsets')));;
        }
        $activeTab = 0;
        $counter = 0;
		foreach ($language->_getLanguageList() as $key => $val) {
            //  	if($val['edit']=='1') {
            if($val['edit']=='1' || $this->code == 'language_content') {  // bei den sprachen selbst sollen immer all angezeigt werden    allow_edit
			    $langPanel->addItem($this->_getLangPanel($val,$store_id));
		}
            if($language->code == $val['code'])
            {
                $activeTab = $counter;
            }
            $counter++;
		}
        $langPanel->setActiveTab($activeTab);
		return $langPanel;
	}


    /**
     * Creating each Language Tab
     *
     * @param $_lang
     * @param string $store_id
     * @return PhpExt_Panel
     * @throws Exception
     */
    function _getLangPanel($_lang,$store_id='') {

        $val = $_lang;

        $rightPanel = new PhpExt_Panel();
        $rightPanel->setTitle($val['text'])
            ->setIconCssClass($val['code'].'_icon')
            ->setCollapseFirst(true)
            ->setBodyStyle("padding: 5px;")
            ->setLayout(new PhpExt_Layout_FormLayout());

        $len_meta_keywords = 75;
        $len_meta_title = 75;
        $len_meta_des = 150;

        foreach ($this->lang_data[$val['code']] as $header_data)
        {
            $ffield_header_id = '';
            $continue= true;
            if ((strpos($header_data['name'],'_store'.$store_id.'_')===false) &&($store_id!='') ) $continue = false;

            if ($continue)
            {
                $header_data2 = $header_data;

                if ((strpos($header_data['name'],'_store'.$store_id)!==false) && (defined(str_replace("_STORE".$store_id,"",$header_data['text']))))
                    $header_data2['text'] = constant(str_replace("_STORE".$store_id,"",$header_data['text']));

                $fField = $this->_switchFormField($header_data2, $val);
                if (array_value($this->url_data, 'currentType') =='category'){
                    $this->url_data['edit_id'] = $this->url_data['currentId'] ?? 0;
                }
                $ffield_header_id = $this->code.'_'.$header_data['name'].$this->url_data['edit_id'];
                if (isset($this->url_data['store_id'])) {
                    $ffield_header_id .= "_" . $this->url_data['store_id'];
                }
                $fField->setId($ffield_header_id);
                if($header_data2['type'] == "htmleditor") $fField->setCssClass("xtclass-".$this->code);

                if(constant("_SYSTEM_METATAGS_WORDS_COUNTER") == 'true' && $this->url_data['load_section']!='configuration'){
                    if(strpos(strtolower($header_data['name']),'meta_')!==false){
                        $fField->attachListener('change', new PhpExt_Listener(
                            PhpExt_Javascript::functionDef(null,
                                "// Some JS code
                            var number = 0;
                            var len_meta_keywords  = ".$len_meta_keywords.";
                            var len_meta_title   = ".$len_meta_title.";
                            var len_meta_des = ".$len_meta_des.";
                            var a = Ext.getCmp('".$ffield_header_id."').getValue();
                            var number = 0;
                            var matches = a.match(/\b/g);
                            if(matches) {
                                number = matches.length/2;
                            }
                            var nospaces = a.replace(/ /g,'');

                            var coutertext = '".__define("TEXT_WORDS_COUNT").": '+number + ' - ".__define("TEXT_CHARACTER_COUNTER").": ' + a.length ;
                            var pos_des = \"".$ffield_header_id."\".indexOf('description');
                            var pos_title = \"".$ffield_header_id."\".indexOf('title');
                            var pos_keywords = \"".$ffield_header_id."\".indexOf('keywords');
                            var len  =  a.length ;
                            if(pos_keywords == -1 && pos_des == -1 && pos_title > 0){
                                if(len > len_meta_title)
                                    Ext.getCmp('wordcouter_".$ffield_header_id."').getEl().setStyle('border', '1px solid #f00');
                                else
                                    Ext.getCmp('wordcouter_".$ffield_header_id."').getEl().setStyle('border', '1px solid #0f3');
                            }

                            if(pos_keywords == -1 && pos_des > 0 && pos_title == -1){
                                if(len > len_meta_des)
                                    Ext.getCmp('wordcouter_".$ffield_header_id."').getEl().setStyle('border', '1px solid #f00');
                                else
                                    Ext.getCmp('wordcouter_".$ffield_header_id."').getEl().setStyle('border', '1px solid #0f3');
                            }

                            if(pos_keywords > 0 && pos_des == -1 && pos_title == -1){
                                if(len > len_meta_keywords)
                                    Ext.getCmp('wordcouter_".$ffield_header_id."').getEl().setStyle('border', '1px solid #f00');
                                else
                                    Ext.getCmp('wordcouter_".$ffield_header_id."').getEl().setStyle('border', '1px solid #0f3');
                            }

                            Ext.getCmp('wordcouter_".$ffield_header_id."').setValue(coutertext);
                        "
                            )));
                    }
                }
                $rightPanel->addItem($fField);
            }
            if(constant("_SYSTEM_METATAGS_WORDS_COUNTER") == 'true' && $this->url_data['load_section']!='configuration')
            {
                if(strpos(strtolower($ffield_header_id),'meta_')!==false){
                    $a = PhpExt_Form_TextField::createTextField('wordcouter_'.$ffield_header_id, 'wordcouter_'.$ffield_header_id);
                    $a->setHideLabel('wordcouter_'.$ffield_header_id);
                    $a->setDisabled(true);

                    $pos_des = stripos($ffield_header_id, 'description');
                    $pos_title = stripos($ffield_header_id, 'title');
                    $pos_keywords = stripos($ffield_header_id, 'keywords');
                    if($pos_keywords!==false){
                        if(strlen($header_data['value']) > $len_meta_keywords)
                            $a->setCssClass('word_counter_fail');
                        else
                            $a->setCssClass('word_counter_success');
                    }
                    if($pos_des!==false){
                        if(strlen($header_data['value']) > $len_meta_des)
                            $a->setCssClass('word_counter_fail');
                        else
                            $a->setCssClass('word_counter_success');
                    }
                    if($pos_title!==false){
                        if(strlen($header_data['value']) > $len_meta_title)
                            $a->setCssClass('word_counter_fail');
                        else
                            $a->setCssClass('word_counter_success');
                    }
                    $a->setId('wordcouter_'.$ffield_header_id);
                    if(trim($header_data['value']) != ''){
                        $coutertext = __define("TEXT_WORDS_COUNT").": ".str_word_count(trim($header_data['value'])). " - ".__define("TEXT_CHARACTER_COUNTER").": " .strlen(trim($header_data['value'])) ;
                        $a->setValue($coutertext);
                    }else{
                        $a->setValue('');
                    }

                    $rightPanel->addItem($a);
                }
            }
        }

        return $rightPanel;

    }

    /**
     * @return PhpExt_Panel
     * @throws Exception
     */
	function _getEditPanel () {

		$defaultPanel = new PhpExt_Panel();
		$defaultPanel->setTitle(__define("TEXT_DEFAULT"))
		             ->setAutoHeight(true);
		$fl = new PhpExt_Layout_FormLayout();
		$defaultPanel->setBodyStyle("padding: 5px;")->setLayout(new PhpExt_Layout_FormLayout());
		if (is_array($this->nonlang_data))
		foreach ($this->nonlang_data as $header_data) {
			$defaultPanel->addItem($this->_switchFormField($header_data));
		}

		return $defaultPanel;
	}

	function getAddOn($position) {
		$functions = isset($this->params['GridEdit']['functions']) ?: false;

        $jsStack = array();
		if (is_array($functions)) {
			$fh = new FunctionHandler();
			$fh->setNoMasterPanel(true);

			foreach ($functions as $val) {

				$group = $val['grouping'];
				if ($group=='0') $group='MAIN';
				if ($group==$position) {
					$fh->setStack($val['value'],$val['type'],$val['grouping']);
				}
			}

			$fh->_getHandler ();
			$jsStack = $fh->_getJsStack ();
		}
		return $jsStack;
	}

    /**
     * @param $position
     * @param $paneltext
     * @param $groupingPosition
     * @param bool $add_extra_save_btn
     * @return bool|PhpExt_Panel|PhpExt_TabPanel|null
     * @throws Exception
     */
	function _getPanelPosition ($position, $paneltext, $groupingPosition, $add_extra_save_btn = false) {

	    if(empty($paneltext)) $paneltext = "";
		$grouping = $this->_getData('editGroupingNonLang');
        $header_data = $this->_getData('headerNonLangData');
        $addon = $this->getAddOn($position);


        $countPositions = count($groupingPosition) + count($addon);
	        $tabPanel = new PhpExt_TabPanel();
        if ($countPositions > 1 || $position == 'LANGUAGE' || $position == 'STORELANGUAGE')
        {
            $tabPanel->setActiveTab(0)
             		 ->setDeferredRender(false)
             		 ->setEnableTabScroll(true)
            		 ->setDefaults(new PhpExt_Config_ConfigObject(array("autoHeight"=>true,"bodyStyle"=>"margin:20px 10px","hideMode"=>'display')));
        }

        if ($position == 'STORELANGUAGE')
        {
			$tabPanel = $this->_getTabStoreLangPanel($tabPanel);
		}
		elseif ($position != 'LANGUAGE') {
	        foreach ($groupingPosition as $tabPanelIndex)
	        {
                global $store_handler;
                if($store_handler->store_count == 1 && $tabPanelIndex == 'shop') continue;

	            if ( $tabPanelIndex != 'languages') {
                $Panel = new PhpExt_Panel();

				$Panel->setTitle(__define("TEXT_".$tabPanelIndex));

			    $Panel->setAutoHeight(true);
			    $Panel->setBodyStyle("padding: 5px;")->setLayout(new PhpExt_Layout_FormLayout());

                if (is_array($grouping[$tabPanelIndex]))
                foreach ($grouping[$tabPanelIndex] as $headerKey)
                {
                    if($header_data[$headerKey]['type'] == 'status')
                    {
                        $hidden = $header_data[$headerKey];
                        $hidden['type'] = 'hidden';
                        $hidden['value'] = 0;
                        $Panel->addItem($this->_switchFormField($hidden));
                    }

				    $Panel->addItem($this->_switchFormField($header_data[$headerKey]));
                }

                if ($countPositions > 1)
                $tabPanel->addItem($Panel);
                else
                $tabPanel = $Panel;
	            }
	        }

            if (is_array($addon) && is_array($addon[0] ?? 'not-array'))
			foreach ($addon[0] as $nr => $addon_data) {
				$tabPanel->addItem($addon_data);
			}

        } else {
            // langpanel
            $tabPanel = $this->_getTabLangPanel($tabPanel);
        }

        if($add_extra_save_btn && $position == 'MAIN' && $this->checkPermission('edit')) {
            $tabPanel->setButtonAlign("center");
            $tabPanel->addButton($this->_SubmitButton(__define("BUTTON_SAVE"), __define("TEXT_SAVING"), '-grideditform', 'close'));
            $tabPanel->addButton($this->_SubmitButton(__define("BUTTON_UPDATE"), __define("TEXT_SAVING"), '-grideditform', 'update'));
        }

        return $tabPanel;
	}

    /**
     * @param PhpExt_Form_FormPanel $form
     * @return PhpExt_Form_FormPanel
     * @throws Exception
     */
	function _getPanelGroups(PhpExt_Form_FormPanel $form) {

	    $grouping = $this->_getData('editGroupingNonLang');
	    $panelSettings = $this->getSetting('panelSettings');
	    
	    if(is_array($panelSettings) && count($panelSettings)!=0){
            $add_extra_save_btn = false;
            if(in_array('STORELANGUAGE', array_column($panelSettings, 'position'))) {
                $add_extra_save_btn = true;
            }
		    foreach ($panelSettings as $mainPanel) {
		    	$form->addItem($this->_getPanelPosition($mainPanel['position'], $mainPanel['text'], $mainPanel['groupingPosition'], $add_extra_save_btn));
		    }
	    }
        return $form;
	}

    /**
     * @param $id
     * @return string
     * @throws Exception
     */
	function displayEditFormJS ($id) {
		global $language, $xtPlugin;

		$this->getLayout();
        $settings = $this->getSetting('rowActionsFunctions');
        $menu_settings = $this->getSetting('menuActionsFunctions');
        
		$_sr = $this->_storeEdit($id);
		$store = $_sr['store'];
		$reader = $_sr['reader'];
		// Form Panel
		$form = new PhpExt_Form_FormPanel($this->code.$this->getSelectionItem()."-grideditform") ;
		$form = $this->_getLabelPos($form);

		$form->setLabelWidth($this->getSetting('FormLabelWidth'));
		$form->setReader($reader);

		$form->setBodyStyle("background-color:#efefef; color:#000; padding: 5px;");

		$top = new PhpExt_Toolbar_Toolbar();
		$top = $this->getActionMenu($top);
		$top = $this->getUserMenu($top, 'Toolbar', 'edit');
		
		if (($this->getActivate('image') || $this->getActivate('file') || is_array($settings))) {
    		//$top = new PhpExt_Toolbar_Toolbar();
            $top = $this->getDynamicMenu($top);
            $top = $this->getFileMenu($top);
            
		}

		if($this->code == 'payment' && !empty($this->tmpData["data"]["plugin_installed"]))
        {
            global $db;
            $help_link = $db->GetOne("SELECT documentation_link FROM " . TABLE_PLUGIN_PRODUCTS . " where plugin_id = ?", [$this->tmpData["data"]["plugin_installed"]]);
            if (!empty($help_link))
            {
                $js_help_link = PhpExt_Javascript::stm('
                            window.open("' . $help_link . '", "xt-help-wnd");
                        ');
                $top->addButton(1000, '<i class="fa fa-question-circle" aria-hidden="true"></i>', null, new PhpExt_Handler($js_help_link));
            }
        }
		
		$form->addItem($top);
		
		if ($this->getActivate('image')) {
			$form->addItem($this->getAllImagesPreviewPanel());
		}		
		$form = $this->_getPanelGroups($form);
		
		// false &&   to disable template iframe 
		if(false && (array_key_exists('_STORE_DEFAULT_TEMPLATE',$reader->_fields->Collection) == true || (array_key_exists('_SYSTEM_TEMPLATE',$reader->_fields->Collection) == true))){
		
			// news iframe for Templates on Marketplace
			$lang_code = $language->environment_language;
			$news_page = new PhpExt_Panel();
			$news_page->setTitle(__define("TEXT_XT_TEMPLATES"))
			        ->setAutoScroll(false)
			        ->setAutoWidth(true)
					->setAutoHeight(true)
					->setCssStyle('border:none;');
            $news_page->setLayout(new PhpExt_Layout_FitLayout());
			
			$news_page->setHtml('<iframe height="500" width="100%" style="border-width:0" src="https://addons.xt-commerce.com/index.php?page=admin_templates&language='.$lang_code.'"></iframe>');
			$form->addItem($news_page);
		}
		
		// buttons
		$button_panel = new PhpExt_Panel() ;
		$button_panel->setButtonAlign("center");

		if (is_array($settings)) {
		    reset($settings);
		        foreach($settings as $action => $fkt_content) {
		              $Btn = PhpExt_Button::createTextButton(__define('TEXT_'.$action),
		                  new PhpExt_Handler(PhpExt_Javascript::stm($fkt_content)));
		                  $Btn->setIconCssClass($action." x-btn-text ");
		                  $Btn->setId('rowaction_btn_'.$action);
                            // if ($action enthält .*fncDisabled$) $Btn->setDisabled(true); // zb bei neuanlage einer kouponvorlage dürfen momentan die btn artikel/kat/Kunde nicht aktiv sein
                     $button_panel->addButton($Btn);
            }
		}

		$form->addItem($button_panel);

		if($this->checkPermission('edit')) {
			$form->addButton($this->_SubmitButton(__define("BUTTON_SAVE"), __define("TEXT_SAVING"), '-grideditform', 'close'));
			$form->addButton($this->_SubmitButton(__define("BUTTON_UPDATE"), __define("TEXT_SAVING"), '-grideditform', 'update'));
		}
		if($this->checkPermission('cancel')) {
			$form->addButton($this->_CloseButton(__define("BUTTON_CANCEL")));
		}

		if($this->checkPermission('reset'))
		$form->addButton($this->_ResetButton(__define("BUTTON_RESET"), $id));

		$form->setRenderTo(PhpExt_Javascript::variable("Ext.get('".$this->code.$this->getSelectionItem()."-grideditform')"));

        $form->attachListener('afterlayout', new PhpExt_Listener(PhpExt_Javascript::functionDef(null, "
                $(function(){ initPasswordShow(); });
            "))
        );

        ($plugin_code = $xtPlugin->PluginCode(basename(__FILE__).':'.__FUNCTION__.'_form_prepared')) ? eval($plugin_code) : false;

		// debug messages
		$this->getDebugMessages ();

		$js = 'var '.$this->getSelectionItem().' = "'.$id.'";' . PhpExt_Ext::onReady(
		$store->getJavascript(false, "store"),

		PhpExt_Javascript::stm(PhpExt_QuickTips::init()),
		PhpExt_Javascript::assign($this->code."bd","Ext.get('".$this->code.$this->getSelectionItem()."-grideditform')"),
		$form->getJavascript(false, $this->code."gridEditForm"),
		$this->add_multimenuactions(),
                (constant("_SYSTEM_USE_WYSIWYG")=='TinyMce') ? 'tinyMCE.init(tinyMCESettings); ' : ''
		);
        // UBo++   
        $add_js = $this->getSetting('rowActionsJavascript');
        $js .= $add_js."";
        //UBo--

        ($plugin_code = $xtPlugin->PluginCode(basename(__FILE__).':'.__FUNCTION__.'_bottom')) ? eval($plugin_code) : false;

		return '<script type="text/javascript" charset="utf-8">' . $js . '</script><div id="'.$this->code.$this->getSelectionItem().'-grideditform"></div>';

	}

    function getLayout () {
        $this->header = $this->getAutoHeader();
    }

    function getAutoHeader() {
        $first_data = $this->_getData('data', '0');
        $settings = $this->getSetting('panelSettings');

        $data = [];
        $groupingLang = [];
        $groupingNonLang = [];
        $groupingLangData = [];
        $groupingNonLangData = [];

        if (is_array($first_data))
        {
            foreach ($first_data as $header_key => $val)
            {
                $tmp_data = array();
                if (!(in_array($header_key, $this->getSetting('exclude')))
                    && ((is_array($this->getSetting('include')) && in_array($header_key, $this->getSetting('include'))) || !is_array($this->getSetting('include')) )) {

                    $master_key = false;
                    if ($header_key == $this->getMasterKey())
                        $master_key = true;
                    $type = $this->getEditFieldType($header_key);
                    $storeLang = false;
                    if ($this->params['languageStoreTab'])
                        $storeLang = $this->getEditFieldLang($header_key, $val);

                    $lang = false;
                    if ($this->params['languageTab'])
                        $lang = $this->getEditFieldLang($header_key, $val);

                    //// hidden fields in grid
                    $hidden = false;
                    $griddisabled = false;
                    if ($lang != false && $lang != $this->getSetting('display_language')
                        || preg_match('/meta/',$header_key)
                        || preg_match('/permission/',$header_key)
                        || preg_match('/shop/',$header_key)
                        || preg_match('/flag/',$header_key)
                        || preg_match('/template/',$header_key)
                    ) {// || $master_key || $header_key == 'id'
                        $hidden = true;
                        $griddisabled = true;
                    }
                    //// end hidden

                    // readonly
                    $readonly = false;
                    if (preg_match('/date_added/',$header_key) || preg_match('/last_modified/',$header_key)) {
                        $readonly = true;
                    }

                    $renderer = null;
                    if (preg_match('/tax_class_id/',$header_key)) {
                        $renderer = 'taxRenderer';
                    }

                    if (preg_match('/tax_zone_id/',$header_key)) {
                        $renderer = 'zoneRenderer';
                    }

                    if ($header_key == 'manufacturers_id') {
                        $renderer = 'manufacturersRenderer';
                    }

                    if (preg_match('/language_code_plus_all/',$header_key)) {
                        $renderer = 'languageIsoRendererPlusAll';
                    }
                    else if (preg_match('/language_code/',$header_key)) {
                        $renderer = 'languageIsoRenderer';
                    }



                    $url = null;
                    $required = false;
                    if (preg_match('/template/',$header_key)) {
                        $required = false;
                        $url = 'DropdownData.php?get='.$header_key;
                    }


                    /*
                    if (ereg("sort",$header_key) > 0 && ereg("sorting",$header_key) == 0) {
                        $required = false;
                        $url = 'DropdownData.php?get=sort_defaults';
                        $type = 'dropdown';
                    }
                    */
                    $position = 'MAIN';
                    // grouping
                    $groupingPosition = 'default';
                    $groupingSortOrder = 0;
                    if (preg_match('/permission/',$header_key)) {
                        $groupingPosition = 'permission';
                    }
                    if (preg_match('/shipping_permission/',$header_key)) {
                        $groupingPosition = 'shipping_permission';
                    }
                    if (preg_match('/payment_permission/',$header_key)) {
                        $groupingPosition = 'payment_permission';
                    }
                    if (preg_match('/shop/',$header_key) && !preg_match('/shop_id/',$header_key)) {
                        $groupingPosition = 'shop';
                    }
                    if (preg_match('/template/',$header_key)) {
                        $groupingPosition = 'template';
                    }
                    /*
                    if (ereg("price_flag_special",$header_key) > 0) {
                        $groupingPosition = 'price_flag_special';
                    }
                    if (ereg("price_flag_graduated",$header_key) > 0) {
                        $groupingPosition = 'price_flag_graduated';
                    }
                    */
                    if (preg_match('/products_vpe/',$header_key)) {
                        $groupingPosition = 'products_vpe';
                    }
                    if (preg_match('/startpage/',$header_key)) {
                        $groupingPosition = 'startpage';
                    }

                    $grouping = array('header_key' => $header_key,
                        'position' => $groupingPosition,
                        'sortorder' => $groupingSortOrder,
                        'text' => __define('TEXT_'.$groupingPosition));

                    $tmp_data = array('name' => $header_key,
                        'text' => __define('TEXT_'.$header_key),
                        'masterkey' => $master_key,
                        'type' => $type,
                        'lang' => $lang != '' ? true : false,
                        'lang_code' => $lang,
                        'value' => is_array($val) ? $val : $val,
                        'hidden' => $hidden,
                        'min' => null,
                        'max' => null,
                        'readonly' => $readonly,
                        'required' => $required,
                        'griddisabled' => $griddisabled,
                        'url' => $url,
                        'sort_order' => 0,
                        'height' => null,
                        'width' => null,
                        /*										'use_function' => '',
                                                                'set_function' => '', */
                        'renderer' => $renderer
                    );

                    if (isset($this->header[$header_key]) && $this->header[$header_key]) {
                        $tmp_data = array_merge($tmp_data, $this->header[$header_key]);
                    }

                    $this->setActivate($tmp_data['type']);
                    $this->setActivate($tmp_data['renderer']);

                    // grouping merge for full data array
                    if (isset($this->grouping[$header_key]) && $this->grouping[$header_key]) {
                        $grouping = array_merge($grouping, $this->grouping[$header_key]);
                        if ($grouping['position']) {
                            $groupingPosition = $grouping['position'];
                        }
                        if (!isset($this->grouping[$header_key]['text']) || $this->grouping[$header_key]['text'] == '') {
                            $grouping['text'] = __define('TEXT_'.$groupingPosition);
                        }
                    }

                    $data[] = $tmp_data;
                    if (($lang != 'exclude' )) {
                        if ($lang) {
                            $this->lang_data[$lang][] = $tmp_data;
                            $groupingLang[$groupingPosition][] = $header_key;
                            $groupingLangData[$groupingPosition] = $grouping;
                        } else {
                            $groupingNonLang[$groupingPosition][] = $header_key;
                            $groupingNonLangData[$groupingPosition] = $grouping;
                            $this->nonlang_data[$header_key] = $tmp_data;
                        }
                    }

                    if ($storeLang)
                        $this->setPanelSortPosition('languages', 'STORELANGUAGE');
                    elseif ($lang)
                        $this->setPanelSortPosition('languages', 'LANGUAGE');
                    else
                        $this->setPanelSortPosition($groupingPosition, $position);
                }
            }
        }

        $this->_setData('editGroupingLang', $groupingLang);
        $this->_setData('editGroupingNonLang', $groupingNonLang);
        $this->_setData('editGroupingLangData', $groupingLangData);
        $this->_setData('editGroupingNonLangData', $groupingNonLangData);

        $this->_setData('headerNonLangData', $this->nonlang_data);
        $this->_setData('headerLangData', $this->lang_data);

        $this->_setData('header', $data);

        return $data;
    }


    function _getData ($key, $pos = '') {
        if ($key == 'obj_data' && empty($pos) && $this->getSetting('display_adminActionStatus'))  {
            $data = $this->tmpData[$key];
            $_data = [];
            if (count($data->data) > 0)
            {
                foreach ($data->data as $key => $val) {
                    $_data[$key] = array_merge(array('adminActionStatus' => $this->adminActionStatus($val[$this->getSetting('master_key')])),$val);
                }
            }
            $data->data = $_data;
            return $data;
        }

        if (empty($pos))
            return $this->tmpData[$key] ?? [];

        return $this->tmpData[$key][$pos];
    }



	function _storeSingle($id) {
		$grid_data = array();
		if ($this->grid_data)
		foreach ($this->grid_data as $key => $data) {
			if ($id == $data[$this->getMasterKey()])
			$grid_data[] = $data;
		}
		//$this->singledata = array(array(1,'b','c'));
		$reader = $this->_reader('Array');
		// Store
		$store = new PhpExt_Data_Store();
		$store->setReader($reader);
		//              ->setData(PhpExt_Javascript::variable("singledata"));
		//		$store->setReader($reader);
		if (count($grid_data) > 0 )
		$store->setData($grid_data);
		return $store;
	}

	// Store
	function _storeEdit($id) {
		$store = new PhpExt_Data_Store();

		$reader = $this->_reader();

		$store->setProxy(new PhpExt_Data_HttpProxy($this->getSetting('readfull_url').'get_data=true'))
		->setReader($reader)
		->setBaseParams(array("limit"=> $this->getSetting('PageSize')));

		return array('store' => $store, 'reader' => $reader);
	}
	function _switchFormField ($line_data, $lang_data = array())
    {
	    global $xtPlugin;

		$label = $line_data['name'];
		//		$name = __define($line_data['text']);
		$name = $line_data['text'];
		$value = true;

		$panel = false;

        ($plugin_code = $xtPlugin->PluginCode(basename(__FILE__).':'.__FUNCTION__.'_top')) ? eval($plugin_code) : false;
        if(isset($plugin_return_value))
            return $plugin_return_value;

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
				$data->setCssClass('admin-textarea');

				break;
			case "admininfo":
				$data = PhpExt_Form_TextArea::createTextArea($label, $name);
				$line_data['value'] = str_ireplace(['<br>', '<br/>', '<br />', '\\\n'], "\n", $line_data['value']);
                $line_data['value'] = implode("\n", array_map('trim', explode("\n", $line_data['value'])));

				$data->setReadOnly(true);
				if (isset($line_data['height'])) {
					$data->setHeight($line_data['height']);
				} else {
				}
				if (isset($line_data['width'])) {
					$data->setWidth($line_data['width']);
				} else {
					$data->setWidth('100%');
				}
				$data->setCssClass('form_info');

                $data->attachListener('render', new PhpExt_Listener(PhpExt_Javascript::functionDef(null, "
                    { 
                        let elem = document.getElementById(self.id);
                        
                        setTimeout(function(){
                        
                            let style = window.getComputedStyle(elem);
                            //console.log(style);
                            let paddingTop = parseInt(style.paddingTop);   
                            let paddingBottom = parseInt(style.paddingBottom);   
                            let height = parseInt(style.height);   
                            let scrollHeight = elem.scrollHeight;
                            
                            //console.log(height, scrollHeight, paddingTop, paddingBottom);
                            
                            if(height > elem.scrollHeight)
                            {
                               height = height + paddingTop + paddingBottom;
                            }
                            else
                            {
                                height = elem.scrollHeight;
                            }
                            
                            //console.log(height);
                            elem.style.height = height + 'px';
                        
                        }, 2000, elem);
                    };
                ", ['self']))
                );

                break;
			case "htmleditor":
                $editor = constant("_SYSTEM_USE_WYSIWYG");
				if($editor=='SimpleHtmlEditor'){
					
                                    $data = PhpExt_Form_HtmlEditor::createHtmlEditor($label, $name);
                                    $data->setWidth('100%');
                                    if (isset($line_data['required'])) {
                                        $line_data['required'] = '';
                                    }
                                    break;

				}elseif($editor=='TinyMce'){
					
 					$data = PhpExt_Form_TextArea::createTextArea($label, $name);
    				$data->setCssClass('TinyMce');
					break;

                }elseif($editor=='froala'){  
                
                    $data = PhpExt_Form_FroalaEditor::createFroalaEditor($label, $name);  

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
			case "logo":
				$uniqueID = $name.'_'.time();
				$data = PhpExt_Form_TextField::createTextField($label, $name, $uniqueID);

				$panel = $this->_logoUploadPanel($data, $uniqueID, $line_data);

				if (!$line_data['value'])
				$value = false;
				break;
			case "date":
				$data = PhpExt_Form_DateField::createDateField($label, $name);
				$data->setFormat('Y-m-d H:i:s'); 

				break;
			case "dropdown":
				if (empty($line_data['url'])) {
					// default dropdownstatus
					$url = 'DropdownData.php?get=status_truefalse';
				} else {
					$url = $line_data['url'];
				}
				$data = $this->_comboBox($label, $name, $url,$line_data['width'],$line_data['listner']);
				if(constant('_SYSTEM_AUTOLOAD') == 'true') // vs $line_data['store_autoLoad'] == true
				    $data->getStore()->setAutoLoad(true);
				break;
            case "dropdown3":
                if (empty($line_data['url'])) {
                    // default dropdownstatus
                    $url = 'DropdownData.php?get=status_truefalse';
                } else {
                    $url = $line_data['url'];
                }
                $data = $this->_multiComboBox3($name, $label, $url,$line_data['width'],$line_data['listner']);
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
				$data = $this->_multiComboBox($name, $label, $url);
				break;
			case "itemselect":
				if (empty($line_data['url'])) {
					// default dropdownstatus
					$url = 'DropdownData.php?get=status_truefalse';
				} else {
					$url = $line_data['url'];
				}
				if (!isset($line_data['valueUrl']) || empty($line_data['valueUrl'])) {
					$valueUrl = '';
				} else {
					$valueUrl = $line_data['valueUrl'];
				}

				$width = $line_data['width'] ? $line_data['width'] : 250;
                $height = $line_data['height'] ? $line_data['height'] : 250;

				$params = ['height' => $height, 'width' => $width];
				$data = $this->_itemSelect($name, $label, $url, $line_data['value'], $valueUrl, $params);

                $data->setWidth($width * 2 + 20);

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
				$line_data_value = (isset($line_data['value']) && strlen($line_data['value']) ? $line_data['value'] : "");
				$data = PhpExt_Form_Hidden::createHidden($label, $line_data_value);
				$line_data['required'] = false; // not required, user can not enter anything
				break;

			case "password":
				$data = PhpExt_Form_PasswordField::createPasswordField($label,$name);
                $data->setWidth('300');
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
		if(isset($line_data['cssClass'])) $data->setCssClass($line_data['cssClass']);
		////////////////////////////////////////////////////////////////////////
		// field validation
		// readonly
		if (array_value($line_data,'readonly') && !$this->getSetting('edit_masterkey')) {
			$data->setReadOnly(true);
		}
        if (array_value($line_data,'disabled') && !$this->getSetting('edit_masterkey')) {
            $data->setDisabled(true);
        }
		// minimum
		if (array_value($line_data,'min')) {
			$data->setMinLength($line_data['min']);
			$data->setMinLengthText(__define("ERROR_MIN"));
		}
		// maximum
		if (array_value($line_data,'max')) {
			$data->setMaxLength($line_data['max']);
			$data->setMaxLengthText(__define("ERROR_MAX"));
		}
		// required
		if (array_value($line_data,'required')) {
			$data->setAllowBlank(false);
			$data->setBlankText(__define("ERROR_BLANK"));
		}
        // regex
        if (isset($line_data['regex']) && $line_data['regex']) {
            $data->setRegEx(new PhpExt_JavascriptStm('/'.$line_data['regex'].'/'));
            $data->setRegExText(__define("ERROR_REGEX"));
        }
		// field validation end
		////////////////////////////////////////////////////////////////////////

		// set field value
		if ($value && $line_data['value']!=='' && $line_data['value']!=='0000-00-00 00:00:00') {
			$data->setValue($line_data['value']);
		}
		if ($panel)
		$data = $panel;

        ($plugin_code = $xtPlugin->PluginCode(basename(__FILE__).':'.__FUNCTION__.'_bottom')) ? eval($plugin_code) : false;

		return $data;
	}

}
