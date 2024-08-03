<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $price, $db, $tax;

require_once(_SRV_WEBROOT . _SRV_WEB_FRAMEWORK . 'admin/classes/class.adminDB_DataSave.php');

/** @var  $order order */

$arr_coupon = $_SESSION['sess_coupon'];
if (is_array($arr_coupon)) {

    $data = array();
    $data['coupon_id'] = $arr_coupon['coupon_id'];
    $data['coupon_token_id'] = $arr_coupon['coupons_token_id'];
    $data['redeem_date'] = $order->order_data['date_purchased_plain'];
    $data['redeem_ip'] = $order->order_data['customers_ip'];
    $data['customers_id'] = $order->customer;
    $data['order_id'] = $order->oID;

    if ($arr_coupon['coupon_amount'] > 0) {
        $data['redeem_amount'] = $arr_coupon['coupon_amount'];
        $products_tax = $tax->data[$arr_coupon["coupon_tax_class"]];
        if($products_tax)
        {
            $data['redeem_amount'] = $price->_AddTax($data['redeem_amount'], $products_tax);
        }
    }
    else if($arr_coupon['coupon_percent'] > 0)
    {
        $coupon_total_before_discount = 0;
        $coupon_saving = 0;
        $cp = new xt_coupons();
        $cpic = $cp->_get_coupon_products_in_cart($_SESSION['sess_coupon']['coupon_id']);
        foreach ($_SESSION['cart']->show_content as &$pr)
        {

            $oldPrice = round($pr['products_price_before_discount']['plain'],2);
            $coupon_saving += ($oldPrice - $pr['products_price']['plain']) * $pr['products_quantity'];
        }
        $data['redeem_amount'] = abs($coupon_saving);
    }
    else if ($arr_coupon['coupon_free_shipping'] == 1) {
        $coupon_sub = $_SESSION['cart']->sub_content['xt_coupon'];

        $price_o = $coupon_sub['products_price'];
        $taxclass = $coupon_sub['products_tax_class'];
        // brutto berechnen !!

        $tax_data = $tax->data[$taxclass];

        $price_o = $price->_AddTax($price_o, $tax_data);

        if ($price_o < 0) $price_o *= -1;
        $data['redeem_amount'] = $price_o;
    }

    $obj = new stdClass;
    $o = new adminDB_DataSave(TABLE_COUPONS_REDEEM, $data, false, __CLASS__);
    $obj = $o->saveDataSet();

    if ($arr_coupon['coupons_token_id'] > 0) {
        unset($data);
        $data = array();
        $data['coupons_token_id'] = $arr_coupon['coupons_token_id'];
        $data['coupon_id'] = $arr_coupon['coupon_id'];
        $data['coupon_token_code'] = $arr_coupon['coupon_token_code'];
        /// RESTWERT todo
        if ($arr_coupon['coupon_amount_leftover'] > 0) {
            $data['coupon_token_amount'] = $arr_coupon['coupon_amount_leftover'];
        } else {
            $data['coupon_token_order_id'] = $order->oID;
            $data['coupon_token_amount'] = 0.0;
        }
        ///
        $data['coupon_token_status'] = 1;
        $obj = new stdClass;
        $o = new adminDB_DataSave(TABLE_COUPONS_TOKEN, $data, false, __CLASS__);
        $obj = $o->saveDataSet();
    }

    $sql = "select count(coupon_id) as c from " . TABLE_COUPONS_REDEEM . " where coupon_id = ?;";
    $rs = $db->execute($sql,array((int)$arr_coupon['coupon_id']));
    $count = $rs->fields('c');
    $sql = "update " . TABLE_COUPONS . " set coupon_order_ordered = ? where coupon_id = ?;";
    $db->execute($sql,array((int)$count,(int)$arr_coupon['coupon_id']));


    unset($_SESSION['sess_coupon']);
}
