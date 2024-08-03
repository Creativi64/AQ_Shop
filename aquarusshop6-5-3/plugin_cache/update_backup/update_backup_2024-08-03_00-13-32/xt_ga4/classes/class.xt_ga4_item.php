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

class xt_ga4_item {

  protected $index = 1;
  protected $item_list_id = '';
  protected $item_list_name = '';



  public function set_name($name) {
    $this->item_name = $name;
  }

  public function set_id($id) {
    $this->item_id = $id;
  }

  public function set_price($price) {
    $this->price = number_format($price, 2, '.', '');
  }

  public function set_brand($brand) {
    $this->item_brand = $brand;
  }

  public function set_category($category) {
    $this->item_category = $category;
  }

  public function set_category2($category) {
    $this->item_category2 = $category;
  }

  public function set_category3($category) {
    $this->item_category3 = $category;
  }

  public function set_category4($category) {
    $this->item_category4 = $category;
  }

  public function set_quantity($quantity) {
    $this->quantity = round($quantity,0);
  }

  public function set_index($index) {
    $this->index = $index;
  }

  public function set_variant($variant) {
    $this->item_variant = $variant;
  }

  public function getItemDataLayerArray() {

    $item = [];
    $item=array('item_name'=>$this->item_name,
      'item_id'=>$this->item_id,
      'item_brand'=>$this->item_brand,
      'item_category'=>$this->item_category,
      'item_category2'=>$this->item_category2 ? $this->item_category2 : '',
      'item_category3'=>$this->item_category3 ? $this->item_category3 : '',
      'item_category4'=>$this->item_category4 ? $this->item_category4 : '',
      'item_variant'=>$this->item_variant ? $this->item_variant : '',
      'item_list_name'=>$this->item_list_name,
      'item_list_id'=>$this->item_list_id,
      'index'=>$this->index,
      'quantity'=>$this->quantity,
      'currency'=>'EUR',
      'price'=>$this->price);

    return $item;

  }



}
