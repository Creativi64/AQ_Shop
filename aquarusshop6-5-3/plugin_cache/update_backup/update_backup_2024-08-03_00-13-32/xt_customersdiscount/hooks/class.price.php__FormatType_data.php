<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if(/*$_POST['action']!='update_product' &&*/ $data['format_type']=='graduated_discount')
{
    $cheapest_price = $data['cheapest_price'];
    $cheapest_price_otax = $data['cheapest_price_otax'];

    $cheapest_price = (string)$cheapest_price;
    $cheapest_price = (float)$cheapest_price;
    $cheapest_Fprice= $this->_StyleFormat($cheapest_price);

    $cheapest_price_otax = (string)$cheapest_price_otax;
    $cheapest_price_otax = (float)$cheapest_price_otax;
    $cheapest_Fprice_otax= $this->_StyleFormat($cheapest_price_otax);

    $price = $data['price'];
    $price_otax = $data['price_otax'];
    $old_price = $data['old_price'];
    $old_price_otax = $data['old_price_otax'];

    $price = (string)$price;
    $price = (float)$price;
    $Fprice= $this->_StyleFormat($price);

    $price_otax = (string)$price_otax;
    $price_otax = (float)$price_otax;
    $Fprice_otax= $this->_StyleFormat($price_otax);

    $old_price = (string)$old_price;
    $old_price = (float)$old_price;
    $old_Fprice= $this->_StyleFormat($old_price);

    $old_price_otax = (string)$old_price_otax;
    $old_price_otax = (float)$old_price_otax;
    $old_Fprice_otax= $this->_StyleFormat($old_price_otax);

    $saving_price = $old_price-$price;
    $saving_price = (string)$saving_price;
    $saving_price = (float)$saving_price;
    $saving_Fprice= $this->_StyleFormat($saving_price);


    $tpl_data1 = array('SPECIAL_PRICE_SAVE'=>array('formated'=>$saving_Fprice,'plain'=>$saving_price),'SPECIAL_PRICE' => array('formated'=>$Fprice,'plain'=>$price), 'SPECIAL_PRICE_OTAX'=>array('formated'=>$Fprice_otax,'plain'=>$price_otax),'OLD_PRICE'=>array('formated'=>$old_Fprice,'plain'=>$old_price),'OLD_PRICE_OTAX'=>array('formated'=>$old_Fprice_otax,'plain'=>$old_price_otax), 'date_expired' => $data['date_expired'], 'date_available' => $data['date_available']);
    $tpl_data2 = array('PRODUCTS_PRICE' => array('formated'=>$Fprice,'plain'=>$price), 'PRODUCTS_PRICE_OTAX'=>array('formated'=>$Fprice_otax,'plain'=>$price_otax),'CHEAPEST_PRICE'=>array('formated'=>$cheapest_Fprice,'plain'=>$cheapest_price),'CHEAPEST_PRICE_OTAX'=>array('formated'=>$cheapest_Fprice_otax,'plain'=>$cheapest_price_otax), 'date_expired' => $data['date_expired'], 'date_available' => $data['date_available']);

    $tpl_data = array_merge($tpl_data1, $tpl_data2);

    $tpl = 'price_graduated_discount.html';
}