<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

// if total is already zero, there must NOT be any further discount
if ($data['costs']['0']['payment_cost_discount']==1 && $_SESSION['cart']->total['plain']<=0)
{
  $data['costs']['0']['payment_price']=0;
}
