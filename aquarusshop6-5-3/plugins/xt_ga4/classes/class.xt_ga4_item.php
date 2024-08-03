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

class xt_ga4_item
{

    protected mixed $index = 1;
    protected mixed $item_list_id = '';
    protected mixed $item_list_name = '';
    protected mixed $item_name = '';
    protected mixed $item_id = '';

    protected mixed $price = '';
    protected mixed $item_brand = '';
    protected mixed $item_category = '';
    protected mixed $item_category2 = '';
    protected mixed $item_category3 = '';
    protected mixed $item_category4 = '';

    protected mixed $quantity = '';
    protected mixed $item_variant = '';


    public function set_name($name)
    {
        $this->item_name = $name;
    }

    public function set_id($id)
    {
        $this->item_id = $id;
    }

    public function set_price($price)
    {
        $this->price = number_format($price, 2, '.', '');
    }

    public function set_brand($brand)
    {
        $this->item_brand = $brand;
    }

    public function set_category($category)
    {
        $this->item_category = $category;
    }

    public function set_category2($category)
    {
        $this->item_category2 = $category;
    }

    public function set_category3($category)
    {
        $this->item_category3 = $category;
    }

    public function set_category4($category)
    {
        $this->item_category4 = $category;
    }

    public function set_quantity($quantity)
    {
        $this->quantity = round($quantity, 0);
    }

    public function set_index($index)
    {
        $this->index = $index;
    }

    public function set_variant($variant)
    {
        $this->item_variant = $variant;
    }

    public function getItemDataLayerArray($currency_code = '')
    {
        global $currency;

        if(empty($currency_code))
            $currency_code = $currency->code;

        $item = [
            'item_name'         => $this->item_name,
            'item_id'           => $this->item_id,
            'item_brand'        => $this->item_brand,
            'item_category'     => $this->item_category,
            'item_category2'    => $this->item_category2 ? $this->item_category2 : '',
            'item_category3'    => $this->item_category3 ? $this->item_category3 : '',
            'item_category4'    => $this->item_category4 ? $this->item_category4 : '',
            'item_variant'      => $this->item_variant ? $this->item_variant : '',
            'item_list_name'    => $this->item_list_name,
            'item_list_id'      => $this->item_list_id,
            'index'             => $this->index,
            'quantity'          => $this->quantity,
            'currency'          => $currency_code,
            'price'             => $this->price
        ];

        return $item;
    }


}
