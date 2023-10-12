<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS.'xt_reviews/hooks/page_registry_php_bottom.php';

if(!defined('REVIEWS_UPDATER'))
    xt_reviews_install();

function xt_reviews_install()
{
    global $db;

    $db->Execute("DELETE FROM " . TABLE_ADMIN_NAVIGATION . " WHERE text in ('xt_reviews')");
    $db->Execute("INSERT INTO " . TABLE_ADMIN_NAVIGATION . " (`pid` ,`text` ,`icon` ,`url_i` ,`url_d` ,`sortorder` ,`parent` ,`type` ,`navtype` ,`iconCls`) VALUES (NULL , 'xt_reviews', 'images/icons/user_comment.png', '&plugin=xt_reviews', 'adminHandler.php', '4000', 'shop', 'I', 'W', 'far fa-comments');");
    $db->Execute("UPDATE " . DB_PREFIX . "_products SET products_average_rating = 0, products_rating_count=0");

    $sql = 'CREATE TABLE IF NOT EXISTS `' . DB_PREFIX . "_products_reviews` (
  `review_id` int(11) NOT NULL auto_increment,
  `products_id` int(11) NOT NULL,
  `customers_id` int(11) NOT NULL,
  `orders_id` int(11) NOT NULL default '0',
  `store_id` int(11) default '0',
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
) ENGINE=" . DB_STORAGE_ENGINE . " DEFAULT CHARSET=utf8";

    $db->Execute($sql);

    $sql = 'CREATE TABLE IF NOT EXISTS `' . DB_PREFIX . "_products_reviews_permission` (
  `pid` int(11) NOT NULL,
  `permission` tinyint(1) DEFAULT '0',
  `pgroup` varchar(255) NOT NULL,
  PRIMARY KEY (`pid`,`pgroup`)
) ENGINE=" . DB_STORAGE_ENGINE . " DEFAULT CHARSET=utf8";

    $db->Execute($sql);

    $idx = $db->GetOne("SHOW INDEXES FROM ".TABLE_PRODUCTS_REVIEWS. " WHERE Key_name = 'idx_status'");
    if(empty($idx))
        $db->Execute("ALTER TABLE ".TABLE_PRODUCTS_REVIEWS." ADD INDEX `idx_status` (`review_status` ASC) ");

    $idx = $db->GetOne("SHOW INDEXES FROM ".TABLE_PRODUCTS_REVIEWS. " WHERE Key_name = 'idx_customers_id'");
    if(empty($idx))
        $db->Execute("ALTER TABLE ".TABLE_PRODUCTS_REVIEWS." ADD INDEX `idx_customers_id` (`customers_id` ASC) ");

    $idx = $db->GetOne("SHOW INDEXES FROM ".TABLE_PRODUCTS_REVIEWS. " WHERE Key_name = 'idx_products_id'");
    if(empty($idx))
        $db->Execute("ALTER TABLE ".TABLE_PRODUCTS_REVIEWS." ADD INDEX `idx_products_id` (`products_id` ASC) ");

    $idx = $db->GetOne("SHOW INDEXES FROM ".TABLE_PRODUCTS_REVIEWS. " WHERE Key_name = 'idx_orders_id'");
    if(empty($idx))
        $db->Execute("ALTER TABLE ".TABLE_PRODUCTS_REVIEWS." ADD INDEX `idx_orders_id` (`orders_id` ASC) ");


    $langs = array('de', 'en');
    $tpls = array('review-notification-mail');
    $mail_dir = _SRV_WEBROOT . 'plugins/xt_reviews/installer/mails/';
    _installMailTemplates($langs, $tpls, $mail_dir);

    $content_dir = _SRV_WEBROOT . 'plugins/xt_reviews/installer/content/';
    xt_reviews_install_content($langs, $content_dir);
}

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

function xt_reviews_install_content($langs, $content_dir)
{
    global $store_handler, $db;

    $_content = new content();
    $_content->store_field_exists = true;
    $obj = $_content->_set([], 'new');
    $new_id = $obj->new_id;

    $data = [
        'content_id' => $new_id,
        'content_parent' => 0,
        'content_status' => 1,
        'content_hook' => 0,
        'content_form' => '',
        'content_image' => '',
        'content_sort' => 0,
    ];

    foreach($store_handler->getStores() as $store)
    {
        foreach ($langs as $lang)
        {
            $body = file_exists($content_dir . $lang . '/' .'body.html')  ? _getFileContent($content_dir . $lang . '/' .'body.html')  : '';
            $title =  file_exists($content_dir . $lang . '/' . 'title.txt') ? _getFileContent($content_dir . $lang . '/' . 'title.txt') : '';

            $merge_data = [
                'content_title_store'.$store['id'].'_'.$lang        => $title,
                'content_body_store'.$store['id'].'_'.$lang         => $body,
                'content_store_id_store'.$store['id'].'_'.$lang     => $store['id']
            ];

            $data = array_merge($data, $merge_data);
        }
    }

    $obj = $_content->_set($data, 'edit');

    $db->Execute("UPDATE ".TABLE_PLUGIN_CONFIGURATION." SET config_value = ? WHERE config_key = 'XT_REVIEWS_INFO_CONTENT_ID'", [$new_id]);
}

function _getFileContent($filename) {
    $handle = fopen($filename, 'rb');
    $content = fread($handle, filesize($filename));
    fclose($handle);
    return $content;
}

function xt_reviews_update_order_ids()
{
    require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS.'xt_reviews/classes/class.xt_reviews.php';

    global $db, $store_handler;

    $max_stores = 1;
    $lines = file(_SRV_WEBROOT.'lic/license.txt');
    foreach($lines as $line) {
        $needle = 'maxstores: ';
        if(strpos($line, 'maxstores: ') === 0)
        {
            $max_stores = (int) explode($needle, $line)[1];
        }
    }

    $no_order_id_reviews = $db->GetAssoc("SELECT review_id, products_id, customers_id FROM ".TABLE_PRODUCTS_REVIEWS." WHERE orders_id = 0 AND customers_id > 0");

    $xt_reviews = new xt_reviews();
    foreach($no_order_id_reviews as $review_id => $details)
    {
        $order_id = xt_reviews::getLastOrderId($details['products_id'], $details['customers_id']);
        if($order_id > 0)
        {
            // mit der orders_id haben wir auch die shop_id
            $shop_id = $db->GetOne('SELECT shop_id FROM '.TABLE_ORDERS.' WHERE orders_id = ?', [$order_id]);
            $db->Execute("UPDATE ".TABLE_PRODUCTS_REVIEWS." SET orders_id = ?, shop_id = ? WHERE review_id = ?", [$order_id, $shop_id, $review_id]);
        }
        else {
            // wir versuchen eine shop_id zu ermittlen
            if($max_stores == 1)
            {
                $shop_id = $store_handler->shop_id;
                $db->Execute("UPDATE ".TABLE_PRODUCTS_REVIEWS." SET shop_id = ? WHERE review_id = ?", [$shop_id, $review_id]);
            }
            else if(!empty($details['customers_id'])) {
                // über den kunden wenn vorhanden
                $shop_id = $db->GetOne('SELECT shop_id FROM '.TABLE_CUSTOMERS.' WHERE customers_id = ?', [$details['customers_id']]);
                $db->Execute("UPDATE ".TABLE_PRODUCTS_REVIEWS." SET shop_id = ? WHERE review_id = ?", [$shop_id, $review_id]);
            }
            else {
                // wir wissen nichts
            }
        }

    }
}

function xt_reviews_update_shop_ids()
{
    require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS.'xt_reviews/classes/class.xt_reviews.php';

    global $db, $store_handler;

    $no_order_id_reviews = $db->GetAssoc("SELECT review_id, products_id, customers_id FROM ".TABLE_PRODUCTS_REVIEWS." WHERE orders_id = 0 AND customers_id > 0");

    $xt_reviews = new xt_reviews();
    foreach($no_order_id_reviews as $review_id => $details)
    {
        $order_id = xt_reviews::getLastOrderId($details['products_id'], $details['customers_id']);
        if($order_id > 0)
        {
            // mit der orders_id haben wir auch die shop_id
            $shop_id = $db->GetOne('SELECT shop_id FROM '.TABLE_ORDERS.' WHERE orders_id = ?', [$order_id]);
            $db->Execute("UPDATE ".TABLE_PRODUCTS_REVIEWS." SET orders_id = ?, shop_id = ? WHERE review_id = ?", [$order_id, $shop_id, $review_id]);
        }
        else {
            // wir versuchen eine shop_id zu ermittlen
            $lines = file(_SRV_WEBROOT.'lic/license.txt');
            foreach($lines as $line) {
                $needle = 'maxstores: ';
                if(strpos($line, 'maxstores: ') === 0)
                {
                    $max_stores = (int) explode($needle, $line)[1];
                    if($max_stores == 1)
                    {
                        $shop_id = $store_handler->shop_id;
                        $db->Execute("UPDATE ".TABLE_PRODUCTS_REVIEWS." SET shop_id = ? WHERE review_id = ?", [$shop_id, $review_id]);
                    }
                    else {
                        // hm wissen wir nicht
                    }
                }
            }
        }

    }
}

