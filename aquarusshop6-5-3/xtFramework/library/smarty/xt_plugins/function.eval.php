<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {eval} function plugin
 *
 * Type:     function<br>
 * Name:     eval<br>
 * Purpose:  evaluate a template variable as a template<br>
 * @link http://smarty.php.net/manual/en/language.function.eval.php {eval}
 *       (Smarty online manual)
 * @author Monte Ohrt <monte at ohrt dot com>
 * @param array
 * @param Smarty
 */
function smarty_function_eval ($params, &$smarty)
{

    if (!isset($params['var'])) {
        $smarty->trigger_error("eval: missing 'var' parameter");
        return;
    }

    if ($params['var'] == '') {
        return;
    }

    $_contents = $smarty->fetch('string:'.$params['var']);

    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $_contents);
    } else {
        return $_contents;
    }
}

/* vim: set expandtab: */

?>