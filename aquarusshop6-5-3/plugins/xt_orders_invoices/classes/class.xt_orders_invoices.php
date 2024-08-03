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

use Smarty\Smarty;
                        
defined('_VALID_CALL') or die('Direct Access is not allowed.');


require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_orders_invoices/classes/class.xt_orders_invoices_templates.php';
require_once _SRV_WEBROOT . 'xtFramework/admin/classes/class.adminDB_DataSave.php';

use Dompdf\Dompdf;
use Dompdf\Options;

class xt_orders_invoices
{

    const PAGE_SIZE = 25;
    protected $_table = TABLE_ORDERS_INVOICES;
    protected $_table_products = TABLE_ORDERS_INVOICES_PRODUCTS;
    protected $_master_key = COL_INVOICE_ID;
    public    $sql_limit = null;
    protected $_table_lang = null;
    protected $_table_seo = null;
    protected $_print_template = null;

    public $url_data;
    protected $position;

    public function __construct()
    {
        
    }

    public function db_get_template($tpl_code) {
		global $db;

		$sql = "SELECT `customers_status` FROM " . TABLE_ORDERS . " WHERE `orders_id`=?";
		$custStatus = $db->GetOne($sql, array($this->url_data['oid']));

        list($shopId, $langCode) = explode('_', $tpl_code);
        $template = new xt_orders_invoices_templates($this->url_data['type'], $shopId, $custStatus);
		$tpl = $template->getTemplate($langCode);
		
        if ($tpl['template_use_be_lng']==1)
        {
            global $language;
            $langCode = $language->content_language;
        }
		
		$tpl_source = $this->_db_get_template_trans($tpl, $langCode);
		return $tpl_source;
        //return true;
    }
    
    public function db_get_template_by_template_id($tpl_code) {
        global $db;

        list($tplId, $langCode) = explode('_', $tpl_code);

        $oit = new xt_orders_invoices_templates();
        $oit->setPosition('admin');
        $tpl = $oit->getTemplateById($tplId, $langCode);

        if ($tpl['template_use_be_lng']==1)
        {
            global $language;
            $langCode = $language->content_language;
        }

        $tpl_source = $this->_db_get_template_trans($tpl, $langCode);
		return $tpl_source;
        //return true;
    }
    
    private function _db_get_template_trans($tpl, $langCode) {
    	global $db,$tpl_trans;
    	
    	$this->_print_template = $tpl;
    	$tpl_source = $tpl['body'];

    	// for some templates - eg in-house packaging list - it might be useful NOT to use the order but the backend language
    	$tpl_trans = $db->GetAssoc("SELECT language_key, language_value FROM ".TABLE_LANGUAGE_CONTENT." WHERE language_code=? /*and class='admin'*/", array($langCode));

    	$tpl_source = preg_replace_callback('/({txt)\s(key=)([A-Z_]+)(\})/',function($m) use ($tpl_trans)
    		{ 
    			if (isset($m[3])) { 
    				return $tpl_trans[$m[3]]; 
    			}
    		},$tpl_source);

    	
    	return $tpl_source;
    }

    public function db_get_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj)
    {
        $tpl_timestamp = time();
        return true;
    }

    public function db_get_secure($tpl_name, &$smarty_obj)
    {
        return true;
    }

    public function db_get_trusted($tpl_name, &$smarty_obj)
    {
        
    }

    /**
     *
     * check if there is an invoice id
     *
     * @param $orderId
     * @return bool
     *
     */
    public function isExistByOrderId($orderId)
    {
        global $db;

        $orderId = (int) $orderId;
        $count = $db->GetOne("SELECT MAX(invoice_id) FROM " . $this->_table . " WHERE orders_id = ?", array($orderId));
        return $count;
        }

    /**
     *
     * check if there is an canceled invoice id
     *
     * @param $orderId
     * @return bool
     *
     */
    public function isExistByOrderIdCanceled($orderId)
    {
        global $db;

        $orderId = (int) $orderId;
        $count = $db->GetOne("SELECT MAX(invoice_id) FROM " . $this->_table . " WHERE orders_id = ? AND invoice_status=0", array($orderId));
        return $count;
    }

    function _getParams($invoice_id = false)
    {
        global $language,$xtPlugin;
		
		($plugin_code = $xtPlugin->PluginCode('class.xt_orders_invoices.php:_getParams_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;
		
        $header[COL_INVOICE_NUMBER] = array('type' => 'text');
        $header['invoice_issued_date'] = array('type' => 'date');
        $header['invoice_due_date'] = array('type' => 'date');
        $header['invoice_paid'] = array('type' => 'status');
        $header['invoice_sent'] = array('type' => 'status');
        $header['invoice_sent_date'] = array('type' => 'date');
        $header['invoice_total'] = array('type' => 'price');

        $params['header'] = $header;
        $params['master_key'] = $this->_master_key;
        $params['default_sort'] = 'invoice_issued_date';
        $params['SortField'] = 'invoice_issued_date';
        $params['SortDir'] = "DESC";
		$params['display_searchPanel']  = true;
        $params['display_newBtn'] = false;
        $params['display_editBtn'] = false;
        $params['display_deleteBtn'] = false;

        $pageSize = self::PAGE_SIZE;// (int)_SYSTEM_ADMIN_PAGE_SIZE_PRODUCT;
        if($pageSize && is_int($pageSize)) $params['PageSize'] = $pageSize;

        if (isset($this->sql_limit))
        {
            $exp= explode(",",$this->sql_limit);
            $params['PageSize'] = trim($exp[1]);
        }

        $rowActions[] = array('iconCls' => 'xt_orders_invoices_edit', 'qtipIndex' => 'qtip1', 'tooltip' => XT_ORDERS_INVOICES_TEXT_EDIT);
        $extF = new ExtFunctions();
        $extF->setCode('editInvoice');

        if($invoice_id)
        {
            // include from order edit page
            $invoice_id_string = $invoice_id;
        }
        else {
            // deafault framework call eg ExtAdminHandler
            $invoice_id_string = "record.data.invoice_id";
        }

        $window = $extF->_RemoteWindow("TEXT_XT_ORDERS_INVOICES", "XT_ORDERS_INVOICES_TEXT_EDIT", "adminHandler.php?plugin=xt_orders_invoices&load_section=xt_orders_invoices&pg=getEditForm&id='+".$invoice_id_string."+'", '', array('invoice_paid' => 1), 500, 400, '');

        $add_to_url = (isset($_SESSION['admin_user']['admin_key']))? '&sec='.$_SESSION['admin_user']['admin_key']: '';
        $saveBtn = PhpExt_Button::createTextButton(
                        __define('TEXT_SAVE'), new PhpExt_Handler(PhpExt_Javascript::stm("
                            if (Ext.ComponentMgr.get('invoice_payment').isValid()) {
                                var conn = new Ext.data.Connection();
                                conn.request({
                                    url: 'adminHandler.php?plugin=xt_orders_invoices&load_section=xt_orders_invoices".$add_to_url."&pg=saveEditForm',
                                    method: 'POST',
                                    params: Ext.ComponentMgr.get('invoiceEditForm').getForm().getValues(),
                                    error: function(responseObject) {
                                        Ext.Msg.alert('" . __define('TEXT_ALERT') . "', '" . __define('TEXT_NO_SUCCESS') . "');
                                    },
                                    waitMsg: 'SAVING...',
                                    success: function(responseObject) {
                                        if (new_window) { new_window.destroy() } else { this.destroy() }
                                        contentTabs.getActiveTab().getUpdater().refresh();
                                        Ext.Msg.alert('" . __define('TEXT_ALERT') . "','" . __define('TEXT_SUCCESS') . "');
                                    }
                                });
                            } else {
                                Ext.ComponentMgr.get('invoice_payment').focus();
                            }"
                        ))
        );
        $saveBtn->setIcon('images/icons/table_save.png')
                ->setIconCssClass("x-btn-text");
        $window->addButton($saveBtn);
        $rowActionsFunctions['xt_orders_invoices_edit'] = 'if (typeof(Ext.WindowMgr.get("editInvoiceRemoteWindow")) != "undefined") { Ext.WindowMgr.get("editInvoiceRemoteWindow").destroy(); } ' . $window->getJavascript(false, 'new_window') . ' new_window.show();';

        $rowActions[] = array('iconCls' => 'xt_orders_invoices_view', 'qtipIndex' => 'qtip1', 'tooltip' => XT_ORDERS_INVOICES_TEXT_VIEW);

        $js = "window.open('adminHandler.php?plugin=xt_orders_invoices&load_section=xt_orders_invoices".$add_to_url."&pg=getInvoicePdf&type=invoice&id='+".$invoice_id_string.", '');";

        $rowActionsFunctions['xt_orders_invoices_view'] = $js;

        $rowActions[] = array('iconCls' => 'xt_orders_invoices_cancel', 'qtipIndex' => 'qtip1', 'tooltip' => XT_ORDERS_INVOICES_TEXT_CANCEL);
        $js = "Ext.Msg.show({
            title:'" . XT_ORDERS_INVOICES_TEXT_CANCEL . "',
            msg: '" . XT_ORDERS_INVOICES_CONFIRM_CANCEL . "',
            buttons: Ext.Msg.YESNO,
            animEl: 'elId',
            fn: function(btn) {
                if (btn == 'yes') {
                    var conn = new Ext.data.Connection();
                    conn.request({
                        url: 'adminHandler.php?plugin=xt_orders_invoices&load_section=xt_orders_invoices".$add_to_url."&pg=cancel',
                        method: 'GET',
                        params: {'".COL_INVOICE_ID."': $invoice_id_string},
                        success: function(responseObject) {
                            contentTabs.getActiveTab().getUpdater().refresh();
                            Ext.MessageBox.alert('Message', '" . XT_ORDERS_INVOICES_TEXT_CANCELED . "');
                        }
                    });
                }
            },
            icon: Ext.MessageBox.QUESTION
        });";
        $rowActionsFunctions['xt_orders_invoices_cancel'] = $js;

        $rowActions[] = array('iconCls' => 'xt_orders_invoices_email', 'qtipIndex' => 'qtip1', 'tooltip' => XT_ORDERS_INVOICES_BUTTON_EMAIL);
        $rowActionsFunctions['xt_orders_invoices_email'] = $this->getEmailFormHandler();

        $params['rowActions'] = $rowActions;
        $params['rowActionsFunctions'] = $rowActionsFunctions;
		($plugin_code = $xtPlugin->PluginCode('class.xt_orders_invoices.php:_getParams_bottom')) ? eval($plugin_code) : false;
        return $params;
    }

    public function setPosition($position)
    {
        $this->position = $position;
    }
	
	function _getSearchIDs($search_data) {
        global $xtPlugin, $db, $language, $seo,$filter;

        $sql_tablecols = array('invoice_firstname','invoice_lastname','invoice_number','invoice_id','invoice_company');

        ($plugin_code = $xtPlugin->PluginCode('class.xt_orders_invoices.php:_getSearchIDs_array')) ? eval($plugin_code) : false;

        foreach ($sql_tablecols as $tablecol) {
            $sql_where[]= "(".$tablecol." LIKE '%".$filter->_filter($search_data)."%')";
        }
        $where = implode(' or ', $sql_where);
        ($plugin_code = $xtPlugin->PluginCode('class.xt_orders_invoices.php:_getSearchIDs_querry')) ? eval($plugin_code) : false;
       $data = array();
        $table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key,$where);
        $d = $table_data->getData();
        if (count($d) > 0) {
            foreach($d as $records){
                array_push($data,$records['invoice_id']);
            } 
        }
        ($plugin_code = $xtPlugin->PluginCode('class.xt_orders_invoices.php:_getSearchIDs_bottom')) ? eval($plugin_code) : false;
        return $data;
    }
    function _get($id = 0)
    {
		global $xtPlugin;
        if ($this->position != 'admin') {
            return false;
        }
		($plugin_code = $xtPlugin->PluginCode('class.xt_orders_invoices.php:_get_top')) ? eval($plugin_code) : false;
        $default_array = array(
            'invoice_status' => '',
            COL_INVOICE_ID => '',
            COL_INVOICE_NUMBER => '',
            'invoice_issued_date' => '',
            'invoice_due_date' => '',
            'orders_id' => '',
            'invoice_fullname' => '',
            'invoice_total' => '',
            'invoice_currency' => '',
            'invoice_paid' => '',
            'invoice_payment' => ''
        );
		
        $sql_where = '';
		 if ($this->url_data['get_data'] && $this->url_data['query']) {
            $tmp_search_result = $this->_getSearchIDs($this->url_data['query']);
            if (count($tmp_search_result)>0){
                 $sql_where .= " invoice_id IN (".implode(',', $tmp_search_result).")";
            }
        }

        if (!$id && !isset($this->sql_limit)) {
            $this->sql_limit = "0,".self::PAGE_SIZE;
        }

        $obj = new stdClass;
        $obj->totalCount = 1;
        $table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key, $sql_where, $this->sql_limit);
        if ($this->url_data['get_data']) {
            $data = $table_data->getData();
            $obj->totalCount = $table_data->_total_count;
            if (is_array($data))
            {
                foreach ($data as $key => $arr) {
                    $data[$key]['invoice_fullname'] = $data[$key]['invoice_firstname'].' '.$data[$key]['invoice_lastname'];
                    $data[$key]['invoice_number'] = $data[$key]['invoice_prefix'].$data[$key]['invoice_number'];
                }
            }
        } elseif ($id) {
            $data = $this->_buildDataByInvoiceId($id);
        } else {
            $data = array($default_array);
        }

		($plugin_code = $xtPlugin->PluginCode('class.xt_orders_invoices.php:_get_bottom')) ? eval($plugin_code) : false;

        $obj->data = $data;

        return $obj;
    }

    public function getEditForm()
    {
        $id = (int) $this->url_data['id'];
        $table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key);
        $invoice = $table_data->getData($id);

        $form = new PhpExt_Form_FormPanel('invoiceEditForm');
        $form->addItem(PhpExt_Form_Hidden::createHidden(COL_INVOICE_ID, $id));

        $field = PhpExt_Form_TextField::createTextField('invoice_payment', __define('TEXT_INVOICE_PAYMENT'), 'invoice_payment');
        $field->setValue($invoice[0]['invoice_payment'])
                ->setMaxLength(25)
                ->setRegEx(PhpExt_Javascript::inlineStm('new RegExp(/^[0-9a-zA-Z_\/\\\-]*$/)'));
        $form->addItem($field);

        $check = PhpExt_Form_Checkbox::createCheckbox('invoice_paid', __define('TEXT_INVOICE_PAID'), null, 1);
        $check->setChecked((int) $invoice[0]['invoice_paid']);
        $form->addItem($check);

        $field = PhpExt_Form_TextArea::createTextArea('invoice_comment', __define('TEXT_XT_ORDERS_INVOICES_COMMENT'), 'invoiceComment');
        $field->setValue($invoice[0]['invoice_comment'])
            ->setMaxLength(1024)
            ->setCssStyle('width: 300px;height:120px;');
        $form->addItem($field);

        $form->setRenderTo(PhpExt_Javascript::variable("Ext.get('form_div')"));
        $js = PhpExt_Ext::OnReady($form->getJavascript(false, "invoiceEditForm"));

        return '<script type="text/javascript">' . $js . '</script><div id="form_div"></div>';
    }

    function saveEditForm()
    {
        global $db, $xtPlugin;

        if ($this->url_data[COL_INVOICE_ID]) {
            $data = array(
                $this->_master_key => (int) $this->url_data[COL_INVOICE_ID],
                'invoice_payment' => _stripslashes($this->url_data['invoice_payment']),
                'invoice_paid' => (int) $this->url_data['invoice_paid'],
                'invoice_comment' => _stripslashes($this->url_data['invoice_comment']),
            );

            ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':save')) ? eval($plugin_code) : false;
            $db->AutoExecute($this->_table, $data, 'UPDATE', $this->_master_key . ' = ' . $db->Quote($data[$this->_master_key]));
        }

        header('Content-Type: application/json; charset=' . _SYSTEM_CHARSET);
        die(json_encode(true));
    }

    public function getInvoicePdf()
    {
        $id = (int) $this->url_data['id'];

        $data = $this->_buildDataByInvoiceId($id);
        if (!$data) {
            return false;
        }

        $this->_getPdfContent($data);
    }
    
	public function printButtonPDF() {
        global $db;

        $oid = (int) $this->url_data['oid'];
        $cid = (int) $this->url_data['cid'];

        $sql = "SELECT `" . $this->_master_key . "` FROM " . $this->_table . " WHERE `orders_id` = ?";
        $invoiceId = $db->GetOne($sql, array($oid));

        if ($invoiceId)
        {
            $data = $this->_buildDataByInvoiceId($invoiceId);
        }
        else {
            $tmpInvoice = $this->issueTmp($oid);
            $data = $this->_buildData($tmpInvoice);
        }
		
        if (!$data) {
            return false;
        }

        $this->_getPdfContent($data);
    }
    
    public function _getPdfContent($data, $returnPdf = false, $template_id = false)
    {
        global $db, $language, $xtPlugin;

        $langCode= $data['order']['order_data']['language_code'];
        if ( !$language->_checkLanguageCode($langCode)) $langCode = $language->code;
        
        $smarty = new Smarty();
        $smarty->setCompileDir(_SRV_WEBROOT . 'templates_c');
        
        $smarty->addTemplateDir('./'._SRV_WEB_TEMPLATES._STORE_TEMPLATE.'/');
        $smarty->addTemplateDir('./'._SRV_WEB_TEMPLATES._SYSTEM_TEMPLATE.'/');

        Template::addPluginsDir($smarty, array(
            _SRV_WEBROOT.'xtFramework/library/smarty/xt_plugins'));

		
        if ($template_id===false) {
        	$tpl_source = $this->db_get_template($data['invoice']['shop_id'] . '_' . $langCode); 
        } else {
        	$tpl_source = $this->db_get_template_by_template_id($template_id . '_' . $langCode);
        }

        
        $data['logopath'] = _SRV_WEBROOT.'media/logo/';

        if(file_exists($data['logopath'].'logo_pdf_s-'.$data['invoice']['shop_id'].'_l-'.$langCode.'.png'))
        {
            $data['logo'] = 'logo_pdf_s-'.$data['invoice']['shop_id'].'_l-'.$langCode.'.png';
        }
        else if(file_exists($data['logopath'].'logo_pdf_s-'.$data['invoice']['shop_id'].'.png'))
        {
            $data['logo'] = 'logo_pdf_s-'.$data['invoice']['shop_id'].'.png';
        }
        else if(file_exists($data['logopath'].'logo_pdf_l-'.$langCode.'.png'))
        {
            $data['logo'] = 'logo_pdf_l-'.$langCode.'.png';
        }
        else if(file_exists($data['logopath'].'logo_pdf'.'.png'))
        {
            $data['logo'] = 'logo_pdf'.'.png';
        }
        else
        {
            $data['logo'] = $data['config']['_store_logo'];
        }

        ($plugin_code = $xtPlugin->PluginCode('class.xt_orders_invoices.php:_getPdfContent_assign_samrty_data')) ? eval($plugin_code) : false;

        $smarty->assign('data', $data);

        /*
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        die();
        */

        if(_STORE_IMAGES_PATH_FULL == 'true'){
            $path_base_url = _SYSTEM_BASE_URL;
        }else{
            $path_base_url = '';
        }
        $smarty->assign('tpl_url_path', $path_base_url._SRV_WEB._SRV_WEB_TEMPLATES._STORE_TEMPLATE.'/');
        $smarty->assign('tpl_path', _SRV_WEBROOT._SRV_WEB_TEMPLATES.$this->selected_template.'/');
        $smarty->assign('tpl_url_path_system', $path_base_url._SRV_WEB._SRV_WEB_TEMPLATES._SYSTEM_TEMPLATE.'/');
        $smarty->assign('tpl_path_system', _SRV_WEBROOT._SRV_WEB_TEMPLATES.$this->system_template.'/');

        try
        {
            $html = $smarty->fetch('eval:' . $tpl_source);
        } catch (\Smarty\Exception $e)
        {
            $html = $e->getMessage().'<br><br>'. $e->getTraceAsString();
        }

        require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_orders_invoices/library/dompdf/dompdf_config.custom.inc.php';
        //require_once _SRV_WEBROOT . _SRV_WEB_FRAMEWORK . 'library/dompdf/dompdf_config.inc.php';

        $options = new Options(
            [
                'isHtml5ParserEnabled' => true,
                'chroot' => _SRV_WEBROOT
            ]
        );
        $dompdf = new Dompdf($options);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->setBasePath(_SRV_WEBROOT);

        //define('ORDER_INVOICES_PDF_DEBUG', true);

        $fname = $this->getPdfFilename($data);
        if(defined('ORDER_INVOICES_PDF_DEBUG') && constant('ORDER_INVOICES_PDF_DEBUG'))
            file_put_contents(_SRV_WEBROOT._SRV_WEB_LOG.$fname.'.html', $html);

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':loadHtml')) ? eval($plugin_code) : false;

        $dompdf->loadHtml($html);
        $dompdf->render();

        if ($returnPdf) {
            $s = $dompdf->output();
            //file_put_contents( _SRV_WEBROOT . _SRV_WEB_EXPORT. $fname . '.pdf', $s);
            return $s;
        } else {
            if (ob_get_level())
                ob_end_clean();
            $dompdf->stream($fname . '.pdf', array('Attachment' => XT_ORDERS_INVOICES_SEND_AS_ATTACHMENT));
            die();
        }
    }
    
    private function getPdfFileName($tpldata) {
        $smarty = new Smarty();
        $smarty->setCompileDir(_SRV_WEBROOT . 'templates_c');
        $smarty->assign('data', $tpldata);
        // create some shortcuts to ease file name definition in backend
        $smarty->assign('orders_id', $tpldata['order']['order_data']['orders_id']);
        $smarty->assign('invoice_number', $tpldata['invoice']['invoice_number']);
        $smarty->assign('shop_name', str_replace(' ','-',$tpldata['config']['_store_name']));
        $smarty->assign('shop_id', $tpldata['order']['order_data']['shop_id']);
        $smarty->assign('template_type', str_replace(' ','-',$this->_print_template['template_type']));

        $var = $this->_print_template['template_pdf_out_name'];
        if(empty($var))
        {
            $var = 'template_pdf_out_name--not-set--check-tpl-permissions';
        }
        $fname = $this->smarty_function_eval(array('var'=>$var), $smarty);

        return $fname;
    }

    public function autoGenerate($orders_id,$email) {
        global $db, $xtPlugin;

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':top')) ? eval($plugin_code) : false;
        if(isset($plugin_return_value))
            return $plugin_return_value;

        $result = $this->isExistByOrderId($orders_id);

        if (!$result) {

            $invoice_id = $this->issue($orders_id);
            $this->sendPdf($invoice_id, $email);

        } else {
           // $invoice_id = $result;
            // use existing
        }

    }

    public function sendPdf($invoice_id='',$email='')
    {
        global $db, $xtPlugin;
        if ($invoice_id=='' or is_array($invoice_id)) {
            $id = (int) $this->url_data[COL_INVOICE_ID];
        } else {
            $id = $invoice_id;
        }

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':top')) ? eval($plugin_code) : false;
        if(isset($plugin_return_value))
            return $plugin_return_value;

        $data = $this->_buildDataByInvoiceId($id);
        if (!$data) {
            return false;
        }

        if ($email=='') {
            $email = $this->url_data['email_recipients'];
        }

        $regmail = '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD';
        $recipientsTmp = preg_split('/[,;\s]/', $email, 0, PREG_SPLIT_NO_EMPTY);
        $recipientsList = array();
        // check if recipients are valid emails
        foreach ($recipientsTmp as &$recipient) {
            $recipient = trim($recipient);
            if (preg_match($regmail, $recipient)) {
                $recipientsList[] = $recipient;
            }
        }
        unset($recipient);
        
        $r = new stdClass();
        $r->msg = '';
        $r->success = false;

        if ($recipientsList && ($pdfContent = $this->_getPdfContent($data, true))) {
            $mail = new xtMailer('send_invoice', $data['order']['order_data']['language_code'], $data['order']['order_data']['customers_status'], -1, $data['invoice']['shop_id']);
            foreach ($recipientsList as $recipient) {
                $mail->_addReceiver($recipient, ($recipient == $data['order']['order_customer']['customers_email_address']) ? $data['invoice']['invoice_fullname'] : '');
            }
            $mail->_assign('data', $data);
            $fname = $this->getPdfFilename($data);
            $mail->AddStringAttachment($pdfContent, $fname . '.pdf');
            $db->Execute("UPDATE ". $this->_table ." SET invoice_sent=1, invoice_sent_date = NOW() WHERE ".COL_INVOICE_ID."=?", array($id));

            ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':sendMail')) ? eval($plugin_code) : false;
            $mail->_sendMail();
			$r->success = !$mail->IsError();
			if(!$r->success) {
				$r->msg = !empty($mail->ErrorInfo) ? $mail->ErrorInfo : TEXT_ERROR;
			}
        } else {
            $r->msg = 'no recipient or no pdf content';
        }
        
        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':bottom')) ? eval($plugin_code) : false;
        return json_encode($r);
    }

    public function issue($_orders_id='')
    {
        global $db, $price,$xtPlugin;

        if ($_orders_id>0 && !is_array($_orders_id)) {
            $id = $_orders_id;
        } else {

            $id = (int) $this->url_data['id'];
        }

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':top')) ? eval($plugin_code) : false;
        if(isset($plugin_return_value))
            return $plugin_return_value;


        // delete existing
        //$this->_delete(null, $id); // invoice_id, orders_id

        // shop id ermitteln
        $shopId = $db->GetOne("SELECT shop_id from ". TABLE_ORDERS ." WHERE orders_id=?", array($id));
        // hat der shop separaten kreis
        $separateAssignment = $this->isSeparateAssignmentForShop($shopId);

        if ($this->url_data['start_no']!=false && $this->url_data['start_no']!="false")
        {
            $invoice_number = $this->url_data['start_no'];
        }
        else {
            if ($separateAssignment)
            {
                $sql = "SELECT `config_value` FROM " . TABLE_PLUGIN_CONFIGURATION ." WHERE `config_key`= 'XT_ORDERS_INVOICE_SEPARATE_NUMBER_ASSIGNMENT_LAST_USED' AND `shop_id`=?";
                $invoice_number = $db->GetOne($sql, array($shopId));
            }
            else {
                $sql = "SELECT `config_value` FROM " . TABLE_CONFIGURATION . " WHERE `config_key`= '_INVOICE_NUMBER_GLOBAL_LAST_USED'";
                $invoice_number = $db->GetOne($sql);
            }

            if (!$invoice_number)
            {
                $invoice_number = 0;
            }
            $invoice_number++;
        }

        $order = new order();
        $orderData = $order->_buildData($id);

        $baseDate = strtotime($orderData['order_data']['date_purchased_plain']);
        if (
                _STORE_ORDERS_INVOICES_ANCHOR == 'shipment'
                && $orderData['order_data']['orders_status_id'] == 33 // 33 means Shipped
        ) {
            $baseDate = strtotime($orderData['order_data']['last_modified']);
        }

        $sql = "SELECT `config_value` FROM " . TABLE_PLUGIN_CONFIGURATION ." WHERE `config_key`= 'XT_ORDERS_INVOICE_PREFIX' AND `shop_id`=?";
        $prefix = $db->GetOne($sql, array($shopId));

        $item = array();
        $item['invoice_number'] = (int) $invoice_number;
        $item['invoice_prefix'] = $prefix;
        $item['orders_id'] = (int) $orderData['order_data']['orders_id'];
        $item['customers_id'] = (int) $orderData['order_data']['customers_id'];
        $item['invoice_company'] = $orderData['order_data']['billing_company'];
        $item['invoice_firstname'] = $orderData['order_data']['billing_firstname'];
        $item['invoice_lastname'] = $orderData['order_data']['billing_lastname'];
        $item['invoice_issued_date'] = date('Y-m-d H:i:s');
        if(isset($orderData['order_data']['invoice_due_date']))
        {
            $item['invoice_due_date'] = $orderData['order_data']['invoice_due_date'];
        }
        else
        {
        $item['invoice_due_date'] = date('Y-m-d H:i:s', strtotime('+' . _STORE_ORDERS_INVOICES_DAYS . ' day', $baseDate));
        }
        $item['invoice_ordered_date'] = $orderData['order_data']['date_purchased_plain'];
        $item['invoice_total'] = (float) $orderData['order_total']['total']['plain'];
        $item['invoice_total_otax'] = (float) $orderData['order_total']['total_otax']['plain'];
        $item['invoice_currency'] = $orderData['order_data']['currency_code'];
        $item['invoice_shipping_currency'] = $orderData['order_data']['currency_code'];
        $item['invoice_packaging_currency'] = $orderData['order_data']['currency_code'];
        $item['invoice_comment'] = $this->url_data['comment'];
        $item['invoice_status'] = 1;

        if (is_array($orderData['order_total_data']) && count($orderData['order_total_data'])) {
            foreach ($orderData['order_total_data'] as $subPrice) {
                if ($subPrice['orders_total_key'] == 'shipping') {
                    $item['invoice_shipping_price'] = (float) $subPrice['orders_total_final_price']['plain_otax'];
                    $item['invoice_shipping_tax_rate'] = (float) $subPrice['orders_total_tax_rate'];
                }
            }
        }

        ($plugin_code = $xtPlugin->PluginCode('xt_orders_invoices:issue_save_invoice')) ? eval($plugin_code) : false;

        $adbs = new adminDB_DataSave($this->_table, $item);
        $res = $adbs->saveDataSet();

        $invoiceId = $res->new_id;

        foreach ($orderData['order_products'] as $product) {
            $item = array();
            $item[COL_INVOICE_ID] = (int) $invoiceId;
            $item['products_id'] = (int) $product['products_id'];
            $item['products_model'] = $product['products_model'];
            $item['products_name'] = $product['products_name'];
            $item['products_quantity'] = (float) $product['products_quantity'];
            $item['products_unit'] = (int) $product['products_unit'];
            $item['products_price'] = (float) $product['products_price']['plain_otax'];
            $item['products_discount_rate'] = (float) $product['products_discount'];
            $item['products_tax_rate'] = (float) $product['products_tax_rate'];
            $item['products_currency'] = $orderData['order_data']['currency_code'];

			($plugin_code = $xtPlugin->PluginCode('xt_orders_invoices:issue_loop')) ? eval($plugin_code) : false;
			
            // HACK to revert original price without discount
            if ($item['products_discount_rate']) {
                $price->_setCurrency($item['products_currency']);
                $item['products_price'] = $price->_removeTax($item['products_price'], -1 * $item['products_discount_rate']);
            }

            ($plugin_code = $xtPlugin->PluginCode('xt_orders_invoices:issue_foreach_save_product')) ? eval($plugin_code) : false;

            $adbs = new adminDB_DataSave($this->_table_products, $item);
            $res = $adbs->saveDataSet();
        }

        if ($separateAssignment)
        {
            $sql = "UPDATE " . TABLE_PLUGIN_CONFIGURATION ." SET `config_value`=$invoice_number WHERE `config_key`= 'XT_ORDERS_INVOICE_SEPARATE_NUMBER_ASSIGNMENT_LAST_USED' AND `shop_id`=?";
            $db->Execute($sql, array($shopId));
        }
        else {
            $sql = "UPDATE " . TABLE_CONFIGURATION . " SET `config_value`=$invoice_number WHERE `config_key`= '_INVOICE_NUMBER_GLOBAL_LAST_USED'";
        $db->Execute($sql);
        }

		($plugin_code = $xtPlugin->PluginCode('xt_orders_invoices:issue_bottom')) ? eval($plugin_code) : false;
		
        if ($_orders_id>0 && !is_array($_orders_id)) {
            return $invoiceId;
        } else {

            header('Content-Type: application/json; charset=' . _SYSTEM_CHARSET);
            echo $invoiceId;
            die();
        }
    }

    public function issueTmp($_orders_id='')
    {
        global $db, $price,$xtPlugin;

        if ($_orders_id>0 && !is_array($_orders_id)) {
            $id = $_orders_id;
        } else {

            $id = (int) $this->url_data['id'];
        }

        // shop id ermitteln
        $shopId = $db->GetOne("SELECT shop_id from ". TABLE_ORDERS ." WHERE orders_id=?", array($id));

        $invoice_number = '';

        $order = new order();
        $orderData = $order->_buildData($id);

        $baseDate = strtotime($orderData['order_data']['date_purchased_plain']);
        if (
            _STORE_ORDERS_INVOICES_ANCHOR == 'shipment'
            && $orderData['order_data']['orders_status_id'] == 33 // 33 means Shipped
        ) {
            $baseDate = strtotime($orderData['order_data']['last_modified']);
        }

        $sql = "SELECT `config_value` FROM " . TABLE_PLUGIN_CONFIGURATION ." WHERE `config_key`= 'XT_ORDERS_INVOICE_PREFIX' AND `shop_id`=?";
        $prefix = $db->GetOne($sql, array($shopId));

        $item = array();
        $item['invoice_number'] = (int) $invoice_number;
        $item['invoice_prefix'] = $prefix;
        $item['orders_id'] = (int) $orderData['order_data']['orders_id'];
        $item['customers_id'] = (int) $orderData['order_data']['customers_id'];
        $item['invoice_company'] = $orderData['order_data']['billing_company'];
        $item['invoice_firstname'] = $orderData['order_data']['billing_firstname'];
        $item['invoice_lastname'] = $orderData['order_data']['billing_lastname'];
        $item['invoice_due_date'] = date('Y-m-d H:i:s', strtotime('+' . _STORE_ORDERS_INVOICES_DAYS . ' day', $baseDate));
        $item['invoice_ordered_date'] = $orderData['order_data']['date_purchased_plain'];
        $item['invoice_total'] = (float) $orderData['order_total']['total']['plain'];
        $item['invoice_total_otax'] = (float) $orderData['order_total']['total_otax']['plain'];
        $item['invoice_currency'] = $orderData['order_data']['currency_code'];
        $item['invoice_shipping_currency'] = $orderData['order_data']['currency_code'];
        $item['invoice_packaging_currency'] = $orderData['order_data']['currency_code'];
        $item['invoice_comment'] = $this->url_data['comment'];
        $item['invoice_status'] = 1;

        if (is_array($orderData['order_total_data']) && count($orderData['order_total_data'])) {
            foreach ($orderData['order_total_data'] as $subPrice) {
                if ($subPrice['orders_total_key'] == 'shipping') {
                    $item['invoice_shipping_price'] = (float) $subPrice['orders_total_final_price']['plain_otax'];
                    $item['invoice_shipping_tax_rate'] = (float) $subPrice['orders_total_tax_rate'];
                }
            }
        }
        return $item;
    }

    public function cancel()
    {
        global $db;

        if ($this->url_data[COL_INVOICE_ID]) {
            $data = array(
                $this->_master_key => (int) $this->url_data[COL_INVOICE_ID],
                'invoice_status' => 0
            );
            $db->AutoExecute($this->_table, $data, 'UPDATE', $this->_master_key . ' = ' . $db->Quote($data[$this->_master_key]));
        }
        return true;
    }

    public function getOrderData($orderId)
    {
        $data = $this->_buildDataByOrderId($orderId);

        if ($data) {
            $data['invoice']['invoice_number_with_prefix'] = $data['invoice']['invoice_prefix'].$data['invoice']['invoice_number'];
            return array($data['invoice']);
        } else {
            return array();
        }

        return $data;
    }

    protected function _buildData($invoice)
    {
        global $db, $price, $currency, $xtPlugin, $tax, $xtLink;

        $now = date('Y-m-d H:i:s');
        $invoiceData = $invoice;
        $invoiceData['invoice_total'] = (float) $invoiceData['invoice_total'];
        $invoiceData['document'] = 'Invoice';
        $invoiceData['invoice_fullname'] = $invoiceData['invoice_firstname'] . ' ' . $invoiceData['invoice_lastname'];
        $invoiceData['invoice_overdue'] = ($invoiceData['invoice_due_date'] < $now);

        $price->_setCurrency($invoiceData['invoice_currency']);
        $invoiceData['invoice_total_formatted'] = number_Format($invoiceData['invoice_total'], $currency->decimals, $currency->dec_point, $currency->thousands_sep);
        $invoiceData['invoice_total_formatted_net'] = number_Format($invoiceData['invoice_total_otax'], $currency->decimals, $currency->dec_point, $currency->thousands_sep);
        $invoiceData['invoice_total_formatted2'] = $price->_StyleFormat($invoiceData['invoice_total']);

        $price->_setCurrency($invoiceData['invoice_shipping_currency']);
        $invoiceData['invoice_shipping_tax'] = $price->_calcTax($invoiceData['invoice_shipping_price'], $invoiceData['invoice_shipping_tax_rate']);
        $invoiceData['invoice_shipping_total'] = $price->_AddTax($invoiceData['invoice_shipping_price'], $invoiceData['invoice_shipping_tax_rate']);
        $invoiceData['invoice_shipping_price_formatted'] = number_Format($invoiceData['invoice_shipping_price'], $currency->decimals, $currency->dec_point, $currency->thousands_sep);
        $invoiceData['invoice_shipping_tax_rate_formatted'] = number_Format($invoiceData['invoice_shipping_tax_rate'], $currency->decimals, $currency->dec_point, $currency->thousands_sep);
        $invoiceData['invoice_shipping_tax_formatted'] = number_Format($invoiceData['invoice_shipping_tax'], $currency->decimals, $currency->dec_point, $currency->thousands_sep);
        $invoiceData['invoice_shipping_total_formatted'] = number_Format($invoiceData['invoice_shipping_total'], $currency->decimals, $currency->dec_point, $currency->thousands_sep);

        $taxes = array();
        if ($invoiceData['invoice_shipping_tax_rate'] > 0) {
            $rate = (int)$invoiceData['invoice_shipping_tax_rate'];
            $taxes[$rate] += $invoiceData['invoice_shipping_tax'];
        }

        $invoiceData['invoice_subtotal_price'] = 0;
        $invoiceData['invoice_subtotal_discount'] = 0;
        $invoiceData['invoice_subtotal_tax'] = 0;
        $invoiceData['invoice_final_subtotal_price'] = 0;
        
        $order = new order();
        $orderData = $order->_buildData($invoiceData['orders_id']);
        $products = array();
        $total_weight = 0;
        $o_products = $orderData['order_products'];
        foreach($o_products as $k =>$value) {
            $product = array();
            $invoice_p = product::getProduct($value['products_id']);

            $product['db_data'] = $invoice_p->data;

            $product['products_id'] = $value['products_id'];
            $product['products_name'] = $value['products_name'];
            $product['products_quantity'] = $value['products_quantity'];
            $product['products_unit'] = $value['products_unit'];
            $product['products_model'] = $value['products_model'];
            $product['products_ean'] = $value['products_ean'];

            $product['products_tax'] = $value['products_tax']['plain'];
            $product['products_image'] = $invoice_p->data['products_image'];
            $product['date_available'] = $invoice_p->data['date_available'];
            $product['products_digital'] = $invoice_p->data['products_digital'];
            $product['products_shippingtime'] = version_compare(_SYSTEM_VERSION,'6.2.0', '>=') ? $invoice_p->data['shipping_status_data']['id'] : $invoice_p->data['products_shippingtime'];
            $product['products_serials'] = $value['products_serials'];
            $product['products_vpe'] = $invoice_p->data['products_vpe'];
            $product['products_vpe_value'] = $invoice_p->data['products_vpe_value'];
            $product['products_vpe_status'] = $invoice_p->data['products_vpe_status'];
            $product['products_weight'] = $value['products_weight'];
            $total_weight = $total_weight + $invoice_p->data['products_weight']*$value['products_quantity'];
            $product['products_final_price'] = $value['products_price']['plain'];
            $product['products_price'] = $value['products_price']['plain'];

            $product['products_discount'] = (($value['products_price']['plain']* 100)/(100-$value['products_discount']))-$value['products_price']['plain'];
            $product['products_discount'] = round($product['products_discount'],2);

            $tax_bak = $tax;

            $customers_status_tax_rates_calculation_base = $db->GetOne("SELECT customers_status_tax_rates_calculation_base FROM ".TABLE_CUSTOMERS_STATUS." WHERE customers_status_id = ?", array($orderData["order_data"]["customers_status"]));

            switch ($customers_status_tax_rates_calculation_base)
            {
                case 'shipping_address':
                    /**
                     *   egal was wohin, die shipping address ist grundlage der berechnung
                     */
                    $tax = new tax($orderData["order_data"]["delivery_country_code"]);
                    break;
                case 'payment_address':
                    /**
                     *   egal was wohin, die payment address ist grundlage der berechnung
                     */
                    $tax = new tax($orderData["order_data"]["billing_country_code"]);
                    break;
                default:
                    /**
                     *   b2c_eu  digital wird gesondert, anhand der rechungsadresse berechnet
                     */
                    $tax = new tax($orderData["order_data"]["billing_country_code"]);
            }

            $cleanProduct = new product($value['products_id'],'price',1);

            if(USER_POSITION=='admin')
            {
                $cleanProduct->buildData('default');
                //error_log('after build data');
            }
            $cleanPrice = false;

            //error_log(print_r($cleanProduct->data,true));

            // FIX issue #118  Darstellung Rabatt
            /*
            if(is_array($cleanProduct->data['group_price']))
            {
                $cleanPrice = $cleanProduct->data['group_price']['cheapest'];
                $product['cleanPrice'] = $cleanPrice.'#1';
            }
            else if(is_array($cleanProduct->data['products_price']))
            {
                $cleanPrice =  $cleanProduct->data['products_price']['old_plain'] ? $cleanProduct->data['products_price']['old_plain'] : $cleanProduct->data['products_price']['plain'];
                $product['cleanPrice'] = $cleanPrice . '#2';
            }
            else
            {
                $cleanPrice =  $cleanProduct->data['products_price'];
                $product['cleanPrice'] = $cleanPrice.'#3';
            }
            $cleanPrice = round($cleanPrice,2 );

            if ($cleanProduct->data['flag_has_specials']!=true && $cleanPrice>$value['products_price']['plain']) {
                $product_discount = $cleanPrice - $value['products_price']['plain'];
                $product['products_discount']=$product_discount;
                $product['cleanPrice'] .='#1';
            }
            else if ($cleanProduct->data['flag_has_specials']==true && $cleanPrice>$value['products_price']['plain_otax'])
            {
                //$product_discount = 100-(round($value['products_price']['plain']/$cleanPrice,2))*100;
                $product_discount = $cleanPrice - $value['products_price']['plain'];
                $product['products_discount']=$product_discount;
                $product['cleanPrice'] .='#2';
            }
            */
            // FIX issue #118  Darstellung Rabatt
            $product['products_discount'] = 0;
            if($value['products_discount'] > 0)
            {
                $org_price = ($value['products_price']['plain'] / (100-$value['products_discount'])) * 100;
                $product['products_discount'] = $org_price - $value['products_price']['plain'];
            }


            $product['products_discount_rate'] = $value['products_discount'];
            $product['products_tax_rate'] = $value['products_tax_rate'];
            $product['products_price_formatted'] = number_Format($product['products_price'], $currency->decimals, $currency->dec_point, $currency->thousands_sep);
            $product['products_discount_rate_formatted'] = number_Format($product['products_discount_rate'], $currency->decimals, $currency->dec_point, $currency->thousands_sep);
            $product['products_discount_formatted'] = number_Format($product['products_discount'], $currency->decimals, $currency->dec_point, $currency->thousands_sep);
            $product['products_tax_rate_formatted'] = number_Format($product['products_tax_rate'], $currency->decimals, $currency->dec_point, $currency->thousands_sep);
            $product['products_tax_formatted'] = number_Format($product['products_tax'], $currency->decimals, $currency->dec_point, $currency->thousands_sep);
            $product['products_final_price_formatted'] = number_Format($product['products_final_price'], $currency->decimals, $currency->dec_point, $currency->thousands_sep);

            $product['products_total_price'] = $value['products_final_price']['plain'];
            $product['products_total_discount'] = $product['products_quantity'] * $product['products_discount'];
            $product['products_total_tax'] = $value['products_final_tax']['plain'];
            $product['products_final_total_price'] = $value['products_final_price']['plain'];
            $product['products_total_price_formatted'] = number_Format($product['products_total_price'], $currency->decimals, $currency->dec_point, $currency->thousands_sep);
            $product['products_total_discount_formatted'] = number_Format($product['products_total_discount'], $currency->decimals, $currency->dec_point, $currency->thousands_sep);
            $product['products_total_tax_formatted'] = number_Format($product['products_total_tax'], $currency->decimals, $currency->dec_point, $currency->thousands_sep);
            $product['products_final_total_price_formatted'] = number_Format($product['products_final_total_price'], $currency->decimals, $currency->dec_point, $currency->thousands_sep);

            if ($product['products_tax_rate'] > 0) {
                $rate = (int)$product['products_tax_rate'];
                $taxes[$rate] += $product['products_total_tax'];
            }

            $invoiceData['invoice_subtotal_price'] += $product['products_final_total_price'];
            $invoiceData['invoice_subtotal_discount']+= $product['products_total_discount'];
            $invoiceData['invoice_subtotal_tax'] += $product['products_total_tax'];
            $invoiceData['invoice_final_subtotal_price'] += $product['products_final_total_price'];

            if(array_key_exists('xt_product_options', $xtPlugin->active_modules) && constant('XT_PRODUCT_OPTIONS_ACTIVE')=='true'){


                $products_data = unserialize($value["products_data"]);
                if($products_data && $products_data['options'])
                {
                    $product['products_info_data']['options'] = $products_data['options'];
                }

                /*
                require_once _SRV_WEBROOT._SRV_WEB_PLUGINS.'xt_product_options/classes/class.xt_product_options.php';
                $temp_prod = product::getProduct($product['products_id']);
                if(!is_array($temp_prod->data) || empty($temp_prod->data))
                    $temp_prod->buildData();
                $xt_po = new xt_product_options($product['products_id'], $temp_prod->data);

                $xt_po->load_product_options();
                $product['products_information'] = $xt_po->option_data;
                */
            }

            $product['serial_number'] = false;
            if(array_key_exists('xt_serials', $xtPlugin->active_modules)){

                require_once _SRV_WEBROOT._SRV_WEB_PLUGINS.'xt_serials/classes/class.product_serials.php';
                $xt_serials = new product_serials($product['products_id']);

                $product['serial_number'] = $xt_serials->_getSerial($invoiceData['orders_id'], $value['orders_products_id']);
            }
			($plugin_code = $xtPlugin->PluginCode('xt_orders_invoices:_buildData_product_loop')) ? eval($plugin_code) : false;
            
            $products[] = $product;
            
        }

        $orderData['total_weight']['plain'] = $total_weight;
        $orderData['total_weight']['formated'] = number_Format($total_weight, $currency->decimals, $currency->dec_point, $currency->thousands_sep). ' kg';

        $taxes = array();
        $o_total = $orderData['order_total']['total_tax'];
        if (is_array($o_total))
        {
            foreach ($o_total as $k=>$v){
                $item = array();
                $item['rate'] = $v['tax_key'];
                $item['rate_formatted'] = number_Format($item['rate'], $currency->decimals, $currency->dec_point, $currency->thousands_sep);
                $item['amount'] = $v['tax_value']['plain'];
                $item['amount_formatted'] = number_Format($item['amount'], $currency->decimals, $currency->dec_point, $currency->thousands_sep);

                $taxes[$k] = $item;
            }
        }

        $invoiceData['invoice_subtotal_price_formatted'] = number_Format($invoiceData['invoice_subtotal_price'], $currency->decimals, $currency->dec_point, $currency->thousands_sep);
        $invoiceData['invoice_subtotal_discount_formatted'] = number_Format($invoiceData['invoice_subtotal_discount'], $currency->decimals, $currency->dec_point, $currency->thousands_sep);
        $invoiceData['invoice_subtotal_tax_formatted'] = number_Format($invoiceData['invoice_subtotal_tax'], $currency->decimals, $currency->dec_point, $currency->thousands_sep);
        $invoiceData['invoice_final_subtotal_price_formatted'] = number_Format($invoiceData['invoice_final_subtotal_price'], $currency->decimals, $currency->dec_point, $currency->thousands_sep);

        
        if (!isset($invoiceData['shop_id'])) {
            $invoiceData['shop_id'] = $orderData['order_data']['shop_id'];
        }
        
        
        $invoiceData['totalLines'] = array();
        if (is_array($orderData['order_total_data']) && count($orderData['order_total_data'])) {
            foreach ($orderData['order_total_data'] as $subPrice) {
             	if ($subPrice['orders_total_key'] == 'xt_coupon') {
                    $invoiceData['invoice_coupon_price'] = (float) $subPrice['orders_total_final_price']['plain_otax'];
                    $invoiceData['invoice_coupon_redeem'] = true;
                    $invoiceData['invoice_coupon_tax_rate'] = (float) $subPrice['orders_total_tax_rate'];
                    $invoiceData['invoice_coupon_price_formatted'] = number_Format($subPrice['orders_total_price']['plain'], $currency->decimals, $currency->dec_point, $currency->thousands_sep);
                } else if ($subPrice['orders_total_key'] == 'payment') {
                    $invoiceData['invoice_payment_cost'] = (float) $subPrice['orders_total_final_price']['plain_otax'];
                    $invoiceData['invoice_payment_name'] =$subPrice['orders_total_name'];
                    $invoiceData['invoice_payment_tax_rate'] = (float) $subPrice['orders_total_tax_rate'];
                    $invoiceData['invoice_payment_cost_formatted'] = number_Format($subPrice['orders_total_price']['plain'], $currency->decimals, $currency->dec_point, $currency->thousands_sep);
                } else if ($subPrice['orders_total_key'] !== 'shipping') {
					$invoiceData['totalLines'][] = array(
						'invoice_payment_cost' => (float) $subPrice['orders_total_final_price']['plain_otax'],
						'invoice_payment_name' => $subPrice['orders_total_name'],
						'invoice_payment_tax_rate' => (float) $subPrice['orders_total_tax_rate'],
						'invoice_payment_cost_formatted' => number_Format($subPrice['orders_total_price']['plain'], $currency->decimals, $currency->dec_point, $currency->thousands_sep),
					);
				}
            }
        }
        
		if ($invoiceData['invoice_total_formatted']!==$orderData['order_total']['total']['plain'])
		{
			$invoiceData['invoice_total_formatted'] = number_Format($orderData['order_total']['total']['plain'], $currency->decimals, $currency->dec_point, $currency->thousands_sep);
			$invoiceData['invoice_total_formatted_net'] = number_Format($orderData['order_total']['total_otax']['plain'], $currency->decimals, $currency->dec_point, $currency->thousands_sep);
			$invoiceData['invoice_total_formatted2'] = $price->_StyleFormat($orderData['order_total']['total']['plain']);
		}
		
        $config = array();
        $rs = $db->Execute("SELECT * FROM " . TABLE_CONFIGURATION_MULTI . (int)$invoiceData['shop_id'] . " WHERE 1");
        while (!$rs->EOF) {
            $config[strtolower($rs->fields['config_key'])] = $rs->fields['config_value'];
            $rs->MoveNext();
        }
        $rs->Close();

        $rs = $db->Execute("SELECT * FROM ".TABLE_CONFIGURATION_LANG_MULTI." WHERE store_id=? AND language_code=?",array($invoiceData['shop_id'],$orderData['order_data']['language_code']));
        while (!$rs->EOF) {
            $config[strtolower($rs->fields['config_key'])] = $rs->fields['language_value'];
            $rs->MoveNext();
        }
        $rs->Close();

        // prepare links
        $shopUrl = '';
        $query = "SELECT * FROM " . TABLE_MANDANT_CONFIG . " where shop_id =?";
        $store_config = $db->GetArray($query, array($invoiceData['shop_id']));
        if (is_array($store_config))
        {
            $domain = $store_config[0]['shop_ssl_domain'];
            $exportUrl       = $store_config[0]['shop_ssl'] === 0 || $store_config[0]['shop_ssl'] == 'no_ssl' ? 'http://'.$domain : 'https://'.$domain;
            $exportSecureUrl = $store_config[0]['shop_ssl'] === 0 || $store_config[0]['shop_ssl'] == 'no_ssl' ? 'http://'.$domain : 'https://'.$domain;

            $tmp_xtlink = new xtLink();
            $tmp_xtlink->setLinkURL($exportUrl);
            $tmp_xtlink->setSecureLinkURL($exportSecureUrl);

            $shopUrl = $tmp_xtlink->_link(['page' => 'index'], ['/xtAdmin']);
        }

        $c_status = new customers_status();
        $c_status->_getStatus($orderData['order_customer']['customers_status']);

        if($c_status->customers_status_show_price_tax=='1') {
        	$invoiceData['invoice_shipping_total_formatted'] = number_Format($invoiceData['invoice_shipping_total'], $currency->decimals, $currency->dec_point, $currency->thousands_sep);
        } else {
        	$invoiceData['invoice_shipping_total_formatted'] = number_Format($invoiceData['invoice_shipping_price'], $currency->decimals, $currency->dec_point, $currency->thousands_sep);
        }
        
        if(is_array($orderData['order_data']['bank_details']) && count($orderData['order_data']['bank_details']))
        {
            foreach($orderData['order_data']['bank_details'] as $k => $v)
            {
                $s = TEXT_BAD_ACCOUNT_NAME .': '.$v['bad_account_name']."
                ".TEXT_BAD_BANK_IDENTIFIER_CODE .': '.$v['bad_bank_identifier_code']."
                ".TEXT_BAD_INTERNATIONAL_BANK_ACCOUNT_NUMBER.': '.$v['bad_international_bank_account_number']."
                ".TEXT_BAD_REFERENCE_NUMBER.': '.$v['bad_reference_number']."\n";

                try
                {
                    if (!empty($v['bad_due_date']))
                    {
                        $dts = DateTime::createFromFormat('Y-m-d', $v['bad_due_date'])->format('d.m.Y');
                        $s .= TEXT_BAD_DUE_DATE . ': ' . $dts . "\n";
                    }
                }
                catch(Exception $e)
                {
                    $s .= TEXT_BAD_DUE_DATE . ': ' . $v['bad_due_date'] . "\n";
                }

                if ($v['bad_tech_issuer'] == 'xt_paypal_plus')
                {
                    $ca = sprintf(TEXT_PPP_PUI_SUCCESS_CLAIM_ASSIGN_2, $config['_store_shopowner_company']);
                    $s .= "
                        $ca
                    ";
                }

                $invoiceData['invoice_bank_details'][$k] = $s;
            }
        }

        $tax = $tax_bak;
        
        $data = array();
        $data['invoice'] = $invoiceData;
        $data['products'] = $products;
        $data['taxes'] = $taxes;
        $data['order'] = $orderData;
        $data['config'] = $config;
        $data['shopUrl'] = $shopUrl;
		
		($plugin_code = $xtPlugin->PluginCode('xt_orders_invoices:_buildData_bottom')) ? eval($plugin_code) : false;
		
		unset($data['config']['_store_smtp_auth']);
        unset($data['config']['_store_smtp_host']);
        unset($data['config']['_store_smtp_port']);
        unset($data['config']['_store_smtp_username']);
		unset($data['config']['_store_smtp_password']);
		
        return $data;
    }

    protected function _buildDataByInvoiceId($id)
    {
        global $db;

        $invoice = $db->Execute("SELECT i.*, o.shop_id FROM " . $this->_table . " AS i INNER JOIN " . TABLE_ORDERS . " AS o ON i.orders_id=o.orders_id WHERE ".COL_INVOICE_ID."=?", array($id));
        if (!$invoice->RecordCount()) {
            return false;
        }

        return $this->_buildData($invoice->fields);
    }

    protected function _buildDataByOrderId($orderId)
    {
        global $db;

        $invoice = $db->Execute("SELECT i.*, o.shop_id FROM " . $this->_table . " AS i INNER JOIN " . TABLE_ORDERS . " AS o ON i.orders_id=o.orders_id WHERE i.orders_id=? ORDER BY i.invoice_id DESC ", array($orderId));
        if (!$invoice->RecordCount()) {
            return false;
        }

        return $this->_buildData($invoice->fields);
    }

    public function getStartIdForm($data)
    {
        $form = new PhpExt_Form_FormPanel('invoiceStartIdForm');

        // shop ermitteln
        $shopId = $data['shop_id'];
        // hat der shop separaten kreis ?
        $separateAssignment = $this->isSeparateAssignmentForShop($shopId);

        $fieldTitle = 'TEXT_INVOICE_START_ID_GLOBAL';
        $storeName = "";
        if ($separateAssignment)
        {
            $fieldTitle = 'TEXT_INVOICE_START_ID_SHOP';
            global $store_handler;
            $storeName = $store_handler->getStoreName($shopId);
        }

        $field = PhpExt_Form_TextField::createTextField('invoice_start_id', __define($fieldTitle)." <span style='font-weight:bold'>".$storeName."</span>", 'invoice_start_id');
        $field->setValue(1)
                ->setMaxLength(6)
                ->setLabelCssStyle('width: 150px;')
                ->setWidth(75)
                ->setRegEx(PhpExt_Javascript::inlineStm('new RegExp(/^[0-9]*$/)'));
        $form->addItem($field);

        $shopIdField = PhpExt_Form_Hidden::createHidden('shop_id', $data['shop_id'], 'shop_id');
        $form->addItem($shopIdField);

        $form->setRenderTo(PhpExt_Javascript::variable("Ext.get('form_div')"));
        $js = PhpExt_Ext::OnReady($form->getJavascript(false, "invoiceStartIdForm"));

        return '<script type="text/javascript">' . $js . '</script><div id="form_div"></div>';
    }

    public function saveStartIdForm($data)
    {
        $startNo = (int) $data['invoice_start_id'];
        if (!$startNo) {
            $startNo = 1;
        }

        $r = new stdClass();
        $r->startNo = $startNo;
        $r->withComment = intval($data['withComment']);
        return json_encode($r);
    }

    public function getCommentForm($data)
    {
        $form = new PhpExt_Form_FormPanel('invoiceCommentForm');

        $field = PhpExt_Form_TextArea::createTextArea('invoice_comment', __define('TEXT_XT_ORDERS_INVOICES_COMMENT'), 'invoiceComment');
        $field->setValue("")
            ->setMaxLength(1024)
            ->setCssStyle('width: 300px;height:120px;')
            ->setLabelCssStyle('width: 100px;');
        $form->addItem($field);

        $form->setRenderTo(PhpExt_Javascript::variable("Ext.get('comment_form_div')"));
        $js = PhpExt_Ext::OnReady($form->getJavascript(false, "invoiceCommentForm"));

        return '<script type="text/javascript">' . $js . '</script><div id="comment_form_div"></div>';
    }

    public function getEmailForm()
    {
        $id = (int) $this->url_data['id'];
        $data = $this->_buildDataByInvoiceId($id);
        if (!$data) {
            return false;
        }

        $form = new PhpExt_Form_FormPanel('invoiceEditForm');
        $form->addItem(PhpExt_Form_Hidden::createHidden(COL_INVOICE_ID, $id));

        $field = PhpExt_Form_TextArea::createTextArea('email_recipients', __define('TEXT_EMAIL_RECIPIENTS'), 'email_recipients');
        $field->setValue($data['order']['order_customer']['customers_email_address'])
                ->setWidth(200)
                ->setHeight(100);
        $form->addItem($field);

        $form->setRenderTo(PhpExt_Javascript::variable("Ext.get('form_div')"));
        $js = PhpExt_Ext::OnReady($form->getJavascript(false, "invoiceEditForm"));

        return '<script type="text/javascript">' . $js . '</script><div id="form_div"></div>';
    }

    public function getEmailFormHandler()
    {
        $extF = new ExtFunctions();
        $extF->setCode('editInvoice');
        $window = $extF->_RemoteWindow("TEXT_XT_ORDERS_INVOICES", "XT_ORDERS_INVOICES_TEXT_EMAIL", "adminHandler.php?plugin=xt_orders_invoices&load_section=xt_orders_invoices&pg=getEmailForm&id='+record.id+'", '', array(), 350, 250, '');
        $add_to_url = (isset($_SESSION['admin_user']['admin_key']))? '&sec='.$_SESSION['admin_user']['admin_key']: '';
        $saveBtn = PhpExt_Button::createTextButton(
                        __define('TEXT_EMAIL_SEND'), new PhpExt_Handler(PhpExt_Javascript::stm("
                            this.setDisabled(true);
                            var btn = this
                            var conn = new Ext.data.Connection();
                            conn.request({
                                url: 'adminHandler.php?plugin=xt_orders_invoices&load_section=xt_orders_invoices".$add_to_url."&pg=sendPdf',
                                method: 'POST',
                                params: Ext.ComponentMgr.get('invoiceEditForm').getForm().getValues(),
                                error: function(responseObject) {
                                    Ext.Msg.alert('" . __define('TEXT_ALERT') . "', '" . __define('TEXT_NO_SUCCESS') . "');
                                    btn.setDisabled(false);
                                },
                                waitMsg: 'SENDING...',
                                success: function(responseObject) {
                                    if (new_window) { new_window.destroy() } else { this.destroy() }
                                    var jObj = JSON.parse(responseObject.responseText);
                                    if (jObj.success!=true) Ext.Msg.alert('" . __define('TEXT_ALERT') . "', '" . __define('TEXT_NO_SUCCESS') . ": ' + jObj.msg)
                                    else Ext.Msg.alert('" . __define('TEXT_ALERT') . "','" . __define('TEXT_SUCCESS') . "');
                                    btn.setDisabled(false);
                                }
                            });"
                        ))
        );
        $saveBtn->setIcon('images/icons/email.png')
                ->setIconCssClass("x-btn-text");
        $window->addButton($saveBtn);

        return 'if (typeof(Ext.WindowMgr.get("editInvoiceRemoteWindow")) != "undefined") { Ext.WindowMgr.get("editInvoiceRemoteWindow").destroy(); } ' . $window->getJavascript(false, 'new_window') . ' new_window.show();';
    }

    public function isSeparateAssignmentForShop($shopId)
    {
        if ($shopId)
        {
            global $db;
            $separateAssignment = $db->GetOne("SELECT `config_value` FROM " . TABLE_PLUGIN_CONFIGURATION . " WHERE `config_key`= 'XT_ORDERS_INVOICE_SEPARATE_NUMBER_ASSIGNMENT' AND `shop_id`=?", array($shopId) );
            if (!empty($separateAssignment) ) return true;
        }
        return false;
    }

    public function isSeparateAssignmentForOrder($orderId)
    {
        global $db;
        $shopId = $db->GetOne("SELECT `shop_id` FROM " . TABLE_ORDERS . " WHERE `orders_id`= ?",array($orderId) );
        return $this->isSeparateAssignmentForShop($shopId);
    }

    public function isSeparateAssignmentStartedForShop($shopId)
    {
        if ($shopId)
        {
            global $db;
            $separateAssignmentProcessed = $db->GetOne("SELECT `config_value` FROM " . TABLE_PLUGIN_CONFIGURATION . " WHERE `config_key`= 'XT_ORDERS_INVOICE_SEPARATE_NUMBER_ASSIGNMENT_LAST_USED' AND `shop_id`=?", array($shopId) );
            if (!empty($separateAssignmentProcessed) ) return true;
        }
        return false;
    }

    private function _delete($invoice_id = null, $orders_id = null)
    {
        global $db;

        if(is_null($orders_id) && is_null($invoice_id)) return;

        if($orders_id)
        {
            $invoice_id = $db->GetOne("SELECT ".COL_INVOICE_ID." FROM ".TABLE_ORDERS_INVOICES. " WHERE orders_id=?", array((int)$orders_id));
        }
        if($invoice_id)
        {
            $db->Execute("DELETE FROM ".TABLE_ORDERS_INVOICES_PRODUCTS. " WHERE ".COL_INVOICE_ID."=?", array((int)$invoice_id));
            $db->Execute("DELETE FROM ".TABLE_ORDERS_INVOICES         . " WHERE ".COL_INVOICE_ID."=?", array((int)$invoice_id));
        }
    }
    function smarty_function_eval ($params, &$smarty)
    {
        if(empty($smarty))
        {
            $smarty = new Smarty();
            $smarty->setCompileDir(_SRV_WEBROOT . 'templates_c');
        }
    	if (!isset($params['var'])) {
    		trigger_error("eval: missing 'var' parameter");
    		return '';
    	}
    
    	if ($params['var'] == '') {
    		return '';
    	}

        try
        {
            $_contents = $smarty->fetch('string:' . $params['var']);
        } catch (\Smarty\Exception $e)
        {
            $_contents = $e->getMessage();
        }

        if (!empty($params['assign'])) {
    		$smarty->assign($params['assign'], $_contents);
    	} else {
    		return $_contents;
    	}
    }

}
