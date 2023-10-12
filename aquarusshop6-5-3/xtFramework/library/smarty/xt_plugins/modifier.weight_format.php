<?php
/*
* Smarty plugin
* -------------------------------------------------------------
* Purpose: weight converter, kg to other unit
* Usage:    In the template, use
            {$weight|weight_format}    =>    5,00 kg
            or
            {$weight|weight_format:"pound"}    =>    11,02 pound
            or
            {$weight|weight_format:"oz":4}    =>    176,3698 oz
            or
            {$weight|weight_format:"g":0:".":""}    =>    5000 g
* Params:    
            int       xt_weight        the weight in kg
            string    format           the format, the output shall be: kg, g, mg, t, oz, pound, st
            int       max_precision    the max rounding precision, 25 mg instead of 25,00 mg; but 25,5 mg with max_precision 2
                                       force precision with float eg  3.  >  25,000 mg
            string    dec_point        the decimal separator
            string    thousands_sep    the thousands separator    
* -------------------------------------------------------------
*/
function smarty_modifier_weight_format($xt_weight, $format = 'kg', $max_precision = 2, $dec_point = ",", $thousands_sep = ".")
{
    static $sizes = array();
    
    if(!count($sizes)) {
        $sizes["kg"]    =    1;             // kg: xt default in products details
        $sizes["g"]     =    1000;          // gram
        $sizes["mg"]    =    1000000;       // milli gram
        $sizes["t"]     =    0.001;         // tonne
        $sizes["oz"]    =    35.273966;     // ounce
        $sizes["pound"] =    2.204623;      // pound
        $sizes["st"]    =    0.157473;      // stones
        
        $sizes = array_reverse($sizes,true);
    }

    if(!array_key_exists($format, $sizes))
    {
        return $xt_weight.' '.$format;
    }

    $multiplier = $sizes[$format];
    $precision = $max_precision;

    if(is_int($max_precision))
    {
        $precision = 0;

        for (; $precision <= $max_precision; $precision++)
        {
            $weight_max_prec = round($xt_weight * $multiplier, $max_precision);
            $weight_cur_prec = round($xt_weight * $multiplier, $precision);
            if ($weight_max_prec == $weight_cur_prec)
            {
                break;
            }
        }
    }

    $weight = number_format($xt_weight * $multiplier, $precision, $dec_point, $thousands_sep);
    return $weight ." ".$format;
}
