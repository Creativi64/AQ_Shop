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

if (CSRF_PROTECTION!='false')
{
	$xtc_acl->checkAdminKey();
}


include (_SRV_WEBROOT_ADMIN.'page_includes.php');


if (!$_REQUEST['type'])
$_REQUEST['type'] = 'images';

if(preg_match('/files/', $_REQUEST['type'])){
	$data_type = $_REQUEST['type'];
	$check_data_type = 'files';
	$accept = '.zip,.pdf';
}else{
	$data_type = 'images';
    $accept = '.jpeg,.jpg,.gif,.png';
}

$mft = new MediaFileTypes();
$types = $mft->getFileExt($check_data_type);

        if($_GET['current_id']!='')
        $tmp_add .= '&current_id='.(int)$_GET['current_id'];
        
        if($_GET['current_sort']!='')
        $tmp_add .= '&current_sort='.(int)$_GET['current_sort'];

// default upload
$uploadtype = 'single';

if ($_REQUEST['uploadtype'])
$uploadtype = $_REQUEST['uploadtype'];


switch ($uploadtype) {
	
	
	case "multiple":
		
		break;

	case "single":
		
		$js = "function ajaxsubmit () {
		    var options = {
		        target:        '#uploadOutputSingle',  // target element(s) to be updated with server response
		        beforeSubmit:  showRequest,
				success: refreshTabs,
		    };

            // bind 'myForm' and provide a simple callback function
            jQuery('#uploadFormSimple').ajaxForm(options);
            }
       
            function showRequest(formData, jqForm, options) { 
			    var queryString = $.param(formData); 
			 
			    // jqForm is a jQuery object encapsulating the form element.  To access the 
			    // DOM element for the form do this: 
			    var formElement = jQuery('#simpleUploadfile').attr('value'); 
			 
			    jQuery('#uploadOutputSingle').html('<img src=\"images/wait.gif\" \/><br \/>".TEXT_UPLOADING."' + formElement);


			    return true; 
			} 
            
			function refreshTabs() {
			   	if  (typeof(Ext.getCmp('MediaImageListgridForm')) != 'undefined') {
			    	Ext.getCmp('MediaImageListgridForm').getStore().load();
			    }
			   	if (typeof(Ext.getCmp('MediaImageSearchgridForm')) != 'undefined') {
			    	Ext.getCmp('MediaImageSearchgridForm').getStore().load();
			    }
			}
       ajaxsubmit();
       
       ";

		if ($_REQUEST['link_id'])
			$link_id = '<input name="link_id" type="hidden" value="'. $_GET['link_id'].'" id="simpleUploadtype"/>';
			
		if ($_REQUEST['mgID']) 
			$link_id .= '<input name="mgID" type="hidden" value="'. $_GET['mgID'].'" id="simpleUploadmgID"/>';
				
		$uploadForm = '<form id="uploadFormSimple" action="upload_process.php" method="post" enctype="multipart/form-data">
			<div class="fieldset flash" id="fsUploadProgress_single">
			<div id="uploadOutputSingle"></div>
			</div>

			<div class="fieldset">
			    <span class="btn-sm">'.$accept.'</span>
                <input name="type" type="hidden" value="'. $_GET['type'].'" id="simpleUploadtype"/>
                <input name="currentType" type="hidden" value="'. $_GET['currentType'].'" id="simpleUploadCurrentType"/>
                <input name="upload_type" type="hidden" value="'. $uploadtype.'" id="simpleUploadUploadType"/>
                
                '.$link_id.'
				<input name="file" type="file" accept="'.$accept.'" value="" id="simpleUploadfile" class="upload btn-app btn-block" />
				<input id="uploadButton" type="submit" value="'.TEXT_UPLOAD_SUBMIT.'" class="btn-sm btn-primary" style="margin-left:10px" onclick="javascript:if(document.getElementById(\'simpleUploadfile\').files.length == 0) return false;">
			</div>		
		</form>';
		
		break;
		
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="https://www.w3.org/1999/xhtml" >
<head>
<script type="text/javascript"><?php echo $js;?></script>

<title>FileUpload</title>
</head>
<style>

.upload_form {
    height: 100%;
    margin: auto;
    width: 50%;
    padding: 140px 0;
}

</style>
<body>
<div class="upload_form">
	<?php echo $uploadForm; ?>
</div>
</body>
</html>