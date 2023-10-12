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

include_once '../xtFramework/admin/main.php';

if (!$xtc_acl->isLoggedIn()) {
	die('login required');
}

include (_SRV_WEBROOT_ADMIN.'page_includes.php');

		$obj = new stdClass;
		if (!$_FILES["Filedata"] && $_FILES["file"]) {
			$_FILES["Filedata"] = $_FILES["file"];
			
		} elseif ($_REQUEST['Filename']) {
		$filename = $_REQUEST['Filename'];
		}
		
		if (!$filename) {
			$filename = $_FILES['Filedata']['name'];
		}
		
		if (isset($_POST['currentType'])) {
			$pfile = _SRV_WEBROOT.'plugins/'.$_POST['currentType'].'/classes/class.'.$_POST['currentType'].'.php';

			if (file_exists($pfile)) {
				require_once $pfile;
			}				
			
		} 		
		
		$md = new MediaData();
		$md->setClass($_POST["currentType"]);
		$md->url_data = $_REQUEST;
		$class = "Media".ucfirst($md->_getFileTypesByExtension($filename));

		if (class_exists($class)) {
		$md = new $class;
		$md->url_data = $_REQUEST;
		$obj = $md->Upload($filename);

		
		if($obj->success==1){
			if ($_REQUEST['upload_type'] == 'multiple') {
				echo "Flash requires that we output something or it won't fire the uploadSuccess event";
			} else {
				echo "<div>".TEXT_UPLOAD_SUCCESS."</div>";
			}
		}else{
			if ($_REQUEST['upload_type'] == 'multiple') {
				echo "There was a problem with the upload ".(!empty($obj->waitMsg) ? $obj->waitMsg:'');
				exit(0);
			} else {

			echo "<div>".TEXT_UPLOAD_ERROR.' '.(!empty($obj->waitMsg) ? $obj->waitMsg:'')."</div>";
            echo "<div>post_max_size = ".ini_get('post_max_size')."</div>";
            echo "<div>upload_max_filesize = ".ini_get('upload_max_filesize')."</div>";
			}
		}
		} else {
			echo "class not found";
		}
