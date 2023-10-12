<?php
use function GuzzleHttp\json_decode;
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

require_once('../xtFramework/library/vendor/autoload.php');

date_default_timezone_set('Europe/Berlin');
$sys_dir = $_SERVER['SCRIPT_NAME'];
$sys_dir = substr($sys_dir, 0, strripos($sys_dir, '/') + 1);
$sys_dir = str_replace('xtWizard/', '', $sys_dir);

// Check if license file exists
$lic_file = dirname(__DIR__) . DIRECTORY_SEPARATOR . "lic" . DIRECTORY_SEPARATOR . "license.txt";

// check if license dir is write-able
$isWriteable = true;
if (!is_writable( dirname(__DIR__) . DIRECTORY_SEPARATOR . "lic" . DIRECTORY_SEPARATOR)) {
    $isWriteable = false;
}

$errors = array();
$language = isset($_GET['install_lang']) ? $_GET['install_lang'] : "de";
$default_country = 'DE';

$langFile = 'lang' . DIRECTORY_SEPARATOR . $language . '.yml';

$handle = fopen (dirname(__DIR__) . DIRECTORY_SEPARATOR . 'media/lang/de_countries.csv',"r");
$country_data = array();
while ( ($data = fgetcsv ($handle, 1000, ";",'"')) !== FALSE ) {
	$country = array();
	$country['code']=$data[2];
	$country['name']=$data[1];
	$country_data[]=$country;
}
fclose ($handle);

if (file_exists($langFile)) {

	$contents = file_get_contents($langFile);
	$languageParts = explode("\n", $contents);
	
	foreach ($languageParts as $part) {
		$delimiterPos = strpos($part, "=", 0);
			
		if ($delimiterPos === false) {
			continue;
		}
		$systemParts = substr($part, 0, $delimiterPos);
		$value = substr($part, $delimiterPos+1);
			
		list($plugin, $type, $key) = explode(".", $systemParts);
		if (!defined($key)) {
			$value = str_replace(array("\r", "\n"), '', $value);
			define($key, $value);
		}
	}
}
if (!empty($_POST)) {
	
	
	if ($_GET['action']) {
		
		switch ($_GET['action']) {
		
			case 'new_license':
			
				$data = array();
				$data['email']=$_POST['email'];
				$data['company']=$_POST['company'];
				$data['first_name']=$_POST['first_name'];
				$data['last_name']=$_POST['last_name'];
				$data['street_address']=$_POST['street_address'];
				$data['city']=$_POST['city'];
				$data['zip_code']=$_POST['zip_code'];
				$data['country_code']=$_POST['country_code'];
				$data['phone']=$_POST['phone'];
				$data['language_code']=$language;
				$data['domain']=$_SERVER['HTTP_HOST'];
				$data['remote_ip']=$_SERVER['SERVER_ADDR'];
                if (isset($_POST['newsletter'])) $data['newsletter']=$_POST['newsletter'];
				
				$webservice = 'https://webservices.xt-commerce.com/';
				
				$client = new GuzzleHttp\Client ( [
				'base_uri' => $webservice,
				'auth' => [
						'public',
						'public'
				]
				] );
				$_field_errors = array ();
				$fatal = false;
				try {


					$response = $client->request ( 'POST', 'license/register_trial', [
							'json' => $data
					] );


				
				} catch (GuzzleHttp\Exception\ClientException $e) {


				if ($e->hasResponse ()) {

					
					$response = json_decode ( $e->getResponse ()->getBody () );

					if ($response->error == 'INVALID_PARAMETER') {
						foreach ( $response as $e_field => $e_msg ) {
							$_field_errors [$e_field] = $e_msg [0];
						}
					}
				}
				
				} catch (GuzzleHttp\Exception\RequestException $e) {

                        $message = $e->getMessage();

                        if (strstr($message,'cURL error 60: SSL certificate problem: unable to get local issuer certificate')) {
                            $errors[]=_ERROR_CURL_SSL . "\n";
                        } else {
                            $errors[]=$message . "\n";
                        }


						$fatal=true;
				}
				
				if (count($_field_errors)==0 && !$fatal) {
					
					$body = $response->getBody();
					$body = json_decode($body);

					
					$lic_file = (dirname(__DIR__)) . "/lic/";
					foreach ($body->LIC_FILE as $file) {
								$fileName = (string)$file->Name;
								$fileContent = (string)$file->Content;
								$fileContent = base64_decode($fileContent);
									
							if (!file_exists($lic_file . $fileName)); {
									file_put_contents($lic_file . $fileName, $fileContent);
							}
					}
					header("Location: index.php");
					break;
					
				}
				
			
				
			break;
				
		}	
		
	}
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>xt:Commerce - <?php echo TEXT_NO_LICENSE; ?></title>

    <!-- Bootstrap -->
    <link href="templates/css/bootstrap.min.css" rel="stylesheet">
    <link href="templates/css/custom.css" rel="stylesheet">
	<link rel="shortcut icon" href="templates/img/logo/favicon.ico" type="image/x-icon" />
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="templates/js/jquery-1.11.1.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="templates/js/bootstrap.min.js"></script>
  </head>
  <body>

    <!-- Begin page content -->
    <div class="container">
    	<div class="header">
	        <ul class="nav nav-pills pull-right">
	                <label for="install_lang"><?php echo _TEXT_SELECT_LANGUAGE; ?></label>
	                <a href="no-license.php?install_lang=de"><img src="../media/flags/de.gif" /></a>
	                <a href="no-license.php?install_lang=en"><img src="../media/flags/gb.gif" /></a>
	        </ul>
        <h3 class="text-muted"><img src="templates/img/top_logo.jpg"alt="" /></h3>
      	</div>
<?php if (!$isWriteable) { ?>

    <div class="alert alert-danger">
        <div class="panel-heading"><?php echo TEXT_LIC_NOT_WRITEABLE_HEADER; ?></div>
        <div class="panel-body"><?php echo TEXT_LIC_NOT_WRITEABLE; ?></div>
    </div>

<?php } else { ?>

<div class="panel panel-warning">
  <div class="panel-heading"><?php echo TEXT_NO_LICENSE_HEADING; ?></div>
  <div class="panel-body"><?php echo TEXT_NO_LICENSE_FREE; ?></div>
</div>
		
		
      <div class="row" style="margin: 0px;">
      	<?php if (!empty($errors)) : ?>
      	<div class="alert alert-danger" role="alert"><?php echo join("<br/>", $errors); ?></div>
      	<?php endif;?>

	    
	    
	    <div class="panel-group" id="accordion">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">
        <?php echo TEXT_LICENSE_FORM_HEADER_KLARNA; ?></a>
      </h4>
    </div>
    <div id="collapse1" class="panel-collapse collapse in">
      <!-- start free license block -->
      <div class="panel-body">
      
      
   
   
   	<form method="post" id="registerForm" action="no-license.php?action=new_license" class="form-horizontal">

   	<div class="col-md-12">
   	     <?php if (isset ($_field_errors) && is_array($_field_errors)) : ?>
      	<div class="alert alert-danger" role="alert"><?php echo TEXT_ERROR_LICENSE_FORM; ?></div>
      	<?php endif;?>
   	</div>
    		<fieldset class="col-xs-12">
    			<div class="col-xs-6">
                    <div>
                        <img src="https://www.xt-commerce.com/wp-content/uploads/2019/03/product_box_6_pro_shadow-2.png" style="height:250px;"/>
                    </div>
    			<div class="form-group m-0">
    			<label class="form-group" for="company"><?php echo _TEXT_SHOPOWNER_COMPANY; ?>:</label>
    				<input type="text" name="company" id="company" value="<?php if (isset($data['company'])) echo $data['company'];?>" size="40" class="form-control"/>
    			</div>
    			<div class="form-group m-0">
    				<label class="form-group<?php if (isset($_field_errors['first_name'])) echo ' is-invalid';?>" for="first_name"><?php echo _TEXT_SHOPOWNER_FIRSTNAME; ?>:*</label>
    				<input type="text" name="first_name" size="40" value="<?php if (isset($data['first_name'])) echo $data['first_name'];?>" class="form-control"/>
    			</div>
    			<div class="form-group m-0<?php if (isset($_field_errors['last_name'])) echo ' has-error';?>">
    				<label class="form-group" for="last_name"><?php echo _TEXT_SHOPOWNER_LASTNAME;?>:*</label>
    				<input type="text" name="last_name" size="40" value="<?php if (isset($data['last_name'])) echo $data['last_name'];?>" class="form-control"/>
    			</div>

    			
    			
    				
    			</div>
    			<div class="col-xs-6">
    				
    				<div class="form-group m-0<?php if (isset($_field_errors['street_address'])) echo ' has-error';?>">
    			<label class="form-group" for="street_address"><?php echo _TEXT_SHOPOWNER_STREETADDRESS; ?>:*</label>
    				<input type="text" name="street_address" size="40" value="<?php if (isset($data['street_address'])) echo $data['street_address'];?>" class="form-control"/>
    			</div>
    			<div class="form-group m-0<?php if (isset($_field_errors['zip_code'])) echo ' has-error';?>">
    			<label class="form-group" for="zip_code"><?php echo _TEXT_SHOPOWNER_ZIP; ?>:*</label>
    				<input type="text" name="zip_code" size="10" value="<?php if (isset($data['zip_code'])) echo $data['zip_code'];?>" class="form-control"/>
    			</div>
    			<div class="form-group m-0<?php if (isset($_field_errors['city'])) echo ' has-error';?>">
    			<label class="form-group" for="city"><?php echo _TEXT_SHOPOWNER_CITY; ?>:*</label>
    				<input type="text" name="city" size="40" value="<?php if (isset($data['city'])) echo $data['city'];?>" class="form-control"/>
    			</div>
    			<div class="form-group m-0">
    			<label class="form-group" for="country_code"><?php echo _TEXT_DATABASE_COUNTRY; ?>:</label>
                    <select name="country_code" class="form-control">
                        <?php 
                        foreach ($country_data as $key => $cntry) {                      
                            $selected = '';
                            if($cntry['code'] == $default_country) $selected = 'selected="yes"';
                        	echo '<option value="'.$cntry['code'].'" '.$selected. '>'.$cntry['name'].'</option>';
                        	
                        }?>
                    </select>
    			</div>
    			<div class="form-group m-0">
    			<label class="form-group" for="phone"><?php echo _TEXT_SHOPOWNER_TELEPHONE; ?>:</label>
    				<input type="text" name="phone" size="40" value="<?php if (isset($data['phone'])) echo $data['phone'];?>" class="form-control"/>
    			</div>
    			<div class="form-group m-0">
    			
    			<div class="form-group m-0<?php if (isset($_field_errors['email'])) echo ' has-error';?>">
    			<label class="form-group" for="email"><?php echo _TEXT_SHOPOWNER_EMAIL ?>:*</label>
    				<input type="text" name="email" value="<?php if (isset($data['email'])) echo $data['email'];?>" size="40" class="form-control"/>
    			</div>
                    <div class="form-group m-0">
                    <label class="checkbox form-group" for="newsletter"></label>
                    <input tabindex="13" type="checkbox" name="newsletter" value="1"<?php if (isset($data['newsletter'])) echo ' checked="checked"';?>> <?php echo _TEXT_SIGNUP_NEWSLETTER; ?>
                    </div>



                </div>
    		</fieldset>

    		<fieldset class="col-xs-12">
    			<div class="col-xs-6">
    				<p class="">* <?php echo TEXT_MUST;?></p>
    			</div>
    		</fieldset>
    		<input type="submit" id="submitbutton" class="btn btn-large btn-success next-btn" value="<?php echo TEXT_BUTTON_NEXT; ?>" style="float:right">
		</form>
   
      
      
      
      </div>
      <!-- end free license block -->
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapse3">
        <?php echo TEXT_LICENSE_FORM_HEADER_OTHER; ?></a>
      </h4>
    </div>
    <div id="collapse3" class="panel-collapse collapse">
      <div class="panel-body">
      <ul class="list-inline">
					<li><a target="_blank" href="<?php echo TEXT_SHOP_LICENSE_LINK; ?>" class="btn btn-primary"><?php echo TEXT_SHOP_LICENSE_LINK_TEXT; ?></a></li>
	  </ul>
      </div>
    </div>
  </div>

   <?php } ?>

<br /><br />
            <div id="footer">
                <div class="container">xt:Commerce 6 &copy; <a target="_blank" href="https://www.xt-commerce.com">xt:Commerce</a> 2007-<?php echo date("Y", strtotime("now")); ?></div>
            </div>

			</div>
		</div>
      </div>
    </div>

    <script type="text/javascript">
	    $(document).ready(function() {
	        $("body").tooltip({ selector: '[data-toggle=tooltip]' });
            $("#submitbutton").attr("enabled", true);
            $("#registerForm").submit(function (e) {
                //disable the submit button
                $("#submitbutton").attr("disabled", true);
                return true;

            });
	    });
    </script>
  </body>
</html>
