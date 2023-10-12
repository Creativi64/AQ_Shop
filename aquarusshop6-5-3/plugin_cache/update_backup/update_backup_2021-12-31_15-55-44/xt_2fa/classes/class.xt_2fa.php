<?php
/*
 #########################################################################
 #                       xt:Commerce Shopsoftware
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # Copyright 2007-2020 xt:Commerce International Ltd. All Rights Reserved.
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


class xt_2fa
{

    protected $_table = DB_PREFIX.'_plg_twofa_users';


    public function setPosition($position)
    {
        $this->position = $position;
    }


    function _getParams(){}

    /**
     * row action popup logic
     * @return string
     * @throws Exception
     */
    public function get2FaHandler() {

        $extF = new ExtFunctions();
        $extF->setCode('edit2FA');
        $window = $extF->_RemoteWindow("TEXT_2FA_LOGIN", "TEXT_2FA_TAB_TITLE", "adminHandler.php?plugin=xt_2fa&load_section=xt_2fa&pg=get2FAForm&id='+record.id+'", '', array(), 400, 600, '');
        $add_to_url = (isset($_SESSION['admin_user']['admin_key']))? '&sec='.$_SESSION['admin_user']['admin_key']: '';
        $saveBtn = PhpExt_Button::createTextButton(
            __define('TEXT_2FA_BUTTON_ACTIVATE'), new PhpExt_Handler(PhpExt_Javascript::stm("
                            this.setDisabled(true);
                            var btn = this
                            var conn = new Ext.data.Connection();
                            conn.request({
                                url: 'adminHandler.php?plugin=xt_2fa&load_section=xt_2fa".$add_to_url."&pg=checkAuthCode',
                                method: 'POST',
                                params: Ext.ComponentMgr.get('TwoFAauthForm').getForm().getValues(),
                                error: function(responseObject) {
                                    Ext.Msg.alert('" . __define('TEXT_ALERT') . "', '" . __define('TEXT_NO_SUCCESS') . "');
                                    btn.setDisabled(false);
                                },
                                waitMsg: 'SENDING...',
                                success: function(responseObject) {
                                    if (new_window) { new_window.destroy() } else { this.destroy() }
                                    var jObj = JSON.parse(responseObject.responseText);

                                    if (jObj.success!=true) {
                                        Ext.Msg.alert('" . __define('TEXT_ALERT') . "', '" . __define('TEXT_NO_SUCCESS') . ": ' + jObj.msg)
                                        } else {
                                        Ext.Msg.alert('" . __define('TEXT_SUCCESS') . "',jObj.msg);
                                        }
                                    btn.setDisabled(false);
                                }
                            });"
            ))
        );
        $saveBtn->setIcon('images/icons/email.png')
            ->setIconCssClass("x-btn-text");
        $window->addButton($saveBtn);

        return 'if (typeof(Ext.WindowMgr.get("edit2FARemoteWindow")) != "undefined") { Ext.WindowMgr.get("edit2FARemoteWindow").destroy(); } ' . $window->getJavascript(false, 'new_window') . ' new_window.show();';


    }

    /**
     * check if validation is activated for user id & check input
     * @param $user_id
     * @param $code
     * @return bool|false|int|string
     */
    public function validation($user_id, $code) {
        global $db;

        $rs = $db->Execute("SELECT * FROM ".TABLE_ADMIN_ACL_AREA_USER." aau, ".$this->_table." plg WHERE aau.user_id = plg.user_id and aau.user_id=?",$user_id);
        if ($rs->RecordCount()==1) {
            if ($rs->fields['plugin_xt_twofa_status']==1 && $rs->fields['auth_code']!='') {
                return $this->validateLogin($rs->fields['auth_code'],$code);
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    /**
     * validate secret/code combination
     * @param string $secret
     * @param string $code
     * @return bool
     * @throws \RobThree\Auth\TwoFactorAuthException
     */
    private function validateLogin($secret='',$code='') {

        $ga = new \RobThree\Auth\TwoFactorAuth(null,6,30,'sha1');

        if ($ga->verifyCode($secret, $code, 2)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * validate Auth Code for generated QRCode (activation/deactivation)
     * @return false|string
     */
    public function checkAuthCode() {
        global $db;

        $ga = new \RobThree\Auth\TwoFactorAuth(null,6,30,'sha1');

        if (isset($this->url_data['twofa_secret']) && isset($this->url_data['twofa_verify_code'])) {
            $secret = $this->url_data['twofa_secret'];
            $code =  $this->url_data['twofa_verify_code'];
            $user_id =  (int)$this->url_data['user_id'];
            if ($ga->verifyCode($secret, $code, 2)) {
                $db->Execute("UPDATE ".TABLE_ADMIN_ACL_AREA_USER." SET plugin_xt_twofa_status=1 WHERE user_id='".$user_id."'");
                $db->AutoExecute($this->_table,array('user_id'=>$user_id,'auth_code'=>$secret));
                $r = new stdClass();
                $r->msg = TEXT_2FA_SUCCESS;
                $r->success = true;
            } else {
                $r = new stdClass();
                $r->msg = TEXT_2FA_WRONG_CODE.$secret.$code;
                $r->success = false;
            }
            return json_encode($r);
        } elseif(isset($this->url_data['action']) && $this->url_data['action']=='deactivate' && isset($this->url_data['twofa_verify_code'])) {

            $code =  $this->url_data['twofa_verify_code'];
            $user_id =  (int)$this->url_data['user_id'];

            $rs = $db->Execute(
                "SELECT auth_code as secret FROM ".$this->_table." WHERE user_id=?",
                array($user_id)
            );
            if ($rs->RecordCount()!=1) {
                $r = new stdClass();
                $r->msg = TEXT_2FA_WRONG_CODE;
                $r->success = false;
                return json_encode($r);
            }

            if ($ga->verifyCode($rs->fields['secret'], $code, 2)) {

                $db->Execute("UPDATE ".TABLE_ADMIN_ACL_AREA_USER." SET plugin_xt_twofa_status=0 WHERE user_id=?",array($user_id));
                $db->Execute("DELETE FROM ".$this->_table." WHERE user_id=?",array($user_id));

                $r = new stdClass();
                $r->msg = TEXT_2FA_SUCCESS_REMOVED;
                $r->success = true;
            } else {
                $r = new stdClass();
                $r->msg = TEXT_2FA_WRONG_CODE;
                $r->success = false;
            }
            return json_encode($r);
        }
    }

    /**
     * 2FA Form
     * @return string
     * @throws Exception
     */
    public function get2FAForm() {
        global $db;

        $user_id = (int)$this->url_data['id'];


        $user = new acl_user();
        $user->setPosition('admin');
        $user_data = $user->_get($user_id);

        if ($user_data->data[0]['plugin_xt_twofa_status']==1) {

            $form = new PhpExt_Form_FormPanel('TwoFAauthForm');

            $html_output='<p>'.TEXT_2FA_DEACTIVATE_DESC.'</p>';

            $infoPanel = new PhpExt_Panel();
            $infoPanel->setHtml($html_output)->setBorder(false);
            $form->addItem($infoPanel);

            $form->addItem(PhpExt_Form_Hidden::createHidden('user_id', $user_id));
            $form->addItem(PhpExt_Form_Hidden::createHidden('action', 'deactivate'));

            $field = PhpExt_Form_TextArea::createTextArea('twofa_verify_code', __define('TEXT_2FA_AUTH_CODE'), 'twofa_verify_code');
            $field->setValue('')
                ->setFieldCssClass('')
                ->setWidth(150)
                ->setHeight(25);
            $form->addItem($field);


            $form->setRenderTo(PhpExt_Javascript::variable("Ext.get('twofa_form_div')"));
            $js = PhpExt_Ext::OnReady($form->getJavascript(false, "twofa_form_div"));

            return '<script type="text/javascript">' . $js . '</script><div id="twofa_form_div"></div>';



        } elseif (count($user_data->data)==1 && $user_data->data[0]['user_id']==$user_id) {
            $form = new PhpExt_Form_FormPanel('TwoFAauthForm');

            $html_output='<p>'.TEXT_2FA_SCAN_CODE_DESC.'</p>';

            $ga = new \RobThree\Auth\TwoFactorAuth(null,6,30,'sha1');

            if (!isset($_SESSION['admin_user']['plg_2fa_auth_secret_'.$user_id])) {
                $secret = $ga->createSecret();
                $_SESSION['admin_user']['plg_2fa_auth_secret_'.$user_id] = $secret;
            } else {
                $secret = $_SESSION['admin_user']['plg_2fa_auth_secret_'.$user_id];
            }

            $qrUrl = $ga->getQRCodeImageAsDataUri('xtCommerce '.$user_data->data[0]['handle'], $secret,200);

            $html_output.= "<img src=".$qrUrl." height=\"200px\" width=\"200px\"/><br/>";

            $html_output.='<p>Key: <input type="text" readonly value="'.$secret.'"></p>';
            $html_output.='<p>Server Time: '.date('m/d/Y h:i:s a', time()).'</p>';

            $infoPanel = new PhpExt_Panel();
            $infoPanel->setHtml($html_output)->setBorder(false);
            $form->addItem($infoPanel);

            $form->addItem(PhpExt_Form_Hidden::createHidden('user_id', $user_id));
            $form->addItem(PhpExt_Form_Hidden::createHidden('twofa_secret', $secret));

            $field = PhpExt_Form_TextArea::createTextArea('twofa_verify_code', __define('TEXT_2FA_AUTH_CODE'), 'twofa_verify_code');
            $field->setValue('')
                ->setWidth(150)
                ->setHeight(25);
            $form->addItem($field);


            $form->setRenderTo(PhpExt_Javascript::variable("Ext.get('twofa_form_div')"));
            $js = PhpExt_Ext::OnReady($form->getJavascript(false, "twofa_form_div"));

            return '<script type="text/javascript">' . $js . '</script><div id="twofa_form_div"></div>';
        }
    }
}