<?php 

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if ($_GET['type']=='cleancache' && isset($_GET['typeid'])) {
	$typeid = $_GET['typeid'];
	$params = 'cleancache=true&typeid='.$_GET['typeid'].'&seckey='._SYSTEM_SECURITY_KEY;
	$iframe_target = $xtLink->_adminlink(array('default_page'=>'cronjob.php', 'params'=>$params));
	echo '<iframe src="'.$iframe_target.'" frameborder="0" width="100%" height="500"></iframe>';
}
