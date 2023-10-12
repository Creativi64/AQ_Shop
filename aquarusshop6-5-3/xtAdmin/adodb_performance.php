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

include '../xtFramework/admin/main.php';


if (!$xtc_acl->isLoggedIn()) {
	die('login required');
}
/*
$rs = $db->Execute("SHOW GRANTS FOR CURRENT_USER");
$super_privilege = false;
while (!$rs->EOF) {
	__debug($rs->fields);
	if ($rs->fields['Privilege']=='Super') $super_privilege=true;
	$rs->MoveNext();
}

if (!$super_privilege) die (ERROR_SUPER_PRIVILEGES_NEEDED);
*/
define('ADODB_PERF_NO_RUN_SQL',1);
$perf =& NewPerfMonitor($db);

$perf->table = '<table class="monitor" width="100%">';


switch ($_GET['mode']) {
	
	case 'health':
		echo $perf->HealthCheck();
		break;
	
	case 'query':
		echo $perf->ExpensiveSQL($numsql=10);
		echo $perf->SuspiciousSQL($numsql=10);
		echo $perf->InvalidSQL($numsql=10);
		break;

}

?>