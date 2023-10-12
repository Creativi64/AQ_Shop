<?php
defined('_VALID_CALL') or die('Direct Access is not allowed.');


global $db, $store_handler;

global $ADODB_THROW_EXCEPTIONS;
$ADODB_THROW_EXCEPTIONS = true;
try
{

    $db->Execute("
    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "_plg_twofa_users` (
      `user_id` int(11) NOT NULL,
      `auth_code` varchar(64) NOT NULL,
      `auth_status` int(1) NULL DEFAULT '0',
      UNIQUE INDEX `user_id` (`user_id`)
    ) ENGINE=".DB_STORAGE_ENGINE."  DEFAULT CHARSET=utf8; ");


    if (!$this->_FieldExists('plugin_xt_twofa_status', TABLE_ADMIN_ACL_AREA_USER))
    {
        $db->Execute("ALTER TABLE `" . TABLE_ADMIN_ACL_AREA_USER . "` ADD COLUMN `plugin_xt_twofa_status` INT(1) DEFAULT '0' ;");
    }


}
catch(Exception $e){
    error_log($e->getMessage());
    // ignore or update ?
    $output .= "<div style='border:1px solid red; background:#f0bcb8;padding:10px;'>Irgendetwas hat nicht funktioniert! Scheint, dass Tabellen bereits existieren!<br/>Something went wrong!<br/>&nbsp<br/>".$e->getMessage().'</div>';
}
$ADODB_THROW_EXCEPTIONS = false;



