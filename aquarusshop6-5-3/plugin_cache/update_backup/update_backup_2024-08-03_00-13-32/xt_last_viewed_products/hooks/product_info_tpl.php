<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $p_info;

if(constant('XT_LAST_VIEWED_PRODUCTS_IN_PRODUCTS') == 1)
{
    global $last_viewed_products;
    if(is_null($last_viewed_products)) $last_viewed_products = new last_viewed_products();

	$module_content = $last_viewed_products->getLastViewedProductListing();
	
	if(count($module_content) > 1){
		$tpl_data = ['_last_viewed_products' => $module_content];

		$tpl = 'xt_last_viewed_products.html';
		$template = new Template();
		$template->getTemplatePath($tpl, 'xt_last_viewed_products', '', 'plugin');

		$tmp_data = $template->getTemplate('xt_last_viewed_products_smarty', $tpl, $tpl_data);
		echo $tmp_data;
	}
}
