<?php

global $current_product_id,$xtLink;
$tpl_data = array();

$tpl_data['link_priceinquiry'] = $xtLink->_link(array('page'=> 'inquiry','params'=>'info='.$current_product_id));

$tpl = 'product_info_button.html';
$template = new Template();
$template->getTemplatePath($tpl, 'xt_priceinquiry', '', 'plugin');

$tpl = $template->getTemplate('xt_priceinquiry_smarty', $tpl, $tpl_data);

echo $tpl;