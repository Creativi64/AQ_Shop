<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if($_SESSION['filters_order']['filter_klarna_not_captured'] != "")
{
    $where_ar[] = "
                (payment_code = 'xt_klarna_kp' 
                    AND date_purchased > DATE_SUB(CURDATE(), INTERVAL 6 WEEK)
                    AND kp_order_fraud_status = 'ACCEPTED'
                    AND kp_order_status IN ('AUTHORIZED', 'PART_CAPTURED'))
                    ";
}
