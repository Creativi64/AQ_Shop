<?php


defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;

$db->Execute("DELETE FROM ".TABLE_ADMIN_NAVIGATION." WHERE text in ('xt_reviews')");
$db->Execute("INSERT INTO ".TABLE_ADMIN_NAVIGATION." (`pid` ,`text` ,`icon` ,`url_i` ,`url_d` ,`sortorder` ,`parent` ,`type` ,`navtype` ,`iconCls`) VALUES (NULL , 'xt_reviews', 'images/icons/user_comment.png', '&plugin=xt_reviews', 'adminHandler.php', '4000', 'shop', 'I', 'W', 'far fa-comments');");
$db->Execute("UPDATE ".DB_PREFIX."_products SET products_average_rating = 0, products_rating_count=0");

$sql = 'CREATE TABLE IF NOT EXISTS `'.DB_PREFIX."_products_reviews` (
  `review_id` int(11) NOT NULL auto_increment,
  `products_id` int(11) NOT NULL,
  `customers_id` int(11) NOT NULL,
  `orders_id` int(11) NOT NULL default '0',
  `review_rating` int(1) NOT NULL,
  `review_date` datetime NOT NULL,
  `review_status` int(1) NOT NULL default '0',
  `language_code` char(2) NOT NULL,
  `review_text` text CHARACTER SET utf8mb4 NULL default NULL,
  `review_title` text CHARACTER SET utf8mb4 NULL default NULL,
  `review_source` varchar(64) default NULL,
  `admin_comment` text CHARACTER SET utf8mb4 NULL default NULL,
  PRIMARY KEY  (`review_id`),
  KEY `products_id_review_status` (`products_id`,`review_status`)
) ENGINE=".DB_STORAGE_ENGINE." DEFAULT CHARSET=utf8";

$db->Execute($sql);

$sql = 'CREATE TABLE IF NOT EXISTS `'.DB_PREFIX."_products_reviews_permission` (
  `pid` int(11) NOT NULL,
  `permission` tinyint(1) DEFAULT '0',
  `pgroup` varchar(255) NOT NULL,
  PRIMARY KEY (`pid`,`pgroup`)
) ENGINE=".DB_STORAGE_ENGINE." DEFAULT CHARSET=utf8";

$db->Execute($sql);


$langs = array('de','en');
$tpls = array('review-notification-mail');
$mail_dir = _SRV_WEBROOT.'plugins/xt_reviews/installer/mails/';
_installMailTemplates($langs, $tpls, $mail_dir);

function _installMailTemplates($langs, $tpls, $mail_dir) {
    global $db;

    foreach($tpls as $tpl)
    {
        $data = array(
            'tpl_type' => $tpl,
            'tpl_special' => '-1',
        );
        $c = (int) $db->GetOne("SELECT count(tpl_id) FROM ".TABLE_MAIL_TEMPLATES." where `tpl_type` = '".$data['tpl_type']."'");
        if ($c>0)
        {
            continue;
        }
        try {
            $db->AutoExecute(TABLE_MAIL_TEMPLATES ,$data);
        } catch (exception $e) {
            return $e->getMessage();
        }
        $tplId = $db->GetOne("SELECT `tpl_id` FROM `".TABLE_MAIL_TEMPLATES."` WHERE `tpl_type`='".$data['tpl_type']."'");

        foreach($langs as $lang)
        {
            $html = file_exists($mail_dir.$lang.'/'.$tpl.'_html.txt') ?  _getFileContent($mail_dir.$lang.'/'.$tpl.'_html.txt') : '';
            $txt = file_exists($mail_dir.$lang.'/'.$tpl.'_txt.txt') ?  _getFileContent($mail_dir.$lang.'/'.$tpl.'_txt.txt') : '';
            $subject = file_exists($mail_dir.$lang.'/'.$tpl.'_subject.txt') ?  _getFileContent($mail_dir.$lang.'/'.$tpl.'_subject.txt') : '';

            $data = array(
                'tpl_id' => $tplId,
                'language_code' => $lang,
                'mail_body_html' => $html,
                'mail_body_txt' => $txt,
                'mail_subject' => $subject,
            );
            try {
                $db->AutoExecute(TABLE_MAIL_TEMPLATES_CONTENT ,$data);
            } catch (exception $e) {
                return $e->getMessage();
            }
        }
    }
}

function _getFileContent($filename) {
    $handle = fopen($filename, 'rb');
    $content = fread($handle, filesize($filename));
    fclose($handle);
    return $content;
}