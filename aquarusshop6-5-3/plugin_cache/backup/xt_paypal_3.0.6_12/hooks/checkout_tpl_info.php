<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if (constant('XT_PAYPAL_EXPRESS') == 1 && $_SESSION['paypalExpressCheckout']==true)
{
    if (array_key_exists('XT_PPEXPRESS__conditions_accepted', $_SESSION) && $_SESSION['XT_PPEXPRESS__conditions_accepted']==1)
    {
            $smarty->tpl_vars['conditions_accepted']=1;
        unset($_SESSION['XT_PPEXPRESS__conditions_accepted']);
    }
    else
    {
            $smarty->tpl_vars['conditions_accepted']=0;
    }
}
