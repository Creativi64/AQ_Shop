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

class acl{

    var $password_min_length = 10;

    function __construct() {
        $this->_errorMsg='';
    }

    function reset_admin_password($data) {
        global $db,$filter, $xtPlugin;

        require _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'classes/class.bruto_force_protection.php';
        $bruto_force = new bruto_force_protection();

        $reset_email = $filter->_filter($data['reset_email']);
        $captcha = $filter->_filter($data['captcha']);
        if ($reset_email == '' or $captcha == '') {
            $this->_errorMsg=__text('ERROR_LOGIN_EMPTY_PARAMS');

            return false;
        }

        // ip block
        if(array_key_exists("HTTP_X_FORWARDED_FOR", $_SERVER) && $_SERVER["HTTP_X_FORWARDED_FOR"]){
            $customers_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        }else{
            $customers_ip = $_SERVER["REMOTE_ADDR"];
        }

        // bruto force
        if (!$bruto_force->_isLocked($reset_email) && !$bruto_force->_isLocked($customers_ip)) {

            include _SRV_WEBROOT.'/xtFramework/library/captcha/php-captcha.inc.php';
            if (PhpCaptcha::Validate($captcha)) {


                // check user
                $query = "SELECT * FROM ".TABLE_ADMIN_ACL_AREA_USER." WHERE email=?  and status='1'";
                $rs = $db->Execute($query, array($reset_email));
                if ($rs->RecordCount()!=1) {

                    $bruto_force->escalateLoginFail($reset_email,'1','admin_wrong_email');
                    $bruto_force->escalateLoginFail($customers_ip,'1','admin_wrong_email');

                    $this->_errorMsg=_RESET_PASS_WRONG_INPUT;
                    return false;
                } else {

                    $this->ResetPassword($rs->fields['user_id'],$reset_email);
                    $this->_successMsg = SUCCESS_CAPTCHA_VALID;


                    return true;
                }

            } else {
                $bruto_force->escalateLoginFail($reset_email,'1','admin_wrong_captcha');
                $bruto_force->escalateLoginFail($customers_ip,'1','admin_wrong_captcha');

                $this->_errorMsg = sprintf(__text('ERROR_LOGIN_COUNT'),$bruto_force->failed,$bruto_force->lock_time);
            }


        } else {
            $this->_errorMsg = sprintf(__text('ERROR_LOGIN_LOCKED'),$bruto_force->lock_time);
        }


    }

    function checkCode($code)
    {	global $db;
        $ar = explode(":",$code);

        if (isset($ar[1]) && strlen($ar[1])==32) {

            $query = "SELECT * FROM ".TABLE_ADMIN_ACL_AREA_USER." WHERE user_id=? and password_request_key=? and password_request_key!=''";
            $rs = $db->Execute($query, array($ar[0], $ar[1]));
            if ($rs->RecordCount()>0)
            {

                $password = $this->generateRandomString($this->password_min_length,3);
                $pw = new xt_password();
                $enc_password = $pw->hash_password($password);
                $db->Execute(
                    "UPDATE ".TABLE_ADMIN_ACL_AREA_USER." SET password_request_key='',user_password=? WHERE user_id=?",
                    array($enc_password, $ar[0])
                );

                $mail = new xtMailer('new_password');
                $mail->_addReceiver($rs->fields['email'],'Admin');

                $mail->_assign('NEW_PASSWORD',$password);
                $mail->_sendMail();
                $this->_successMsg = SUCCESS_PASSWORD_SEND;
                header('Location: ' . 'login.php?action=reset_password');
                exit;
            }else{
                $this->_errorMsg= __text('ERROR_REMEMBER_KEY_ERROR');
            }
        } else {
            $this->_errorMsg= __text('ERROR_REMEMBER_KEY_ERROR');
        }
    }

    function ResetPassword($id,$reset_email)
    {
        global $db,$filter,$xtLink;

        $request_key = $this->generateRandomString(32,0);

        $db->Execute(
            "UPDATE ".TABLE_ADMIN_ACL_AREA_USER." SET password_request_key=? WHERE user_id=?",
            array($request_key, $id)
        );

        $mail = new xtMailer('password_optin');
        $mail->_addReceiver($reset_email,'Admin');

        $remember_link = $xtLink->_adminlink(array('default_page'=>'xtAdmin/reset_admin_password.php', 'params'=>'action=check_code&remember='.$id.':'.$request_key));

        $mail->_assign('remember_link',$remember_link);
        $mail->_sendMail();

    }

    function checkAdminKey()
    {
        global $logHandler,$xtPlugin;

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.':checkAdminKey')) ? eval($plugin_code) : false;

        if (isset($_SESSION['admin_user']['admin_key']) &&
            (
                $_SESSION['admin_user']['admin_key']==$_GET['sec'] || _SYSTEM_SECURITY_KEY==$_GET['seckey'] ||
                $_SESSION['admin_user']['admin_key']==$_POST['sec'] || _SYSTEM_SECURITY_KEY==$_POST['seckey']
            )// TODO vereinheitlichen von sec/seckey admin_key/_SYSTEM_SECURITY_KEY
        )
        {
            return true;
        }

        if (CSRF_PROTECTION=='debug')
        {
            $log_data = array();
            if (!isset($_GET['sec']) && !isset($_GET['seckey']) && !isset($_POST['sec']) && !isset($_POST['seckey']))
            {
                $log_data['msg'] = 'Get parameter sec/seckey not set';
            }
            else $log_data['msg'] ='Admin key doesn\'t match ';

            if (!isset($_GET['load_section'])) $section = $_SERVER['PHP_SELF'];
            else $section = $_GET['load_section'];

            if ($_GET['pg']) $section .=' / '.$_GET['pg'];

            $log_data['get'] = $_GET;
            $log_data['post'] = $_POST;

            $identification ='1000'; // default value to filter Admin Key failure
            $logHandler->_addLog('csrf',$section,$identification, json_encode($log_data) );
        }
        else if (CSRF_PROTECTION=='true')  die(__text('TEXT_WRONG_ADMIN_SESSION_SECURITY_KEY'));

    }

    function login($data) {
        global $db,$filter, $xtPlugin;

        require _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'classes/class.bruto_force_protection.php';
        $bruto_force = new bruto_force_protection();


        $handle = $filter->_filter($data['handle']);
        $passwd = $filter->_filter($data['passwd']);
        if ($handle == '' or $passwd == '') {
            $this->_errorMsg=__text('ERROR_LOGIN_EMPTY_PARAMS');
            return false;
        }

        // bruto force
        if (!$bruto_force->_isLocked($handle)) {

            // query for user
            $query = "SELECT user_id,name,u.group_id,ip_restriction,handle,u.user_password FROM ".TABLE_ADMIN_ACL_AREA_USER." u, ".TABLE_ADMIN_ACL_AREA_GROUPS." g WHERE u.group_id = g.group_id and u.handle=? and g.status='1' and u.status='1'";
            $rs = $db->Execute($query, array($handle));
            if ($rs->RecordCount()!=1) {
                // TODO implement bruto force check
                $bruto_force->escalateLoginFail($handle,'1','admin_login');
                $this->_errorMsg = sprintf(__text('ERROR_LOGIN_COUNT'),$bruto_force->failed,$bruto_force->lock_time);
                //$this->_errorMsg=_LOGIN_WRONG_INPUT;
                return false;
            } else {

                // check IP
                $admin_ip = $this->getCurrentIp();

                $ip_check = $this->validateAdminIp($rs->fields['ip_restriction'],$rs->fields['user_id'],true);
                if (!$ip_check) {
                    $this->_errorMsg = __text('ERROR_LOGIN_IP_MISSMATCH');
                    return false;
                }



                $pw = new xt_password();
                $pw_check = $pw->verify_admin_password($passwd, $rs->fields['user_password'], $rs->fields['user_id']);
                if ($pw_check===false) {
                    $bruto_force->escalateLoginFail($handle,'1','admin_login');
                    $this->_errorMsg = sprintf(__text('ERROR_LOGIN_COUNT'),$bruto_force->failed,$bruto_force->lock_time);
                    return false;
                }

                $_SESSION['admin_user']['user_id'] = $rs->fields['user_id'];
                $_SESSION['admin_user']['user_name'] = $rs->fields['handle'];
                $_SESSION['admin_user']['group_id'] = $rs->fields['group_id'];
                $_SESSION['admin_user']['group_name'] = $rs->fields['name'];
                $_SESSION['admin_user']['login_time'] = time();
                $_SESSION['admin_user']['admin_key'] = md5($_SESSION['admin_user']['login_time'].$rs->fields['user_id']);

                ($plugin_code = $xtPlugin->PluginCode(__CLASS__.':login_success_bottom')) ? eval($plugin_code) : false;
                if(isset($plugin_return_value))
                    return $plugin_return_value;

                header('Location: ' . 'ejsadmin.php');
                //return true;
            }
        } else {
            $this->_errorMsg = sprintf(__text('ERROR_LOGIN_LOCKED'),$bruto_force->lock_time);
        }


    }

    function getLoginTime() {
        return $_SESSION['admin_user']['login_time'];
    }
    function getRefrechTime() {
        return time() + SESSION_REFRESH_TIME;
    }

    function getUsername() {
        return $_SESSION['admin_user']['user_name'];
    }


    function logout() {
        unset($_SESSION['admin_user']);
    }

    function getCurrentIp() {
        $admin_ip = '::1';

        if (getenv("HTTP_X_FORWARDED_FOR"))
        {
            $admin_ip = getenv("HTTP_X_FORWARDED_FOR");
        }
        else if (getenv("REMOTE_ADDR"))
        {
            $admin_ip = getenv("REMOTE_ADDR");
        }
        return $admin_ip;
    }

    function validateAdminIp($allowed_ip,$admin_id,$addlog=false) {
        global $logHandler;

        $admin_ip = $this->getCurrentIp();

        if (strlen($allowed_ip)>2) {
            $allowed = explode(',',$allowed_ip);
            if (in_array($admin_ip, $allowed)) {
                return true;
            } else {
                if ($addlog) $logHandler->_addLog('error','login',$admin_id,array('error'=>'admin ip missmatch','admin ip'=>$admin_ip));
                return false;
            }
        } else {
            return true;
        }
    }

    function isLoggedIn() {
        global $db;

        if (isset($_SESSION['admin_user']['user_id'])) {

            // check if admin is still active, and validate ip
            $sql = "SELECT * FROM ".TABLE_ADMIN_ACL_AREA_USER." WHERE user_id=?";
            $record = $db->Execute($sql, array((int)$_SESSION['admin_user']['user_id']));
            if($record->RecordCount() == 1) {

                // check IP

                $ip_check = $this->validateAdminIp($record->fields['ip_restriction'],$_SESSION['admin_user']['user_id'],true);
                if ($ip_check) {
                    return true;
                } else {
                    // current ip not allowed anymore, logout
                    return false;
                }


            } else {

                // admin got deactivated, logout
                $this->logout();
                header('location: login.php');
                exit;
            }

            //return true;
        }
        return false;
    }

    function loadPermissions(){
        global $db;

        $sql = "SELECT * FROM ".TABLE_ADMIN_ACL_AREA_PERMISSIONS." ap, ".TABLE_ADMIN_ACL_AREA." a WHERE a.area_id = ap.area_id and ap.group_id=?";
        $record = $db->Execute($sql, array((int)$_SESSION['admin_user']['group_id']));
        if($record->RecordCount() > 0){
            while(!$record->EOF){

                $_SESSION['admin_user']['permissions'][$record->fields['area_name']] = $record->fields;

                $record->MoveNext();
            }$record->Close();
        }else{
            return false;
        }
    }

    function check_area_name($area){
        global $db;

        $sql = "SELECT * FROM ".TABLE_ADMIN_ACL_AREA." WHERE area_name =?";
        $record = $db->Execute($sql, array($area));
        if($record->RecordCount() == 0){
            $record = array('area_name'=>$area, 'category'=>'default');
            $db->AutoExecute(TABLE_ADMIN_ACL_AREA, $record, 'INSERT');
        }
    }

    /**
     * generate more secure token/passwords
     * @param number $length
     * @param number $specialSigns
     * @return string
     */
    function generateRandomString($length=32,$specialSigns = 0) {

        $newpass = "";
        $laenge=$length;
        $laengeS = $specialSigns;
        $string="ABCDEFGHIJKLMNPQRSTUVWXYZabcdefghijklmnpqrstuvwxyz123456789";
        $stringS = "!#$%&()*+,-./";

        mt_srand();

        for ($i=1; $i <= $laenge; $i++) {
            $newpass .= substr($string, mt_rand(0,strlen($string)-1), 1);
        }
        for ($i = 1; $i <= $laengeS; $i++) {
            $newpass .= substr($stringS, mt_rand(0, strlen($stringS) - 1), 1);
        }
        $newpass_split = str_split($newpass);
        shuffle($newpass_split);
        $newpass = implode($newpass_split);
        return $newpass;
    }



    function checkPermission($area,$type) {
        global $db;

        $this->check_area_name($area);

        if(_SYSTEM_ADMIN_RIGHTS=='session'){
            $rights = $_SESSION['admin_user']['permissions'][$area];
        }elseif(_SYSTEM_ADMIN_RIGHTS=='db'){
            $sql = "SELECT * FROM ".TABLE_ADMIN_ACL_AREA_PERMISSIONS." ap, ".TABLE_ADMIN_ACL_AREA." a WHERE a.area_id = ap.area_id and a.area_name=? and ap.group_id=?";
            $rs = $db->Execute($sql, array($area, (int)$_SESSION['admin_user']['group_id']));
            $rights = $rs->fields;
        }

        //fix:
        if($type=='view')
            $type = 'read';

        $type = 'acl_'.$type;

        if(_SYSTEM_ADMIN_PERMISSIONS=='whitelist'){

            if($_SESSION['admin_user']['user_id']==1){
                return true;
            }

            if(!is_array($rights)){
                return false;
            }

            if ($rights[$type]=='1') {
                return true;
            } else {
                return false;
            }

        }elseif(_SYSTEM_ADMIN_PERMISSIONS=='blacklist'){

            if($_SESSION['admin_user']['user_id']==1){
                return true;
            }

            if(!is_array($rights)){
                return true;
            }

            if ($rights[$type]=='1') {
                return false;
            } else {
                return true;
            }

        }

    }
}