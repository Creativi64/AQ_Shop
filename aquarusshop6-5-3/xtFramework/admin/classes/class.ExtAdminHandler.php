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

class ExtAdminHandler extends ExtGrid {

    var $task, $new;
    /**
    * @var xt_backend_cls
    */
    var $class;
    protected $tmpDataRow;
    /**
     * @var array
     */
    protected $registerParams;
    /**
     * @var bool|string
     */
    protected $sql_limit;

    /**
     * ExtAdminHandler constructor.
     * @param $code
     * @param $link_data
     * @throws Exception
     */
    function __construct($code, $link_data) {

        parent::__construct();

		$this->getClassFile($code, $link_data);

		$this->class = new $code;
		$this->setUrlData($link_data);
		// set admin pos
		$this->class->setPosition(USER_POSITION);

		
		// unique selection id
		$this->setSelectionItem ();
		$this->code = $code;
		$this->class->_AdminHandler = $this;

		$this->params = $this->class->_getParams();
		$this->setCode($code);

		$this->setSetting('gridTitle', __define('TITLE_'.$code));
		$this->setSetting('headTitle', __define('HEADING_'.$code));


		$this->new = false;
        $this->task = new adminTask();
		$this->_setTask();
	}

	//////////////////////////////////////////////////////////////////////////
	/* task functions */
	function _setTask() {
		if(!isset($this->task)) $this->task = new adminTask();
		$this->task->setClass($this->code);
	}

	function _setTaskData($action, $id = 0) {
		$this->task->setAction($action);
		$this->task->setActiveID($id);
		$this->task->_set();
	}

	function checkTask($id){
		return $this->task->checkTask($id);
	}

	function adminActionStatus ($id) {
		return $this->task->isDisabled($id);
	}
	/* task  functions end */
	//////////////////////////////////////////////////////////////////////////

	function getEditID() {
		return $this->edit_id;
	}

	function setEdit($id) {
		$this->edit_id = $id;
	}

	function isEdit(){
		if($this->edit_id)
		return true;
		else
		return false;
	}

	//////////////////////////////////////////////////////////////////////////
	/* class functions */

	function setClassNew() {
		$this->new = true;
	}

	function prepareDataRow(){
	    $rdata = [];
		foreach ($this->tmpDataRow as $ln => $datarow) {
			$sdata = preg_split('/(##_key_##)/',$datarow);
			foreach ($sdata as $rsdata) {
				$lndata = preg_split('/(##_val_##)/',$rsdata);
				if ($lndata[0])
				$rdata[$ln][$lndata[0]] = $lndata[1];

			}
		}
		return $rdata;
	}

	function prepareDataAsArray ($string) {
		$data = preg_split('/##_next_##/',$string);

		foreach ($data as $ln => $datarow) {
			if (!in_array($datarow, $this->tmpDataRow)) {

				$this->tmpDataRow[] = $datarow;
			}
		}
	}

	function saveClassAllData ($data) {
		$this->tmpDataRow = array();
		$new_data = array();
		if ($data['edit_data'])
		$this->prepareDataAsArray($data['edit_data']);
		if ($data['new_data'])
		$this->prepareDataAsArray($data['new_data']);


		if (count($this->tmpDataRow) > 0)
		$new_data = $this->prepareDataRow();

		foreach ($new_data as $save_data) {
			$obj = $this->class->_set($save_data);
			// task
			$save_data[$this->getMasterKey()];
			$this->_setTaskData('save', $obj->ID);
		}


	}

	function saveClassData ($data) {
		if (!$data[$this->getMasterKey()])
		$data[$this->getMasterKey()] = $this->edit_id;


		$obj = $this->class->_set($data);

		// task
		$this->_setTaskData('save', $data[$this->getMasterKey()]);
		return $obj;
	}

	function multiClassData($ids, $task, $user_func, $user_param='empty')
    {
        $obj = new stdClass();
		try
		{
			if ($user_param == 'true')
			{
			$user_param = '1';
			}
			elseif ($user_param == 'false')
			{
                $user_param = '0';
            }

            $id = false;
			if (!$ids && $this->url_data['edit_id'])
			{

                $m_id = $this->url_data['edit_id'];

                    if (strstr($m_id, '_'))
                    {
                    $ids = explode('_', $m_id);
                    $m_id = $ids[1];
                }

                    if ($user_param == 'empty')
                    {
                    call_user_func(array($this->class, $user_func), $m_id);
                    }
                    else
                    {
                    call_user_func_array(array($this->class, $user_func), array($m_id, $user_param));
                }
                // task
                $this->_setTaskData($task, $m_id);
			}
			elseif (!$ids && !$this->url_data['edit_id'])
			{
			
			    $m_id = 'all';
			
				if ($user_param == 'empty')
				{
				call_user_func(array($this->class, $user_func), $m_id);
				}
				else
				{
                    call_user_func_array(array($this->class, $user_func), array($m_id, $user_param));
                }
                // task
                $this->_setTaskData($task, $m_id);
			
			}
			else
			{
                $obj = new stdClass();
                $id = preg_split('/,/', $ids);
                for ($i = 0; $i < count($id); $i++)
                {
                    if ($id[$i])
                    {

                        $m_id = $id[$i];
                            if ($user_param == 'empty')
                            {
                            call_user_func(array($this->class, $user_func), $m_id);
                            }
                            else
                            {
                            call_user_func_array(array($this->class, $user_func), array($m_id, $user_param));
                        }
                        // task
                        $this->_setTaskData($task, $m_id);
                    }
                }
            }

            $obj->success = true;
            $obj->messageText = is_array($id) ? count($id) : 0;
		}
		catch(Exception $e)
		{
			$obj->success = false;
			$obj->messageText = $e->getMessage();
		}
		return $obj;
	}

	function setRegisterParams ($paramKey) {
		$this->registerParams[] = $paramKey;
	}
	function getRegisterParams () {
		return $this->registerParams;
	}
	function isRegisterParams ($paramKey) {
		if ($this->registerParams[$paramKey])
		return true;
		return false;
	}

	function getClassData() {
		$params = $this->params;

		$this->setHeader($params['header']);
		$this->setGrouping($params['grouping']);
		$this->setPanelPos($params['panelPos']);
		$this->setMasterKey($params['master_key']);

		if ($this->edit_id) {
			$this->_setData('obj_data', $this->class->_get($this->edit_id));
			// task
			$this->_setTaskData('edit', $this->edit_id);
		} elseif ($this->new) {

			$id = $this->checkTask('new');

			$data = $this->class->_get($id);
			$id = $data->data[0][$this->getMasterKey()];
			$this->setEdit($id);

			//		    $this->setData($data);
			$this->_setData('obj_data',$data);
			// task
			$this->_setTaskData('new', $id);

		} elseif ($this->url_data['pg'] == 'overview' || $this->url_data['pg'] == '') {
			$data = $this->class->_get();

			if ($data->totalCount > $params['PageSize'] && count($data->data) > $params['PageSize']) {
				$data->data = array_splice($data->data, $this->url_data['start'], $params['PageSize']);
			}

			$data->data = $this->cleanUpData($data->data);

			$this->_setData('obj_data', $data);
			// task
			$this->_setTaskData('view');
		}
		return $this->_getData('obj_data');

	}

	function cleanUpData($data){

		if(is_array($this->params['include']) && count($this->params['include'])>0){
			$old_data = $data;
            $data = [];
			if(is_array($old_data)&&count($old_data)!=0){
				foreach ($old_data as $key => $val){
					$tmp_data = array();
					foreach ($this->params['include'] as $ikey => $ival){
						$data[$key][$ival] = $val[$ival];
					}
				}
			}
			return $data;

		}else{
			return $data;
		}
	}


	function getSingleData($id) {
		return $this->class->_get($id);
	}
	/* class functions end */
	//////////////////////////////////////////////////////////////////////////
    /**
     * @return false|string
     * @throws Exception
     */
	function display() {

	    global $xtPlugin;

		$this->getClassData();
		$this->setUrlData($this->url_data);

        ($plugin_code = $xtPlugin->PluginCode(basename(__FILE__).':'.__FUNCTION__.'_top')) ? eval($plugin_code) : false;
        if(isset($plugin_return_value))
            return $plugin_return_value;
				
		if (($this->edit_id || $this->new || $this->url_data['pg'] == 'edit')) {
			//if (($this->edit_id || $this->new) && (!$this->url_data['pg'] || $this->url_data['pg'] == 'edit')) {
			$this->setSetting('FormLabelPos', 'left');
			$return = $this->displayEditFormJS($this->edit_id);
		} elseif($this->url_data['pg'] != 'overview' && $this->url_data['pg'] != '') {
			$fc = $this->url_data['pg'];
			$return = $this->class->$fc($this->url_data); // display function from class
            // TODO aufpassen / prüfen
            // geändert beim cleanup für xt6.2
            // zb beim hermes_ExtAdminHandler addTracking ist der header leer
            // daher vorerst 'behoben' mit test auch instanceof
            // 2019-12-05 auch ProductsToCategories kommt mit leerem header. brauchen wir diesen check/fehler wirklich?
            if($this->class instanceof xt_backend_cls && (!is_array($this->header) /*|| count($this->header)==0*/))
            {
                $this->task->_killTask();
                echo    'Leider konnte die Seite nicht geladen werden. Bitte dr&uuml;cken Sie F5 oder den Aktualisieren Button in Ihrem Browser und versuchen Sie es erneut. <br />
			            Sollte das Problem nach dem Neuladen erneut auftreten wenden Sie sich bitte an unseren Support unter (helpdesk@xt-commerce.com)<br />';

                echo '
<pre style="text-align:left;font-size: 0.9em;margin:10px">
code => '.$this->code.'
fc => '.$fc.'
header => '. (is_array($this->header) ? count($this->header) : 'false'). '
</pre>';
                die;
            }
		} else {  // set default to grid view
			$this->setSetting('FormLabelPos', 'top');
			$return = $this->displayGridJS().'<script type="text/javascript" src="'. _SYSTEM_BASE_URL .str_replace('/xtAdmin/','',_SRV_WEB). '/xtFramework/admin/filter/collapse_fix.js"></script>';
		}
		if(!is_string($return) && $return != null)
        {
            $return = json_encode($return);
        }

        ($plugin_code = $xtPlugin->PluginCode(basename(__FILE__).':'.__FUNCTION__.'_bottom')) ? eval($plugin_code) : false;
        if(isset($plugin_return_value))
            return $plugin_return_value;

		return $return;
	}


	//////////////////////////////////////////////////////////////////////////
	/* addon functions */

	function getImages () {
		$md = new MediaImages();
		$md->position = 'admin';
		$images = $md->_get();
		return array('images' => $images->data);
	}
	function getFiles () {
		$mf = new MediaFiles();
		$mf->position = 'admin';
		$files = $mf->_get();
		return array('images' => $files->data);
	}
	
	function Upload() {
				
		$obj = new stdClass;
		$filename = $_FILES["Filedata"]["name"];

		$md = new MediaData();
		$md->setClass($this->code);

		$obj = $md->Upload($filename);

		$obj->data = $_FILES;
		return $obj;
	}
	/* addon functions */
	//////////////////////////////////////////////////////////////////////////



	//////////////////////////////////////////////////////////////////////////
	/* settings for grid Langtab etc. */
    /**
     * @return string
     * @throws Exception
     */
	function getSettings() {

		$fsf = new PhpExt_Form_FormPanel();
		$fsf->setLabelWidth(75)
		->setUrl("save-form.php")
		->setFrame(true)
		->setTitle("Simple Form with FieldSets")
		->setBodyStyle("padding:5px 5px 0")
		->setWidth(350);

		//- Fielset 1
		$fieldset1 = new PhpExt_Form_FieldSet();
		$fieldset1
            ->setCheckboxToggle(true)
            ->setTitle("User Information")
            ->setAutoHeight(true);
		$fieldset1
            ->setCollapsed(true)
            ->setDefaults(new PhpExt_Config_ConfigObject(array("width"=>210)));
		// Use the helper function to easily create fields to add them inline
		$fieldset1->addItem(PhpExt_Form_TextField::createTextField("first","First Name")
		->setAllowBlank(false))
		->addItem(PhpExt_Form_TextField::createTextField("last","Last Name"))
		->addItem(PhpExt_Form_TextField::createTextField("company","Company"))
		->addItem(PhpExt_Form_TextField::createTextField("email","Email")
		->setVType(PhpExt_Form_FormPanel::VTYPE_EMAIL));
		$fsf->addItem($fieldset1);

		//- Fieldset 2
		$fieldset2 = new PhpExt_Form_FieldSet();
		$fieldset2->setTitle("Phone Number")
		->setCollapsible(true)
		->addItem(PhpExt_Form_TextField::createTextField("home","Home")
		->setValue("(888) 555-1212"))
		->addItem(PhpExt_Form_TextField::createTextField("business","Business"))
		->addItem(PhpExt_Form_TextField::createTextField("mobile","Mobile"))
		->addItem(PhpExt_Form_TextField::createTextField("fax","Fax"));
		$fieldset2
            ->setDefaults(new PhpExt_Config_ConfigObject(array("width"=>210)))
            ->setAutoHeight(true);
		$fsf->addItem($fieldset2);

		//- Buttons
		$fsf->addButton(PhpExt_Button::createTextButton("Save"));
		$fsf->addButton(PhpExt_Button::createTextButton("Cancel"));
		$fsf->setRenderTo(PhpExt_Javascript::variable("Ext.get('".$this->code."-gridsettingsform')"));


		$js = PhpExt_Ext::onReady(
		PhpExt_QuickTips::init(),
		$fsf->getJavascript(false, "fsf")
		);

		return '<script type="text/javascript" charset="utf-8">'. $js .'</script><div id="'.$this->code.'-gridsettingsform"></div>';

	}
	/* settings for grid Langtab etc. end*/
	//////////////////////////////////////////////////////////////////////////

	function setUrlData($data=[]){

	    $limit = false;
		if (isset($data['limit'])) {
		  if (!$data['start']) $data['start'] = 0;
		  $limit = $data['start'].', '.$data['limit'];
		}

		if ($limit){
			$this->sql_limit = $limit;
			$this->class->sql_limit = $this->sql_limit;
		}

		if (isset($data['query']))
		$this->class->sql_search = $data['query'];
		
		if($this->new == true){
			$data['link_id'] = $this->edit_id;
			$data['edit_id'] = $this->edit_id;
		}
		
		if(isset($data['link_id']) && !is_int($data['link_id'])){
			$tmp_link_id_array = explode('_', $data['link_id']);
			$tmp_link_id_array = array_reverse($tmp_link_id_array);
			$data['link_id'] = $tmp_link_id_array[0];
		}
		
		$this->url_data = $data;
		unset($data['start']);
		unset($data['limit']);

		$this->class->url_data = $this->url_data;

	}

	/**
	 * load class file
	 *
	 * @param string $code
	 * @param array $link_data
	 */
	function getClassFile($code, $link_data=''){
	    global $is_pro_version;

		$fileExist = false;
		
		// check file in plugin
		if (isset($link_data['plugin'])) {
			$file = _SRV_WEBROOT.'plugins/'.$link_data['plugin'].'/classes/class.'.$code.'.php';
			if (file_exists($file)) {
				$fileExist = true;
				require_once $file;
			}			
		} 
		if (!$fileExist && isset($link_data['currentType'])) {
			$pfile = _SRV_WEBROOT.'plugins/'.$link_data['currentType'].'/classes/class.'.$link_data['currentType'].'.php';
			if (file_exists($pfile)) {
				$fileExist = true;
				require_once $pfile;
			} 				
		} 

		// check file in framework
		if (!$fileExist) {
		    if($is_pro_version)
            {
                $file = _SRV_WEBROOT.'xtPro/'._SRV_WEB_FRAMEWORK.'classes/class.'.$code.'.php';
                if (!file_exists($file))
                {
                    $file = _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'classes/class.'.$code.'.php';
                }
            }
            else {
                $file = _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'classes/class.'.$code.'.php';
            }
			if (file_exists($file)) {
				$fileExist = true;
				require_once $file;
			}			
		}

		
		if (!$fileExist) {
			echo ' ERROR: class not found: '.$code;
		}
	}

    function UploadImage($id='')
    {
        if ($id)
            $u_js = "var edit_id = ".$id.";";
        else $u_js = "var edit_id = record.data.products_id;";

        $mg = new MediaGallery();
        $code = $mg->_getParentClass(2);

        $mediaWindow = $this->getMediaWindow3(true, true, true, 'images', "&mgID=2&link_id='+edit_id+'",$code);
        $u_js .= $mediaWindow->getJavascript(false, "new_window") . "new_window.show();";
        return $u_js;
    }

    function getMediaWindow3($show_grid = true, $show_ck_upload = true, $show_simple_upload = true, $type = 'images', $params = '',$force_code='')
    {
        $code = $force_code;

        $tab = array();
        $add_to_url = (isset($_SESSION['admin_user']['admin_key']))? "&sec=".$_SESSION['admin_user']['admin_key']."": '';
        // tab 1 grid
        if ($show_grid)
        {
            $tab[] = array(
                'url' => 'adminHandler.php',
                'url_short' => true,
                'params' => "mgId=2&link_id='+edit_id+'&load_section=MediaImageList&pg=overview&currentType=".$code.$add_to_url,
                'title' => 'TEXT_IMAGES'
            );
        }

        // tab 2 search
        if ($show_grid)
        {
            $tab[] = array(
                'url' => 'adminHandler.php',
                'url_short' => true,
                'params' => 'load_section=MediaImageSearch&pg=overview&currentType='.$code.$add_to_url.$params,
                'title' => 'TEXT_SEARCH_IMAGES'
            );
        }


        // tab 4 simple upload
        if ($show_simple_upload)
        {
            $tab[] = array(
                'url' => 'upload.php',
                'url_short' => true,
                'params' => "mgId=2&link_id='+edit_id+'&load_section=foo&currentType=".$code.$add_to_url,
                'title' => 'TEXT_SIMPLE_UPLOAD'
            );
        }


        // tab 5 ckfinder upload
        if ($show_ck_upload)
        {
            $tab[] = array(
                'url' => 'uploadCKFinder.php',
                'url_short' => true,
                'params' => "mgId=2&link_id='+edit_id+'uploadtype=single&load_section=foo&currentType=".$code.$add_to_url,
                'title' => 'TEXT_CKFINDER_UPLOAD'
            );
        }

        return $this->_TabRemoteWindow('TEXT_MEDIA_MANAGER', $tab);
    }

}
