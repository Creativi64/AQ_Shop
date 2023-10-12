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

class xtMailer extends \PHPMailer\PHPMailer\PHPMailer {

	var $Mailer = _SYSTEM_MAIL_TYPE;
	var $Host = _STORE_SMTP_HOST;
	//var $SMTPAuth = _STORE_SMTP_AUTH; #0000668
	var $Username = _STORE_SMTP_USERNAME;
	var $Password = _STORE_SMTP_PASSWORD;
	var $Port = _STORE_SMTP_PORT;
	var $WordWrap = 75;
	var $SMTPKeepAlive = true;
	var $Sendmail = _SYSTEM_SENDMAIL_PATH;
	var $CharSet = 'UTF-8';
	var $SMTPSecure = _STORE_SMTP_SECURE;
	
	// var $SMTPDebug = '4';
	
	/* for local development (php 5.6 requires proper serverside certificate for openssl)
	var $SMTPOptions = array(
    					'ssl' => array(
        				'verify_peer' => false,
        				'verify_peer_name' => false,
        				'allow_self_signed' => true
    					));
    */ 
	
	protected $_table = TABLE_MAIL_TEMPLATES;
	protected $_table_lang = TABLE_MAIL_TEMPLATES_CONTENT;
	protected $_table_seo = null;
	protected $_master_key = 'tpl_id';
    public $shop_id;
    public $tplData;
    public $permission;
    public $language_code;
    public $Template;
    public $group_id;
    public $mail_type;
    public $perm_array;
    public $old_store;
    public $old_status;
    public $special;

    function __construct($mail_type, $lang = -1, $cGroup = -1, $special = -1, $shop_id = -1) {
		global $language, $store_handler,$xtPlugin, $page, $db;
		/** @var ADORecordSet $rs */

        if (_STORE_SMTP_AUTH=='true' || _STORE_SMTP_AUTH == 1) {
            $this->SMTPAuth=true; 
        } else {
            $this->SMTPAuth=false; 
        }
        
        // Fix multistore smtp credintals
        if ($shop_id !== -1 && !empty($shop_id)) {
        	$config = array();
        	$rs = $db->Execute("SELECT config_key, config_value FROM " . TABLE_CONFIGURATION_MULTI . (int)$shop_id .  " where config_key IN ('_STORE_SMTP_HOST', '_STORE_SMTP_PORT', '_STORE_SMTP_USERNAME', '_STORE_SMTP_PASSWORD', '_STORE_SMTP_AUTH');");
        	if($rs->RecordCount() > 0){
        		while (!$rs->EOF) {
        			$config[$rs->fields['config_key']] = $rs->fields['config_value'];
        			$rs->MoveNext();
        		}$rs->Close();
        	}
        	
        	if (!empty($config)) {
        		if (isset($config['_STORE_SMTP_HOST'])) {
        			$this->Host = $config['_STORE_SMTP_HOST'];
        		}
        		
        		if (isset($config['_STORE_SMTP_PORT'])) {
        			$this->Port = $config['_STORE_SMTP_PORT'];
        		}
        		
        		if (isset($config['_STORE_SMTP_USERNAME'])) {
        			$this->Username = $config['_STORE_SMTP_USERNAME'];
        		}
        		
        		if (isset($config['_STORE_SMTP_PASSWORD'])) {
        			$this->Password = $config['_STORE_SMTP_PASSWORD'];
        		}
        		
        		if (isset($config['_STORE_SMTP_AUTH'])) {
        			$this->SMTPAuth = ($config['_STORE_SMTP_AUTH'] == 'true' || $config['_STORE_SMTP_AUTH'] == 1) ? true : false;
        		}
        		
        		if (isset($config['_STORE_SMTP_SECURE'])) {
        			$this->SMTPSecure = $config['_STORE_SMTP_SECURE'];
        		}
        	}
        }
        
		$this->Template = new Smarty;

		$this->Template->force_compile = true;

        $this->Template->setCaching(Smarty::CACHING_OFF);

		$this->Template->compile_dir = _SRV_WEBROOT.'templates_c';
		
		$this->Template->addPluginsDir(array(
                _SRV_WEBROOT.'xtFramework/library/smarty/xt_plugins')
        );

		$this->mail_type = $mail_type;

        $langs = $language->_getLanguageList('code');

        if ($lang == -1) {
            $this->language_code = $language->code;
        } else if(array_key_exists($lang, $langs)){
            $this->language_code =  $lang;
        } else
            $this->language_code = $language->code;

			$this->special = $special;

			if ($cGroup == -1) {
				$this->group_id = $_SESSION['customer']->customers_status;
			} else {
				$this->group_id = (int) $cGroup;
			}
			if ($shop_id == -1) {
				$this->shop_id = $store_handler->shop_id;
			} else {
				$this->shop_id = (int) $shop_id;
			}

			if(USER_POSITION=='store' && $page->page_name != 'xt_soap' && $page->page_name != 'xt_office_connector'){
				$this->getPermission();
			}else{
				$this->_setStore($this->shop_id);
				$this->_setStatusId($this->group_id);
				$this->getPermission();
				$this->_resetStore();
				$this->_restoreStatusId();
			}
			($plugin_code = $xtPlugin->PluginCode(__CLASS__.':xtMailer_bottom')) ? eval($plugin_code) : false;
			if(isset($plugin_return_value))
			return $plugin_return_value;   
			 
			$this->assignGlobals();

	}

	function assignGlobals() {
		global $db,$store_handler,$xtPlugin, $template, $xtLink;
        /** @var ADORecordSet $rs */

		$assigns = array();
		$assigns['template_language'] = $this->language_code;
		
        $rs = $db->Execute("SELECT * FROM ".TABLE_CONFIGURATION_MULTI . $this->shop_id);

        $conf_data = array();

        while (!$rs->EOF) {
                $conf_data[$rs->fields['config_key']]=$rs->fields['config_value'];
                $rs->MoveNext();
        }

        $rs = $db->Execute("SELECT * FROM ".TABLE_CONFIGURATION_LANG_MULTI." WHERE store_id=? AND language_code=?",array($this->shop_id,$this->language_code));
        while (!$rs->EOF) {
            $conf_data[$rs->fields['config_key']]=$rs->fields['language_value'];
            $rs->MoveNext();
        }
        $rs->Close();

        $shop_url = $shop_url_path = $xtLink->_index();
        if (substr($shop_url_path, -1) != '/') $shop_url_path .= '/';
        $assigns['_system_base_url'] = $shop_url;
        $assigns['_system_logo_url'] = $shop_url_path.'media/logo/'.$conf_data['_STORE_LOGO'];
        $assigns['_system_footer_txt'] = $conf_data['_store_email_footer_txt_'.$this->language_code];
        $assigns['_system_footer_html'] = $conf_data['_store_email_footer_html_'.$this->language_code];
		$assigns['_store_claim'] = $conf_data['_STORE_STORE_CLAIM'];
		$assigns['_store_name'] = $conf_data['_STORE_NAME'];

		$assigns['_system_template'] = $conf_data['_STORE_DEFAULT_TEMPLATE'];

		if (file_exists(_SRV_WEBROOT._SRV_WEB_TEMPLATES.'/'.$conf_data['_STORE_DEFAULT_TEMPLATE'].'/css/mail.css'))
		{
			$assigns['_system_mail_css'] = $shop_url_path._SRV_WEB_TEMPLATES.$conf_data['_STORE_DEFAULT_TEMPLATE'].'/css/mail.css';
		}
		else {
			$assigns['_system_mail_css'] = $shop_url_path._SRV_WEB_TEMPLATES._SYSTEM_TEMPLATE.'/css/mail.css';
		}

		if(is_dir(_SRV_WEBROOT._SRV_WEB_TEMPLATES.$conf_data['_STORE_DEFAULT_TEMPLATE'].'/email'))
		{
			$assigns['_system_root_templates'] =_SRV_WEBROOT._SRV_WEB_TEMPLATES.$conf_data['_STORE_DEFAULT_TEMPLATE'];
		}
		else {
			$assigns['_system_root_templates'] =_SRV_WEBROOT._SRV_WEB_TEMPLATES._SYSTEM_TEMPLATE;
		}

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.':assignGlobals')) ? eval($plugin_code) : false;

		foreach ($assigns as $key => $val) {
			$this->Template->assign($key,$val);
		}
	}

	function getPermission(){
		global $store_handler, $customers_status, $xtPlugin;

		$this->perm_array = array(
			'shop_perm' => array(
				'type'=>'shop',
				'key'=>$this->_master_key,
				'value_type'=>'email',
				'pref'=>'e'
			),

			'group_perm' => array(
				'type'=>'group_permission',
				'key'=>$this->_master_key,
				'value_type'=>'email',
				'pref'=>'e'
			)
		);

		($plugin_code = $xtPlugin->PluginCode(__CLASS__.':getPermission')) ? eval($plugin_code) : false;

		$this->permission = new item_permission($this->perm_array);

		return $this->perm_array;
	}


	function _setSubject($subject) {
		$this->Subject = $subject;
	}

	function _setContent($txt, $html) {
		if ($html != '') {
			$this->IsHTML(true);
			$this->Body = $html;
			$this->AltBody = $txt;
		} else {
			$this->IsHTML(false);
			$this->Body = $txt;
		}
	}

	function _setFrom($address, $name = '') {
		$this->From = $address;
		$this->Sender = $address;
		if ($name != '')
		$this->FromName = $name;
	}

	function _addReceiver($address, $name = '') {
        try
        {
            $this->AddAddress($address, $name);
        } catch (\PHPMailer\PHPMailer\Exception $e)
        {
            global $logHandler;
            $log_data = array();
            $log_data['error'] = 'AddAddress | ' .$e->getCode().' | '.$e->getMessage();
            $logHandler->_addLog('error', 'email', '0', $log_data);
        }
    }

	function _addBCC($address, $name = '') {
        try
        {
            $this->AddBCC($address, $name);
        } catch (\PHPMailer\PHPMailer\Exception $e)
        {
            global $logHandler;
            $log_data = array();
            $log_data['error'] = 'AddAddress | ' .$e->getCode().' | '.$e->getMessage();
            $logHandler->_addLog('error', 'email', '0', $log_data);
        }
    }

	function _addReplyAddress($address, $name = '') {
        try
        {
            $this->AddReplyTo($address, $name);
        } catch (\PHPMailer\PHPMailer\Exception $e)
        {
            global $logHandler;
            $log_data = array();
            $log_data['error'] = 'AddReplyTo | ' .$e->getCode().' | '.$e->getMessage();
            $logHandler->_addLog('error', 'email', '0', $log_data);
        }
    }

	function _addAttachment($files, $name = '') {
		if (is_array($files)) {
			foreach ($files as $key => $file) {
                try
                {
                    $f_name = $name;
                    if (is_array($f_name)) $f_name = $name[$key];
                    $this->AddAttachment($file, $f_name);
                } catch (\PHPMailer\PHPMailer\Exception $e)
                {
                    global $logHandler;
                    $log_data = array();
                    $log_data['error'] = 'AddAttachment | ' .$e->getCode().' | '.$e->getMessage();
                    $logHandler->_addLog('error', 'email', '0', $log_data);
                }
            }
		}
		else {
            try
            {
                $this->AddAttachment($files, $name);
            } catch (\PHPMailer\PHPMailer\Exception $e)
            {
                global $logHandler;
                $log_data = array();
                $log_data['error'] = 'AddAttachment | ' .$e->getCode().' | '.$e->getMessage();
                $logHandler->_addLog('error', 'email', '0', $log_data);
            }
        }
	}

	function _assign($var, $value = '') {
		// broken emails due combination of html-minify and nl2br FXR-954-12490
        if(! defined('_SYSTEM_HTML_MINIFY_OPTION') || constant('_SYSTEM_HTML_MINIFY_OPTION')!=1)
			$value = $this->cleanDataNL2BR($value);
		$this->Template->assign($var, $value);
	}

	function cleanDataNL2BR($value,$t=''){
		if (is_string($value)) {
			// fix to nl2br -> doesn't work on db-string
			$value = str_replace(array('\\r\\n','\\r','\\n'),'<br/>',$value);
			$value = nl2br($value);
		}
		if (is_array($value) && count($value) > 0) {
			$new_value=array();
			foreach ($value as $k => $v) {
				$new_value[$k] = $this->cleanDataNL2BR($v,$k);
			}
			$value = $new_value;
		}
		return $value;
	}

    function prepareMail($from='', $fromName='') {
        global $logHandler, $isEmailTemplate;

        //$this->SMTPDebug = 2;
        //$this->Debugoutput = 'error_log';

        if ($this->mail_type!='none') {
            $this->tplData = $this->_getTPL();

            if (is_array($this->tplData) && isset($this->tplData['mail_body_html'])) {

                $subject = $txt_mail = '';
                try{
                    $isEmailTemplate = true;
                    $html_mail = $this->Template->fetch("eval:".$this->tplData['mail_body_html']);
                    //file_put_contents(_SRV_WEBROOT._SRV_WEB.'send_order.html',$html_mail);
                    //die();
                    $txt_mail = strip_tags($this->Template->fetch("eval:".$this->tplData['mail_body_txt']));
                    $subject = $this->Template->fetch("eval:".$this->tplData['mail_subject']);
                }
                catch (Exception $e)
                {
                    $msg_data_arr = [
                        'Error in E-Mail-Template',
                        'error' => $e->getMessage(),
                        'mail_type' => $this->mail_type,
                        'language_code' => $this->language_code,
                        'shop_id' => $this->shop_id,
                        'store_name' => $this->Template->getTemplateVars('_store_name'),
                    ];
                    $msg_data = print_r($msg_data_arr,true);
                    error_log($msg_data);
                    $logHandler->_addLog('error','email','0',$msg_data_arr);
                    $logHandler->sendDebugMail($msg_data,'Error in E-Mail-Template');
                    return false;
                }
                finally{
                    $isEmailTemplate = false;
                }
                //empty email should not sent
                if(!empty($html_mail)){
                    $this->_setSubject($subject);
                    $this->_setContent($txt_mail, $html_mail);
                    if (is_array($this->tplData['attachments']))
                        $this->_addAttachment($this->tplData['attachments']);

                    // set from
                    if (!empty($from)) {
                        $this->_setFrom($from, !empty($fromName) ? $fromName : $from);
                    } elseif ($this->tplData['email_from']!='') {
                        $admin_email_name = !empty($this->tplData['email_from_name']) ? $this->tplData['email_from_name'] : _STORE_NAME;
                        $this->_setFrom($this->tplData['email_from'], $admin_email_name);
                    } else {
                        $this->_setFrom(_CORE_DEBUG_MAIL_ADDRESS, '');
                    }
                    if ($this->tplData['email_reply']!='') $this->_addReplyAddress($this->tplData['email_reply'], $this->tplData['email_reply_name']);
                    if ($this->tplData['email_forward'] != '') {
                        $emails = explode(',', $this->tplData['email_forward']);
                        foreach ($emails as $key => $val) {
                            $this->_addBCC($val);
                        }
                    }
                }
                else{
                    $log_data = array();
                    $log_data['error'] = $this->ErrorInfo;
                    $logHandler->_addLog('error','email','0',$log_data);
                    return false;
                }
            }
        }
        return true;
    }

    function sendMail($from='', $fromName='')
    {
        global $logHandler, $isEmailTemplate;

        //$this->SMTPDebug = 2;
        //$this->Debugoutput = 'error_log';

        try
        {
            if (!$this->Send())
            {
                $log_data = array();
                $log_data['error'] = $this->ErrorInfo;
                $logHandler->_addLog('error', 'email', '0', $log_data);
                return false;
            }
        }
        catch (\PHPMailer\PHPMailer\Exception $e)
        {
            $log_data = array();
            $log_data['error'] = $e->getCode().' | '.$e->getMessage();
            $logHandler->_addLog('error', 'email', '0', $log_data);
            return false;
        }

        return true;
    }
	
	function _sendMail($from='', $fromName='') {
		global $logHandler, $isEmailTemplate;

		//$this->SMTPDebug = 2;
		//$this->Debugoutput = 'error_log';

		if ($this->mail_type!='none') {
			$this->tplData = $this->_getTPL();

			if (is_array($this->tplData) && isset($this->tplData['mail_body_html'])) {

                $subject = $txt_mail = '';
                try{
                    $isEmailTemplate = true;
                    $html_mail = $this->Template->fetch("eval:".$this->tplData['mail_body_html']);
                    $txt_mail = strip_tags($this->Template->fetch("eval:".$this->tplData['mail_body_txt']));
                    $subject = $this->Template->fetch("eval:".$this->tplData['mail_subject']);
                }
                catch (Exception $e)
                {
                    $msg_data_arr = [
                        'Error in E-Mail-Template',
                        'error' => $e->getMessage(),
                        'mail_type' => $this->mail_type,
                        'language_code' => $this->language_code,
                        'shop_id' => $this->shop_id,
                        'store_name' => $this->Template->getTemplateVars('_store_name'),
                    ];
                    $msg_data = print_r($msg_data_arr,true);
                    error_log($msg_data);
                    $logHandler->_addLog('error','email','0',$msg_data_arr);
                    $logHandler->sendDebugMail($msg_data,'Error in E-Mail-Template');
                    return false;
                }
                finally{
                    $isEmailTemplate = false;
                }
				//empty email should not sent
				if(!empty($html_mail)){
					$this->_setSubject($subject);
					$this->_setContent($txt_mail, $html_mail);
					if (is_array($this->tplData['attachments']))
					$this->_addAttachment($this->tplData['attachments']);
	
					// set from
                    if (!empty($from)) {
                        $this->_setFrom($from, !empty($fromName) ? $fromName : $from);
                    } elseif ($this->tplData['email_from']!='') {
						$admin_email_name = !empty($this->tplData['email_from_name']) ? $this->tplData['email_from_name'] : _STORE_NAME;
						$this->_setFrom($this->tplData['email_from'], $admin_email_name);
					} else {
						$this->_setFrom(_CORE_DEBUG_MAIL_ADDRESS, '');
					}
					if ($this->tplData['email_reply']!='') $this->_addReplyAddress($this->tplData['email_reply'], $this->tplData['email_reply_name']);
					if ($this->tplData['email_forward'] != '') {
						$emails = explode(',', $this->tplData['email_forward']);
						foreach ($emails as $key => $val) {
							$this->_addBCC($val);
						}
					}
                    try
                    {
                        if (!$this->Send())
                        {
                            $log_data = array();
                            $log_data['error'] = $this->ErrorInfo;
                            $logHandler->_addLog('error', 'email', '0', $log_data);
                            return false;
                        }
                        else
                        {
                            // TODO success send logging
                            return true;
                        }
                    } catch (\PHPMailer\PHPMailer\Exception $e)
                    {
                        $log_data = array();
                        $log_data['error'] = $e->getCode().' | '.$e->getMessage();
                        $logHandler->_addLog('error', 'email', '0', $log_data);
                        return false;
                    }
                }
				else{
					$log_data = array();
					$log_data['error'] = $this->ErrorInfo;
					$logHandler->_addLog('error','email','0',$log_data);
                    return false;
				}
			}
		} else {
            try
            {
                if (!$this->Send())
                {
                    $log_data = array();
                    $log_data['error'] = $this->ErrorInfo;
                    $logHandler->_addLog('error', 'email', '0', $log_data);
                    return false;
                }
            } catch (\PHPMailer\PHPMailer\Exception $e)
            {
                $log_data = array();
                $log_data['error'] = $e->getCode().' | '.$e->getMessage();
                $logHandler->_addLog('error', 'email', '0', $log_data);
                return false;
            }
        }
		return true;
	}

	function _getTPL() {
		global $db,$logHandler;
        /** @var ADORecordSet $rs */

		$status = '';
		$status_all = '';
        $tpl_id = false;

		// order status set ?
		if ($this->special != -1) {

			$query = "SELECT * FROM " . TABLE_MAIL_TEMPLATES . " e ".$this->permission->_table." WHERE e.tpl_type=? and e.tpl_special=? ".$this->permission->_where." ";

			$rs = $db->Execute($query, array($this->mail_type, $this->special));
			if ($rs->RecordCount() > 0) {
				// matched
				$data = $rs->fields;
				$tpl_id = $data['tpl_id'];
			} else {
				// not matched, look for ALL
				$query = "SELECT * FROM " . TABLE_MAIL_TEMPLATES . " e ".$this->permission->_table." WHERE e.tpl_type=? and e.tpl_special='ALL' ".$this->permission->_where."";
				$rs = $db->Execute($query, array($this->mail_type));

				if ($rs->RecordCount() > 0) {
					// matched
					$data = $rs->fields;
					$tpl_id = $data['tpl_id'];
				} else {
					// not matched, look for global group permission and special
					$query = "SELECT * FROM " . TABLE_MAIL_TEMPLATES . " e ".$this->permission->_table." WHERE e.tpl_type=? and e.tpl_special=? ".$this->permission->_where."";
					$rs = $db->Execute($query, array($this->mail_type, $this->special));
					if ($rs->RecordCount() > 0) {
						// matched
						$data = $rs->fields;
						$tpl_id = $data['tpl_id'];
					} else {
						// not matched, get ALL entry
						$query = "SELECT * FROM " . TABLE_MAIL_TEMPLATES . " e ".$this->permission->_table." WHERE e.tpl_type=? and e.tpl_special='ALL' ".$this->permission->_where."";
						$rs = $db->Execute($query, array($this->mail_type));
						$tpl_id = $rs->fields['tpl_id'];
					}
				}
			}
		} else {

			$query = "SELECT * FROM " . TABLE_MAIL_TEMPLATES . " e ".$this->permission->_table." WHERE e.tpl_type=? ".$this->permission->_where."";

			$rs = $db->Execute($query, array($this->mail_type));
			if ($rs->RecordCount() > 0) {
				$data = $rs->fields;
				$tpl_id = $data['tpl_id'];
			} else {

				$query = "SELECT * FROM " . TABLE_MAIL_TEMPLATES . " e ".$this->permission->_table." WHERE e.tpl_type=? ".$this->permission->_where."";
				$rs = $db->Execute($query, array($this->mail_type));
				if ($rs->RecordCount() > 0) {
					$data = $rs->fields;
					$tpl_id = $data['tpl_id'];
				}
			}
		}
        // sorry, i didn't found a mail'
        if (!is_numeric($tpl_id) && _SYSTEM_MAIL_DEBUG == 'true') {
            $line = 'E-Mail Template not found in Db, check settings in Backend > Content > E-Mail-Manager. looked for:' . $this->mail_type . ' lang:' . $this->language_code . ' group:' . $this->group_id . ' Special:' . $this->special . ' Shop:' . $this->shop_id;
            //get shop name & URL:
            $query = "SELECT * FROM " . TABLE_MANDANT_CONFIG ." WHERE shop_id=?";
            $rs = $db->Execute($query, array($this->shop_id));
            if ($rs->RecordCount()>0) {
                $line .= ' ,'.$rs->fields['shop_ssl_domain'];
            }
            $logHandler->sendDebugMail($line,'E-Mail Template missing');
            $log_data = array();
            $log_data['error'] = $line;
            $logHandler->_addLog('error','email','0',$log_data);
		}
		
		// build array
		$query = "SELECT * FROM " . TABLE_MAIL_TEMPLATES_CONTENT . " mtc, ".TABLE_MAIL_TEMPLATES." mt WHERE mtc.tpl_id=mt.tpl_id and mtc.tpl_id=? and mtc.language_code=?";
		$rs = $db->Execute($query, array($tpl_id, $this->language_code));

		if ($rs->fields['email_from']=='') $rs->fields['email_from'] = _CORE_DEBUG_MAIL_ADDRESS;

		// get attachments
        include_once _SRV_WEBROOT . _SRV_WEB_FRAMEWORK . 'classes/class.email_to_media.php';
        $attachments = new email_to_media();
        $attachment_array = $attachments->getAttachments($tpl_id);

		if (is_array($attachment_array)) $rs->fields['attachments'] = $attachment_array;
		return $rs->fields;
	}

	
	public function _setStatusId($id) {
		global $customers_status;
		$this->old_status = $customers_status->customers_status_id;
		$customers_status->customers_status_id = $id;
	}
	
	public function _restoreStatusId() {
		global $customers_status;
		$customers_status->customers_status_id = $this->old_status;
		unset($this->old_status);
	}

    function _setStore($id){
       global $store_handler;

       $this->old_store = $store_handler->shop_id;
       $store_handler->shop_id = $id;
   }

   function _resetStore(){
       global $store_handler;

       $store_handler->shop_id = $this->old_store;
       unset($this->old_store);
   }
}

