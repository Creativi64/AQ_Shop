<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if(isset($_GET['page']) && $_GET['page'] == 'xt_soap' )
{
    $qry_where = " where l.languages_id != '' ";
    if ($list_type!='all')
    {
        $qry_where .= " and l.language_status = '1'";
    }
}
