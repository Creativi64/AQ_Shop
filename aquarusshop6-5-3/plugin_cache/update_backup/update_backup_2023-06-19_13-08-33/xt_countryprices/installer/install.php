<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT. _SRV_WEB_PLUGINS.'xt_countryprices/classes/constants.php';

global $db;

$db->Execute("
CREATE TABLE IF NOT EXISTS ".TABLE_PRODUCTS_PRICE_COUNTRY." (
  id int(11) NOT NULL auto_increment,
  products_id int(11) NOT NULL,
  country_code char(2) NOT NULL default 'DE',
  country_price decimal(15,4) NOT NULL,
  status int(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `products_id_country_code` (`products_id`,`country_code`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
");
