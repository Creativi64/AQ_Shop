<?php
/*
* Smarty plugin
* -------------------------------------------------------------
* Purpose: round number to given precision
* -------------------------------------------------------------
*/
function smarty_modifier_number_format_prec($value, $max_precision = 2, $dec_point = null, $thousands_sep = null)
{
    global $currency;

    $max_precision = (int) $max_precision;
    $precision = $max_precision;
    if(is_null($dec_point)) $dec_point = $currency->dec_point;
    if(is_null($thousands_sep)) $thousands_sep = $currency->thousands_sep;

    if(is_int($max_precision))
    {
        $precision = 0;

        for (; $precision <= $max_precision; $precision++)
        {
            $value_max_prec = round($value, $max_precision);
            $value_cur_prec = round($value, $precision);
            if ($value_max_prec == $value_cur_prec)
            {
                break;
            }
        }
    }

    $value = number_format($value, $precision, $dec_point, $thousands_sep);
    return $value;
}
