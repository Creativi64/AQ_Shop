<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT. _SRV_WEB_PLUGINS.'xt_countryprices/classes/constants.php';

if (isset($_SESSION['customer']->customer_default_address['customers_country_code'])) {
    global $db;
    $record = $db->Execute("SELECT country_price 
					FROM ".TABLE_PRODUCTS_PRICE_COUNTRY." 
					WHERE products_id=? and 
					status='1' and 
					country_code=? 
					LIMIT 0,1",
        array((int)$this->data['products_id'],$_SESSION['customer']->customer_default_address['customers_country_code']));
    if($record->RecordCount() == 1){
        $products_price = $record->fields['country_price'];
        //return $true;
    }
}
